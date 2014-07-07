<div class="narrow">
  <?= partial('partials/header') ?>

  <h2 id="introduction">Introduction</h2>

  <div class="col-xs-6 col-md-4" style="float: right;">
    <span class="thumbnail"><img src="/images/quill-ui.png"></span>
  </div>

  <p>This is a simple <a href="http://indiewebcamp.com/micropub">Micropub</a> client for 
     creating text posts on your own website. To use it, you will need to turn your website 
     into an OAuth provider, and implement a Micropub endpoint that this app will send 
     requests to.</p>

  <p>Once you've signed in, you'll see an interface like the one shown which you can use to 
     write a post. Clicking "post" will make a Micropub request to your endpoint.<p>



  <h2 id="endpoints">Configuring Endpoints</h2>

  <h3>Authorization Endpoint</h3>
  <?= partial('partials/auth-endpoint-help') ?>

  <h3>Token Endpoint</h3>
  <?= partial('partials/token-endpoint-help') ?>

  <h3>Micropub Endpoint</h3>
  <?= partial('partials/micropub-endpoint-help') ?>

  <p>The <a href="/creating-a-micropub-endpoint">Creating a Micropub Endpoint</a> tutorial will walk you through how to handle incoming POST requests from apps like this.</p>



  <h2 id="syndication">Syndication Targets</h2>

  <p>You can provide a list of supported syndication targets that will appear as checkboxes when you are creating a new post.</p>

  <p>To do this, your Micropub endpoint will need to respond to a GET request containing a query string of <code>q=syndicate-to</code>. This request will be made with the access token that was generated for this app, so you can choose which syndication targets you want to allow this app to use.</p>

  <p>Below is the request and expected response that Quill looks for.</p>

  <pre><code>GET /micropub?q=syndicate-to HTTP/1.1
Authorization: Bearer xxxxxxxxxx

HTTP/1.1 200 OK
Content-type: application/x-www-form-urlencoded

syndicate-to=syndicate-to=twitter.com%2Faaronpk%2Cfacebook.com%2Faaronpk
</code></pre>

  <p>The response should be a form-encoded reply with a single field, <code>syndicate-to</code>. The value is a comma-separated list of syndication targets. The actual values are up to your Micropub endpoint, but a good convention is to use the domain name of the service (e.g. twitter.com), or domain name and username (e.g. twitter.com/aaronpk).</p>

  <p>If you do include the domain name, Quill will be able to show icons for recognized services next to the checkboxes.</p>

  <p>Quill will check for your supported syndication targets when you sign in, but there is also a link on the new post screen to manually re-check if you'd like.</p>



</div>