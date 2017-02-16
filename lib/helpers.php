<?php

if(isset(Config::$dbType) && Config::$dbType == 'sqlite') {
  ORM::configure('sqlite:' . Config::$dbFilePath);
} else {
  ORM::configure('mysql:host=' . Config::$dbHost . ';dbname=' . Config::$dbName);
  ORM::configure('username', Config::$dbUsername);
  ORM::configure('password', Config::$dbPassword);  
}

function render($page, $data) {
  global $app;
  return $app->render('layout.php', array_merge($data, array('page' => $page)));
};

function partial($template, $data=array(), $debug=false) {
  global $app;

  if($debug) {
    $tpl = new Savant3(\Slim\Extras\Views\Savant::$savantOptions);
    echo '<pre>' . $tpl->fetch($template . '.php') . '</pre>';
    return '';
  }

  ob_start();
  $tpl = new Savant3(\Slim\Extras\Views\Savant::$savantOptions);
  foreach($data as $k=>$v) {
    $tpl->{$k} = $v;
  }
  $tpl->display($template . '.php');
  return ob_get_clean();
}

function js_bookmarklet($partial, $context) {
  return str_replace('+','%20',urlencode(str_replace(array("\n"),array(''),partial($partial, $context))));
}

function session($key) {
  if(array_key_exists($key, $_SESSION))
    return $_SESSION[$key];
  else
    return null;
}

function k($a, $k, $default=null) {
  if(is_array($k)) {
    $result = true;
    foreach($k as $key) {
      $result = $result && array_key_exists($key, $a);
    }
    return $result;
  } else {
    if(is_array($a) && array_key_exists($k, $a) && $a[$k])
      return $a[$k];
    elseif(is_object($a) && property_exists($a, $k) && $a->$k)
      return $a->$k;
    else
      return $default;
  }
}

function display_url($url) {
  $parts = parse_url($url);
  if(isset($parts['path']) && $parts['path'] != '' && $parts['path'] != '/') {
    return preg_replace('/^https?:\/\//','', $url);
  } else {
    return $parts['host'];
  }
}

if(!function_exists('http_build_url')) {
  function http_build_url($parsed_url) {
    $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
    $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
    $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
    $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
    $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
    $pass     = ($user || $pass) ? "$pass@" : '';
    $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
    $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
    return "$scheme$user$pass$host$port$path$query$fragment";
  }
}

function micropub_post_for_user(&$user, $params, $file_path = NULL, $json = false) {
  // Now send to the micropub endpoint
  $r = micropub_post($user->micropub_endpoint, $params, $user->micropub_access_token, $file_path, $json);

  $user->last_micropub_response = substr(json_encode($r), 0, 1024);
  $user->last_micropub_response_date = date('Y-m-d H:i:s');

  // Check the response and look for a "Location" header containing the URL
  if($r['response'] && ($r['code'] == 201 || $r['code'] == 202) 
    && isset($r['headers']['Location'])) {
    $r['location'] = $r['headers']['Location'][0];
    $user->micropub_success = 1;
  } else {
    $r['location'] = false;
  }

  $user->save();

  return $r;
}

function micropub_media_post_for_user(&$user, $file_path) {
  // Send to the media endpoint
  $r = micropub_post($user->micropub_media_endpoint, [], $user->micropub_access_token, $file_path, true, 'file');

  // Check the response and look for a "Location" header containing the URL
  if($r['response'] && preg_match('/Location: (.+)/', $r['response'], $match)) {
    $r['location'] = trim($match[1]);
  } else {
    $r['location'] = false;
  }

  return $r;
}

function micropub_post($endpoint, $params, $access_token, $file_path = NULL, $json = false, $file_prop = 'photo') {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $endpoint);
  curl_setopt($ch, CURLOPT_POST, true);

  // Send the access token in both the header and post body to support more clients
  // https://github.com/aaronpk/Quill/issues/4
  // http://indiewebcamp.com/irc/2015-02-14#t1423955287064
  $httpheaders = array('Authorization: Bearer ' . $access_token);

  if(!$json) {
    $params = array_merge(array(
      'h' => 'entry',
      'access_token' => $access_token
    ), $params);
  }

  if(!$file_path) {
    if($json) {
      $params['access_token'] = $access_token;
      $httpheaders[] = 'Content-type: application/json';
      $post = json_encode($params);
    } else {
      $post = http_build_query($params);
      $post = preg_replace('/%5B[0-9]+%5D/', '%5B%5D', $post); // change [0] to []
    }
  } else {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimetype = finfo_file($finfo, $file_path);
    $multipart = new p3k\Multipart();
    $multipart->addArray($params);
    $multipart->addFile($file_prop, $file_path, $mimetype);
    $post = $multipart->data();
    array_push($httpheaders, 'Content-Type: ' . $multipart->contentType());
  }

  curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheaders);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);

  $response = curl_exec($ch);
  $error = curl_error($ch);
  $sent_headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);

  $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
  $header_str = trim(substr($response, 0, $header_size));

  $request = $sent_headers . (is_string($post) ? $post : http_build_query($post));
  return array(
    'request' => $request,
    'response' => $response,
    'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
    'headers' => parse_headers($header_str),
    'error' => $error,
    'curlinfo' => curl_getinfo($ch)
  );
}

function micropub_get($endpoint, $params, $access_token) {
  $url = parse_url($endpoint);
  if(!k($url, 'query')) {
    $url['query'] = http_build_query($params);
  } else {
    $url['query'] .= '&' . http_build_query($params);
  }
  $endpoint = http_build_url($url);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $endpoint);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $access_token,
    'Accept: application/json'
  ));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  $data = array();
  if($response) {
    $data = json_decode($response, true);
  }
  $error = curl_error($ch);
  return array(
    'response' => $response,
    'data' => $data,
    'error' => $error,
    'curlinfo' => curl_getinfo($ch)
  );
}

function parse_headers($headers) {
  $retVal = array();
  $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
  foreach($fields as $field) {
    if(preg_match('/([^:]+): (.+)/m', $field, $match)) {
      $match[1] = preg_replace_callback('/(?<=^|[\x09\x20\x2D])./', function($m) {
        return strtoupper($m[0]);
      }, strtolower(trim($match[1])));
      // If there's already a value set for the header name being returned, turn it into an array and add the new value
      $match[1] = preg_replace_callback('/(?<=^|[\x09\x20\x2D])./', function($m) {
        return strtoupper($m[0]);
      }, strtolower(trim($match[1])));
      if(isset($retVal[$match[1]])) {
        $retVal[$match[1]][] = trim($match[2]);
      } else {
        $retVal[$match[1]] = [trim($match[2])];
      }
    }
  }
  return $retVal;
}

function get_micropub_config(&$user, $query=[]) {
  $targets = [];

  $r = micropub_get($user->micropub_endpoint, $query, $user->micropub_access_token);
  if($r['data'] && is_array($r['data']) && array_key_exists('syndicate-to', $r['data'])) {
    if(is_array($r['data']['syndicate-to'])) {
      $data = $r['data']['syndicate-to'];
    } else {
      $data = [];
    }

    foreach($data as $t) {
      if(is_array($t) && array_key_exists('service', $t) && array_key_exists('photo', $t['service'])) {
        $icon = $t['service']['photo'];
      } else {
        $icon = false;
      }

      if(is_array($t) && array_key_exists('uid', $t) && array_key_exists('name', $t)) {
        $targets[] = [
          'target' => $t['name'],
          'uid' => $t['uid'],
          'favicon' => $icon
        ];
      }
    }
  }

  if(count($targets))
    $user->syndication_targets = json_encode($targets);

  $media_endpoint = false;
  if($r['data'] && is_array($r['data']) && array_key_exists('media-endpoint', $r['data'])) {
    $media_endpoint = $r['data']['media-endpoint'];
    $user->micropub_media_endpoint = $media_endpoint;
  }

  if(count($targets) || $media_endpoint) {
    $user->save();
  }

  return [
    'targets' => $targets,
    'response' => $r
  ];
}

function static_map($latitude, $longitude, $height=180, $width=700, $zoom=14) {
  return 'https://atlas.p3k.io/map/img?marker[]=lat:' . $latitude . ';lng:' . $longitude . ';icon:small-blue-cutout&basemap=gray&width=' . $width . '&height=' . $height . '&zoom=' . $zoom;
}

function relative_time($date) {
  static $rel;
  if(!isset($rel)) {
    $config = array(
        'language' => '\RelativeTime\Languages\English',
        'separator' => ', ',
        'suffix' => true,
        'truncate' => 1,
    );
    $rel = new \RelativeTime\RelativeTime($config);
  }
  return $rel->timeAgo($date);
}

function instagram_client() {
  return new Andreyco\Instagram\Client(array(
    'apiKey'      => Config::$instagramClientID,
    'apiSecret'   => Config::$instagramClientSecret,
    'apiCallback' => Config::$base_url . 'auth/instagram/callback',
    'scope'       => array('basic','likes'),
  ));
}

function validate_photo(&$file) {
  try {

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && count($_POST) < 1 ) {
      throw new RuntimeException('File upload size exceeded.');
    }
   
    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($file['error']) ||
        is_array($file['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }

    // Check $file['error'] value.
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    // You should also check filesize here.
    if ($file['size'] > 4000000) {
        throw new RuntimeException('Exceeded filesize limit.');
    }

    // DO NOT TRUST $file['mime'] VALUE !!
    // Check MIME Type by yourself.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (false === $ext = array_search(
        $finfo->file($file['tmp_name']),
        array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        ),
        true
    )) {
        throw new RuntimeException('Invalid file format.');
    }

  } catch (RuntimeException $e) {

      return $e->getMessage();
  }
}

// Reads the exif rotation data and actually rotates the photo.
// Only does anything if the exif library is loaded, otherwise is a noop.
function correct_photo_rotation($filename) {
  if(class_exists('IMagick')) {
    $image = new IMagick($filename);
    $orientation = $image->getImageOrientation();
    switch($orientation) {
      case IMagick::ORIENTATION_BOTTOMRIGHT:
        $image->rotateImage(new ImagickPixel('#00000000'), 180);
        break;
      case IMagick::ORIENTATION_RIGHTTOP:
        $image->rotateImage(new ImagickPixel('#00000000'), 90);
        break;
      case IMagick::ORIENTATION_LEFTBOTTOM:
        $image->rotateImage(new ImagickPixel('#00000000'), -90);
        break;
    }
    $image->setImageOrientation(IMagick::ORIENTATION_TOPLEFT);
    $image->writeImage($filename);
  }
}

function sanitize_editor_html($html) {
  #error_log($html."\n");

  $config = HTMLPurifier_Config::createDefault();
  $config->autoFinalize = false;
  $config->set('Cache.DefinitionImpl', null);
  $config->set('HTML.AllowedElements', [
    'a',
    'abbr',
    'b',
    'br',
    'code',
    'del',
    'em',
    'i',
    'img',
    'q',
    'strike',
    'strong',
    'blockquote',
    'pre',
    'p',
    'h1',
    'h2',
    'h3',
    'h4',
    'h5',
    'h6',
    'ul',
    'li',
    'ol',
    'figcaption',
    'figure'
  ]);

  $def = $config->getHTMLDefinition(true);
  // http://developers.whatwg.org/grouping-content.html
  $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
  $def->addElement('figcaption', 'Inline', 'Flow', 'Common');

  // Allow data: URIs 
  $config->set('URI.AllowedSchemes', array('data' => true, 'http' => true, 'https' => true));

  // Strip all classes from elements
  $config->set('Attr.AllowedClasses', '');

  // $def = $config->getHTMLDefinition(true);
  $purifier = new HTMLPurifier($config);
  $sanitized = $purifier->purify($html);
  $sanitized = str_replace("&#xD;","\r",$sanitized);

  # Remove empty paragraphs
  $sanitized = str_replace('<p><br /></p>','',$sanitized);
  $sanitized = str_replace('<p></p>','',$sanitized);

  $indenter = new \Gajus\Dindent\Indenter([
    'indentation_character' => '  '
  ]);
  $indenter->setElementType('h1', \Gajus\Dindent\Indenter::ELEMENT_TYPE_INLINE);
  $indenter->setElementType('h2', \Gajus\Dindent\Indenter::ELEMENT_TYPE_INLINE);
  $indenter->setElementType('h3', \Gajus\Dindent\Indenter::ELEMENT_TYPE_INLINE);
  $indenter->setElementType('h4', \Gajus\Dindent\Indenter::ELEMENT_TYPE_INLINE);
  $indenter->setElementType('h5', \Gajus\Dindent\Indenter::ELEMENT_TYPE_INLINE);
  $indenter->setElementType('h6', \Gajus\Dindent\Indenter::ELEMENT_TYPE_INLINE);
  $sanitized = $indenter->indent($sanitized);

  #error_log($sanitized."\n");

  return $sanitized;
}
