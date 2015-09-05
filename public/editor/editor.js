var editor = new MediumEditor('.editable', {
  buttons: ['bold', 'italic', 'anchor', 'header1', 'header2', 'quote', 'unorderedlist', 'pre'],
  placeholder: {text: 'Write something nice...'},
  paste: {
    // This example includes the default options for paste, if nothing is passed this is what it used
    forcePlainText: false,
    cleanPastedHTML: true,
    cleanReplacements: [],
    cleanAttrs: ['class', 'style', 'dir'],
    cleanTags: ['meta']
  }
});

$(function() {
  $('.editable').mediumInsert({
    editor: editor,
    beginning: true,
    addons: {
      images: {
        deleteScript: '/editor/delete-file',
        fileUploadOptions: {
          url: '/editor/upload'
        }
      },
      embeds: {
        oembedProxy: '/editor/oembed'
      }
    }
  });

  $.post('/editor/test-login', {}, function(response) {
    if(response.logged_in) {
      $('.publish-dropdown .action-publish').removeClass('hidden');
      $('.publish-dropdown .action-signin').addClass('hidden');
    } else {
      $('.publish-dropdown .action-publish').addClass('hidden');
      $('.publish-dropdown .action-signin').removeClass('hidden');      
    }
  });

  $('#publish_btn').click(function(){
    if($('.publish-dropdown').hasClass('hidden')) {
      $('.publish-dropdown').removeClass('hidden');
    } else {
      $('.publish-dropdown').addClass('hidden');
    }
  });

  $('#new_btn').click(function(){
    if(confirm('This will discard your current post. Are you sure?')) {
      reset_page();
    }
  });

  $('#signin-domain').on('keydown', function(e){
    if(e.keyCode == 13) {
      $('#signin-btn').click();
    }
  });
  $('#signin-btn').click(function(){
    window.location = '/auth/start?me=' + encodeURIComponent($('#signin-domain').val()) + '&redirect=/editor';
  });
  $('#publish-confirm').click(function(){
    $('#publish-help').addClass('hidden');
    $('#publish-in-progress').removeClass('hidden');

    $.post('/editor/publish', {
      name: $("#post-name").val(),
      body: editor.serialize().content.value
    }, function(response) {
      if(response.location) {
        reset_page().then(function(){
          $('#publish-success-url').attr('href', response.location);
          $('#publish-in-progress').addClass('hidden');
          $('#publish-error-debug').html('').addClass('hidden');
          $('#publish-error').addClass('hidden');
          $('#publish-success').removeClass('hidden');
        });
      } else {
        $('#publish-in-progress').addClass('hidden');
        $('#publish-error-debug').html(response.response).removeClass('hidden');
        $('#publish-error').removeClass('hidden');
        $('#publish-success').addClass('hidden');
      }
    });    
  });

});

function reset_page() {
  $("#post-name").val('');
  $("#draft-status").text("New");
  return localforage.setItem('currentdraft', {});
}

function onUpdateReady() {
  // Show the notice that says there is a new version of the app
  $("#new_version_available").show();    
}

window.applicationCache.addEventListener('updateready', onUpdateReady);
if(window.applicationCache.status === window.applicationCache.UPDATEREADY) {
  onUpdateReady();
}  

/* ************************************************ */
/* autosave loop */
var autosaveTimeout = false;
function contentChanged() {
  clearTimeout(autosaveTimeout);
  $("#draft-status").text("Draft");
  autosaveTimeout = setTimeout(doAutoSave, 1000);
}
function doAutoSave() {
  autosaveTimeout = false;
  var savedData = {
    title: $("#post-name").val(),
    body: editor.serialize().content.value
  }
  localforage.setItem('currentdraft', savedData).then(function(){
    $("#draft-status").text("Saved");
  });
}
$(function(){
  // Restore draft if present
  localforage.getItem('currentdraft', function(err,val){
    if(val && val.body) {
      $("#post-name").val(val.title);
      $("#content").html(val.body);
      $("#draft-status").text("Restored");
    }
  });
});
/* ************************************************ */


// Not sure why this isn't working
// editor.subscribe('editableInput', function(ev, editable) {
//   console.log("stuff changed");  
// });  

// This one works okay tho, but misses changes from the image uploader
editor.on(document.getElementById('content'), 'input', function(){
  contentChanged();
});
$(function(){
  $('#post-name').on('keyup', contentChanged);
});
