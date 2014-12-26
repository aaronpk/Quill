<div class="narrow">
  <?= partial('partials/header') ?>

  <h2>Signed In As</h2>
  <code><?= session('me') ?></code>

  <!-- 
  <h3>Facebook</h3>
  <input type="button" id="facebook-button" value="Checking" class="btn">
  -->

  <h3>Twitter</h3>
  <input type="button" id="twitter-button" value="Checking" class="btn">

  <h3>Instagram</h3>
  <input type="button" id="instagram-button" value="Checking" class="btn">

</div>
<script>
/*
window.quillFbInit = function() {
  FB.getLoginStatus(function(response) {
    if (response.status === 'connected') {
      // the user is logged in and has authenticated your
      // app, and response.authResponse supplies
      // the user's ID, a valid access token, a signed
      // request, and the time the access token 
      // and signed request each expire
      var uid = response.authResponse.userID;
      var accessToken = response.authResponse.accessToken;

      save_facebook_token(response.authResponse.accessToken);

    } else if (response.status === 'not_authorized') {
      // the user is logged in to Facebook, 
      // but has not authenticated your app
      console.log("Logged in but not authorized");

      $("#facebook-button").val("Sign In").addClass("btn-warning");

    } else {
      // the user isn't logged in to Facebook.
      console.log("User isn't logged in");

      $("#facebook-button").val("Sign In").addClass("btn-warning");
    }
  });
};
window.quillHandleFbLogin = function(response) {
  save_facebook_token(response.authResponse.accessToken);
};

function save_facebook_token(token) {
  console.log("Authed with token: " + token);
  $.post('/auth/facebook', {
    fb_token: token
  }, function(data){
    $("#facebook-button").val("Connected").addClass("btn-success");
  });
}
*/

$(function(){
  /*
  $("#facebook-button").click(function(){
    FB.login(window.quillHandleFbLogin, {scope:'publish_actions,user_likes'});
  });
  */

  $.getJSON("/auth/twitter", function(data){
    // Check if we're already authorized with twitter
    if(data && data.result == 'ok') {
      $("#twitter-button").val("Connected").addClass("btn-success");
    } else if(data && data.url) {
      $("#twitter-button").val("Sign In").data("url", data.url).addClass("btn-warning");
    } else {
      $("#twitter-button").val("Error").addClass("btn-danger");
    }
  });

  $("#twitter-button").click(function(){
    if($(this).data('url')) {
      window.location = $(this).data('url');
    } else {
      $.getJSON("/auth/twitter", {login: 1}, function(data){
        window.location = data.url;
      });
    }
  });

  $.getJSON("/auth/instagram", function(data){
    // Check if we're already authorized with Instagram
    if(data && data.result == 'ok') {
      $("#instagram-button").val("Connected").addClass("btn-success");
    } else if(data && data.url) {
      $("#instagram-button").val("Sign In").data("url", data.url).addClass("btn-warning");
    } else {
      $("#instagram-button").val("Error").addClass("btn-danger");
    }
  });

  $("#instagram-button").click(function(){
    if($(this).data('url')) {
      window.location = $(this).data('url');
    } else {
      $.getJSON("/auth/instagram", {login: 1}, function(data){
        window.location = data.url;
      });
    }
  });

});
</script>
