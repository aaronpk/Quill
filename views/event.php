<div class="narrow">
  <?= partial('partials/header') ?>

    <div style="clear: both;">
      <div class="alert alert-success hidden" id="test_success"><strong>Success! </strong><a href="" id="post_href">View your post</a></div>
      <div class="alert alert-danger hidden" id="test_error"><strong>Something went wrong!</strong><br>Your Micropub endpoint indicated that something went wrong creating the post.</div>
    </div>

    <form role="form" style="margin-top: 20px;" id="note_form">

      <div class="form-group" style="margin-top: 18px;">
        <label>Event Name</label>
        <input type="text" class="form-control" id="event_name" placeholder="" value="">
      </div>

      <div class="form-group" style="margin-top: 18px;">
        <label>Location</label>
        <input type="text" class="form-control" id="event_location" placeholder="" value="">
        <span class="help-block" id="location_preview"></span>
      </div>

      <div id="map" class="hidden" style="width: 100%; height: 180px; border-radius: 4px; border: 1px #ccc solid;"></div>

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
        <label for="note_category">Tags</label>
        <input type="text" id="note_category" value="" class="form-control">
      </div>

      <?php if($this->channels): ?>
        <div class="form-group">
          <label for="note_channel">Channel</label>
          <div id="channel-container">
            <?php
              echo '<select class="form-control" id="note_channel">';
              echo '<option value="none"></option>';
              foreach($this->channels as $ch) {
                echo '<option value="'.htmlspecialchars($ch).'" '.($ch == 'events' ? 'selected' : '').'>'
                   . htmlspecialchars($ch)
               . '</option>';
              }
              echo '</select>';
            ?>
          </div>
        </div>
      <?php endif; ?>

      <div style="float: right; margin-top: 6px;">
        <button class="btn btn-success" id="btn_post">Post</button>
      </div>

    </form>

</div>

<link rel="stylesheet" href="/libs/bootstrap-typeahead/typeahead.css">
<script src="/libs/bootstrap-typeahead/typeahead.min.js"></script>
<?php if(Config::$googleMapsAPIKey): ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= Config::$googleMapsAPIKey ?>&libraries=places"></script>
<?php endif ?>
<script>
  <?php if(Config::$googleMapsAPIKey): ?>
  var map = new google.maps.Map(document.getElementById('map'), {
    center: new google.maps.LatLng(-45,122),
    zoom: 15
  });
  <?php else: ?>
  var map = null;
  <?php endif ?>

  var d = new Date();
  var tzOffset = tz_seconds_to_offset(d.getTimezoneOffset() * 60 * -1);

  var selectedPlace;
  if(map) {
    var gservice = new google.maps.places.AutocompleteService();
    var gplaces = new google.maps.places.PlacesService(map);
    var selectedPlacePin;
  }

  $(function(){
    // Start the event timezone offset in the browser's timezone
    $("#start_date .timezone").attr("placeholder", tzOffset);
    $("#end_date .timezone").attr("placeholder", tzOffset);

    // As soon as a time is entered, move the placeholder offset to the value
    $("#start_date .time").on("keydown", function(){
      $("#start_date .timezone").val($("#start_date .timezone").attr("placeholder"));
    });
    $("#end_date .time").on("keydown", function(){
      $("#end_date .timezone").val($("#end_date .timezone").attr("placeholder"));
    });

    if(map) {
      $("#event_location").typeahead({
        minLength: 3,
        highlight: true
      }, {
        limit: 5,
        async: true,
        source: function(query, sync, async) {
          gservice.getPlacePredictions({ input: query }, function(predictions, status) {
            if (status == google.maps.places.PlacesServiceStatus.OK) {
              async(predictions);
            }
          });
        },
        display: function(item) {
          return item.description;
        },
        templates: {
          suggestion: function(item) {
            return '<span>'+item.description+'</span>';
          }
        }
      }).bind('typeahead:select', function(ev, suggestion) {

        gplaces.getDetails({
          placeId: suggestion.place_id,
          fields: ["geometry", "name", "address_component", "url", "utc_offset"]
        }, function(result, status) {
          if(status != google.maps.places.PlacesServiceStatus.OK) {
            alert('Cannot find address');
            return;
          }
          console.log(result);

          map.setCenter(result.geometry.location);

          if(selectedPlacePin) {
            selectedPlacePin.setMap(null);
            selectedPlacePin = null;
          }
          selectedPlacePin = new google.maps.Marker({
            position: result.geometry.location,
            map: map
          });

          selectedPlace = {
            type: ["h-card"],
            properties: {
              name: [result.name],
              latitude: [result.geometry.location.lat()],
              longitude: [result.geometry.location.lng()],
            }
          };

          address = '';
          locality = '';
          region = '';
          country = '';
          for(var i in result.address_components) {

            if(result.address_components[i].types.includes('street_number')) {
              address += ' '+result.address_components[i].short_name;
            }
            if(result.address_components[i].types.includes('route')) {
              address += ' '+result.address_components[i].short_name;
            }

            if(result.address_components[i].types.includes('locality')) {
              locality = result.address_components[i].long_name;
            }
            if(result.address_components[i].types.includes('administrative_area_level_1')) {
              region = result.address_components[i].long_name;
            }
            if(result.address_components[i].types.includes('country')) {
              country = result.address_components[i].short_name;
            }
          }
          if(address) {
            selectedPlace['properties']['street-address'] = [address.trim()];
          }
          if(locality) {
            selectedPlace['properties']['locality'] = [locality];
          }
          if(region) {
            selectedPlace['properties']['region'] = [region];
          }
          if(country) {
            selectedPlace['properties']['country-name'] = [country];
          }

          if(result.utc_offset) {
            tzOffset = tz_seconds_to_offset(result.utc_offset * 60);
            $("#start_date .timezone").attr("placeholder", tzOffset);
            $("#end_date .timezone").attr("placeholder", tzOffset);
            if($("#start_date .timezone").val()) {
              $("#start_date .timezone").val($("#start_date .timezone").attr("placeholder"));
            }
            if($("#end_date .timezone").val()) {
              $("#end_date .timezone").val($("#end_date .timezone").attr("placeholder"));
            }
          }

          $("#map").removeClass("hidden");
          $("#location_preview").text('');
        });
      });
    }
  });

  $("#note_category").tokenfield({
    createTokensOnBlur: true,
    beautify: true
  });

  $("#btn_post").click(function(){

    var event_start = $("#start_date .date").val();
    if($("#start_date .time").val()) {
      event_start += "T"+$("#start_date .time").val()+$("#start_date .timezone").val();
    }
    var event_end;
    if($("#end_date .date").val()) {
      event_end = $("#end_date .date").val();
      if($("#end_date .time").val()) {
        event_end += "T"+$("#end_date .time").val()+$("#end_date .timezone").val();
      }
    }

    var properties = {
      name: [$("#event_name").val()],
      start: [event_start],
      location: (selectedPlace ? selectedPlace : $("#event_location").val()),
      category: tokenfieldToArray("#note_category")
    };

    if(event_end) {
      properties.end = event_end;
    }

    if($("#note_channel").val()) {
      properties['p3k-channel'] = $("#note_channel").val();
    }

    $.post("/micropub/postjson", {
      data: JSON.stringify({
        "type": ["h-event"],
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
