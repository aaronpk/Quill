    <p>If you publish an <a href="https://indieauth.spec.indieweb.org/#indieauth-server-metadata">IndieAuth Server Metadata</a>
       document, your <code>token_endpoint</code> is declared there and you don't need a separate
       <code>&lt;link&gt;</code> tag for it.</p>
    <p>Otherwise, you can <a href="/creating-a-token-endpoint">create your own token endpoint</a> for 
       your website which can issue access tokens when given an authorization code, but 
       it's easier to use an existing service such as <a href="https://tokens.indieauth.com">tokens.indieauth.com</a>. 
       To use this service as your token endpoint, use the markup provided below.</p>
    <p><pre><code>&lt;link rel="token_endpoint" href="https://tokens.indieauth.com/token"&gt;</code></pre></p>
