<?php

$app->post('/mailgun', function() use($app) {
  $params = $app->request()->params();

  // Find the user for this email
  if(!preg_match('/([^ <>]+)@'.Config::$hostname.'/', $params['To'], $match)) {
    $app->response()->body('invalid recipient');
    return;
  }

  $user = ORM::for_table('users')->where('email_username', $match[1])->find_one();
  if(!$user) {
    $app->response()->body('user not found');
    return;
  }  

  if(!$user->micropub_access_token) {
    $app->response()->body('user has no access token');
    return;
  }

  $data = array(
    'published' => (k($params, 'Date') ? date('c', strtotime(k($params, 'Date'))) : date('c'))
  );

  if(k($params, 'Subject'))
    $data['name'] = k($params, 'Subject');

  if(k($params['body-plain'])
    $data['content'] = k($params, 'body-plain');

  // Set tags for any hashtags used in the body
  if(preg_match_all('/#([^ ]+)/', $data['content'], $matches)) {
    $tags = array();
    foreach($matches[1] as $m)
      $tags[] = $m;
    if($tags) {
      if($user->send_category_as_array != 1) {
        $data['category'] = $tags;
      } else {
        $data['category'] = implode(',', $tags);
      }
    }
  }

  // Handle attachments
  $filename = false;

  foreach($_FILES as $file) {
    // If a photo was included, set the filename to the downloaded file
    if(preg_match('/image/', $file['type'])) {
      $filename = $file['tmp_name'];
    }

    // Sometimes MMSs are sent with a txt file attached instead of in the body
    if(preg_match('/text\/plain/', $file['type'])) {
      $content = trim(file_get_contents($file['tmp_name']));
      if($content) {
        $data['content'] = $content;
      }
    }
  }

  $r = micropub_post_for_user($user, $data, $filename);

  if(k($r, 'location'))
    $result = 'created post at ' . $r['location'];
  else
    $result = 'error creating post';

  $app->response()->body($result);
});
