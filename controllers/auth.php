<?php
use Abraham\TwitterOAuth\TwitterOAuth;

IndieAuth\Client::$clientID = Config::$base_url;
IndieAuth\Client::$redirectURL = Config::$base_url.'auth/callback';

$app->get('/auth/start', function() use($app) {
  $req = $app->request();

  $params = $req->params();

  $defaultScope = 'create update media profile';

  list($authorizationURL, $error) = IndieAuth\Client::begin($params['me'], $defaultScope);

  $me = IndieAuth\Client::normalizeMeURL($params['me']);

  // Double check for a micropub endpoint here for debugging purposes
  if(!$error) {
    $micropubEndpoint = $_SESSION['indieauth']['micropub_endpoint'] = IndieAuth\Client::discoverMicropubEndpoint($me);
    if(!$micropubEndpoint) {
      $error['error'] = 'missing_micropub_endpoint';
    }
  }

  if($error && in_array($error['error'], ['missing_authorization_endpoint','missing_token_endpoint','missing_micropub_endpoint'])) {
    // Display debug info for these particular errors

    $micropubEndpoint = $_SESSION['indieauth']['micropub_endpoint'] = IndieAuth\Client::discoverMicropubEndpoint($me);
    $tokenEndpoint = $_SESSION['indieauth']['token_endpoint'] = IndieAuth\Client::discoverTokenEndpoint($me);
    $authorizationEndpoint = $_SESSION['indieauth']['authorization_endpoint'] = IndieAuth\Client::discoverAuthorizationEndpoint($me);

    $html = render('auth_start', array(
      'title' => 'Sign In',
      'me' => $me,
      'authorizing' => $me,
      'meParts' => parse_url($me),
      'tokenEndpoint' => $tokenEndpoint,
      'micropubEndpoint' => $micropubEndpoint,
      'authorizationEndpoint' => $authorizationEndpoint,
      'authorizationURL' => false
    ));
    $app->response()->body($html);
    return;
  }

  // Handle other errors like connection errors by showing a generic error page
  if($error) {
    $html = render('auth_error', array(
      'title' => 'Sign In',
      'error' => $error['error'],
      'errorDescription' => $error['error_description'],
    ));
    $app->response()->body($html);
    return;
  }

  $micropubEndpoint = $_SESSION['indieauth']['micropub_endpoint'] = IndieAuth\Client::discoverMicropubEndpoint($me);
  $tokenEndpoint = $_SESSION['indieauth']['token_endpoint'] = IndieAuth\Client::discoverTokenEndpoint($me);
  $authorizationEndpoint = $_SESSION['indieauth']['authorization_endpoint'] = IndieAuth\Client::discoverAuthorizationEndpoint($me);

  if(k($params, 'redirect')) {
    $_SESSION['redirect_after_login'] = $params['redirect'];
  }
  if(k($params, 'reply')) {
    $_SESSION['reply'] = $params['reply'];
  }

  // If the user has already signed in before and has a micropub access token,
  // and the endpoints are all the same, skip the debugging screens and redirect
  // immediately to the auth endpoint.
  // This will still get a new access token when they finish logging in.
  $user = ORM::for_table('users')->where('url', $me)->find_one();
  if($user && $user->micropub_access_token
    && $user->micropub_endpoint == $micropubEndpoint
    && $user->token_endpoint == $tokenEndpoint
    && $user->authorization_endpoint == $authorizationEndpoint
    && !array_key_exists('restart', $params)) {

    // Request whatever scope was previously granted
    $authorizationURL = parse_url($authorizationURL);
    $authorizationURL['scope'] = $user->micropub_scope;
    $authorizationURL = http_build_url($authorizationURL);

    $app->redirect($authorizationURL, 302);

  } else {

    if(k($params, 'dontask') && $params['dontask']) {
      // Request whatever scope was previously granted
      $authorizationURL = parse_url($authorizationURL);
      $authorizationURL['scope'] = $user->micropub_scope ?: $defaultScope;
      $authorizationURL = http_build_url($authorizationURL);

      $_SESSION['dontask'] = 1;
      $app->redirect($authorizationURL, 302);
    }

    $html = render('auth_start', array(
      'title' => 'Sign In',
      'me' => $me,
      'authorizing' => $me,
      'meParts' => parse_url($me),
      'tokenEndpoint' => $tokenEndpoint,
      'micropubEndpoint' => $micropubEndpoint,
      'authorizationEndpoint' => $authorizationEndpoint,
      'authorizationURL' => $authorizationURL
    ));
    $app->response()->body($html);
  }
});

$app->get('/auth/redirect', function() use($app) {
  $req = $app->request();
  $params = $req->params();

  // Override scope from the form the user selects

  if(!isset($params['scope']))
    $params['scope'] = '';

  $authorizationURL = parse_url($params['authorization_url']);
  parse_str($authorizationURL['query'], $query);
  $query['scope'] = $params['scope'];
  $authorizationURL['query'] = http_build_query($query);
  $authorizationURL = http_build_url($authorizationURL);

  $app->redirect($authorizationURL);
  return;
});

$app->get('/auth/callback', function() use($app) {
  $req = $app->request();
  $params = $req->params();

  list($token, $error) = IndieAuth\Client::complete($params, true);

  if($error) {
    $html = render('auth_error', [
      'title' => 'Auth Callback',
      'error' => $error['error'],
      'errorDescription' => $error['error_description'],
    ]);
    $app->response()->body($html);
    return;
  }

  $me = $token['me'];

  // Use the discovered endpoints saved in the session
  $micropubEndpoint = $_SESSION['indieauth']['micropub_endpoint'];
  $tokenEndpoint = $_SESSION['indieauth']['token_endpoint'];

  $redirectToDashboardImmediately = false;

  // If a valid access token was returned, store the token info in the session and they are signed in
  if(k($token['response'], array('me','access_token','scope'))) {

    $_SESSION['auth'] = $token['response'];
    $_SESSION['me'] = $me = $token['me'];

    $user = ORM::for_table('users')->where('url', $me)->find_one();
    if($user) {
      // Already logged in, update the last login date
      $user->last_login = date('Y-m-d H:i:s');
      // If they have logged in before and we already have an access token, then redirect to the dashboard now
      if($user->micropub_access_token)
        $redirectToDashboardImmediately = true;
    } else {
      // New user! Store the user in the database
      $user = ORM::for_table('users')->create();
      $user->url = $me;
      $user->date_created = date('Y-m-d H:i:s');
    }
    $user->authorization_endpoint = $_SESSION['indieauth']['authorization_endpoint'];
    $user->token_endpoint = $tokenEndpoint;
    $user->micropub_endpoint = $micropubEndpoint;
    $user->micropub_access_token = $token['response']['access_token'];
    $user->micropub_scope = $token['response']['scope'];
    $user->micropub_response = $token['raw_response'];
    $user->save();
    $_SESSION['user_id'] = $user->id();

    // Make a request to the micropub endpoint to discover the syndication targets and media endpoint if any.
    // Errors are silently ignored here. The user will be able to retry from the new post interface and get feedback.
    get_micropub_config($user, ['q'=>'config']);
  }

  unset($_SESSION['indieauth']);

  if($redirectToDashboardImmediately || k($_SESSION, 'dontask')) {
    unset($_SESSION['dontask']);
    if(k($_SESSION, 'redirect_after_login')) {
      $dest = $_SESSION['redirect_after_login'];
      unset($_SESSION['redirect_after_login']);
      $app->redirect($dest, 302);
    } else {
      $query = [];
      if(k($_SESSION, 'reply')) {
        $query['reply'] = $_SESSION['reply'];
        unset($_SESSION['reply']);
      }
      $app->redirect('/new?' . http_build_query($query), 302);
    }
  } else {
    $html = render('auth_callback', array(
      'title' => 'Sign In',
      'me' => $me,
      'authorizing' => $me,
      'meParts' => parse_url($me),
      'tokenEndpoint' => $tokenEndpoint,
      'auth' => $token['response'],
      'response' => $token['raw_response'],
      'curl_error' => (array_key_exists('error', $token) ? $token['error'] : false),
      'destination' => (k($_SESSION, 'redirect_after_login') ?: '/new')
    ));
    $app->response()->body($html);
  }
});

$app->get('/signout', function() use($app) {
  unset($_SESSION['auth']);
  unset($_SESSION['me']);
  unset($_SESSION['auth_state']);
  unset($_SESSION['user_id']);
  $app->redirect('/', 302);
});

$app->post('/auth/reset', function() use($app) {
  if($user=require_login($app, false)) {
    revoke_micropub_token($user->micropub_access_token, $user->token_endpoint);

    $user->authorization_endpoint = '';
    $user->token_endpoint = '';
    $user->micropub_endpoint = '';
    $user->authorization_endpoint = '';
    $user->micropub_media_endpoint = '';
    $user->micropub_scope = '';
    $user->micropub_access_token = '';
    $user->syndication_targets = '';
    $user->supported_post_types = '';
    $user->save();

    unset($_SESSION['auth']);
    unset($_SESSION['me']);
    unset($_SESSION['auth_state']);
    unset($_SESSION['user_id']);
  }
  $app->redirect('/', 302);
});

$app->post('/auth/twitter', function() use($app) {
  if(!Config::$twitterClientID) {
    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'result' => 'error'
    )));
    return;
  }

  if($user=require_login($app, false)) {
    $params = $app->request()->params();
    // User just auth'd with twitter, store the access token
    $user->twitter_access_token = $params['twitter_token'];
    $user->twitter_token_secret = $params['twitter_secret'];
    $user->save();

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'result' => 'ok'
    )));
  } else {
    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'result' => 'error'
    )));
  }
});

function getTwitterLoginURL(&$twitter) {
  $request_token = $twitter->oauth('oauth/request_token', [
    'oauth_callback' => Config::$base_url . 'auth/twitter/callback'
  ]);
  $_SESSION['twitter_auth'] = $request_token;
  return $twitter->url('oauth/authorize', ['oauth_token' => $request_token['oauth_token']]);
}

$app->get('/auth/twitter', function() use($app) {
  if(!Config::$twitterClientID) {
    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'result' => 'error'
    )));
    return;
  }

  $params = $app->request()->params();
  if($user=require_login($app, false)) {

    // If there is an existing Twitter token, check if it is valid
    // Otherwise, generate a Twitter login link
    $twitter_login_url = false;

    if(array_key_exists('login', $params)) {
      $twitter = new TwitterOAuth(Config::$twitterClientID, Config::$twitterClientSecret);
      $twitter_login_url = getTwitterLoginURL($twitter);
    } else {
      $twitter = new TwitterOAuth(Config::$twitterClientID, Config::$twitterClientSecret,
        $user->twitter_access_token, $user->twitter_token_secret);

      if($user->twitter_access_token) {
        if($twitter->get('account/verify_credentials')) {
          $app->response()['Content-type'] = 'application/json';
          $app->response()->body(json_encode(array(
            'result' => 'ok'
          )));
          return;
        } else {
          // If the existing twitter token is not valid, generate a login link
          $twitter_login_url = getTwitterLoginURL($twitter);
        }
      } else {
        $twitter_login_url = getTwitterLoginURL($twitter);
      }
    }

    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'url' => $twitter_login_url
    )));

  } else {
    $app->response()['Content-type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'result' => 'error'
    )));
  }
});

$app->get('/auth/twitter/callback', function() use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();

    $twitter = new TwitterOAuth(Config::$twitterClientID, Config::$twitterClientSecret,
      $_SESSION['twitter_auth']['oauth_token'], $_SESSION['twitter_auth']['oauth_token_secret']);
    $credentials = $twitter->oauth('oauth/access_token', ['oauth_verifier' => $params['oauth_verifier']]);

    $user->twitter_access_token = $credentials['oauth_token'];
    $user->twitter_token_secret = $credentials['oauth_token_secret'];
    $user->twitter_username = $credentials['screen_name'];
    $user->save();

    $app->redirect('/settings');
  }
});
