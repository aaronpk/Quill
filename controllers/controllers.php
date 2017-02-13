<?php
use Abraham\TwitterOAuth\TwitterOAuth;

function require_login(&$app, $redirect=true) {
  $params = $app->request()->params();
  if(array_key_exists('token', $params)) {
    try {
      $data = JWT::decode($params['token'], Config::$jwtSecret, array('HS256'));
      $_SESSION['user_id'] = $data->user_id;
      $_SESSION['me'] = $data->me;
    } catch(DomainException $e) {
      if($redirect) {
        header('X-Error: DomainException');
        $app->redirect('/', 302);
      } else {
        return false;
      }
    } catch(UnexpectedValueException $e) {
      if($redirect) {
        header('X-Error: UnexpectedValueException');
        $app->redirect('/', 302);
      } else {
        return false;
      }
    }
  }

  if(!array_key_exists('user_id', $_SESSION)) {
    if($redirect)
      $app->redirect('/', 302);
    return false;
  } else {
    return ORM::for_table('users')->find_one($_SESSION['user_id']);
  }
}

function generate_login_token($opts=[]) {
  return JWT::encode(array_merge([
    'user_id' => $_SESSION['user_id'],
    'me' => $_SESSION['me'],
    'created_at' => time()
  ], $opts), Config::$jwtSecret);
}

$app->get('/dashboard', function() use($app) {
  if($user=require_login($app)) {
    render('dashboard', array(
      'title' => 'Dashboard',
      'authorizing' => false
    ));
  }
});

$app->get('/new', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $entry = false;
    $in_reply_to = '';

    if(array_key_exists('reply', $params))
       $in_reply_to = $params['reply'];

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

    render('new-post', array(
      'title' => 'New Post',
      'in_reply_to' => $in_reply_to,
      'micropub_endpoint' => $user->micropub_endpoint,
      'media_endpoint' => $user->micropub_media_endpoint,
      'micropub_scope' => $user->micropub_scope,
      'micropub_access_token' => $user->micropub_access_token,
      'response_date' => $user->last_micropub_response_date,
      'syndication_targets' => json_decode($user->syndication_targets, true),
      'test_response' => $test_response,
      'location_enabled' => $user->location_enabled,
      'user' => $user,
      'authorizing' => false
    ));
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

    render('new-bookmark', array(
      'title' => 'New Bookmark',
      'bookmark_url' => $url,
      'bookmark_name' => $name,
      'bookmark_content' => $content,
      'bookmark_tags' => $tags,
      'token' => generate_login_token(),
      'syndication_targets' => json_decode($user->syndication_targets, true),
      'user' => $user,
      'authorizing' => false
    ));
  }
});

$app->get('/favorite', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $url = '';

    if(array_key_exists('url', $params))
      $url = $params['url'];

    // Check if there was a login token in the query string and whether it has autosubmit=true
    $autosubmit = false;

    if(array_key_exists('token', $params)) {
      try {
        $data = JWT::decode($params['token'], Config::$jwtSecret, ['HS256']);
        $autosubmit = isset($data->autosubmit) && $data->autosubmit;
      } catch(Exception $e) {
      }
    }

    render('new-favorite', array(
      'title' => 'New Favorite',
      'url' => $url,
      'token' => generate_login_token(['autosubmit'=>true]),
      'authorizing' => false,
      'autosubmit' => $autosubmit
    ));
  }
});

$app->get('/event', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    render('event', array(
      'title' => 'Event',
      'authorizing' => false
    ));
  }
});

$app->get('/itinerary', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    render('new-itinerary', array(
      'title' => 'Itinerary',
      'authorizing' => false
    ));
  }
});

$app->get('/photo', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    render('photo', array(
      'title' => 'New Photo',
      'note_content' => '',
      'authorizing' => false
    ));
  }
});

$app->get('/review', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    render('review', array(
      'title' => 'Review',
      'authorizing' => false
    ));
  }
});

$app->get('/repost', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $url = '';

    if(array_key_exists('url', $params))
      $url = $params['url'];

    render('new-repost', array(
      'title' => 'New Repost',
      'url' => $url,
      'token' => generate_login_token(),
      'authorizing' => false
    ));
  }
});

$app->post('/prefs', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();
    $user->location_enabled = $params['enabled'];
    $user->save();
  }
  $app->response()['Content-type'] = 'application/json';
  $app->response()->body(json_encode(array(
    'result' => 'ok'
  )));
});

$app->post('/prefs/timezone', function() use($app) {
  // Called when the interface finds the user's location.
  // Look up the timezone for this location and store it as their default.
  $timezone = false;
  if($user=require_login($app)) {
    $params = $app->request()->params();
    $timezone = p3k\Timezone::timezone_for_location($params['latitude'], $params['longitude']);
    if($timezone) {
      $user->default_timezone = $timezone;
      $user->save();
    }
  }
  $app->response()['Content-type'] = 'application/json';
  $app->response()->body(json_encode(array(
    'result' => 'ok',
    'timezone' => $timezone,
  )));
});

$app->get('/add-to-home', function() use($app) {
  $params = $app->request()->params();
  header("Cache-Control: no-cache, must-revalidate");

  if(array_key_exists('token', $params) && !session('add-to-home-started')) {
    unset($_SESSION['add-to-home-started']);

    // Verify the token and sign the user in
    try {
      $data = JWT::decode($params['token'], Config::$jwtSecret, array('HS256'));
      $_SESSION['user_id'] = $data->user_id;
      $_SESSION['me'] = $data->me;
      $app->redirect('/new', 302);
    } catch(DomainException $e) {
      header('X-Error: DomainException');
      $app->redirect('/', 302);
    } catch(UnexpectedValueException $e) {
      header('X-Error: UnexpectedValueException');
      $app->redirect('/', 302);
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

        $app->redirect('/add-to-home?token='.$token, 302);
      } else {
        unset($_SESSION['add-to-home-started']);
        render('add-to-home', array('title' => 'Quill'));
      }
    }
  }
});

$app->get('/email', function() use($app) {
  if($user=require_login($app)) {

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

    if(!$user->email_username) {
      $host = parse_url($user->url, PHP_URL_HOST);
      $user->email_username = $host . '.' . rand(100000,999999);
      $user->save();
    }

    render('email', array(
      'title' => 'Post-by-Email',
      'micropub_endpoint' => $user->micropub_endpoint,
      'test_response' => $test_response,
      'user' => $user
    ));
  }
});

$app->get('/settings', function() use($app) {
  if($user=require_login($app)) {
    render('settings', [
      'title' => 'Settings',
      'user' => $user,
      'authorizing' => false
    ]);
  }
});

$app->post('/settings/save', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    if(array_key_exists('html_content', $params))
      $user->micropub_optin_html_content = $params['html_content'] ? 1 : 0;

    if(array_key_exists('slug_field', $params) && $params['slug_field'])
      $user->micropub_slug_field = $params['slug_field'];

    if(array_key_exists('syndicate_field', $params) && $params['syndicate_field']) {
      if(in_array($params['syndicate_field'], ['syndicate-to','mp-syndicate-to']))
        $user->micropub_syndicate_field = $params['syndicate_field'];
    }

    $user->save();
    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'result' => 'ok'
    )));
  }
});

$app->post('/settings/html-content', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();
    $user->micropub_optin_html_content = $params['html'] ? 1 : 0;
    $user->save();
    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'html' => $user->micropub_optin_html_content
    )));
  }
});
$app->get('/settings/html-content', function() use($app) {
  if($user=require_login($app)) {
    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'html' => $user->micropub_optin_html_content
    )));
  }
});

function create_favorite(&$user, $url) {
  $micropub_request = array(
    'like-of' => $url
  );
  $r = micropub_post_for_user($user, $micropub_request);

  $tweet_id = false;

  // POSSE favorites to Twitter
  if($user->twitter_access_token && preg_match('/https?:\/\/(?:www\.)?twitter\.com\/[^\/]+\/status(?:es)?\/(\d+)/', $url, $match)) {
    $tweet_id = $match[1];
    $twitter = new TwitterOAuth(Config::$twitterClientID, Config::$twitterClientSecret,
      $user->twitter_access_token, $user->twitter_token_secret);
    $result = $twitter->post('favorites/create', array(
      'id' => $tweet_id
    ));
  }

  return $r;
}

function create_repost(&$user, $url) {
  $micropub_request = array(
    'repost-of' => $url
  );
  $r = micropub_post_for_user($user, $micropub_request);

  $tweet_id = false;

  if($user->twitter_access_token && preg_match('/https?:\/\/(?:www\.)?twitter\.com\/[^\/]+\/status(?:es)?\/(\d+)/', $url, $match)) {
    $tweet_id = $match[1];
    $twitter = new TwitterOAuth(Config::$twitterClientID, Config::$twitterClientSecret,
      $user->twitter_access_token, $user->twitter_token_secret);
    $result = $twitter->post('statuses/retweet/'.$tweet_id);
  }

  return $r;
}

$app->post('/favorite', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $r = create_favorite($user, $params['url']);

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'location' => $r['location'],
      'error' => $r['error']
    )));
  }
});

$app->post('/repost', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $r = create_repost($user, $params['url']);

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'location' => $r['location'],
      'error' => $r['error']
    )));
  }
});

$app->get('/reply/preview', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    if(!isset($params['url']) || !$params['url']) {
      return '';
    }

    $reply_url = trim($params['url']);

    if(preg_match('/twtr\.io\/([0-9a-z]+)/i', $reply_url, $match)) {
      $twtr = 'https://twitter.com/_/status/' . sxg_to_num($match[1]);
      $ch = curl_init($twtr);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_exec($ch);
      $expanded_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
      if($expanded_url) $reply_url = $expanded_url;
    }

    $entry = false;

    $xray = [
      'url' => $reply_url
    ];

    if(preg_match('/twitter\.com\/(?:[^\/]+)\/statuse?s?\/(.+)/', $reply_url, $match)) {
      if($user->twitter_access_token) {
        $xray['twitter_api_key'] = Config::$twitterClientID;
        $xray['twitter_api_secret'] = Config::$twitterClientSecret;
        $xray['twitter_access_token'] = $user->twitter_access_token;
        $xray['twitter_access_token_secret'] = $user->twitter_token_secret;
      }
    }

    // Pass to X-Ray to see if it can expand the entry
    $ch = curl_init('https://xray.p3k.io/parse');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($xray));
    $response = curl_exec($ch);
    $data = @json_decode($response, true);
    if($data && isset($data['data']) && $data['data']['type'] == 'entry') {
      $entry = $data['data'];
      // Create a nickname based on the author URL
      if(array_key_exists('author', $entry)) {
        if($entry['author']['url']) {
          if(!isset($entry['author']['nickname']) || !$entry['author']['nickname'])
            $entry['author']['nickname'] = display_url($entry['author']['url']);
        }
      }
    }

    $mentions = [];
    if($entry) {
      if(array_key_exists('author', $entry)) {
        // Find all @-names in the post, as well as the author name
        $mentions[] = strtolower($entry['author']['nickname']);
      }

      if(preg_match_all('/(^|(?<=[\s\/]))@([a-z0-9_]+([a-z0-9_\.]*)?)/i', $entry['content']['text'], $matches)) {
        foreach($matches[0] as $nick) {
          if(trim($nick,'@') != $user->twitter_username && trim($nick,'@') != display_url($user->url))
            $mentions[] = strtolower(trim($nick,'@'));
        }
      }

      $mentions = array_values(array_unique($mentions));

    }    

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode([
      'canonical_reply_url' => $reply_url,
      'entry' => $entry,
      'mentions' => $mentions
    ]));
  }
});
