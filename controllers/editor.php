<?php

$app->get('/editor', function() use($app) {
  if($user=require_login($app)) {
    $html = $app->render('editor.php');
    $app->response()->body($html);
  }
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
