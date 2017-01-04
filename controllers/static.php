<?php

$app->get('/', function($format='html') use($app) {
  $res = $app->response();
  $params = $app->request()->params();
  if (k($params, 'me')) {
    $app->redirect('/auth/start?'.http_build_query($params), 302);
  }

  render('index', array(
    'title' => 'Quill',
    'meta' => '',
    'authorizing' => false
  ));
});

$app->get('/creating-a-token-endpoint', function() use($app) {
  $app->redirect('http://indiewebcamp.com/token-endpoint', 301);
});

$app->get('/creating-a-micropub-endpoint', function() use($app) {
  render('creating-a-micropub-endpoint', array('title' => 'Creating a Micropub Endpoint', 'authorizing' => false));
});

$app->get('/docs', function() use($app) {
  render('docs', array('title' => 'Documentation', 'authorizing' => false));
});

$app->get('/docs/post-status', function() use($app) {
  render('docs/post-status', array('title' => 'Post Status Documentation', 'authorizing' => false));
});

$app->get('/privacy', function() use($app) {
  render('privacy', array('title' => 'Quill Privacy Policy', 'authorizing' => false));
});

