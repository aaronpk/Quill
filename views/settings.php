<div class="narrow">
  <?= partial('partials/header') ?>

  <h2>Signed In As</h2>

  <table class="table table-condensed">
    <tr>
      <td>me</td>
      <td><code><?= $this->user->url; ?></code> (should be your URL)</td>
    </tr>
    <tr>
      <td>scope</td>
      <td><code><?= $this->user->micropub_scope ?></code> (should be a space-separated list of permissions including "create")</td>
    </tr>
    <tr>
      <td>micropub endpoint</td>
      <td><code><?= $this->user->micropub_endpoint ?></code> (should be a URL)</td>
    </tr>
    <tr>
      <td>media endpoint</td>
      <td><?= $this->user->media_endpoint ? '<code>'.$this->user->media_endpoint.'</code>' : '<a href="https://www.w3.org/TR/micropub/#media-endpoint">no media endpoint</a>' ?></td>
    </tr>
    <tr>
      <td width="140">access token</td>
      <td><code style="word-break: break-word; white-space: pre-wrap;"><?= $this->user->micropub_access_token ?></code></td>
    </tr>
  </table>


  <h3>Twitter</h3>
  <p>Connecting a Twitter account will automatically "favorite" and "retweet" tweets on Twitter when you favorite and retweet a Twitter URL in Quill.</p>
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
