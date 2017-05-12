<div class="narrow">
  <?= partial('partials/header') ?>

  <div style="clear: both;" class="notice-pad">
    <div class="alert alert-success hidden" id="test_success"><strong>Success!</strong><br>Your checkin should be on your website now!<br><a href="" id="post_href">View your post</a></div>
    <div class="alert alert-danger hidden" id="test_error"><strong>Your endpoint did not return a Location header.</strong><br>See <a href="/creating-a-micropub-endpoint">Creating a Micropub Endpoint</a> for more information.</div>
  </div>

  <form role="form" style="margin-top: 20px;" id="note_form">

    <div class="form-group">
      <label>Flight Number (e.g. <code>AS387</code>)</label>
      <input type="text" id="flight" class="form-control" value="AS387">
    </div>

    <div style="float: right; margin-top: 6px;">
      <button class="btn btn-success" id="btn_post">Check In</button>
    </div>

  </form>

</div>
<script>
$(function(){
  $("#btn_post").click(function(){
    if($(this).text() == "Find Flight") {
      $.post("/flight", {
        action: "find",
        flight: $("#flight").val()
      }, function(data){

      });
    } else {
      $("#btn_post").addClass("loading disabled").text("Working...");
      $.post("/flight", {
        action: "checkin",
        flight: $("#flight").val()
      }, function(response){
        if(response.location != false) {
          $("#test_success").removeClass('hidden');
          $("#test_error").addClass('hidden');
          $("#post_href").attr("href", response.location);
          $("#note_form").addClass("hidden");
        } else {
          $("#test_success").addClass('hidden');
          $("#test_error").removeClass('hidden');
          $("#btn_post").removeClass("loading disabled").text("Check In");
        }
      });
    }
    return false;
  });
});
</script>
