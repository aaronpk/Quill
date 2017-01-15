<?php

$app->get('/editor', function() use($app) {
  $user = require_login($app, false);
  $html = $app->render('editor.php', [
    'user' => $user
  ]);
  $app->response()->body($html);
});

$app->post('/editor/publish', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $content = $params['body'];

    if($user->micropub_optin_html_content) {
      $content = ['html' => $params['body']];
    }

    $micropub_request = array(
      'h' => 'entry',
      'name' => $params['name'],
      'content' => $content
    );

    if(array_key_exists('category', $params) && $params['category'])
      $micropub_request['category'] = $params['category'];

    if(array_key_exists('slug', $params) && $params['slug'])
      $micropub_request[$user->micropub_slug_field] = $params['slug'];

    if(array_key_exists('status', $params) && $params['status']) {
      if($params['status'] == 'draft')
        $micropub_request['post-status'] = $params['status'];
    }

    if(array_key_exists('publish', $params) && $params['publish'] != 'now') {
      $micropub_request['published'] = $params['publish'];
    }

    $r = micropub_post_for_user($user, $micropub_request);

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode([
      'location' => $r['location'],
      'response' => trim(htmlspecialchars($r['response']))
    ]));
  }
});

$app->post('/editor/upload', function() use($app) {
  if($user=require_login($app)) {
    $fn = $_FILES['files']['tmp_name'][0];
    $imageURL = false;

    if($user->micropub_media_endpoint) {
      // If the user has a media endpoint, upload to that and return that URL
      correct_photo_rotation($fn);
      $r = micropub_media_post_for_user($user, $fn);
      if(!empty($r['location'])) {
        $imageURL = $r['location'];
      }
    }
    if(!$imageURL) {
      // Otherwise, fake a file uploader by echo'ing back the data URI
      $imageData = base64_encode(file_get_contents($fn));
      $imageURL = 'data:'.mime_content_type($fn).';base64,'.$imageData;
    }
    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode([
      'files' => [
        ['url'=>$imageURL]
      ]
    ]));
  }
});

$app->post('/editor/parse-date', function() use($app) {
  $date = false;
  $params = $app->request()->params();
  if(isset($params['date'])) {
    if($params['date'] == 'now') {
      $date = 'now';
    } else {
      try {
        // Check if the provided date has a timezone offset
        $has_timezone = preg_match('/[-+]\d\d:?\d\d$/', $params['date']);

        if(!$has_timezone && $params['tzoffset']) {
          $s = (-60) * $params['tzoffset'];
          $h = $params['tzoffset'] / (-60);
          $tz = new DateTimeZone($h);
          $d = new DateTime($params['date'], $tz);
        } else {
          $d = new DateTime($params['date']);
        }
        $date = $d->format('c');
      } catch(Exception $e) {
      }
    }
  }

  $app->response()['Content-type'] = 'application/json';
  $app->response()->body(json_encode(['date'=>$date]));
});

$app->post('/editor/delete-file', function() use($app) {
  $app->response()['Content-type'] = 'application/json';
  $app->response()->body(json_encode(['result'=>'deleted']));
});

$app->get('/editor/oembed', function() use($app) {
  $url = 'http://medium.iframe.ly/api/oembed?iframe=1&url='.urlencode($app->request()->params()['url']);
  $json = file_get_contents($url);
  $app->response()['Content-type'] = 'application/json';
  $app->response()->body($json);  
});

$app->post('/editor/test-login', function() use($app) {
  $logged_in = array_key_exists('user_id', $_SESSION);
  $app->response()['Content-type'] = 'application/json';
  $app->response()->body(json_encode(['logged_in'=>$logged_in]));
});
