
<form action="/auth/start" method="get">
  <input type="text" name="me" placeholder="http://me.com" value="" class="form-control"><br>

  <input type="hidden" name="client_id" value="https://indiepost.micropub.net">
  <input type="hidden" name="redirect_uri" value="https://indiepost.micropub.net/auth/callback">
  
  <input type="submit" value="Sign In" class="btn btn-primary">
</form>

