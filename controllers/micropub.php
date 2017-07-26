<?php

$app->get('/micropub/syndications', function() use($app) {
  if($user=require_login($app)) {
    $data = get_micropub_config($user, ['q'=>'syndicate-to']);
    $app->response()['Content-type'] = 'application/json';
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

    $r = micropub_post_for_user($user, $params);

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'request' => htmlspecialchars($r['request']),
      'response' => htmlspecialchars($r['response']),
      'location' => (isset($r['location']) && $r['location'] ? Mf2\resolveUrl($user->micropub_endpoint, $r['location']) : null),
      'error' => $r['error'],
      'curlinfo' => $r['curlinfo']
    )));
  }
});

$app->post('/micropub/multipart', function() use($app) {
  if($user=require_login($app)) {
    // var_dump($app->request()->post());
    //
    // Since $app->request()->post() with multipart is always
    // empty (bug in Slim?) We're using the raw $_POST here.
    // PHP empties everything in $_POST if the file upload size exceeds
    // that is why we have to test if the variables exist first.

    $file = isset($_FILES['photo']) ? $_FILES['photo'] : null;

    if($file) {
      $error = validate_photo($file);

      unset($_POST['null']);

      if(!$error) {
        $file_path = $file['tmp_name'];
        correct_photo_rotation($file_path);
        $r = micropub_post_for_user($user, $_POST, $file);
      } else {
        $r = array('error' => $error);
      }
    } else {
      unset($_POST['null']);
      $r = micropub_post_for_user($user, $_POST);
    }

    // Populate the error if there was no location header.
    if(empty($r['location']) && empty($r['error'])) {
      $r['error'] = "No 'Location' header in response.";
    }

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'response' => (isset($r['response']) ? htmlspecialchars($r['response']) : null),
      'location' => (isset($r['location']) && $r['location'] ? Mf2\resolveUrl($user->micropub_endpoint, $r['location']) : null),
      'error' => (isset($r['error']) ? $r['error'] : null),
    )));
  }
});

$app->post('/micropub/media', function() use($app) {
  if($user=require_login($app)) {
    $file = isset($_FILES['photo']) ? $_FILES['photo'] : null;
    $error = validate_photo($file);
    unset($_POST['null']);

    if(!$error) {
      $file_path = $file['tmp_name'];
      correct_photo_rotation($file_path);
      $r = micropub_media_post_for_user($user, $file_path);
    } else {
      $r = array('error' => $error);
    }

    if(empty($r['location']) && empty($r['error'])) {
      $r['error'] = "No 'Location' header in response.";
    }

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'location' => (isset($r['location']) ? $r['location'] : null),
      'error' => (isset($r['error']) ? $r['error'] : null),
    )));
  }
});

$app->post('/micropub/postjson', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $r = micropub_post_for_user($user, json_decode($params['data'], true), null, true);

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode([
      'location' => (isset($r['location']) && $r['location'] ? Mf2\resolveUrl($user->micropub_endpoint, $r['location']) : null),
      'error' => $r['error'],
      'response' => $r['response']
    ]));
  }
});
