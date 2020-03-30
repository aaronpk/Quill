
function reload_syndications() {
  $.getJSON("/micropub/syndications", function(data){
    if(data.targets) {
      $("#syndication-container").html('<ul></ul>');
      for(var i in data.targets) {
        var target = data.targets[i].target;
        var uid = data.targets[i].uid;
        var favicon = data.targets[i].favicon;
        $("#syndication-container ul").append('<li><button data-syndicate-to="'+htmlspecialchars(uid ? uid : target)+'" class="btn btn-default btn-block">'+(favicon ? '<img src="'+htmlspecialchars(favicon)+'" width="16" height="16"> ':'')+htmlspecialchars(target)+'</button></li>');
      }
      bind_syndication_buttons();
    } else {

    }
    console.log(data);
  });
}

function bind_syndication_buttons() {
  $("#syndication-container button").unbind("click").click(function(){
    $(this).toggleClass('btn-info');
    if(window.saveNoteState) {
      saveNoteState();
    }
    return false;
  });
}


function reload_channels() {
  $.getJSON("/micropub/channels", function(data){
    console.log(data);
    if(data.channels) {
      $("#channel-container").html('<select class="form-control" name="channel"></select>');
      for(var i in data.channels) {
        var channel = data.channels[i];
        $("#channel-container select").append('<option value="'+htmlspecialchars(channel)+'">'+htmlspecialchars(channel)+'</option>');
      }
    } else {

    }
    console.log(data);
  });
}

