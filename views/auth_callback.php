<div class="narrow">
  <?= partial('partials/header') ?>

<?php if($this->tokenEndpoint): ?>

  <?php if(!$this->auth): ?>

    <h3>Bad response from token endpoint</h3>
    <p>Your token endpoint returned a response that was not understood.</p>

  <?php else: ?>

    <?php if(k($this->auth, 'error')): ?>

      <h3>Error</h3>
      <p>Got an error response from the token endpoint:</p>
      <div class="bs-callout bs-callout-danger">
        <h4><?= $this->auth['error'] ?></h4>
        <?= k($this->auth, 'error_description') ? ('<p>'.$this->auth['error_description'].'</p>') : '' ?>
      </div>

    <?php else: ?>

      <!-- Check for all the required parts of the token -->
      <?php if(k($this->auth, array('me','access_token','scope'))): ?>

        <h3>Success!</h3>

        <p>All required values were found! You are now signed in.</p>
        <p><a href="/new" class="btn btn-primary">Continue</a></p>

      <?php else: ?>

        <?php if(!k($this->auth, 'access_token')): ?>
          <h4>Missing <code>access_token</code></h4>
          <p>The token endpoint did not return an access token. The <code>access_token</code> parameter is the token the client will use to make requests to the Micropub endpoint.</p>
        <?php endif; ?>

        <?php if(!k($this->auth, 'me')): ?>
          <h4>Missing <code>me</code></h4>
          <p>The token endpoint did not return a "me" parameter. The <code>me</code> parameter lets this client know what user the token is for.</p>
        <?php endif; ?>

        <?php if(!k($this->auth, 'scope')): ?>
          <h4>Missing <code>scope</code></h4>
          <p>The token endpoint did not return a "scope" parameter. The <code>scope</code> parameter lets this client what permission the token represents.</p>
        <?php endif; ?>

      <?php endif; ?>

    <?php endif; ?>
  <?php endif; ?>


  <h3>Token endpoint response</h3>

  <p>Below is the raw response from your token endpoint (<?= $this->tokenEndpoint ?>):</p>
  <div class="bs-callout bs-callout-info pre">
    <?= $this->curl_error ?>
    <?= htmlspecialchars($this->response) ?>
  </div>


<?php else: ?>


  <h3>Error</h3>
  <p>Could not find your token endpoint. We found it last time, so double check nothing on your website has changed in the mean time.</p>


<?php endif; ?>

</div>