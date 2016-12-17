<div class="narrow">
  <?= partial('partials/header') ?>

  <h2>Signed In As</h2>
  <code><?= session('me') ?></code>

  <h3>Access Token</h3>
  <input type="text" class="form-control" readonly="readonly" value="<?= $this->user->micropub_access_token ?>">

  <h3>Twitter</h3>
  <p>Connecting a Twitter account will automatically "favorite" tweets on Twitter when you favorite a Twitter URL in Quill.</p>
  <input type="button" id="twitter-button" value="Checking" class="btn">

</div>
<script>
$(function(){

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

});
</script>
