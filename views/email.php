<div class="narrow">
  <?= partial('partials/header') ?>

  <div class="jumbotron" style="margin-top: 20px;">
    <p>
      Send email or MMS to<br>
      <a href="mailto:<?= $this->user->email_username . '@' . Config::$hostname ?>"><?= $this->user->email_username . '@' . Config::$hostname ?></a>
    </p>
  </div>

  <div style="width: 80%; margin: 0 auto;">
    <h3>Email Subject</h3>
    <p>If you add a subject line to your email, it will be sent as the "name" property which indicates to your Micropub endpoint that this is a blog post.</p>

    <h3>Email and MMS body</h3>
    <p>The text of your email or MMS will be send as the "content" property, which is the main contents of your post. Plaintext only for now.</p>

    <h3>Photo</h3>
    <p>If you attach a photo to your email or MMS, it will be sent to your Micropub endpoint. (Only one photo is currently supported.)</p>
  </div>

  <div>
    <?php if($this->test_response): ?>
      <h4>Last response from your Micropub endpoint</h4>
      <pre id="test_response" style="width: 100%; min-height: 280px;"><?= htmlspecialchars($this->test_response) ?></pre>
    <?php endif; ?>
  </div>

</div>