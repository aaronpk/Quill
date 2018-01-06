  <div class="narrow">
    <?= partial('partials/header') ?>

      <div style="clear: both;" class="notice-pad">
        <div class="alert alert-success hidden" id="test_success"><strong>Success!</strong><br>Your post should be on your website now!<br><a href="" id="post_href">View your post</a></div>
        <div class="alert alert-danger hidden" id="test_error"><strong>Your endpoint did not return a Location header.</strong><br>See <a href="/creating-a-micropub-endpoint">Creating a Micropub Endpoint</a> for more information.</div>
      </div>

      <form role="form" style="margin-top: 20px;" id="note_form">


        <div class="form-group" id="note-name">
          <label for="note_name">File Name (optional)</label>
          <input type="text" id="note_name" value="<?= htmlspecialchars($this->edit_data['name']) ?>" class="form-control" placeholder="">
        </div>

        <div class="form-group">
          <label for="note_content">Code</label>
          <textarea id="note_content" value="" class="form-control code-snippet" style="height: 12em;"><?= htmlspecialchars($this->edit_data['content']) ?></textarea>
        </div>

        <? if(!$this->url): ?>
        <label for="note_language">Language</label>
        <select class="form-control" id="note_language">
          <?php
            foreach($this->languages as $lang=>$exts):
              ?>
              <option value="<?= $lang ?>"<?= $lang == 'text' ? ' selected="selected"' : '' ?>><?= $lang ?></option>
              <?php
            endforeach;
          ?>
        </select>
        <? endif; ?>

        <div style="float: right; margin-top: 6px;">
          <button class="btn btn-success" id="btn_post"><?= $this->url ? 'Save' : 'Post' ?></button>
        </div>

        <input type="hidden" id="edit_url" value="<?= $this->url ?>">
      </form>

      <div style="clear: both;"></div>

      <hr>
      <div style="text-align: right;" id="bookmarklet">
        Bookmarklet: <a href="javascript:<?= js_bookmarklet('partials/code-bookmarklet', $this) ?>" class="btn btn-default btn-xs">code</a>
      </div>

  </div>
<script>
$(function(){

  var language_map = <?= json_encode($this->language_map) ?>;

  $("#note_name").on("keyup", function(){
    var name = $("#note_name").val();
    if(name && (m=name.match(/\.([a-z]+)$/))) {
      if(language_map[m[1]]) {
        $("#note_language").val(language_map[m[1]]);
      }
    }
  });

  $("#note_content").on('keyup', function(){
    var scrollHeight = document.getElementById("note_content").scrollHeight;
    var currentHeight = parseInt($("#note_content").css("height"));
    if(Math.abs(scrollHeight - currentHeight) > 20) {
      $("#note_content").css("height", (scrollHeight+30)+"px");
    }
  });

  $("#btn_post").click(function(){
    $("#btn_post").addClass("loading disabled");

    var syndications = [];
    $("#syndication-container button.btn-info").each(function(i,btn){
      syndications.push($(btn).data('syndication'));
    });

    var params = {
      content: $("#note_content").val(),
    };
    if($("#edit_url").val() != "") {
      params['edit'] = $("#edit_url").val();
    } else {
      params['language'] = $("#note_language").val();
    }
    if($("#note_name").val() != "") {
      params['name'] = $("#note_name").val();
    }

    $.post("/code", params, function(response){
      if(response.location != false) {

        $("#test_success").removeClass('hidden');
        $("#test_error").addClass('hidden');
        $("#post_href").attr("href", response.location);
        $("#note_form").addClass('hidden');

        window.location = response.location;
      } else {
        $("#test_success").addClass('hidden');
        $("#test_error").removeClass('hidden');
        if(response.error_details) {
          $("#test_error").text(response.error_details);
        }
        $("#btn_post").removeClass("loading disabled");
      }

    });
    return false;
  });

});

</script>
