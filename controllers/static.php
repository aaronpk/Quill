<?php

function doc_pages($page=null) {
  $pages = [
    'signing-in' => 'Signing In',
    'creating-posts' => 'Creating Posts',
    'editor' => 'Rich Editor',
    'note' => 'Note Interface',
    'bookmark' => 'Bookmark Interface',
    'syndication' => 'Syndication',
    'post-status' => 'Post Status',
  ];
  if($page == null) 
    return $pages;
  else
    return $pages[$page];
}


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
  render('docs/index', array(
    'title' => 'Documentation', 
    'authorizing' => false,
    'pages' => doc_pages()
  ));
});

$app->get('/docs/:page', function($page) use($app) {
  if(file_exists('views/docs/'.$page.'.php'))
    render('docs/'.$page, array(
      'title' => doc_pages($page).' - Quill Documentation', 
      'authorizing' => false
    ));
  else
    $app->notFound();
});

$app->get('/privacy', function() use($app) {
  render('privacy', array('title' => 'Quill Privacy Policy', 'authorizing' => false));
});
