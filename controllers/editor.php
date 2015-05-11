<?php

$app->get('/editor', function() use($app) {
  // Don't require login because appcache caches the whole page
  $html = $app->render('editor.php');
  $app->response()->body($html);
});

$app->post('/editor/publish', function() use($app) {

  if($user=require_login($app)) {
    $params = $app->request()->params();

    $micropub_request = array(
      'h' => 'entry',
      'name' => $params['name'],
      'content' => $params['body']
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
  // Fake a file uploader by echo'ing back the data URI
  $fn = $_FILES['files']['tmp_name'][0];
  $imageData = base64_encode(file_get_contents($fn));
  $src = 'data: '.mime_content_type($fn).';base64,'.$imageData;

  $app->response()['Content-type'] = 'application/json';
  $app->response()->body(json_encode([
    'files' => [
      [
        'url'=>$src
      ]
    ]
  ]));
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

// $app->get('/appcache.manifest', function() use($app) {
//   $content = partial('partials/appcache');

//   $app->response()['Content-type'] = 'text/cache-manifest';
//   $app->response()->body($content);
// });
