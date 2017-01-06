<div class="narrow">
  <?= partial('partials/header') ?>

  <h2>Post Status</h2>

  <p>The "Post Status" dropdown in the Quill editor is just an indication to your Micropub endpoint to mark the post as "published" or "draft". If your Micropub endpoint does not support this property, then your post will be published immediately.</p>

  <p>Setting the dropdown to "draft" will include a new property in the Micropub request, called <code>post-status</code> with the value set to <code>draft</code>. You can read more about this extension <a href="https://indieweb.org/Micropub-extensions#Post_Status">on the IndieWeb wiki</a>.</p>

</div>