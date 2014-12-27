<!doctype html>
<html lang="en">
  <head>
    <title>Favoriting</title>
  </head>
  <body>
    <script>
    function favorite_finished() {
      self.close();
    }
    </script>
    <script src="/favorite.js?url=<?= urlencode($this->url) ?>&amp;token=<?= $this->token ?>"></script>

    <?php /*
    <script>
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
          console.log(accessToken);

          FB.api("/<?= $this->facebook_id ?>/likes", "post", function(response){
            console.log(response);
            show_star();
          });

        } else if (response.status === 'not_authorized') {
          // the user is logged in to Facebook, 
          // but has not authenticated your app
          console.log("Logged in but not authorized");
        } else {
          // the user isn't logged in to Facebook.
          console.log("User isn't logged in");
        }
      });
    };
    </script>
    <?= partial('partials/fb-script') ?>
    */ ?>


  </body>
</html>