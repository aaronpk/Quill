<div class="narrow">
  <?= partial('partials/header') ?>

    <div style="clear: both;">
      <div class="alert alert-success hidden" id="test_success"><strong>Success! We found a Location header in the response!</strong><br>Your post should be on your website now!<br><a href="" id="post_href">View your post</a></div>
      <div class="alert alert-danger hidden" id="test_error"><strong>Your endpoint did not return a Location header.</strong><br>See <a href="/creating-a-micropub-endpoint">Creating a Micropub Endpoint</a> for more information.</div>
    </div>

    <form role="form" style="margin-top: 20px;" id="note_form">

      <div class="form-group" style="margin-top: 18px;">
        <label>Event Name</label>
        <input type="text" class="form-control" id="event_name" placeholder="" value="">
      </div>

      <div class="form-group" id="start_date" style="margin-top: 18px;">
        <label>Start Date/Time</label>
        <div class="form-group">
          <input type="text" class="form-control date" placeholder="<?= date('Y-m-d') ?>" value="" style="max-width: 40%; margin-right: 4px; float: left;">
          <input type="text" class="form-control time" placeholder="14:30" value="" style="max-width: 40%; margin-right: 4px; float: left;">
          <input type="text" class="form-control timezone" placeholder="-08:00" style="max-width: 15%;">
        </div>
      </div>

      <div class="form-group" id="end_date" style="margin-top: 18px;">
        <label>End Date/Time (Optional)</label>
        <div class="form-group">
          <input type="text" class="form-control date" placeholder="<?= date('Y-m-d') ?>" value="" style="max-width: 40%; margin-right: 4px; float: left;">
          <input type="text" class="form-control time" placeholder="14:30" value="" style="max-width: 40%; margin-right: 4px; float: left;">
          <input type="text" class="form-control timezone" placeholder="-08:00" style="max-width: 15%;">
        </div>
      </div>

      <div class="form-group" style="margin-top: 18px;">
        <label>Location</label>
        <input type="text" class="form-control" id="event_location" placeholder="" value="">
      </div>


      <div class="form-group" style="margin-top: 18px;">
        <label for="note_category">Tags</label>
        <input type="text" id="note_category" value="" class="form-control">
      </div>

      <div style="float: right; margin-top: 6px;">
        <button class="btn btn-success" id="btn_post">Post</button>
      </div>

    </form>

</div>

<script>
  $(function(){
    var d = new Date();
    $("#start_date .timezone").val(tz_seconds_to_offset(d.getTimezoneOffset() * 60 * -1));
  });

  $("#note_category").tokenfield({
    createTokensOnBlur: true,
    beautify: true
  });

  $("#btn_post").click(function(){

    var event_start = $("#start_date .date").val()+"T"+$("#start_date .time").val()+$("#start_date .timezone").val();
    var event_end;
    if($("#end_date .date").val()) {
      event_end = $("#end_date .date").val()+"T"+$("#end_date .time").val()+$("#end_date .timezone").val();
    }

    var properties = {
      name: $("#event_name").val(),
      start: event_start,
      location: $("#event_location").val(),
      category: tokenfieldToArray("#note_category")
    };

    if(event_end) {
      properties.end = event_end;
    }


    $.post("/micropub/postjson", {
      data: JSON.stringify({
        "type": "h-event",
        "properties": properties
      })
    }, function(response){

      if(response.location != false) {
        $("#test_success").removeClass('hidden');
        $("#test_error").addClass('hidden');
        $("#post_href").attr("href", response.location);
        $("#note_form").slideUp(200, function(){
          $(window).scrollTop($("#test_success").position().top);
        });
      } else {
        $("#test_success").addClass('hidden');
        $("#test_error").removeClass('hidden');
      }

    });
    return false;
  });
</script>
