<div class="narrow">
  <?= partial('partials/header') ?>

  <h2 id="signing-in">Signing In</h2>

  <p>To begin using Quill, you need to sign in. This will grant Quill an access token that it
     will use when it creates posts on your website.</p>

  <p>Authentication happens at a website that you choose, and you authorize Quill to be able 
     to post on your website. If you are familiar with OAuth 2.0, you will recognize many
     of the concepts here.</p>

  <p>When you sign in to Quill, you start by entering your URL. This URL delegates the various
     aspects of signing in and granting authorization to other services, which may or may
     not be part of your website.</p>

  <h2 id="endpoints">Configuring Endpoints</h2>

  <p>To tell Quill where to find the endpoints it will need to log you in, you'll need
     to add some HTML tags to your home page.</p>

  <h3>Authorization Endpoint</h3>
  <?= partial('partials/auth-endpoint-help') ?>

  <h3>Token Endpoint</h3>
  <?= partial('partials/token-endpoint-help') ?>

  <h3>Micropub Endpoint</h3>
  <?= partial('partials/micropub-endpoint-help') ?>

  <p>The <a href="/creating-a-micropub-endpoint">Creating a Micropub Endpoint</a> tutorial will walk you through how to handle incoming POST requests from apps like this.</p>

  <hr>

  <p>Continue to <a href="/docs/creating-posts">Creating Posts</a></p>

</div>
