<?php
use Abraham\TwitterOAuth\TwitterOAuth;
use IndieWeb\DateFormatter;

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

$app->get('/new/last-photo.json', function() use($app) {
  if($user=require_login($app)) {
    $url = null;

    if($user->micropub_media_endpoint) {
      // Request the last file uploaded from the media endpoint
      $response = micropub_get($user->micropub_media_endpoint, ['q'=>'last'], $user->micropub_access_token);
      if(isset($response['data']['url'])) {
        $url = $response['data']['url'];
      }
    }

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'url' => $url
    )));
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

    $like_of = '';

    if(array_key_exists('url', $params))
      $like_of = $params['url'];

    // Check if there was a login token in the query string and whether it has autosubmit=true
    $autosubmit = false;

    if(array_key_exists('token', $params)) {
      try {
        $data = JWT::decode($params['token'], Config::$jwtSecret, ['HS256']);
        if(isset($data->autosubmit) && $data->autosubmit) {
          // Only allow this token to be used for the user who created it
          if($data->user_id == $_SESSION['user_id']) {
            $autosubmit = true;
          }
        }
      } catch(Exception $e) {
      }
    }

    if(array_key_exists('edit', $params)) {
      $edit_data = get_micropub_source($user, $params['edit'], 'like-of');
      $url = $params['edit'];
      if(isset($edit_data['like-of'])) {
        $like_of = $edit_data['like-of'][0];
      }
    } else {
      $edit_data = false;
      $url = false;
    }

    render('new-favorite', array(
      'title' => 'New Favorite',
      'like_of' => $like_of,
      'token' => generate_login_token(['autosubmit'=>true]),
      'authorizing' => false,
      'autosubmit' => $autosubmit,
      'url' => $url
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

$app->get('/flight', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    render('new-flight', array(
      'title' => 'Flight',
      'authorizing' => false
    ));
  }
});

$app->post('/flight', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $location = false;

    if($params['action'] == 'find') {

    } elseif($params['action'] == 'checkin') {

      $payload = [
        'type' => ['h-entry'],
        'properties' => [
          'checkin' => [
            [
              'type' => ['h-card'],
              'properties' => [
                'name' => [$params['flight']],
                'url' => ['http://flightaware.com/live/flight/'.$params['flight']],
              ]
            ]
          ]
        ]
      ];

      $r = micropub_post_for_user($user, $payload, null, true);

      $location = $r['location'];

      if($location) {
        // Store the checkin in the database to enable the cron job tracking the flight
        $flight = ORM::for_table('flights')->create();
        $flight->user_id = $_SESSION['user_id'];
        $flight->date_created = date('Y-m-d H:i:s');
        $flight->active = 1;
        $flight->url = $location;
        $flight->flight = $params['flight'];
        $flight->save();
      }
    }

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'result' => 'ok',
      'location' => $location
    )));
  }
});

$app->get('/flight/:id/:flightID/route.json', function($id, $flightID) use($app) {
  $route = false;

  $flight = ORM::for_table('flights')->where('id', $id)->find_one();
  if($flight) {
    $lastPosition = json_decode($flight->lastposition, true);
    if($lastPosition['InFlightInfoResult']['faFlightID'] == $flightID) {

      // {"type":"FeatureCollection","features":[{"type":"Feature","geometry":{"type":"Point","coordinates":[-122.638351,45.52217]},"properties":{"date":"2016-01-02T23:13:49Z","altitude":42.666666666667}}

      $route = [
        'type' => 'FeatureCollection',
        'features' => []
      ];

      $positions = json_decode($flight->positions, true);
      foreach($positions as $p) {
        $route['features'][] = [
          'type' => 'Feature',
          'geometry' => [
            'type' => 'Point',
            'coordinates' => [$p['lng'], $p['lat']]
          ],
          'properties' => [
            'date' => $p['date'],
            'altitude' => $p['altitude'],
            'heading' => $p['heading'],
            'speed' => $p['speed']
          ]
        ];
      }

    }
  }

  $app->response()['Content-type'] = 'application/json';
  $app->response()->body(json_encode($route));
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

    $repost_of = '';

    if(array_key_exists('url', $params))
      $repost_of = $params['url'];

    if(array_key_exists('edit', $params)) {
      $edit_data = get_micropub_source($user, $params['edit'], 'repost-of');
      $url = $params['edit'];
      if(isset($edit_data['repost-of'])) {
        $repost = $edit_data['repost-of'][0];
        if(is_string($edit_data['repost-of'][0])) {
          $repost_of = $repost;
        } elseif(is_array($repost)) {
          if(array_key_exists('type', $repost) && in_array('h-cite', $repost)) {
            if(array_key_exists('url', $repost['properties'])) {
              $repost_of = $repost['properties']['url'][0];
            }
          } else {
            // Error
          }
        } else {
          // Error: don't know what type of post this is
        }
      }
    } else {
      $edit_data = false;
      $url = false;
    }

    render('new-repost', array(
      'title' => 'New Repost',
      'repost_of' => $repost_of,
      'token' => generate_login_token(),
      'authorizing' => false,
      'url' => $url,
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

$app->get('/view', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $xray = new p3k\XRay();
    $result = $xray->parse($params['url']);
    if(isset($result['data']))
      $entry = $result['data'];
    else
      $entry = [];

    render('view-post', array(
      'title' => 'View',
      'entry' => $entry,
      'authorizing' => false
    ));
  }
});

function create_favorite(&$user, $url) {

  $tweet_id = false;
  $twitter_syndication = false;

  // POSSE favorites to Twitter
  if($user->twitter_access_token && preg_match('/https?:\/\/(?:www\.)?twitter\.com\/[^\/]+\/status(?:es)?\/(\d+)/', $url, $match)) {
    $tweet_id = $match[1];
    $twitter = new TwitterOAuth(Config::$twitterClientID, Config::$twitterClientSecret,
      $user->twitter_access_token, $user->twitter_token_secret);
    $result = $twitter->post('favorites/create', array(
      'id' => $tweet_id
    ));
    if(property_exists($result, 'id_str')) {
      $twitter_syndication = 'https://twitter.com/'.$user->twitter_username.'/status/'.$result->id_str;
    }
  }

  $micropub_request = array(
    'like-of' => $url
  );
  if($twitter_syndication) {
    $micropub_request['syndication'] = $twitter_syndication;
  }
  $r = micropub_post_for_user($user, $micropub_request);

  return $r;
}

function edit_favorite(&$user, $post_url, $like_of) {
  $micropub_request = [
    'action' => 'update',
    'url' => $post_url,
    'replace' => [
      'like-of' => [$like_of]
    ]
  ];
  $r = micropub_post_for_user($user, $micropub_request, null, true);
  return $r;
}

function create_repost(&$user, $url) {

  $tweet_id = false;
  $twitter_syndication = false;

  if($user->twitter_access_token && preg_match('/https?:\/\/(?:www\.)?twitter\.com\/[^\/]+\/status(?:es)?\/(\d+)/', $url, $match)) {
    $tweet_id = $match[1];
    $twitter = new TwitterOAuth(Config::$twitterClientID, Config::$twitterClientSecret,
      $user->twitter_access_token, $user->twitter_token_secret);
    $result = $twitter->post('statuses/retweet/'.$tweet_id);
    if(property_exists($result, 'id_str')) {
      $twitter_syndication = 'https://twitter.com/'.$user->twitter_username.'/status/'.$result->id_str;
    }
  }

  $micropub_request = array(
    'repost-of' => $url
  );
  if($twitter_syndication) {
    $micropub_request['syndication'] = $twitter_syndication;
  }
  $r = micropub_post_for_user($user, $micropub_request);

  return $r;
}

function edit_repost(&$user, $post_url, $repost_of) {
  $micropub_request = [
    'action' => 'update',
    'url' => $post_url,
    'replace' => [
      'repost-of' => [$repost_of]
    ]
  ];
  $r = micropub_post_for_user($user, $micropub_request, null, true);
  return $r;
}

$app->post('/favorite', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $error = false;

    if(isset($params['edit']) && $params['edit']) {
      $r = edit_favorite($user, $params['edit'], $params['like_of']);
      if(isset($r['location']) && $r['location'])
        $location = $r['location'];
      elseif(in_array($r['code'], [200,201,204]))
        $location = $params['edit'];
      elseif(in_array($r['code'], [401,403])) {
        $location = false;
        $error = 'Your Micropub endpoint denied the request. Check that Quill is authorized to update posts.';
      } else {
        $location = false;
        $error = 'Your Micropub endpoint did not return a location header or a recognized response code';
      }
    } else {
      $r = create_favorite($user, $params['like_of']);
      $location = $r['location'];
    }

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'location' => $location,
      'error' => $r['error'],
      'error_details' => $error,
    )));
  }
});

$app->post('/repost', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $error = false;

    if(isset($params['edit']) && $params['edit']) {
      $r = edit_repost($user, $params['edit'], $params['repost_of']);
      if(isset($r['location']) && $r['location'])
        $location = $r['location'];
      elseif(in_array($r['code'], [200,201,204]))
        $location = $params['edit'];
      elseif(in_array($r['code'], [401,403])) {
        $location = false;
        $error = 'Your Micropub endpoint denied the request. Check that Quill is authorized to update posts.';
      } else {
        $location = false;
        $error = 'Your Micropub endpoint did not return a location header or a recognized response code';
      }
    } else {
      $r = create_repost($user, $params['repost_of']);
      $location = $r['location'];
    }

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'location' => $location,
      'error' => $r['error'],
      'error_details' => $error,
    )));
  }
});

$app->get('/code', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $edit_data = ['content'=>'','name'=>''];

    if(array_key_exists('edit', $params)) {
      $source = get_micropub_source($user, $params['edit'], ['content','name']);
      if(isset($source['content']) && is_array($source['content']) && isset($source['content'][0]))
        $edit_data['content'] = $source['content'][0];
      if(isset($source['name']) && is_array($source['name']) && isset($source['name'][0]))
        $edit_data['name'] = $source['name'][0];
      $url = $params['edit'];
    } else {
      $url = false;
    }

    $languages = [
      'php' => ['php'],
      'ruby' => ['rb'],
      'python' => ['py'],
      'perl' => ['pl'],
      'javascript' => ['js'],
      'html' => ['html','htm'],
      'css' => ['css'],
      'bash' => ['sh'],
      'nginx' => ['conf'],
      'apache' => [],
      'text' => ['txt'],
    ]; 
    ksort($languages);
    $language_map = [];
    foreach($languages as $lang=>$exts) {
      foreach($exts as $ext)
        $language_map[$ext] = $lang;
    }

    render('new-code', array(
      'title' => 'New Code Snippet',
      'url' => $url,
      'edit_data' => $edit_data,
      'token' => generate_login_token(),
      'languages' => $languages,
      'language_map' => $language_map,
      'my_hostname' => parse_url($user->url, PHP_URL_HOST),
      'authorizing' => false,
    ));
  }
});

$app->post('/code', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $error = false;

    if(isset($params['edit']) && $params['edit']) {
      $micropub_request = [
        'action' => 'update',
        'url' => $params['edit'],
        'replace' => [
          'content' => [$params['content']]
        ]
      ];
      if(isset($params['name']) && $params['name']) {
        $micropub_request['replace']['name'] = [$params['name']];
      }
      $r = micropub_post_for_user($user, $micropub_request, null, true);

      if(isset($r['location']) && $r['location'])
        $location = $r['location'];
      elseif(in_array($r['code'], [200,201,204]))
        $location = $params['edit'];
      elseif(in_array($r['code'], [401,403])) {
        $location = false;
        $error = 'Your Micropub endpoint denied the request. Check that Quill is authorized to update posts.';
      } else {
        $location = false;
        $error = 'Your Micropub endpoint did not return a location header or a recognized response code';
      }
    } else {
      $micropub_request = array(
        'p3k-content-type' => 'code/' . $params['language'],
        'content' => $params['content'],
      );
      if(isset($params['name']) && $params['name'])
        $micropub_request['name'] = $params['name'];

      $r = micropub_post_for_user($user, $micropub_request);

      $location = $r['location'];
    }

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'location' => $location,
      'error' => $r['error'],
      'error_details' => $error,
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

    $xray_opts = [];

    if(preg_match('/twitter\.com\/(?:[^\/]+)\/statuse?s?\/(.+)/', $reply_url, $match)) {
      if($user->twitter_access_token) {
        $xray_opts['twitter_api_key'] = Config::$twitterClientID;
        $xray_opts['twitter_api_secret'] = Config::$twitterClientSecret;
        $xray_opts['twitter_access_token'] = $user->twitter_access_token;
        $xray_opts['twitter_access_token_secret'] = $user->twitter_token_secret;
      }
    }

    // Pass to X-Ray to see if it can expand the entry
    $xray = new p3k\XRay();
    $xray->http = new p3k\HTTP('Quill ('.Config::$base_url.')');
    $data = $xray->parse($reply_url, $xray_opts);
    if($data && isset($data['data'])) {
      if($data['data']['type'] == 'entry') {
        $entry = $data['data'];
      } elseif($data['data']['type'] == 'event') {
        $entry = $data['data'];
        $content = '';
        if(isset($entry['start']) && isset($entry['end'])) {
          $formatted = DateFormatter::format($entry['start'], $entry['end'], false);
          if($formatted)
            $content .= $formatted;
          else {
            $start = new DateTime($entry['start']);
            $end = new DateTime($entry['end']);
            if($start && $end)
              $content .= 'from '.$start->format('Y-m-d g:ia').' to '.$end->format('Y-m-d g:ia');
          }
        } elseif(isset($entry['start'])) {
          $formatted = DateFormatter::format($entry['start'], false, false);
          if($formatted)
            $content .= $formatted;
          else {
            $start = new DateTime($entry['start']);
            if($start)
              $content .= $start->format('Y-m-d g:ia');
          }
        }

        $entry['content']['text'] = $content;
      }
      // Create a nickname based on the author URL
      if($entry && array_key_exists('author', $entry)) {
        if($entry['author']['url']) {
          if(!isset($entry['author']['nickname']) || !$entry['author']['nickname'])
            $entry['author']['nickname'] = display_url($entry['author']['url']);
        }
      }
    }

    $mentions = [];
    if($entry) {
      if(array_key_exists('author', $entry) && isset($entry['author']['nickname'])) {
        // Find all @-names in the post, as well as the author name
        $mentions[] = strtolower($entry['author']['nickname']);
      }

      if(isset($entry['content']) && $entry['content'] && isset($entry['content']['text'])) {
        if(preg_match_all('/(^|(?<=[\s\/]))@([a-z0-9_]+([a-z0-9_\.]*)?)/i', $entry['content']['text'], $matches)) {
          foreach($matches[0] as $nick) {
            if(trim($nick,'@') != $user->twitter_username && trim($nick,'@') != display_url($user->url))
              $mentions[] = strtolower(trim($nick,'@'));
          }
        }
      }

      $mentions = array_values(array_unique($mentions));
    }

    $syndications = [];
    if($entry && isset($entry['syndication'])) {
      foreach($entry['syndication'] as $s) {
        $host = parse_url($s, PHP_URL_HOST);
        switch($host) {
          case 'twitter.com':
          case 'www.twitter.com':
            $icon = 'twitter.ico'; break;
          case 'facebook.com':
          case 'www.facebook.com':
            $icon = 'facebook.ico'; break;
          case 'github.com':
          case 'www.github.com':
            $icon = 'github.ico'; break;
          default:
            $icon = 'default.png'; break;
        }
        $syndications[] = [
          'url' => $s,
          'icon' => $icon
        ];
      }
    }

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode([
      'canonical_reply_url' => $reply_url,
      'entry' => $entry,
      'mentions' => $mentions,
      'syndications' => $syndications,
    ]));
  }
});

$app->get('/edit', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    if(!isset($params['url']) || !$params['url']) {
      $app->response()->body('no URL specified');
    }

    // Query the micropub endpoint for the source properties
    $source = micropub_get($user->micropub_endpoint, [
        'q' => 'source',
        'url' => $params['url']
      ], $user->micropub_access_token);

    $data = $source['data'];

    if(array_key_exists('error', $data)) {
      render('edit/error', [
        'title' => 'Error',
        'summary' => 'Your Micropub endpoint returned an error:',
        'error' => $data['error'],
        'error_description' => $data['error_description']
      ]);
      return;
    }

    if(!array_key_exists('properties', $data) || !array_key_exists('type', $data)) {
      render('edit/error', [
        'title' => 'Error',
        'summary' => '',
        'error' => 'Invalid Response',
        'error_description' => 'Your endpoint did not return "properties" and "type" in the response.'
      ]);
      return;
    }

    // Start checking for content types
    $type = $data['type'][0];
    $error = false;
    $url = false;

    if($type == 'h-review') {
      $url = '/review';
    } elseif($type == 'h-event') {
      $url = '/event';
    } elseif($type != 'h-entry') {
      $error = 'This type of post is not supported by any of Quill\'s editing interfaces. Type: '.$type;
    } else {
      if(array_key_exists('bookmark-of', $data['properties'])) {
        $url = '/bookmark';
      } elseif(array_key_exists('like-of', $data['properties'])) {
        $url = '/favorite';
      } elseif(array_key_exists('repost-of', $data['properties'])) {
        $url = '/repost';
      }
    }

    if($error) {
      render('edit/error', [
        'title' => 'Error',
        'summary' => '',
        'error' => 'There was a problem!',
        'error_description' => $error
      ]);
      return;      
    }

    // Until all interfaces are complete, show an error here for unsupported ones
    if(!in_array($url, ['/favorite','/repost'])) {
      render('edit/error', [
        'title' => 'Not Yet Supported',
        'summary' => '',
        'error' => 'Not Yet Supported',
        'error_description' => 'Editing is not yet supported for this type of post.'
      ]);
      return;      
    }

    $app->redirect($url . '?edit=' . $params['url'], 302);
  }
});
