<div class="narrow">
  <?= partial('partials/header') ?>

  <h2>Favorite</h2>

  <img src="/images/favorite-interface.jpg" style="max-width: 300px; float: right;">

  <p>The favorite interface is for creating favorite posts.</p>

  <h3>Bookmarklet</h3>

  <p>On the favorite interface, you can drag a bookmarklet to your bookmarks toolbar. This will let you favorite pages that you're viewing with a single click.</p>

  <h3>Post Properties</h3>

  <p>The following properties will be sent in the Micropub request. This request will be sent as a form-encoded request.</p>

  <p>The access token is sent in the Authorization HTTP header:</p>
  <pre>Authorization: Bearer XXXXXXXXX</pre>

  <ul>
    <li><code>h=entry</code> - This indicates that this is a request to create a new <a href="https://indieweb.org/h-entry">h-entry</a> post.</li>
    <li><code>like-of</code> - This is the URL that you are favoriting.</li>
  </ul>

  <hr>

  <p>Back to <a href="/docs/creating-posts">Creating Posts</a></p>

</div>
