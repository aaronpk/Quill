<h2>Introduction</h2>

<div class="col-xs-6 col-md-4" style="float: right;">
  <span class="thumbnail"><img src="/images/indiepost-ui.png"></span>
</div>

<p>This is a simple <a href="http://indiewebcamp.com/micropub">Micropub</a> client for creating text posts on your own website. To use it, you will need to turn your website into an OAuth provider, and implement a Micropub endpoint that this app will send requests to.</p>

<p>Once you've signed in, you'll see an interface like the below which you can use to write a post. Clicking "post" will make a Micropub request to your endpoint.<p>

<h2>Configuring Endpoints</h2>

<h3>Authorization Endpoint</h3>
<?= partial('partials/auth-endpoint-help') ?>

<h3>Token Endpoint</h3>
<?= partial('partials/token-endpoint-help') ?>

<h3>Micropub Endpoint</h3>
<?= partial('partials/micropub-endpoint-help') ?>

<p>The <a href="/creating-a-micropub-endpoint">Creating a Micropub Endpoint</a> tutorial will walk you through how to handle incoming POST requests from apps like this.</p>
