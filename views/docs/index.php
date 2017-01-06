<div class="narrow">
  <?= partial('partials/header') ?>

  <h2>Introduction</h2>

  <div class="col-xs-6 col-md-4" style="float: right;">
    <span class="thumbnail"><img src="/images/quill-note-interface.png"></span>
  </div>

  <p>Quill is a simple <a href="https://indieweb.org/micropub">Micropub</a> client for 
     creating posts on your own website. To use it, your website will need to have
     a Micropub endpoint, and this app will send requests to it to create posts.</p>

  <p>There are Micropub plugins for various content management systems such as 
     <a href="https://wordpress.org/plugins/micropub/">Wordpress</a>, and is supported
     natively by some software such as <a href="https://withknown.com">Known</a>.
     It's also a relatively simple protocol you can implement if you are building
     your own website.</p>

  <p>Once you've signed in, you'll be able to use the various interfaces  see an interface like the one shown which you can use to 
     write a post. Clicking "post" will make a Micropub request to your endpoint.<p>

  <ul>
    <?php foreach($this->pages as $k=>$v): ?>
      <li><a href="/docs/<?= $k ?>"><?= $v ?></a></li>
    <?php endforeach; ?>
  </ul>

</div>
