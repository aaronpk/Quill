<?php

function require_login(&$app) {
  $params = $app->request()->params();
  if(array_key_exists('token', $params)) {
    try {
      $data = JWT::decode($params['token'], Config::$jwtSecret);
      $_SESSION['user_id'] = $data->user_id;
      $_SESSION['me'] = $data->me;
    } catch(DomainException $e) {
      header('X-Error: DomainException');
      $app->redirect('/', 301);
    } catch(UnexpectedValueException $e) {
      header('X-Error: UnexpectedValueException');
      $app->redirect('/', 301);
    }
  }

  if(!array_key_exists('user_id', $_SESSION)) {
    $app->redirect('/');
    return false;
  } else {
    return ORM::for_table('users')->find_one($_SESSION['user_id']);
  }
}

function generate_login_token() {
  return JWT::encode(array(
    'user_id' => $_SESSION['user_id'],
    'me' => $_SESSION['me'],
    'created_at' => time()
  ), Config::$jwtSecret);
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

    $html = render('new-post', array(
      'title' => 'New Post',
      'micropub_endpoint' => $user->micropub_endpoint,
      'micropub_scope' => $user->micropub_scope,
      'micropub_access_token' => $user->micropub_access_token,
      'response_date' => $user->last_micropub_response_date,
      'syndication_targets' => json_decode($user->syndication_targets, true),
      'test_response' => $test_response,
      'location_enabled' => $user->location_enabled
    ));
    $app->response()->body($html);
  }
});


$app->get('/bookmark', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $url = '';
    $name = '';
    $content = '';
    $tags = '';

    if(array_key_exists('url', $params))
      $url = $params['url'];

    if(array_key_exists('name', $params))
      $name = $params['name'];

    if(array_key_exists('content', $params))
      $content = $params['content'];

    $html = render('new-bookmark', array(
      'title' => 'New Bookmark',
      'bookmark_url' => $url,
      'bookmark_name' => $name,
      'bookmark_content' => $content,
      'bookmark_tags' => $tags,
      'token' => generate_login_token(),
      'syndication_targets' => json_decode($user->syndication_targets, true)
    ));
    $app->response()->body($html);
  }
});

$app->post('/prefs', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();
    $user->location_enabled = $params['enabled'];
    $user->save();
  }
  $app->response()->body(json_encode(array(
    'result' => 'ok'
  )));
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

$app->get('/add-to-home', function() use($app) {
  $params = $app->request()->params();

  if(array_key_exists('token', $params) && !session('add-to-home-started')) {

    // Verify the token and sign the user in
    try {
      $data = JWT::decode($params['token'], Config::$jwtSecret);
      $_SESSION['user_id'] = $data->user_id;
      $_SESSION['me'] = $data->me;
      $app->redirect('/new', 301);
    } catch(DomainException $e) {
      header('X-Error: DomainException');
      $app->redirect('/', 301);
    } catch(UnexpectedValueException $e) {
      header('X-Error: UnexpectedValueException');
      $app->redirect('/', 301);
    }

  } else {

    if($user=require_login($app)) {
      if(array_key_exists('start', $params)) {
        $_SESSION['add-to-home-started'] = true;
        
        $token = JWT::encode(array(
          'user_id' => $_SESSION['user_id'],
          'me' => $_SESSION['me'],
          'created_at' => time()
        ), Config::$jwtSecret);

        $app->redirect('/add-to-home?token='.$token, 301);
      } else {
        unset($_SESSION['add-to-home-started']);
        $html = render('add-to-home', array('title' => 'Quill'));
        $app->response()->body($html);
      }
    }
  }
});

$app->get('/micropub/syndications', function() use($app) {
  if($user=require_login($app)) {
    $data = get_syndication_targets($user);
    $app->response()->body(json_encode(array(
      'targets' => $data['targets'],
      'response' => $data['response']
    )));
  }
});

$app->post('/micropub/post', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    // Remove any blank params
    $params = array_filter($params, function($v){
      return $v !== '';
    });

    // Now send to the micropub endpoint
    $r = micropub_post($user->micropub_endpoint, $params, $user->micropub_access_token);
    $request = $r['request'];
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
      'request' => htmlspecialchars($request),
      'response' => htmlspecialchars($response),
      'location' => $location,
      'error' => $r['error'],
      'curlinfo' => $r['curlinfo']
    )));
  }
});

