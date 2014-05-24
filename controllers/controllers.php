<?php

function require_login(&$app) {
  if(!array_key_exists('user_id', $_SESSION)) {
    $app->redirect('/');
    return false;
  } else {
    return ORM::for_table('users')->find_one($_SESSION['user_id']);
  }
}

$app->get('/new', function() use($app) {
  if($user=require_login($app)) {

    $entry = false;
    $photo_url = false;

    $test_response = '';
    if($user->last_micropub_response) {
      try {
        if(@json_decode($user->last_micropub_response)) {
          $d = json_decode($user->last_micropub_response);
          $test_response = $d->response;
        }
      } catch(Exception $e) {
      }
    }

    $html = render('dashboard', array(
      'title' => 'New Post',
      'micropub_endpoint' => $user->micropub_endpoint,
      'micropub_scope' => $user->micropub_scope,
      'micropub_access_token' => $user->micropub_access_token,
      'response_date' => $user->last_micropub_response_date,
      'test_response' => $test_response
    ));
    $app->response()->body($html);
  }
});

$app->get('/creating-a-token-endpoint', function() use($app) {
  $app->redirect('http://indiewebcamp.com/token-endpoint', 301);
});
$app->get('/creating-a-micropub-endpoint', function() use($app) {
  $html = render('creating-a-micropub-endpoint', array('title' => 'Creating a Micropub Endpoint'));
  $app->response()->body($html);
});

$app->get('/docs', function() use($app) {
  $html = render('docs', array('title' => 'Documentation'));
  $app->response()->body($html);
});

$app->post('/micropub/post', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    // Now send to the micropub endpoint
    $r = micropub_post($user->micropub_endpoint, $params, $user->micropub_access_token);
    $response = $r['response'];

    $user->last_micropub_response = json_encode($r);
    $user->last_micropub_response_date = date('Y-m-d H:i:s');

    // Check the response and look for a "Location" header containing the URL
    if($response && preg_match('/Location: (.+)/', $response, $match)) {
      $location = $match[1];
      $user->micropub_success = 1;
    } else {
      $location = false;
    }

    $user->save();

    $app->response()->body(json_encode(array(
      'response' => htmlspecialchars($response),
      'location' => $location,
      'error' => $r['error'],
      'curlinfo' => $r['curlinfo']
    )));
  }
});

