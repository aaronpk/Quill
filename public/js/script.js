
  function tz_seconds_to_offset(seconds) {
    var tz_offset = '';
    var hours = zero_pad(Math.abs(seconds / 60 / 60));
    var minutes = zero_pad(Math.floor(seconds / 60) % 60);
    return (seconds < 0 ? '-' : '+') + hours + ":" + minutes;
  }

  function zero_pad(num) {
    num = "" + num;
    if(num.length == 1) {
      num = "0" + num;
    }
    return num;
  }

  function csv_to_array(val) {
    if(val.length > 0) {
      return val.split(/[, ]+/);
    } else {
      return [];
    }
  }


$(function(){

  // Set the date from JS
  var d = new Date();
  $("#note_date").val(d.getFullYear()+"-"+zero_pad(d.getMonth()+1)+"-"+zero_pad(d.getDate()));
  $("#note_time").val(zero_pad(d.getHours())+":"+zero_pad(d.getMinutes())+":"+zero_pad(d.getSeconds()));
  $("#note_tzoffset").val(tz_seconds_to_offset(d.getTimezoneOffset() * 60 * -1));

  // ctrl-s to save
  $(window).on('keydown', function(e){
    if(e.keyCode == 83 && e.ctrlKey){
      $("#btn_post").click();
    }
  });

})

function auto_prefix_url_field(field) {
  var str = field.value;
  if(!/^https?:\/\//.test(str)) {
    str = "http://" + str;
  }
  field.value = str;
}
