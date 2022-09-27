  <div class="narrow">
    <?= partial('partials/header') ?>

      <div style="clear: both;" class="notice-pad">
        <div class="alert alert-success hidden" id="test_success"><strong>Success! </strong><a href="" id="post_href">View your post</a></div>
        <div class="alert alert-danger hidden" id="test_error"><strong>Something went wrong!</strong><br>Your Micropub endpoint indicated that something went wrong creating the post.</div>
      </div>

      <form role="form" style="margin-top: 20px;" id="note_form">

        <div class="form-group">
          <label for="tweet_url">Tweet to Import</label>
          <input type="text" id="tweet_url" value="<?= $this->tweet_url ?>" class="form-control">
        </div>


        <div style="float: right; margin-top: 6px;">
          <button class="btn btn-success" id="btn_post">Import</button>
        </div>

        <div style="float: right; margin-top: 6px; margin-right: 6px;">
          <button class="btn btn-default" id="btn_preview">Preview</button>
        </div>
        
      </form>

      <div style="clear: both;"></div>
      
      <div id="preview_data" class="hidden">
        <pre></pre>
      </div>

  </div>

<script>
$(function(){

  $("#btn_preview").click(function(e){
    
    $("#btn_preview").addClass("loading disabled");
    
    $.post("/twitter/preview", {
      tweet_url: $("#tweet_url").val(),
    }, function(response){
      $("#preview_data pre").text(response.json);
      $("#preview_data").removeClass("hidden");
      $("#btn_preview").removeClass("loading disabled");
    });
    
    return false;
  });

  $("#btn_post").click(function(){
    $("#btn_post").addClass("loading disabled");

    $.post("/twitter", {
      tweet_url: $("#tweet_url").val(),
    }, function(response){
      
      if(response.location != false) {

        $("#test_success").removeClass('hidden');
        $("#test_error").addClass('hidden');
        $("#post_href").attr("href", response.location);
        $("#note_form").addClass('hidden');

        window.location = response.location;
      } else {
        $("#test_success").addClass('hidden');
        $("#test_error").removeClass('hidden');
        if(response.error_details) {
          $("#test_error").text(response.error_details);
        }
        $("#btn_post").removeClass("loading disabled");
      }

    });
    return false;
  });

});

</script>
