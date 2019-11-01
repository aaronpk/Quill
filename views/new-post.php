  <div class="narrow">
    <?= partial('partials/header') ?>

      <form role="form" style="margin-top: 20px;" id="note_form">

        <?php if(supports_post_type($this->user, 'reply')): ?>

        <div id="reply">
          <div class="reply-section hidden">
            <div class="form-group has-feedback">
              <label for="note_in_reply_to">Reply To (a URL you are replying to)</label>
              <input type="text" id="note_in_reply_to" value="<?= $this->in_reply_to ?>" class="form-control">
              <span class="loading hidden glyphicon glyphicon-refresh glyphicon-spin form-control-feedback"></span>
            </div>
            <div class="reply-context hidden">
              <div class="reply-author">
                <img src="" width="48" class="author-img">
              </div>
              <div class="reply-content">
                <img src="" class="post-img hidden">
                <div class="author"><div class="syndications"></div><span class="name"></span> <span class="url"></span></div>
                <h4 class="post-name hidden"></h4>
                <span class="content"></span>
              </div>
            </div>
          </div>
          <a href="" id="expand-reply" class="btn btn-xs btn-info">Reply</a>
        </div>

        <div class="form-group hidden" id="form_rsvp">
          <label for="note_rsvp">RSVP</label>
          <select id="note_rsvp" class="form-control">
            <option value="yes">Yes</option>
            <option value="no">No</option>
            <option value="maybe">Maybe</option>
            <option value="interested">Interested</option>
            <option value=""></option>
          </select>
        </div>

        <?php endif ?>

        <div class="form-group hidden" id="note-name">
          <label for="note_name">Issue Title</label>
          <input type="text" id="note_name" value="" class="form-control" placeholder="">
        </div>

        <div class="form-group">
          <div id="note_content_remaining" class="pcheck206"><img src="/images/twitter.ico"> <span>280</span></div>
          <label for="note_content">Content</label>
          <textarea id="note_content" value="" class="form-control" style="height: 4em;"></textarea>
        </div>

        <div class="form-group hidden" id="content-type-selection">
          <label for="note_content_type">Content Type</label>
          <select class="form-control" id="note_content_type">
            <option value="text/plain">Text</option>
            <option value="text/markdown">Markdown</option>
          </select>
        </div>

        <div class="form-group" id="form_tags">
          <label for="note_category">Tags</label>
          <input type="text" id="note_category" value="" class="form-control" placeholder="e.g. web, personal">
        </div>

        <div class="form-group" id="form_slug">
          <label for="note_slug">Slug</label>
          <input type="text" id="note_slug" value="" class="form-control">
        </div>

        <?php if(supports_post_type($this->user, 'photo')): ?>

        <div class="form-group hidden" id="photo-previews">
        </div>

        <a href="javascript:addNewPhoto();" id="expand-photo-section"><i class="glyphicon glyphicon-camera" style="color: #aaa; font-size: 36px;"></i></a>

        <?php endif ?>


        <?php if($this->supported_visibility): ?>
          <div class="form-group" style="margin-top: 1em;">
            <label for="visibility">Visibility</label>
            <select class="form-control" id="visibility">
              <?php
                foreach(['Public','Unlisted','Protected','Private'] as $v):
                  if(in_array(strtolower($v), $this->supported_visibility)):
                    echo '<option value="'.strtolower($v).'">'.$v.'</option>';
                  endif;
                endforeach;
              ?>
            </select>
          </div>
        <?php endif ?>

        <?php if($this->syndication_targets): ?>
        <div id="syndication-targets" class="form-group" style="margin-top: 1em;">
          <label for="note_syndicate-to">Syndicate <a href="javascript:reload_syndications()">(refresh list)</a></label>
          <div id="syndication-container">
            <?php
              echo '<ul>';
              foreach($this->syndication_targets as $syn) {
                echo '<li>'
                 . '<button data-syndicate-to="'.(isset($syn['uid']) ? htmlspecialchars($syn['uid']) : htmlspecialchars($syn['target'])).'" class="btn btn-default btn-block">'
                   . ($syn['favicon'] ? '<img src="'.htmlspecialchars($syn['favicon']).'" width="16" height="16"> ' : '')
                   . htmlspecialchars($syn['target'])
                 . '</button>'
               . '</li>';
              }
              echo '</ul>';
            ?>
          </div>
        </div>
        <?php endif ?>

        <div class="form-group">
          <label for="note_location">Location</label>
          <input type="checkbox" id="note_location_chk" value="">
          <img src="/images/spinner.gif" id="note_location_loading" style="display: none;">

          <input type="text" name="note_location_msg" id="note_location_msg" value="" class="form-control" placeholder="" readonly="readonly">
          <input type="hidden" id="note_location">
          <input type="hidden" id="location_enabled" value="<?= $this->location_enabled ?>">

          <div id="note_location_img" style="display: none;">
            <img src="" height="180" id="note_location_img_wide" class="img-responsive">
            <img src="" height="320" id="note_location_img_small" class="img-responsive">
          </div>
        </div>

        <button class="btn btn-success" id="btn_post">Post</button>
      </form>

      <div class="alert alert-success hidden" id="test_success"><strong>Success! </strong><a href="" id="post_href">View your post</a></div>
      <div class="alert alert-danger hidden" id="test_error"><strong>Something went wrong!</strong><br>Your Micropub endpoint indicated that something went wrong creating the post.</div>

      <div id="test_response_container" class="hidden">
        <h4>Micropub Response</h4>
        <p>Below is the response from your Micropub endpoint. This may contain helpful information that can be used to troubleshoot the issue.</p>
        <pre id="test_response" style="width: 100%; min-height: 240px;"></pre>
      </div>

      <hr>
      <div style="text-align: right;">
        <a href="/add-to-home?start">Add to Home Screen</a>
      </div>
  </div>

<!-- Add Photo -->
<div class="modal fade" id="photo-modal" tabindex="-1" role="dialog" aria-labelledby="photo-modal-title" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="photo-modal-title">Add Photo</h4>
      </div>
      <div class="modal-body">

        <div id="modal_photo_preview" class="hidden">
          <img>
        </div>

        <label id="note_photo_button" class="btn btn-default btn-file" style="margin-bottom: 1em;">
          Choose File <input type="file" name="note_photo" id="note_photo" accept="image/*">
        </label>

        <div style="margin-bottom: 1em;">
          <input type="url" id="note_photo_url" class="form-control" placeholder="Paste image URL">
        </div>

        <?php if($this->media_endpoint): ?>
          <input type="text" id="note_photo_alt" class="form-control" placeholder="Image alt text">
        <?php endif ?>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary save-btn">Add</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Photo -->
<div class="modal fade" id="edit-photo-modal" tabindex="-1" role="dialog" aria-labelledby="edit-photo-modal-title" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="edit-photo-modal-title">Edit Photo</h4>
      </div>
      <div class="modal-body">

        <div id="modal_edit_photo_preview" style="margin-bottom: 4px;">
          <img>
        </div>

        <input type="text" id="note_edit_photo_alt" class="form-control" placeholder="Image alt text">
        <input type="hidden" id="modal_edit_photo_index">

      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> -->
        <button type="button" class="btn btn-danger remove-btn" data-dismiss="modal">Remove</button>
        <button type="button" class="btn btn-primary save-btn">Save</button>
      </div>
    </div>
  </div>
</div>

<style type="text/css">

#reply {
  margin-bottom: 1em;
}

#note_content_remaining {
  float: right;
  font-size: 0.8em;
  font-weight: bold;
}

#modal_photo_preview {
  text-align: center;
}
#modal_photo_preview img {
  max-height: 40vh;
}
#modal_photo_preview img, #modal_edit_photo_preview img {
  max-width: 100%;
  border-radius: 4px;
  margin-bottom: 4px;
}

#photo-previews span {
  width: 24%;
  height: 180px;
  margin-right: 1px;

  position: relative;
  overflow: hidden;
  display: inline-block;
}
#photo-previews img {
  position: absolute;
  left: 50%;
  top: 50%;
  height: 100%;
  width: auto;
  -webkit-transform: translate(-50%,-50%);
      -ms-transform: translate(-50%,-50%);
          transform: translate(-50%,-50%);
  cursor: pointer;
}


.pcheck206 { color: #6ba15c; } /* tweet fits within the limit even after adding RT @username */
.pcheck207 { color: #c4b404; } /* danger zone, tweet will overflow when RT @username is added */
.pcheck200,.pcheck208 { color: #59cb3a; } /* exactly fits 280 chars, both with or without RT */
.pcheck413 { color: #a73b3b; } /* over max tweet length */

.reply-context {
  display: flex;
  flex-direction: row;
  padding: 4px;
  margin: 0 3em;
  border: 1px #ccc solid;
  border-radius: 4px;
  background: #f4f4f4;
  max-height: 140px;
  overflow-y: hidden;
}
.reply-context .reply-content {
  flex: 1 0;
}
.reply-context img.author-img {
  border-radius: 4px;
  width: 48px;
  margin-right: 4px;
}
.reply-context .syndications {
  float: right;
  padding-right: 4px;
}
.reply-context .syndications img {
  width: 16px;
}
.reply-context .author {
  color: #777;
  font-weight: bold;
  font-size: 0.9em;
}
.reply-context .author .url {
  color: #aaa;
}
.reply-context img.post-img {
  float: right;
  width: 200px;
}

</style>

<script>
function saveNoteState() {
  var state = {
    content: $("#note_content").val(),
    inReplyTo: $("#note_in_reply_to").val(),
    category: $("#note_category").val(),
    slug: $("#note_slug").val(),
    photos: photos
  };
  state.syndications = [];
  $("#syndication-container button.btn-info").each(function(i,btn){
    state.syndications[$(btn).data('syndicate-to')] = 'selected';
  });
  // console.log("saving",state);
  localforage.setItem('current-note', state);
}

function restoreNoteState() {
  localforage.getItem('current-note', function(err,note){
    if(note) {
      $("#note_content").val(note.content);
      $("#note_in_reply_to").val(note.inReplyTo);
      $("#note_category").val(note.category);
      $("#note_slug").val(note.slug);
      if(note.photos) {
        photos = note.photos;
        refreshPhotoPreviews();
      }
      if(note.inReplyTo) {
        expandReplySection();
      }
      $("#syndication-container button").each(function(i,btn){
        if($(btn).data('syndicate-to') in note.syndications) {
          $(btn).addClass('btn-info');
        }
      });
      $("#note_content").change();
      if($("#note_content").val().match(/`/)) {
        switchToMarkdown();
      }
      activateTokenField();
    } else {
      activateTokenField();
    }
  });
}

function expandReplySection() {
  $("#expand-reply").click();
  $("#note_in_reply_to").change();
}

function activateTokenField() {
  $("#note_category").tokenfield({
    createTokensOnBlur: true,
    beautify: true,
  });
}


var hasMediaEndpoint = <?= $this->media_endpoint ? 'true' : 'false' ?>;
var photos = [];

function addNewPhoto() {
  // Reset modal
  $("#note_photo").val("");
  $("#note_photo_url").val("");
  $("#note_photo_alt").val("");
  $("#modal_photo_preview").addClass("hidden");
  $("#note_photo_button").removeClass("hidden");
  $("#note_photo_url").removeClass("hidden");

  // Show the modal
  $("#photo-modal").modal();
}

$(function(){
  // Check if there's a pending file at the media endpoint
  $.getJSON("/new/last-photo.json", function(response){
    if(response.url) {
      photos.push({
        url: response.url,
        alt: null,
        external: true
      });
      refreshPhotoPreviews();
    }
  });

  $("#note_photo").on("change", function(e){

    // If the user has a media endpoint, upload the photo to it right now
    if(hasMediaEndpoint) {
      var formData = new FormData();
      formData.append("null","null");
      formData.append("photo", e.target.files[0]);
      var request = new XMLHttpRequest();
      request.open("POST", "/micropub/media");
      request.onreadystatechange = function() {
        if(request.readyState == XMLHttpRequest.DONE) {
          try {
            var response = JSON.parse(request.responseText);
            if(response.location) {
              $("#modal_photo_preview img").attr("src", response.location);
              $("#note_photo_url").removeClass("hidden").val(response.location);
              $("#note_photo_button").addClass("hidden");
            } else {
              console.log("Endpoint did not return a location header", response);
            }
          } catch(e) {
            console.log(e);
          }
        }
      }
      request.send(formData);
    } else {
      $("#modal_photo_preview img").attr("src", URL.createObjectURL(e.target.files[0]));
    }

    $("#modal_photo_preview").removeClass("hidden");
    $("#note_photo_button").addClass("hidden");
    $("#note_photo_url").addClass("hidden");
  });

  $("#note_photo_url").on("change", function(){
    $("#modal_photo_preview img").attr("src", $(this).val());
    $("#modal_photo_preview").removeClass("hidden");
    $("#note_photo_button").addClass("hidden");
  });

  $("#photo-modal .save-btn").click(function(){
    if($("#note_photo_url").val()) {
      photos.push({
        url: $("#note_photo_url").val(),
        alt: $("#note_photo_alt").val(),
        external: true
      });
    } else {
      photos.push({
        url: URL.createObjectURL(document.getElementById("note_photo").files[0]),
        alt: $("#note_photo_alt").val(),
        file: document.getElementById("note_photo").files[0],
        external: false
      });
    }
    $("#photo-modal").modal('hide');
    refreshPhotoPreviews();
    saveNoteState();
  });

  $("#edit-photo-modal .save-btn").click(function(){
    var index = $("#modal_edit_photo_index").val();
    photos[index].alt = $("#note_edit_photo_alt").val();
    refreshPhotoPreviews();
    saveNoteState();
    $("#edit-photo-modal").modal('hide');
  });

  $("#edit-photo-modal .remove-btn").click(function(){
    var new_photos = [];
    for(i=0; i<photos.length; i++) {
      if(i != $("#modal_edit_photo_index").val()) {
        new_photos.push(photos[i]);
      }
    }
    photos = new_photos;
    refreshPhotoPreviews();
    saveNoteState();
  });

  $(document).bind('keydown', function(e){
    // Easter egg: press ctrl+shift+c to reveal a content type selection
    if(e.keyCode == 67 && e.ctrlKey && e.shiftKey) {
      $("#content-type-selection").removeClass("hidden");
    }
    // Easter egg: press ctrl+shift+m to switch to markdown
    if(e.keyCode == 77 && e.ctrlKey && e.shiftKey) {
      switchToMarkdown();
    }
  });

});

function switchToMarkdown() {
  $("#content-type-selection select").val("text/markdown");
  $("#content-type-selection").removeClass("hidden");
}

function refreshPhotoPreviews() {
  $("#photo-previews").html("");
  for(i=0; i<photos.length; i++) {
    var img = document.createElement('img');
    img.setAttribute('src', photos[i].url);
    img.setAttribute('alt', photos[i].alt);
    img.setAttribute('title', photos[i].alt);
    var span = document.createElement('span');
    span.appendChild(img);
    $("#photo-previews").append(span);
  }
  if(photos.length == 0) {
    $("#photo-previews").addClass("hidden");
  } else {
    $("#photo-previews").removeClass("hidden");
  }
  $("#photo-previews img").unbind("click").bind("click", function(){
    $("#modal_edit_photo_preview img").attr("src", $(this).attr("src"));
    var index = false;
    for(i=0; i<photos.length; i++) {
      if(photos[i].url == $(this).attr("src")) {
        index = i;
      }
    }
    $("#note_edit_photo_alt").val(photos[index].alt);
    $("#modal_edit_photo_index").val(index);
    $("#edit-photo-modal").modal();
  });
}

/*
  $("#note_photo").on("change", function(e){
    // If the user has a media endpoint, upload the photo to it right now
    if(hasMediaEndpoint) {
      // TODO: add loading state indicator here
      console.log("Uploading file to media endpoint...");
      var formData = new FormData();
      formData.append("null","null");
      formData.append("photo", e.target.files[0]);
      var request = new XMLHttpRequest();
      request.open("POST", "/micropub/media");
      request.onreadystatechange = function() {
        if(request.readyState == XMLHttpRequest.DONE) {
          try {
            var response = JSON.parse(request.responseText);
            if(response.location) {
              // Replace the file upload form with the URL
              replacePhotoWithPhotoURL(response.location);
              saveNoteState();
            } else {
              console.log("Endpoint did not return a location header", response);
            }
          } catch(e) {
            console.log(e);
          }
        }
      }
      request.send(formData);
    } else {
      $("#photo_preview").attr("src", URL.createObjectURL(e.target.files[0]) );
      $("#photo_preview_container").removeClass("hidden");
    }
  });
*/


$(function(){

  var userHasSetCategory = false;

  $("#note_content, #note_category, #note_in_reply_to, #note_slug").on('keyup change', function(e){
    saveNoteState();
  });

  $("#note_content").on('keyup', function(e){
    var scrollHeight = document.getElementById("note_content").scrollHeight;
    var currentHeight = parseInt($("#note_content").css("height"));
    if(Math.abs(scrollHeight - currentHeight) > 20) {
      $("#note_content").css("height", (scrollHeight+30)+"px");
    }
    // If you type a backtick in the content, and are replying to a github issue, switch to markdown
    if(e.key == '`') {
      if($("#note_in_reply_to").val().match(/github\.com/)) {
        switchToMarkdown();
      }
    }
  });

  $("#visibility").on('change', function(e){
    if($(this).val() == 'private') {
      $("#syndication-targets").addClass('hidden');
    } else {
      $("#syndication-targets").removeClass('hidden');
    }
  });

  $("#expand-reply").click(function(){
    $('.reply-section').removeClass('hidden');
    $(this).addClass('hidden');
    return false;
  });

  // Preview the photo when one is chosen
  $("#photo_preview_container").addClass("hidden");

  $("#note_content").on('change keyup', function(e){
    var text = $("#note_content").val();
    var tweet_length = tw_text_proxy(text).length;
    var tweet_check = tw_length_check(text, 280, "<?= $this->user->twitter_username ?>");
    var remaining = 280 - tweet_length;
    $("#note_content_remaining span").html(remaining);
    $("#note_content_remaining").removeClass("pcheck200 pcheck206 pcheck207 pcheck208 pcheck413");
    $("#note_content_remaining").addClass("pcheck"+tweet_check);

    // If the user didn't enter any categories, add them from the post
    // if(!userHasSetCategory) {
    //   var tags = $("#note_content").val().match(/#[a-z][a-z0-9]+/ig);
    //   if(tags) {
    //     $("#note_category").val(tags.map(function(tag){ return tag.replace('#',''); }).join(", "));
    //   }
    // }
  });

  $("#note_in_reply_to").on('change', function(){
    var reply_to = $("#note_in_reply_to").val();

    if(reply_to == "") {
      $(".reply-section").addClass("hidden");
      $(".reply-context").addClass("hidden");
      $("#note-name").addClass("hidden");
      $("#note_content").siblings("label").text("Content");
      $("#expand-reply").removeClass("hidden");
      return;
    }

    if($("#note_in_reply_to").val().match(/^https:\/\/github\.com\/([^\/]+)\/([^\/]+)$/)) {
      // Add the "name" field for issues
      $("#note-name").removeClass("hidden");
      $("#note_content").siblings("label").text("Description");
    }

    $(".reply-section .loading").removeClass("hidden");
    $.get("/reply/preview", {url:reply_to}, function(data){

      if(data.canonical_reply_url != reply_to) {
        $("#note_in_reply_to").val(data.canonical_reply_url);
      }
      // var category = csv_to_array($("#note_category").val());
      // for(var i in data.entry.category) {
      //   if($.inArray(data.entry.category[i], category) == -1) {
      //     category.push(data.entry.category[i]);
      //   }
      // }
      // $("#note_category").val(category.join(", "));

      /*
      // stop auto-populating usernames in replies, since Twitter no longer requires it
      if($("#note_content").val() == "" && data.mentions) {
        var mentions = '';
        for(var i in data.mentions) {
          mentions += '@'+data.mentions[i]+' ';
        }
        $("#note_content").val(mentions);
      }
      */

      if(data.entry) {
        $(".reply-context .content").text(data.entry.content.text);
        if(data.entry.name) {
          $(".reply-context .post-name").text(data.entry.name).removeClass('hidden');
        } else {
          $(".reply-context .post-name").addClass('hidden');
        }
        if(data.entry.author) {
          $(".reply-context .author .name").text(data.entry.author.name);
          $(".reply-context .author .url").text(data.entry.author.url);
          $(".reply-context img.author-img").attr('src', data.entry.author.photo);
          $(".reply-context .reply-author").removeClass("hidden");
        } else {
          $(".reply-context .reply-author").addClass("hidden");
        }
        if(data.entry.photo) {
          $(".reply-context img.post-img").attr('src', data.entry.photo[0]).removeClass('hidden');
        } else {
          $(".reply-context img.post-img").addClass('hidden');
        }
        if(data.entry.type == "event") {
          $("#form_rsvp").removeClass("hidden");
        } else {
          $("#form_rsvp").addClass("hidden");
        }
        if(data.syndications) {
          $(".reply-context .syndications").html('');
          for(var i in data.syndications) {
            var syn = data.syndications[i];
            $(".reply-context .syndications").append('<a href="'+syn.url+'"><img src="/images/services/'+syn.icon+'"></a>');
          }
        }

        $(".reply-context").removeClass("hidden");
      }

      $(".reply-section .loading").addClass("hidden");

    });
  });

  $("#note_category").on('keydown keyup', function(){
    userHasSetCategory = true;
  });
  $("#note_category").on('change', function(){
    if($("#note_category").val() == "") {
      userHasSetCategory = false;
    }
  });

  // When the reply URL is in the query string, or loads from localstorage, make sure
  // to run the event handlers to expand the reply section
  if($("#note_in_reply_to").val() != "") {
    expandReplySection();
  }

  $("#btn_post").click(function(){

    // Collect all the syndication buttons that are pressed
    var syndications = [];
    $("#syndication-container button.btn-info").each(function(i,btn){
      syndications.push($(btn).data('syndicate-to'));
    });

    var category = tokenfieldToArray("#note_category");

    var formData = new FormData();
    var entry = {};
    var doMultipart = false;
    var hasAltText = false;

    if(v=$("#note_name").val()) {
      formData.append("name", v);
      entry['name'] = [v];
    }
    if(v=$("#note_content").val()) {
      formData.append("content", v);
      entry['content'] = [v];
    }
    if(v=$("#note_in_reply_to").val()) {
      formData.append("in-reply-to", v);
      entry['in-reply-to'] = [v];
    }
    if(v=$("#note_location").val()) {
      formData.append("location", v);
      entry['location'] = [v];
    }
    if(category.length > 0) {
      for(var i in category) {
        formData.append("category[]", category[i]);
      }
      entry['category'] = category;
    }
    if(syndications.length > 0) {
      for(var i in syndications) {
        formData.append("<?= $this->user->micropub_syndicate_field ?>[]", syndications[i]);
      }
      entry["<?= $this->user->micropub_syndicate_field ?>"] = syndications;
    }
    if(v=$("#note_slug").val()) {
      formData.append("<?= $this->user->micropub_slug_field ?>", v);
      entry["<?= $this->user->micropub_slug_field ?>"] = v;
    }
    if(!$("#form_rsvp").hasClass("hidden") && $("#note_rsvp").val()) {
      formData.append("rsvp", $("#note_rsvp").val());
      entry["rsvp"] = $("#note_rsvp").val();
    }

    if($("#visibility").val()) {
      formData.append("visibility", $("#visibility").val());
      entry["visibility"] = $("#visibility").val();
    }

    function appendPhotoToFormData(photo, prop) {
      if(photo.external) {
        if(photo.alt) {
          hasAltText = true;
          formData.append(prop+"[value]", photo.url);
          formData.append(prop+"[alt]", photo.alt);
          entry['photo'].push({
            value: photo.url,
            alt: photo.alt
          })
        } else {
          formData.append(prop, photo.url);
          entry['photo'].push(photo.url);
        }
      } else {
        formData.append(prop, photo.file);
        doMultipart = true;
      }
    }

    if(photos.length == 1) {
      entry['photo'] = [];
      appendPhotoToFormData(photos[0], "photo");
    } else if(photos.length > 1) {
      entry['photo'] = [];
      for(i=0; i<photos.length; i++) {
        appendPhotoToFormData(photos[i], "photo[]");
      }
    }

    if(!$("#content-type-selection").hasClass("hidden")) {
      entry['p3k-content-type'] = $("#note_content_type").val();
      formData.append('p3k-content-type', $("#note_content_type").val());
    }

    // Need to append a placeholder field because if the file size max is hit, $_POST will
    // be empty, so the server needs to be able to recognize a post with only a file vs a failed one.
    // This will be stripped by Quill before it's sent to the Micropub endpoint
    formData.append("null","null");

    $("#btn_post").addClass("loading disabled").text("Working...");
    if(doMultipart || !hasAltText) {
      var request = new XMLHttpRequest();
      request.open("POST", "/micropub/multipart");
      request.onreadystatechange = function() {
        if(request.readyState == XMLHttpRequest.DONE) {
          handle_post_response(request.responseText);
        }
      }
      request.send(formData);
    } else {
      // Convert all single-value properties to arrays
      for(var k in entry) {
        if(typeof entry[k] == "string") {
          entry[k] = [entry[k]];
        }
      }
      $.post("/micropub/postjson", {
        data: JSON.stringify({
          "type": ["h-entry"],
          "properties": entry
        })
      }, function(response) {
        handle_post_response(response);
      });
    }

    return false;
  });

  function handle_post_response(response) {
    try {
      if(typeof response == "string") {
        response = JSON.parse(response);
      }
      localforage.removeItem('current-note', function(){
        if(response.location) {
          window.location = response.location;
          // console.log(response.location);
        } else {
          $("#test_response").html(response.response);
          $("#test_response_container").removeClass('hidden');
          $("#test_success").addClass('hidden');
          $("#test_error").removeClass('hidden');
        }
      });
    } catch(e) {
      $("#test_success").addClass('hidden');
      $("#test_error").removeClass('hidden');
    }
    $("#btn_post").removeClass("loading disabled").text("Post");
  }

  function location_error(msg) {
    $("#note_location_msg").val(msg);
    $("#note_location_chk").removeAttr("checked");
    $("#note_location_loading").hide();
    $("#note_location_img").hide();
    $("#note_location_msg").removeClass("img-visible");
  }

  var map_template_wide = "<?= static_map('{lat}', '{lng}', 180, 700, 15) ?>";
  var map_template_small = "<?= static_map('{lat}', '{lng}', 320, 480, 15) ?>";

  function fetch_location() {
    $("#note_location_loading").show();

    navigator.geolocation.getCurrentPosition(function(position){

      $("#note_location_loading").hide();
      var geo = "geo:" + (Math.round(position.coords.latitude * 100000) / 100000) + "," + (Math.round(position.coords.longitude * 100000) / 100000) + ";u=" + position.coords.accuracy;
      $("#note_location_msg").val(geo);
      $("#note_location").val(geo);
      $("#note_location_img_small").attr("src", map_template_small.replace('{lat}', position.coords.latitude).replace('{lng}', position.coords.longitude));
      $("#note_location_img_wide").attr("src", map_template_wide.replace('{lat}', position.coords.latitude).replace('{lng}', position.coords.longitude));
      $("#note_location_img").show();
      $("#note_location_msg").addClass("img-visible");

      $.post("/prefs/timezone", {
        latitude: position.coords.latitude,
        longitude: position.coords.longitude
      });

    }, function(err){
      if(err.code == 1) {
        location_error("The website was not able to get permission");
      } else if(err.code == 2) {
        location_error("Location information was unavailable");
      } else if(err.code == 3) {
        location_error("Timed out getting location");
      }
    });
  }

  $("#note_location_chk").click(function(){
    if($(this).attr("checked") == "checked") {
      if(navigator.geolocation) {
        $.post("/prefs", {
          enabled: 1
        });
        fetch_location();
      } else {
        location_error("Browser location is not supported");
      }
    } else {
      $("#note_location_img").hide();
      $("#note_location_msg").removeClass("img-visible");
      $("#note_location_msg").val('');
      $("#note_location").val('');

      $.post("/prefs", {
        enabled: 0
      });
    }
  });

  if($("#location_enabled").val() == 1) {
    $("#note_location_chk").attr("checked","checked");
    fetch_location();
  }

  bind_syndication_buttons();

  if($("#note_in_reply_to").val() != "") {
    $("#note_in_reply_to").change();
  } else {
    restoreNoteState();
  }

});

<?= partial('partials/syndication-js') ?>

</script>
