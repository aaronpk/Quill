  <div class="narrow">
    <?= partial('partials/header') ?>

      <div style="clear: both;">
        <div class="alert alert-success hidden" id="test_success"><strong>Success! We found a Location header in the response!</strong><br>Your post should be on your website now!<br><a href="" id="post_href">View your post</a></div>
        <div class="alert alert-danger hidden" id="test_error"><strong>Your endpoint did not return a Location header.</strong><br>See <a href="/creating-a-micropub-endpoint">Creating a Micropub Endpoint</a> for more information.</div>
      </div>

      <form role="form" style="margin-top: 20px;" id="note_form">

        <div class="form-group">
          <label for="note_url">URL to Repost (<code>repost-of</code>)</label>
          <input type="text" id="note_url" value="<?= $this->url ?>" class="form-control">
        </div>

        <div style="float: right; margin-top: 6px;">
          <button class="btn btn-success" id="btn_post">Post</button>
        </div>

      </form>

      <div style="clear: both;"></div>

  </div>

<script>
$(function(){

  $("#btn_post").click(function(){

    $.post("/repost", {
      url: $("#note_url").val()
    }, function(response){

      if(response.location != false) {

        $("#test_success").removeClass('hidden');
        $("#test_error").addClass('hidden');
        $("#post_href").attr("href", response.location);

        window.location = response.location;
      } else {
        $("#test_success").addClass('hidden');
        $("#test_error").removeClass('hidden');
      }

    });
    return false;
  });

});

</script>
