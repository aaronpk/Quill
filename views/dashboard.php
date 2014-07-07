  <div class="narrow">
    <?= partial('partials/header') ?>

      <form role="form" style="margin-top: 20px;" id="note_form">

        <div class="form-group">
          <label for="note_content"><code>content</code></label>
          <textarea id="note_content" value="" class="form-control" style="height: 4em;"></textarea>
        </div>

        <div class="form-group">
          <label for="note_in_reply_to"><code>in-reply-to</code> (optional, a URL you are replying to)</label>
          <input type="text" id="note_in_reply_to" value="" class="form-control">
        </div>

        <div class="form-group">
          <label for="note_category"><code>category</code> (optional, comma-separated list of tags)</label>
          <input type="text" id="note_category" value="" class="form-control" placeholder="e.g. web, personal">
        </div>

        <div class="form-group">
          <label for="note_slug"><code>slug</code> (optional)</label>
          <input type="text" id="note_slug" value="" class="form-control">
        </div>

        <div class="form-group">
          <label for="note_syndicate-to"><code>syndicate-to</code> <a href="javascript:reload_syndications()">(refresh)</a></label>
          <div id="syndication-container">
            <?php
            if($this->syndication_targets) {
              echo '<ul>';
              foreach($this->syndication_targets as $syn) {
                echo '<li><button data-syndication="'.$syn['target'].'" class="btn btn-default btn-block"><img src="'.$syn['favicon'].'" width="16" height="16"> '.$syn['target'].'</button></li>';
              }
              echo '</ul>';
            } else {
              ?><div class="bs-callout bs-callout-warning">No syndication targets were found on your site. 
              You can provide a <a href="/docs#syndication">list of supported syndication targets</a> that will appear as checkboxes here.</div><?php
            }
            ?>
          </div>
        </div>

        <div class="form-group">
          <label for="note_location"><code>location</code></label>
          <input type="checkbox" id="note_location_chk" value="">
          <img src="/images/spinner.gif" id="note_location_loading" style="display: none;">

          <input type="text" id="note_location_msg" value="" class="form-control" placeholder="" readonly="readonly">
          <input type="hidden" id="note_location">
          <input type="hidden" id="location_enabled" value="<?= $this->location_enabled ?>">

          <div id="note_location_img" style="display: none;">
            <img src="" height="180" id="note_location_img_wide" class="img-responsive">
            <img src="" height="320" id="note_location_img_small" class="img-responsive">
          </div>
        </div>

        <button class="btn btn-success" id="btn_post">Post</button>
      </form>

      <div class="alert alert-success hidden" id="test_success"><strong>Success! We found a Location header in the response!</strong><br>Your post should be on your website now!<br><a href="" id="post_href">View your post</a></div>
      <div class="alert alert-danger hidden" id="test_error"><strong>Your endpoint did not return a Location header.</strong><br>See <a href="/creating-a-micropub-endpoint">Creating a Micropub Endpoint</a> for more information.</div>


      <div id="last_request_container" style="display: none;">
        <h4>Request made to your Micropub endpoint</h4>
        <pre id="test_request" style="width: 100%; min-height: 140px;"></pre>
      </div>

      <?php if($this->test_response): ?>
        <h4>Last response from your Micropub endpoint <span id="last_response_date">(<?= relative_time($this->response_date) ?>)</span></h4>
      <?php endif; ?>
      <pre id="test_response" style="width: 100%; min-height: 240px;"><?= htmlspecialchars($this->test_response) ?></pre>


      <div class="callout">
        <p>Clicking "Post" will post this note to your Micropub endpoint. Below is some information about the request that will be made.</p>

        <table class="table table-condensed">
          <tr>
            <td>me</td>
            <td><code><?= session('me') ?></code> (should be your URL)</td>
          </tr>
          <tr>
            <td>scope</td>
            <td><code><?= $this->micropub_scope ?></code> (should be a space-separated list of permissions including "post")</td>
          </tr>
          <tr>
            <td>micropub endpoint</td>
            <td><code><?= $this->micropub_endpoint ?></code> (should be a URL)</td>
          </tr>
          <tr>
            <td>access token</td>
            <td>String of length <b><?= strlen($this->micropub_access_token) ?></b><?= (strlen($this->micropub_access_token) > 0) ? (', ending in <code>' . substr($this->micropub_access_token, -7) . '</code>') : '' ?> (should be greater than length 0)</td>
          </tr>
        </table>
      </div>

  </div>

<script>
$(function(){

  $("#btn_post").click(function(){

    var syndications = [];
    $("#syndication-container button.btn-info").each(function(i,btn){
      syndications.push($(btn).data('syndication'));
    });

    $.post("/micropub/post", {
      content: $("#note_content").val(),
      'in-reply-to': $("#note_in_reply_to").val(),
      location: $("#note_location").val(),
      category: $("#note_category").val(),
      slug: $("#note_slug").val(),
      'syndicate-to': syndications.join(',')
    }, function(data){
      var response = JSON.parse(data);

      if(response.location != false) {
        $("#note_form").slideUp(200, function(){
          $(window).scrollTop($("#test_success").position().top);
        });

        $("#test_success").removeClass('hidden');
        $("#test_error").addClass('hidden');
        $("#post_href").attr("href", response.location);

        $("#note_content").val("");
        $("#note_in_reply_to").val("");
        $("#note_category").val("");
        $("#note_slug").val("");

      } else {
        $("#test_success").addClass('hidden');
        $("#test_error").removeClass('hidden');
      }

      $("#last_response_date").html("(just now)");
      $("#test_request").html(response.request);
      $("#last_request_container").show();
      $("#test_response").html(response.response);
    });
    return false;
  });

  function location_error(msg) {
    $("#note_location_msg").val(msg);
    $("#note_location_chk").removeAttr("checked");
    $("#note_location_loading").hide();
    $("#note_location_img").hide();
    $("#note_location_msg").removeClass("img-visible");
  }

  var map_template_wide = "<?= static_map('{lat}', '{lng}', 180, 700, 15) ?>";
  var map_template_small = "<?= static_map('{lat}', '{lng}', 320, 480, 15) ?>";

  function fetch_location() {
    $("#note_location_loading").show();

    navigator.geolocation.getCurrentPosition(function(position){

      $("#note_location_loading").hide();
      var geo = "geo:" + (Math.round(position.coords.latitude * 100000) / 100000) + "," + (Math.round(position.coords.longitude * 100000) / 100000) + ";u=" + position.coords.accuracy;
      $("#note_location_msg").val(geo);
      $("#note_location").val(geo);
      $("#note_location_img_small").attr("src", map_template_small.replace('{lat}', position.coords.latitude).replace('{lng}', position.coords.longitude));
      $("#note_location_img_wide").attr("src", map_template_wide.replace('{lat}', position.coords.latitude).replace('{lng}', position.coords.longitude));
      $("#note_location_img").show();
      $("#note_location_msg").addClass("img-visible");

    }, function(err){
      if(err.code == 1) {
        location_error("The website was not able to get permission");
      } else if(err.code == 2) {
        location_error("Location information was unavailable");
      } else if(err.code == 3) {
        location_error("Timed out getting location");
      }
    });
  }

  $("#note_location_chk").click(function(){
    if($(this).attr("checked") == "checked") {
      if(navigator.geolocation) {
        $.post("/prefs", {
          enabled: 1
        });
        fetch_location();
      } else {
        location_error("Browser location is not supported");
      }
    } else {
      $("#note_location_img").hide();
      $("#note_location_msg").removeClass("img-visible");
      $("#note_location_msg").val('');
      $("#note_location").val('');

      $.post("/prefs", {
        enabled: 0
      });
    }
  });

  if($("#location_enabled").val() == 1) {
    $("#note_location_chk").attr("checked","checked");
    fetch_location();
  }

  bind_syndication_buttons();
});

function reload_syndications() {
  $.getJSON("/micropub/syndications", function(data){
    if(data.targets) {
      $("#syndication-container").html('<ul></ul>');
      for(var i in data.targets) {
        var target = data.targets[i].target;
        var favicon = data.targets[i].favicon;
        $("#syndication-container ul").append('<li><button data-syndication="'+target+'" class="btn btn-default btn-block"><img src="'+favicon+'" width="16" height="16"> '+target+'</button></li>');
      }
      bind_syndication_buttons();
    } else {

    }
    console.log(data);
  });
}

function bind_syndication_buttons() {
  $("#syndication-container button").unbind("click").click(function(){
    $(this).toggleClass('btn-info');
    return false;
  });
}

</script>
<style type="text/css">

  #syndication-container ul {
    list-style-type: none;
    margin: 0;
    padding: 10px;
  }
  #syndication-container li {
    padding: 0;
    margin-bottom: 6px;
  }
  #syndication-container button {
    max-width: 240px;
    text-shadow: none;
  }
  #syndication-container button img {
    float: left;
    margin-left: 10px;
  }
  
  #last_response_date {
    font-size: 80%;
    font-weight: normal;
  }

  #btn_post {
    margin-bottom: 10px;
  }

  @media all and (max-width: 480px) {
    #note_location_img_wide {
      display: none;
    }
    #note_location_img_small {
      display: block;
    }
  }
  @media all and (min-width: 480px) {
    #note_location_img_wide {
      display: block;
    }
    #note_location_img_small {
      display: none;
    }
  }

  .img-visible {
    -webkit-border-bottom-right-radius: 0;
    -webkit-border-bottom-left-radius: 0;
    -moz-border-radius-bottomright: 0;
    -moz-border-radius-bottomleft: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
  }

  #note_location_img img {
    margin-top: -1px;
    border: 1px solid #ccc;
    -webkit-border-bottom-right-radius: 4px;
    -webkit-border-bottom-left-radius: 4px;
    -moz-border-radius-bottomright: 4px;
    -moz-border-radius-bottomleft: 4px;
    border-bottom-right-radius: 4px;
    border-bottom-left-radius: 4px;
  }

  .callout {
    border-left: 4px #5bc0de solid;
    background-color: #f4f8fa;
    padding: 20px;
    margin-top: 10px;
  }
  .callout table {
    margin-bottom: 0;
  }

</style>

