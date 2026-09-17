<div class="narrow">
  <?= partial('partials/header') ?>

  <h2><?= $this->error ?></h2>

  <p><?= $this->errorDescription ?></p>

<?php if(in_array($this->error, ['missing_iss','invalid_iss','invalid_issuer'])): ?>
  <p>Your site publishes an IndieAuth Server Metadata document, so this app expects the authorization
     server to return an <code>iss</code> parameter matching the <code>issuer</code> in that document.
     Either your authorization server did not return it, or it did not match.</p>

  <p>If you switched to a different website part way through signing in, <a href="/">start over</a> and
     this will usually clear up. Otherwise, check that the <code>issuer</code> value in your metadata
     document matches what your authorization server sends back.</p>
<?php endif; ?>

</div>
