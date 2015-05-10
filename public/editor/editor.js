var editor = new MediumEditor('.editable', {
  buttons: ['bold', 'italic', 'anchor', 'header1', 'header2', 'quote', 'unorderedlist', 'pre'],
  paste: {
    // This example includes the default options for paste, if nothing is passed this is what it used
    forcePlainText: false,
    cleanPastedHTML: true,
    cleanReplacements: [],
    cleanAttrs: ['class', 'style', 'dir'],
    cleanTags: ['meta']
  }
});

$(function () {
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
  $('.editable').focus(function(){
    $('.placeholder').removeClass('placeholder');
  });
});

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
