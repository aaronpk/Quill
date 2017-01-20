<div class="narrow">
  <?= partial('partials/header') ?>

  <h2>Bookmark</h2>

  <img src="/images/bookmark-interface.png" style="max-width: 300px; float: right;">

  <p>The bookmark interface is for creating bookmark posts.</p>

  <h3>Bookmarklet</h3>

  <p>On the bookmark interface, you can drag a bookmarklet to your bookmarks toolbar. This will let you bookmark pages that you're viewing with a single click. You can select text in the page to include the selected text in the "content" of your bookmark post. The title of the page will be prefilled as the name of the bookmark post.</p>

  <h3>Post Properties</h3>

  <p>The following properties will be sent in the Micropub request. This request will be sent as a form-encoded request.</p>

  <p>The access token is sent in the Authorization HTTP header:</p>
  <pre>Authorization: Bearer XXXXXXXXX</pre>

  <ul>
    <li><code>h=entry</code> - This indicates that this is a request to create a new <a href="https://indieweb.org/h-entry">h-entry</a> post.</li>
    <li><code>bookmark-of</code> - This is the URL that you are bookmarking.</li>
    <li><code>name</code> - The name of the bookmark post, typically this is the same as the name of the page you are bookmarking. The Javascript bookmarklet will autofill this from the <code>&lt;title&gt;</code> tag of the page.</li>
    <li><code>content</code> - The text of the bookmark post. You can use this to add your own commentary, or post a quote from the page you're bookmarking. The bookmarklet will autofill this if you have text selected on the page.</li>
    <li><code>category[]</code> - This property will be repeated for each tag you've entered in the "tags" field.</li>
    <li><code>mp-syndicate-to[]</code> - Each syndication destination selected will be sent in this property. The values will be the <code>uid</code> that your endpoint returns. See <a href="/docs/syndication">Syndication</a> for more details. (If you are using an older Micropub endpoint that expects <code>syndicate-to</code>, you can customize this property in the settings.)</li>
  </ul>

  <hr>

  <p>Back to <a href="/docs/creating-posts">Creating Posts</a></p>

</div>
