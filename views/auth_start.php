<div class="narrow">
  <?= partial('partials/header') ?>

<div id="authorization_endpoint">
  <h3>Authorization Endpoint</h3>

  <p><i>The authorization endpoint tells this app where to direct your browser to sign you in.</i></p>

  <?php if($this->authorizationEndpoint): ?>
    <div class="bs-callout bs-callout-success">Found your authorization endpoint: <code><?= $this->authorizationEndpoint ?></code></div>
  <?php else: ?>
    <div class="bs-callout bs-callout-danger">Could not find your authorization endpoint!</div>
    <p>You need to set your authorization endpoint in a <code>&lt;link&gt;</code> tag on your home page.</p>
    <?= partial('partials/auth-endpoint-help') ?>
  <?php endif; ?>
</div>

<div id="token_endpoint">
  <h3>Token Endpoint</h3>

  <p><i>The token endpoint is where this app will make a request to get an access token after obtaining authorization.</i></p>

  <?php if($this->tokenEndpoint): ?>
    <div class="bs-callout bs-callout-success">Found your token endpoint: <code><?= $this->tokenEndpoint ?></code></div>
  <?php else: ?>
    <div class="bs-callout bs-callout-danger">Could not find your token endpoint!</div>
    <p>You need to set your token endpoint in a <code>&lt;link&gt;</code> tag on your home page.</p>
    <?= partial('partials/token-endpoint-help') ?>
  <?php endif; ?>

</div>

<div id="micropub_endpoint">
  <h3>Micropub Endpoint</h3>

  <p><i>The Micropub endpoint is the URL this app will use to post new photos.</i></p>

  <?php if($this->micropubEndpoint): ?>
    <div class="bs-callout bs-callout-success">
      Found your Micropub endpoint: <code><?= $this->micropubEndpoint ?></code>
    </div>
  <?php else: ?>
    <div class="bs-callout bs-callout-danger">Could not find your Micropub endpoint!</div>
    <p>You need to set your Micropub endpoint in a <code>&lt;link&gt;</code> tag on your home page.</p>
    <?= partial('partials/micropub-endpoint-help', $this) ?>
  <?php endif; ?>

</div>

<?php if($this->authorizationURL): ?>

  <h3>Ready!</h3>

  <p>Clicking the button below will take you to <strong>your</strong> authorization server which is where you will allow this app to be able to post to your site.</p>

  <form action="/auth/redirect" method="get">
    <p>Choose the scope to request:</p>
    <ul style="list-style-type: none;">
      <li><input type="radio" name="scope" value="create update" checked="checked"> create update</li>
      <li><input type="radio" name="scope" value="create"> create</li>
      <li><input type="radio" name="scope" value="post"> post (legacy)</li>
    </ul>

    <button class="btn btn-primary" type="submit" id="auth-submit">Authorize</button>

    <input type="hidden" name="authorization_url" value="<?= $this->authorizationURL ?>">
  </form>

<?php endif; ?>

</div>
