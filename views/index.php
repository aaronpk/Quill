<div class="narrow">

  <div class="jumbotron h-app h-x-app">
    <h1><img src="/images/quill-logo-144.png" height="72" style="margin-bottom: 13px;" class="u-logo p-name" alt="Quill">Quill</h1>

    <p class="tagline">Quill is a simple app for posting text notes to your website.</p>

<? if(session('me')): 
        if ( profile('photo') ) {
		?>   		<img src="<?php echo profile('photo'); ?>" height="125" alt="Profile Image" />
<?php }
        if ( profile('name') ) {
	?>  <p><?php echo profile('name'); ?></p>
<?php	}
    ?>
      
      <p>You're already signed in!<p>
      <p><a href="/dashboard" class="btn btn-primary">Continue</a></p>
    <? else: ?>
      <p>To use Quill, sign in with your domain. Your website will need to support <a href="https://indieweb.org/micropub">Micropub</a> for creating new posts.</p>

      <form action="/auth/start" method="get" class="form-inline">
        <input type="url" name="me" placeholder="https://example.com" value="" class="form-control" onchange="auto_prefix_url_field(this)" autofocus>
        <input type="submit" value="Sign In" class="btn btn-primary">
        <input type="hidden" name="client_id" value="<?= Config::$base_url ?>">
        <input type="hidden" name="redirect_uri" value="<?= Config::$base_url ?>auth/callback">
      </form>
    <? endif; ?>

    <a href="" class="u-url"></a>
  </div>

</div>
