<div class="narrow">
  <?= partial('partials/header') ?>

  <h2>Signed In As</h2>

  <table class="table table-condensed">
    <tr>
      <td>me</td>
      <td><code><?= $this->user->url; ?></code> (should be your URL)</td>
    </tr>
    <?php if(profile('name')) { ?>
    <tr>
      <td>Name</td>
      <td><code><?= profile('name'); ?></code> </td>
     </tr>
    <?php } ?>
    <?php if(profile('photo')) { ?>
    <tr>
      <td>Photo</td>
      <td><code><?= profile('photo'); ?></code> </td>
     </tr>
    <?php } ?>
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
    <?php if($this->user->supported_post_types): ?>
    <tr>
      <td>supported post types</td>
      <td>
        <ul>
        <?php
        $types = json_decode($this->user->supported_post_types, true);
        foreach($types as $type) {
          echo '<li>'.htmlspecialchars($type['name']).' ('.$type['type'].')</li>';
        }
        ?>
        </ul>
      </td>
    </tr>
    <?php endif ?>
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

  <h3>Post Format Settings</h3>
  <table class="table table-condensed" width="100%">
    <tr>
      <td>Weight Unit</td>
      <td width="160">
        <div style="margin-bottom:4px;"><input type="text" id="weight-unit" value="<?= $this->user->weight_unit ?>" class="form-control"></div>
        <div><input type="button" class="btn btn-primary" value="Save" id="save-weight-unit"></div>
      </td>
      <td>The unit to be used for <a href="/weight">weight posts</a>.</td>
    </tr>
  </table>

  <h3>Syndication Targets</h3>

        <div class="form-group">
          <label for="note_syndicate-to"><a href="javascript:reload_syndications()">Reload</a></label>
          <div id="syndication-container">
            <?php
            if($this->syndication_targets) {
              echo '<ul>';
              foreach($this->syndication_targets as $syn) {
                echo '<li>'
                 . '<button data-syndicate-to="'.(isset($syn['uid']) ? htmlspecialchars($syn['uid']) : htmlspecialchars($syn['target'])).'" class="btn btn-default btn-block">'
                   . ($syn['favicon'] ? '<img src="'.htmlspecialchars($syn['favicon']).'" width="16" height="16"> ' : '')
                   . htmlspecialchars($syn['target'])
                 . '</button>'
               . '</li>';
              }
              echo '</ul>';
            } else {
              ?><div class="bs-callout bs-callout-warning">No syndication targets were found on your site.
              Your server can provide a <a href="/docs/syndication">list of supported syndication targets</a> that will appear as buttons here.</div><?php
            }
            ?>
          </div>
        </div>


  <h3>Channels</h3>

        <div class="form-group">
          <label for="note_channels"><a href="javascript:reload_channels()">Reload</a></label>
          <div id="channel-container">
            <?php
            if($this->channels) {
              echo '<select class="form-control" name="channel">';
              foreach($this->channels as $ch) {
                echo '<option value="'.htmlspecialchars($ch).'">'
                   . htmlspecialchars($ch)
               . '</option>';
              }
              echo '</select>';
            } else {
              ?><div class="bs-callout bs-callout-warning">No channels were found on your site.
              Your server can provide a <a href="/docs/channels">list of channels</a> that will appear as buttons here.</div><?php
            }
            ?>
          </div>
        </div>



  <?php if(!Config::$twitterClientID): ?>
    <h3>Twitter</h3>
    <p>Connecting a Twitter account will automatically "favorite" and "retweet" tweets on Twitter when you favorite and retweet a Twitter URL in Quill.</p>
    <input type="button" id="twitter-button" value="Checking" class="btn">
  <?php endif ?>


  <h3>Backwards Compatibility</h3>

  <p>You can customize some of the properties that are sent in the Micropub request to work with older software.</p>

  <table class="table table-condensed" width="100%">
    <tr>
      <td>Slug</td>
      <td width="160">
        <div style="margin-bottom:4px;"><input type="text" id="slug-field-name" value="<?= $this->user->micropub_slug_field ?>" placeholder="mp-slug" class="form-control"></div>
        <div><input type="button" class="btn btn-primary" value="Save" id="save-slug-field"></div>
      </td>
      <td>Choose the name of the field that the slug will be sent in. This should be set to <code>mp-slug</code> unless your software is using a custom property or the deprecated <code>slug</code> property.</td>
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
      <td>Choose the name of the field that the syndication values will be sent in. This should be set to <code>mp-syndicate-to</code> unless your software is using the deprecated <code>syndicate-to</code> property.</td>
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

  <?php if(!Config::$twitterClientID): ?>
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
  <?php endif ?>

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


  $("#save-weight-unit").click(function(){
    $.post("/settings/save", {
      weight_unit: $("#weight-unit").val()
    });
  });

});

<?= partial('partials/syndication-js') ?>

</script>
