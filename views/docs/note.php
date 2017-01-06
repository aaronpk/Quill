<div class="narrow">
  <?= partial('partials/header') ?>

  <h2>Note</h2>

  <img src="/images/note-interface.png" style="max-width: 300px; float: right;">

  <p>The note interface is for creating simple text posts and optionally adding images.</p>

  <h3>Adding Photos</h3>

  <p>If your Micropub server supports a <a href="https://www.w3.org/TR/micropub/#media-endpoint">Media Endpoint</a>, then at the time you select a photo, Quill uploads the file to your Media Endpoint and shows a preview in the interface. The image URL will be sent as a string in the request.</p>

  <p>If your Micropub server does not support a Media Endpoint, then when you add an image, it is not uploaded until you click "post", and then is sent to your Micropub endpoint as a file.</p>

  <h3>Post Properties</h3>

  <p>The following properties will be sent in the Micropub request. This request will be sent as either a form-encoded or a multipart-encoded request, depending on whether there are photos and whether you have a Media Endpoint.</p>

  <p>If you have a Media Endpoint, then you'll always get a form-encoded request with the URL of any photos. If you do not have a Media Endpoint, and if there is a photo, you'll get a multipart request so that photos are uploaded directly to your Micropub endpoint.</p>

  <p>The access token is sent in the Authorization HTTP header:</p>
  <pre>Authorization: Bearer XXXXXXXXX</pre>

  <ul>
    <li><code>h=entry</code> - This indicates that this is a request to create a new <a href="https://indieweb.org/h-entry">h-entry</a> post.</li>
    <li><code>content</code> - The text of your post. Your endpoint is expected to treat this as plaintext, and handle all escaping as necessary.</li>
    <li><code>category[]</code> - This property will be repeated for each tag you've entered in the "tags" field.</li>
    <li><code>in-reply-to</code> - If you tap the Reply button and enter a URL, the URL will be sent in this property.</li>
    <li><code>location</code> - If you check the "location" box, then this property will be a Geo URI with the location the browser detected. You will see a preview of the value in the note interface along with a map.</li>
    <li><code>photo</code> or <code>photo[]</code> - If your server supports a Media Endpoint, this will be set to the URL that your endpoint returned when it uploaded the photo. Otherwise, this will be one of the parts in the multipart request with the image file itself.</li>
    <li><code>mp-slug</code> - If you enter a slug, this will be sent in the request. You can customize the name of this property in settings.</li>
    <li><code>syndicate-to[]</code> - (Note: this is deprecated and will be replaced with "mp-syndicate-to[]" soon) Each syndication destination selected will be sent in this property. The values will be the <code>uid</code> that your endpoint returns. See <a href="/docs/syndication">Syndication</a> for more details.</li>
  </ul>

  <hr>

  <p>Back to <a href="/docs/creating-posts">Creating Posts</a></p>

</div>
