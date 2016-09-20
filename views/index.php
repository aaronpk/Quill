<div class="narrow">

  <div class="jumbotron h-x-app">
    <h1><img src="/images/quill-logo-144.png" height="72" style="margin-bottom: 13px;" class="u-logo p-name" alt="Quill">Quill</h1>

    <p class="tagline">Quill is a simple app for posting text notes to your website.</p>

    <p>To use Quill, sign in with your domain. Your website will need to support <a href="http://indiewebcamp.com/micropub">Micropub</a> for creating new posts.</p>

    <form action="/auth/start" method="get" class="form-inline">
      <input type="url" name="me" placeholder="http://example.com" value="http://" class="form-control" autofocus>
      <input type="submit" value="Sign In" class="btn btn-primary">
      <input type="hidden" name="client_id" value="https://quill.p3k.io">
      <input type="hidden" name="redirect_uri" value="https://quill.p3k.io/auth/callback">
    </form>

  </div>

</div>
