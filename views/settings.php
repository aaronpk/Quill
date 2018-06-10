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
      <td><?= $this->user->micropub_media_endpoint ? '<code>'.$this->user->micropub_media_endpoint.'</code>' : '<a href="https://www.w3.org/TR/micropub/#media-endpoint">no media endpoint</a>' ?></td>
    </tr>
    <tr>
      <td width="140">access token</td>
      <td><code style="word-break: break-word; white-space: pre-wrap;"><?= $this->user->micropub_access_token ?></code></td>
    </tr>
    <tr>
      <td>
        <input type="button" class="btn btn-default" value="Reset Login" id="reset-login">
      </td>
      <td>
        Clicking this button will tell your token endpoint to revoke the token, Quill will forget the access token stored, forget all cached endpoints, and sign you out. If you sign back in, you will start over and see the debugging screens and scope options again.
      </td>
    </tr>
  </table>


  <h3>Twitter</h3>
  <p>Connecting a Twitter account will automatically "favorite" and "retweet" tweets on Twitter when you favorite and retweet a Twitter URL in Quill.</p>
  <input type="button" id="twitter-button" value="Checking" class="btn">


  <h3>Backwards Compatibility</h3>

  <p>You can customize some of the properties that are sent in the Micropub request to work with your specific endpoint.</p>

  <table class="table table-condensed" width="100%">
    <tr>
      <td>Slug</td>
      <td width="160">
        <div style="margin-bottom:4px;"><input type="text" id="slug-field-name" value="<?= $this->user->micropub_slug_field ?>" placeholder="mp-slug" class="form-control"></div>
        <div><input type="button" class="btn btn-primary" value="Save" id="save-slug-field"></div>
      </td>
      <td>Choose the name of the field that the slug will be sent in. This should be set to <code>mp-slug</code> unless your endpoint is using a custom property or the deprecated <code>slug</code> property.</td>
    </tr>
    <tr>
      <td>Syndication</td>
      <td>
        <div style="margin-bottom:4px;">
          <select id="syndicate-to-field-name">
            <option value="mp-syndicate-to" <?= $this->user->micropub_syndicate_field == 'mp-syndicate-to' ? 'selected="selected"' : '' ?>>mp-syndicate-to</option>
            <option value="syndicate-to" <?= $this->user->micropub_syndicate_field == 'syndicate-to' ? 'selected="selected"' : '' ?>>syndicate-to</option>
          </select>
        </div>
        <div><input type="button" class="btn btn-primary" value="Save" id="save-syndicate-to-field"></div>
      </td>
      <td>Choose the name of the field that the syndication values will be sent in. This should be set to <code>mp-syndicate-to</code> unless your endpoint is using the deprecated <code>syndicate-to</code> property.</td>
    </tr>
    <tr>
      <td>Send HTML Content</td>
      <td><input type="checkbox" id="send-html-content" <?= $this->user->micropub_optin_html_content ? 'checked="checked"' : '' ?>></td>
      <td>When checked, content from Quill's HTML editor will be sent in a property called <code>content[html]</code> rather than just <code>content</code>. See the <a href="https://www.w3.org/TR/micropub/#new-article-with-html">Micropub specification</a> for more details.</td>
    </tr>
  </table>


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

  $("#send-html-content").click(function(){
    var enabled = $(this).attr("checked") == "checked";
    $.post("/settings/save", {
      html_content: (enabled ? 1 : 0)
    });
  });

  $("#save-slug-field").click(function(){
    $.post("/settings/save", {
      slug_field: $("#slug-field-name").val()
    });
  });

  $("#save-syndicate-to-field").click(function(){
    $.post("/settings/save", {
      syndicate_field: $("#syndicate-to-field-name").val()
    });
  });

  $("#reset-login").click(function(){
    $.post("/auth/reset", function(){
      window.location = "/";
    });
  });

});
</script>
