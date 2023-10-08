<div class="narrow">
  <?= partial('partials/header') ?>

    <div style="clear: both;" class="notice-pad">
      <div class="alert alert-success hidden" id="test_success"><strong>Success! </strong><a href="" id="post_href">View your post</a></div>
      <div class="alert alert-danger hidden" id="test_error"><strong>Something went wrong!</strong><br>Your Micropub endpoint indicated that something went wrong creating the post.</div>
    </div>

    <form role="form" style="margin-top: 20px;" id="note_form">

      <h4>Legs</h4>
      <div class="form-group" id="itinerary-legs-container">
        <div style="display:none;" id="leg-template">
          <div class="itinerary-leg">
            <input type="hidden" class="template" value="1">
            <div class="remove">&times;</div>
            <div class="row">
              <div class="col-xs-3">
                <label>Transit Type</label>
                <select class="leg-transit-type form-control">
                  <option value="air">Air</option>
                  <option value="train">Train</option>
                  <option value="bus">Bus</option>
                  <option value="boat">Boat</option>
                  <option value="generic">Generic</option>
                </select>
              </div>
              <div class="col-xs-3">
                <label>Operator</label>
                <input type="text" class="form-control leg-operator" placeholder="Operator" value="">
              </div>
              <div class="col-xs-3">
                <label>Number</label>
                <input type="text" class="form-control leg-number" placeholder="Number" value="">
              </div>
            </div>
            <div class="row">
              <div class="col-xs-2">
                <label>Origin</label>
                <input type="text" class="form-control leg-origin" placeholder="Origin" value="">
              </div>
              <div class="col-xs-9">
                <label>Departure</label>
                <div class="form-group leg-departure">
                  <input type="text" class="form-control leg-departure-date date" style="max-width:160px; float:left; margin-right: 4px;" value="">
                  <input type="text" class="form-control leg-departure-time time" style="max-width:85px; float:left; margin-right: 4px;" value="">
                  <span><input type="text" class="form-control leg-departure-tz tz" style="max-width:75px;" value=""></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-2">
                <label>Destination</label>
                <input type="text" class="form-control leg-destination" placeholder="Destination" value="">
              </div>
              <div class="col-xs-9">
                <label>Arrival</label>
                <div class="form-group leg-arrival">
                  <input type="text" class="form-control leg-arrival-date date" style="max-width:160px; float:left; margin-right: 4px;" value="">
                  <input type="text" class="form-control leg-arrival-time time" style="max-width:85px; float:left; margin-right: 4px;" value="">
                  <span><input type="text" class="form-control leg-arrival-tz tz" style="max-width:75px;" value=""></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <button class="btn btn-default" id="btn_add_leg">Add Leg</button>

      <div class="form-group" style="margin-top: 18px;">
        <label for="note_category">Tags</label>
        <input type="text" id="note_category" value="" class="form-control">
      </div>

      <div style="float: right; margin-top: 6px;">
        <button class="btn btn-success" id="btn_post">Post</button>
      </div>

    </form>

</div>



<style type="text/css">
.itinerary-leg {
  margin-bottom: 10px;
  padding: 8px 8px;
  border-left: 4px #5bc0de solid;
  background-color: #f4f8fa;
}
.itinerary-leg .row {
  margin-bottom: 10px;
}
.itinerary-leg .remove {
  float: right;
  margin-right: 10px;
  margin-top: 0;
  font-size: 20px;
  cursor: pointer;
  color: #40A9C7;
}
.itinerary-leg .remove:hover {
  color: #7ECDE4;
}
</style>

<script>
$(function(){

  $("#note_category").tokenfield({
    createTokensOnBlur: true,
    beautify: true
  });

  $("#btn_add_leg").click(function(){
    add_leg();
    return false;
  });

  function bind_leg_x() {
    $(".itinerary-leg .remove").unbind("click").click(function(){
      // Don't allow the only leg to be removed. (2 because there is an invisible one as the template)
      if($(".itinerary-leg").length > 2) {
        $(this).parent().remove();
      }
    });
  }

  function timezone_for_airport(code, callback) {
    $.getJSON("/airport-info?code="+code, function(data){
      callback(data.offset);
    });
  }

  function bind_leg_timezone() {
    $(".itinerary-leg .leg-origin").unbind("change").change(function(el){
      timezone_for_airport($(this).val(), function(offset){
        $(el.target).parents(".itinerary-leg").find(".leg-departure-tz").val(offset);
        $(el.target).parents(".itinerary-leg").find(".leg-departure-tz").parent().addClass("has-success");
      });
    });
    $(".itinerary-leg .leg-destination").unbind("change").change(function(el){
      timezone_for_airport($(this).val(), function(offset){
        $(el.target).parents(".itinerary-leg").find(".leg-arrival-tz").val(offset);
        $(el.target).parents(".itinerary-leg").find(".leg-arrival-tz").parent().addClass("has-success");
      });
    });
    $(".leg-departure-date").unbind("change").change(function(el){
      $(el.target).parents(".itinerary-leg").find(".leg-arrival-date").val($(el.target).val());
    });
  }

  function add_leg() {
    var last_date = $(".itinerary-leg:last .date").val();
    var last_airport = $(".itinerary-leg:last .leg-destination").val();
    var last_operator = $(".itinerary-leg:last .leg-operator").val();
    
    $("#itinerary-legs-container").append($("#leg-template").html());

    $(".itinerary-leg:last .template").val(0);
    var d = new Date();
    if(last_date) {
      $(".itinerary-leg:last .date").val(last_date);
    } else {
      $(".itinerary-leg:last .date").val(d.getFullYear()+"-"+zero_pad(d.getMonth()+1)+"-"+zero_pad(d.getDate()));
    }
    $(".itinerary-leg:last .time").val(zero_pad(d.getHours())+":"+zero_pad(d.getMinutes())+":00");
    $(".itinerary-leg:last .tz").val(tz_seconds_to_offset(d.getTimezoneOffset() * 60 * -1));
    $(".itinerary-leg:last .leg-origin").val(last_airport);
    $(".itinerary-leg:last .leg-operator").val(last_operator);
    
    /*
    $('.itinerary-leg:last .date').datepicker({
      'format': 'yyyy-mm-dd',
      'autoclose': true,
      'todayHighlight': true
    });

    $('.itinerary-leg:last .time').timepicker({
      'showDuration': true,
      'timeFormat': 'g:ia'
    });

    $('.itinerary-leg:last').datepair();
    */

    bind_leg_x();
    bind_leg_timezone();
  }

  add_leg();

  $("#btn_post").click(function(){

    var itinerary = [];

    $(".itinerary-leg").each(function(){
      if($(this).find(".template").val() == 1) { return; }

      var departure = $(this).find(".leg-departure-date").val()+"T"+$(this).find(".leg-departure-time").val()+$(this).find(".leg-departure-tz").val();
      var arrival = $(this).find(".leg-arrival-date").val()+"T"+$(this).find(".leg-arrival-time").val()+$(this).find(".leg-arrival-tz").val();

      itinerary.push({
        "type": ["h-leg"],
        "properties": {
          "transit-type": [$(this).find(".leg-transit-type").val()],
          "operator": [$(this).find(".leg-operator").val()],
          "number": [$(this).find(".leg-number").val()],
          "origin": [$(this).find(".leg-origin").val()],
          "destination": [$(this).find(".leg-destination").val()],
          "departure": [departure],
          "arrival": [arrival]
        }
      });
    });

    var category = tokenfieldToArray("#note_category");

    properties = {
      itinerary: itinerary
    };
    if(category.length > 0) {
      properties['category'] = category;
    }

    $("#btn_post").addClass("loading disabled").text("Working...");
    $.post("/micropub/postjson", {
      data: JSON.stringify({
        "type": ["h-entry"],
        "properties": properties
      })
    }, function(response){

      if(response.location != false) {
        $("#test_success").removeClass('hidden');
        $("#test_error").addClass('hidden');
        $("#post_href").attr("href", response.location);
        $("#note_form").addClass("hidden");
      } else {
        $("#test_success").addClass('hidden');
        $("#test_error").removeClass('hidden');
        $("#btn_post").removeClass("loading disabled").text("Post");
      }

    });
    return false;
  });

});

<?= partial('partials/syndication-js') ?>

</script>
