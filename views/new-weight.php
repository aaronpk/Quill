  <div class="narrow">
    <?= partial('partials/header') ?>

      <div style="clear: both;" class="notice-pad">
        <div class="alert alert-success hidden" id="test_success"><strong>Success! </strong><a href="" id="post_href">View your post</a></div>
        <div class="alert alert-danger hidden" id="test_error"><strong>Something went wrong!</strong><br>Your Micropub endpoint indicated that something went wrong creating the post.</div>
      </div>

      <form style="margin-top: 20px;" id="weight_form">

        <div class="form-group">
          <label for="weight_num">Weight (in <?= $this->unit ?>)</label>
          <input type="number" id="weight_num" class="form-control">
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
    $("#btn_post").addClass("loading disabled");

    $.post("/weight", {
      weight_num: $("#weight_num").val()
    }, function(response){
      if(response.location != false) {

        $("#test_success").removeClass('hidden');
        $("#test_error").addClass('hidden');
        $("#post_href").attr("href", response.location);

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
