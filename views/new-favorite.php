  <div class="narrow">
    <?= partial('partials/header') ?>

      <div style="clear: both;" class="notice-pad">
        <div class="alert alert-success hidden" id="test_success"><strong>Success! We found a Location header in the response!</strong><br>Your post should be on your website now!<br><a href="" id="post_href">View your post</a></div>
        <div class="alert alert-danger hidden" id="test_error"><strong>Your endpoint did not return a Location header.</strong><br>See <a href="/creating-a-micropub-endpoint">Creating a Micropub Endpoint</a> for more information.</div>
      </div>

      <form role="form" style="margin-top: 20px;" id="note_form">

        <div class="form-group">
          <label for="note_url">URL to Favorite (<code>like-of</code>)</label>
          <input type="text" id="note_url" value="<?= $this->url ?>" class="form-control">
        </div>

        <div style="float: right; margin-top: 6px;">
          <button class="btn btn-success" id="btn_post">Post</button>
        </div>

      </form>

      <div style="clear: both;"></div>

      <hr>
      <div style="text-align: right;" id="bookmarklet">
        Bookmarklet: <a href="javascript:<?= js_bookmarklet('partials/favorite-bookmarklet', $this) ?>" class="btn btn-default btn-xs">favorite</a>
      </div>

  </div>

<script>
$(function(){

  var autosubmit = window.location.search.match('autosubmit=true');

  if(autosubmit) {
    $(".footer, #bookmarklet").hide();
  }

  $("#btn_post").click(function(){
    $("#btn_post").addClass("loading disabled").text("Working...");

    var syndications = [];
    $("#syndication-container button.btn-info").each(function(i,btn){
      syndications.push($(btn).data('syndication'));
    });

    $.post("/favorite", {
      url: $("#note_url").val()
    }, function(response){
      if(response.location != false) {

        if(autosubmit) {
          $("#btn_post").hide();
        } else {
          $("#test_success").removeClass('hidden');
          $("#test_error").addClass('hidden');
          $("#post_href").attr("href", response.location);
        }

        window.location = response.location;
      } else {
        $("#test_success").addClass('hidden');
        $("#test_error").removeClass('hidden');
        $("#btn_post").removeClass("loading disabled").text("Post");
      }

    });
    return false;
  });

  if(autosubmit) {
    $("#btn_post").click();
  }

  bind_syndication_buttons();
});

<?= partial('partials/syndication-js') ?>

</script>
