<?php

ORM::configure('mysql:host=' . Config::$dbHost . ';dbname=' . Config::$dbName);
ORM::configure('username', Config::$dbUsername);
ORM::configure('password', Config::$dbPassword);

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

function get_timezone($lat, $lng) {
  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://timezone-api.geoloqi.com/timezone/'.$lat.'/'.$lng);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $tz = @json_decode($response);
    if($tz)
      return new DateTimeZone($tz->timezone);
  } catch(Exception $e) {
    return null;
  }
  return null;
}

function micropub_post($endpoint, $params, $access_token) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $endpoint);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $access_token
  ));
  curl_setopt($ch, CURLOPT_POST, true);
  $post = http_build_query(array_merge(array(
    'h' => 'entry'
  ), $params));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  $response = curl_exec($ch);
  $error = curl_error($ch);
  $sent_headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
  $request = $sent_headers . $post;
  return array(
    'request' => $request,
    'response' => $response,
    'error' => $error,
    'curlinfo' => curl_getinfo($ch)
  );
}

function micropub_get($endpoint, $params, $access_token) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $endpoint . '?' . http_build_query($params));
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $access_token
  ));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  $data = array();
  if($response) {
    parse_str($response, $data);
  }
  $error = curl_error($ch);
  return array(
    'response' => $response,
    'data' => $data,
    'error' => $error,
    'curlinfo' => curl_getinfo($ch)
  );
}

function get_syndication_targets(&$user) {
  $targets = array();

  $r = micropub_get($user->micropub_endpoint, array('q'=>'syndicate-to'), $user->micropub_access_token);
  if($r['data'] && array_key_exists('syndicate-to', $r['data'])) {
    $targetURLs = preg_split('/, ?/', $r['data']['syndicate-to']);
    foreach($targetURLs as $t) {

      // If the syndication target doesn't have a scheme, add http
      if(!preg_match('/^http/', $t))
        $tmp = 'http://' . $t;

      // Parse the target expecting it to be a URL
      $url = parse_url($tmp);

      // If there's a host, and the host contains a . then we can assume there's a favicon
      // parse_url will parse strings like http://twitter into an array with a host of twitter, which is not resolvable
      if(array_key_exists('host', $url) && strpos($url['host'], '.') !== false) {
        $targets[] = array(
          'target' => $t,
          'favicon' => 'http://' . $url['host'] . '/favicon.ico'
        );
      } else {
        $targets[] = array(
          'target' => $t,
          'favicon' => false
        );
      }
    }
  }
  if(count($targets)) {
    $user->syndication_targets = json_encode($targets);
    $user->save();
  }

  return array(
    'targets' => $targets,
    'response' => $r
  );
}

function static_map($latitude, $longitude, $height=180, $width=700, $zoom=14) {
  return 'http://static-maps.pdx.esri.com/img.php?marker[]=lat:' . $latitude . ';lng:' . $longitude . ';icon:small-blue-cutout&basemap=gray&width=' . $width . '&height=' . $height . '&zoom=' . $zoom;
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
