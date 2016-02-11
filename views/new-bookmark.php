  <div class="narrow">
    <?= partial('partials/header') ?>

      <div style="float: right; margin-top: 6px;">
        <button class="btn btn-success" id="btn_post">Save Bookmark</button>
      </div>

      <div style="clear: both;">
        <div class="alert alert-success hidden" id="test_success"><strong>Success! We found a Location header in the response!</strong><br>Your post should be on your website now!<br><a href="" id="post_href">View your post</a></div>
        <div class="alert alert-danger hidden" id="test_error"><strong>Your endpoint did not return a Location header.</strong><br>See <a href="/creating-a-micropub-endpoint">Creating a Micropub Endpoint</a> for more information.</div>
      </div>

      <form role="form" style="margin-top: 20px;" id="note_form">

        <div class="form-group">
          <label for="note_bookmark"><code>bookmark-of</code></label>
          <input type="text" id="note_bookmark" value="<?= $this->bookmark_url ?>" class="form-control">
        </div>

        <div class="form-group">
          <label for="note_name"><code>name</code></label>
          <input type="text" id="note_name" value="<?= $this->bookmark_name ?>" class="form-control">
        </div>

        <div class="form-group">
          <label for="note_content"><code>content</code> (optional)</label>
          <textarea id="note_content" value="" class="form-control" style="height: 5em;"><?= $this->bookmark_content ?></textarea>
        </div>

        <div class="form-group">
          <label for="note_category"><code>category</code> (optional, comma-separated list of tags)</label>
          <input type="text" id="note_category" value="<?= $this->bookmark_tags ?>" class="form-control" placeholder="e.g. web, personal">
        </div>

        <div class="form-group">
          <label for="note_syndicate-to"><code>syndicate-to</code> <a href="javascript:reload_syndications()">(refresh)</a></label>
          <div id="syndication-container">
            <?php
            if($this->syndication_targets) {
              echo '<ul>';
              foreach($this->syndication_targets as $syn) {
                echo '<li><button data-syndication="'.$syn['target'].'" class="btn btn-default btn-block"><img src="'.$syn['favicon'].'" width="16" height="16"> '.$syn['target'].'</button></li>';
              }
              echo '</ul>';
            } else {
              ?><div class="bs-callout bs-callout-warning">No syndication targets were found on your site.
              You can provide a <a href="/docs#syndication">list of supported syndication targets</a> that will appear as checkboxes here.</div><?php
            }
            ?>
          </div>
        </div>
      </form>


      <hr>
      <div style="text-align: right;">
        Bookmarklet: <a href="javascript:<?= js_bookmarklet('partials/bookmark-bookmarklet', $this) ?>" class="btn btn-default btn-xs">bookmark</a>
      </div>

  </div>

<script>
$(function(){

  // ctrl-s to save
  $(window).on('keydown', function(e){
    if(e.keyCode == 83 && e.ctrlKey){
      $("#btn_post").click();
    }
  });

  $("#btn_post").click(function(){

    var syndications = [];
    $("#syndication-container button.btn-info").each(function(i,btn){
      syndications.push($(btn).data('syndication'));
    });

    $.post("/micropub/post", {
      'bookmark-of': $("#note_bookmark").val(),
      name: $("#note_name").val(),
      content: $("#note_content").val(),
      category: $("#note_category").val(),
      'syndicate-to': syndications
    }, function(data){
      var response = JSON.parse(data);

      if(response.location != false) {

        $("#test_success").removeClass('hidden');
        $("#test_error").addClass('hidden');
        $("#post_href").attr("href", response.location);

        // $("#note_bookmark").val("");
        // $("#note_content").val("");
        // $("#note_category").val("");

        window.location = response.location;
      } else {
        $("#test_success").addClass('hidden');
        $("#test_error").removeClass('hidden');
      }

    });
    return false;
  });

  bind_syndication_buttons();
});

<?= partial('partials/syndication-js') ?>

</script>
