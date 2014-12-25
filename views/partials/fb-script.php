<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '<?= Config::$fbClientID ?>',
      xfbml      : true,
      version    : 'v2.2'
    });
    if(window.quillFbInit) {
      window.quillFbInit();
    }
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>