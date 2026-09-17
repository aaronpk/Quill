    <p>The modern way to tell apps about your authorization endpoint is to publish an
       <a href="https://indieauth.spec.indieweb.org/#indieauth-server-metadata">IndieAuth Server Metadata</a>
       document and link to it from your home page. The metadata document declares your
       <code>authorization_endpoint</code> and <code>token_endpoint</code> together, along with the
       <code>issuer</code> that this app checks when you sign in.</p>
    <p><pre><code>&lt;link rel="indieauth-metadata" href="https://example.com/.well-known/oauth-authorization-server"&gt;</code></pre></p>
    <p>You can also declare the authorization endpoint on its own. You can create your own authorization endpoint, but it's easier to use an existing service such as <a href="https://indieauth.com/">IndieAuth.com</a>. To delegate to IndieAuth.com, you can use the markup provided below.</p>
    <p><pre><code>&lt;link rel="authorization_endpoint" href="https://indieauth.com/auth"&gt;</code></pre></p>
