  <div class="narrow">
    <?= partial('partials/header') ?>

      <div style="clear: both;" class="notice-pad">
        <div class="alert alert-success hidden" id="test_success"><strong>Success! </strong><a href="" id="post_href">View your post</a></div>
        <div class="alert alert-danger hidden" id="test_error"><strong>Something went wrong!</strong><br>Your Micropub endpoint indicated that something went wrong creating the post.</div>
      </div>

      <form style="margin-top: 20px;" id="weight_form">

        <div class="form-group">
          <label for="exercise_activity">Activity</label>
          <select id="exercise_activity" class="form-control">
            <option value="indoor-cycling">Indoor Cycling</option>
          </select>
        </div>

        <div class="form-group">
          <label for="exercise_minutes">Minutes</label>
          <input type="number" id="exercise_minutes" class="form-control">
        </div>

        <div class="form-group">
          <label for="exercise_heartrate">Avg Heart Rate</label>
          <input type="number" id="exercise_heartrate" class="form-control">
        </div>

        <div class="form-group">
          <label for="date">Date and Time</label>
          <input type="text" id="date" class="form-control" value="<?= date('Y-m-d H:i:s') ?>">
        </div>
        
        <div style="float: right; margin-top: 6px;">
          <button class="btn btn-success" id="btn_post">Post</button>
        </div>
      </form>

      <div style="clear: both;"></div>

  </div>
<script>
$(function(){

  var d = new Date();
  var tzOffset = tz_seconds_to_offset(d.getTimezoneOffset() * 60 * -1);
  $("#date").val( $("#date").val() + " " + tzOffset);
  
  $("#btn_post").click(function(){
    $("#btn_post").addClass("loading disabled");

    $.post("/exercise", {
      activity: $("#exercise_activity").val(),
      minutes: $("#exercise_minutes").val(),
      heartrate: $("#exercise_heartrate").val(),
      published: $("#date").val()
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
