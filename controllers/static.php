<?php

$app->get('/', function($format='html') use($app) {
  $res = $app->response();
  $params = $app->request()->params();
  if (k($params, 'me')) {
    $app->redirect('/auth/start?'.http_build_query($params), 302);
  }

  ob_start();
  render('index', array(
    'title' => 'Quill',
    'meta' => '',
    'authorizing' => false
  ));
  $html = ob_get_clean();
  $res->body($html);
});

$app->get('/creating-a-token-endpoint', function() use($app) {
  $app->redirect('http://indiewebcamp.com/token-endpoint', 301);
});

$app->get('/creating-a-micropub-endpoint', function() use($app) {
  $html = render('creating-a-micropub-endpoint', array('title' => 'Creating a Micropub Endpoint', 'authorizing' => false));
  $app->response()->body($html);
});

$app->get('/docs', function() use($app) {
  $html = render('docs', array('title' => 'Documentation', 'authorizing' => false));
  $app->response()->body($html);
});

$app->get('/privacy', function() use($app) {
  $html = render('privacy', array('title' => 'Quill Privacy Policy', 'authorizing' => false));
  $app->response()->body($html);
});