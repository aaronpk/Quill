<div class="narrow">
  <?= partial('partials/header') ?>

  <h2>Rich Editor</h2>

  <img src="/images/rich-editor-interface.png" style="max-width: 100%;">

  <p>The rich editor allows you to write posts with some basic formatting and embedded images.</p>

  <h3>Embedded Images</h3>

  <p>When editing, you can add images to your post in the interface. There are two ways embedded images are handled.</p>

  <p>If your Micropub server supports a <a href="https://www.w3.org/TR/micropub/#media-endpoint">Media Endpoint</a>, then at the time you add the image to the interface, Quill uploads the file to your Media Endpoint and embeds it in the editor as an <code>&lt;img&gt;</code> tag pointing to the file on your server. When you publish the post, the HTML will contain this img tag.</p>

  <pre><?= htmlspecialchars('<img src="https://media.example.com/image/10000.png">'); ?></pre>

  <p>If your Micropub server does not support a Media Endpoint, then when you add an image in the editor, the image is converted to a data URI, and will be sent to your Micropub endpoint when you publish the post. You don't need to do anything special to handle the image, since if you render this HTML directly, your viewers will see the image! Of course this means your HTML file will increase by the size of the image, so you may wish to implement a <a href="https://www.w3.org/TR/micropub/#media-endpoint">Media Endpoint</a> in order to handle images in your posts separately.</p>

  <pre><?= htmlspecialchars('<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAZIAAAET...'); ?></pre>

  <h3>Post Properties</h3>

  <p>The following properties will be sent in the Micropub request. This request will be sent as a JSON request.</p>

  <p>The access token is sent in the Authorization HTTP header:</p>
  <pre>Authorization: Bearer XXXXXXXXX</pre>

  <ul>
    <li><code>name</code> - The title of your post.</li>
    <li><code>content.html</code> - The full HTML of your post in the editor. This may include data-uri-encoded images.</li>
    <li><code>category</code> - This property will be repeated for each tag you've entered in the "tags" field.</li>
    <li><code>mp-slug</code> - If you enter a slug, this will be sent in the request. You can customize the name of this property in settings.</li>
    <li><code>post-status</code> - If you choose "draft" from the status dropdown, then this property will be set to "draft". Otherwise, it will not be included in the request. This is an indication to your endpoint that this is a draft post and should not be made public. Of course it's up to your endpoint to implement draft posts in whatever way you choose.</li>
  </ul>

  <p>This will be sent as a JSON request, so the request will look something like the following.</p>

  <pre>POST /micropub HTTP/1.1
Content-type: application/json
Authorization: Bearer XXXXXXXXXXX

{
  "type": "h-entry",
  "properties": {
    "name": [
      "Post Title"
    ],
    "content": [
      {
        "html": "&lt;p&gt;The HTML contents of your post from the editor&lt;/p&gt;"
      }
    ],
    "mp-slug": [
      "slug"
    ],
    "category": [
      "foo",
      "bar"
    ]
  }
}
  </pre>

  <hr>

  <p>Back to <a href="/docs/creating-posts">Creating Posts</a></p>

</div>
