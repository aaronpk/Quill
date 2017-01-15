var editor = new MediumEditor('.editable', {
  toolbar: {
    buttons: ['bold', 'italic', 'anchor', 'h2', 'h3', 'quote', 'pre', 'unorderedlist']
  },
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
      $('#publish-confirm').show();
      $('#publish-success').addClass('hidden');
      $('#publish-error').addClass('hidden');
      $('#publish-help').removeClass('hidden');
      $('#publish-fields').removeClass('hidden');
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
    $('#publish-fields').addClass('hidden');

    var category = csv_to_array($("#post-tags").tokenfield('getTokensList'));

    $.post('/editor/publish', {
      name: $("#post-name").val(),
      body: editor.serialize().content.value,
      category: category,
      slug: $("#post-slug").val(),
      status: $("#post-status").val(),
      publish: $("#post-publish-date").val()
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
        $('#publish-fields').removeClass('hidden');
      }
    });    
  });

  $("#micropub-html-btn").click(function(){
    $.post('/settings/html-content', {
      html: 1
    }, function(data){
      $('.micropub-html-warning').hide();
    });
  });

  $("#post-status").change(function(){
    $("#published-status-warning").removeClass("hidden");
  });

  $("#post-publish-date").change(function(){
    if($("#post-publish-date").val() == "") {
      $("#post-publish-date").val("now");
    } else {
      // Parse and verify the publish date when it's changed
      $.post('/editor/parse-date', {
        date: $("#post-publish-date").val(),
        tzoffset: (new Date().getTimezoneOffset())
      }, function(response) {
        if(response.date) {
          $("#post-publish-date").val(response.date);
        } else {
          $("#post-publish-date").val("now");
        }
      });
    }
  });

  $.getJSON('/settings/html-content', function(data){
    if(data.html == '0') {
      $('.micropub-html-warning').show();
    }
  });
});

function reset_page() {
  $("#post-name").val('');
  $("#post-slug").val('');
  $("#post-tags").tokenfield('setTokens',[]);
  $("#post-status").val('published');
  $("#post-publish-date").val('now');
  $("#content").html('');
  $("#draft-status").text("New");
  $("#publish-confirm").hide();
  return localforage.setItem('currentdraft', {});
}

function csv_to_array(val) {
  if(val.length > 0) {
    return val.split(/[, ]+/);
  } else {
    return [];
  }
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
    body: editor.serialize().content.value,
    tags: $("#post-tags").tokenfield('getTokensList'),
    slug: $("#post-slug").val(),
    status: $("#post-status").val(),
    publish: $("#post-publish-date").val()
  }
  localforage.setItem('currentdraft', savedData).then(function(){
    $("#draft-status").text("Saved");
  });
}
function activateTokenField() {
  $("#post-tags").tokenfield({
    createTokensOnBlur: true,
    beautify: true
  }).on('tokenfield:createdtoken', contentChanged)
    .on('tokenfield:removedtoken', contentChanged);
}
$(function(){
  // Restore draft if present
  localforage.getItem('currentdraft', function(err,val){
    if(val && val.body) {
      $("#post-name").val(val.title);
      $("#content").html(val.body);
      $("#draft-status").text("Restored");
      $("#post-tags").val(val.tags);
      $("#post-slug").val(val.slug);
      $("#post-status").val(val.status);
      $("#post-publish-date").val(val.publish);
      // drop the cursor into the editor which clears the placeholder text
      $("#content").focus().click();
      activateTokenField();
    } else {
      activateTokenField();
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
  $('#post-name, #post-tags, #post-slug, #post-publish-date').on('keyup', contentChanged);
});
