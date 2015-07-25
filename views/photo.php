  <div class="narrow">
    <?= partial('partials/header') ?>

      <form method="POST" role="form" style="margin-top: 20px;" id="note_form" enctype="multipart/form-data">

        <div class="form-group">
          <label for="note_photo"><code>photo</code></label>
          <input type="file" name="note_photo" id="note_photo" accept="image/jpg,image/jpeg,image/gif,image/png">
          <p class="help-block">Photo JPEG, GIF or PNG.</p>
        </div> 

        <div class="form-group">
          <label for="note_content"><code>content</code> (optional)</label>
          <textarea name="note_content" id="note_content" value="" class="form-control" style="height: 4em;"><? if(isset($this->note_content)) echo $this->note_content ?></textarea>
        </div>

        <button class="btn btn-success" id="btn_post">Post</button>
      </form>

      <? if(!empty($this->location)): ?>
        <div class="alert alert-success">
          <strong>Success!</strong> Photo posted to: <em><a href="<?= $this->location ?>"><?= $this->location ?></a></em>
        </div>
      <? endif ?>

      <? if(!empty($this->error)): ?>
        <div class="alert alert-danger">
          <strong>Error:</strong> <em><?= $this->error ?></em>
        </div>
      <? endif ?>

      <? if(!empty($this->response)): ?>
        <h4>Response:</h4>
        <pre><?= $this->response ?></pre>
      <? endif ?>


  </div>