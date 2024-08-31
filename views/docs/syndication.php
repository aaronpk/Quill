<div class="narrow">
  <?= partial('partials/header') ?>

  <h2>Syndication Targets</h2>

  <p>You can provide a list of supported <a href="https://www.w3.org/TR/micropub/#syndication-targets">syndication targets</a> that will appear as checkboxes when you are creating a new post.</p>

  <p>To do this, your Micropub endpoint will need to respond to a GET request containing a query string of <code>q=syndicate-to</code>. This request will be made with the access token that was generated for this app, so you can choose which syndication targets you want to allow this app to use.</p>

  <p>Below is the request and expected response that Quill looks for.</p>

  <pre><code>GET /micropub?q=syndicate-to HTTP/1.1
Authorization: Bearer xxxxxxxxxx

HTTP/1.1 200 OK
Content-type: application/json

{
  "syndicate-to": [
    {
      "uid": "https://news.indieweb.org/en",
      "name": "IndieNews"
    }
  ]
}
</code></pre>

  <p>The specific values of names and uids are up to your Micropub endpoint, but a good convention is to use the domain name of the service (e.g. https://news.indieweb.org), or domain name and username (e.g. https://mastodon.social/@aaronpk) for the uid, and a friendly name like "IndieNews" or "mastodon.social/@aaronpk" as the name.</p>

  <p>Quill will check for your supported syndication targets when you sign in, but there is also a link on the new post screen to manually re-check if you'd like.</p>

  <p>When you create a post and tap one of the syndication options, the value of <code>uid</code> is sent in a property called <code>mp-syndicate-to</code>, which instructs your endpoint to syndicate to that target. Note that Quill doesn't know whether the target is Mastodon, IndieNews, or something else, and doesn't talk to the service directly. It's just an instruction to your endpoint to syndicate to that destination.</p>

</div>
