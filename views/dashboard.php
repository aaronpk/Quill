<div class="narrow">
  <?= partial('partials/header') ?>

  <div style="  margin-top: 4em; margin-bottom: 4em;">
    <ul class="post-type-icons">
      <?php if(supports_post_type($this->user, 'article')): ?>
        <li><a href="/editor">📄</a></li>
      <?php endif; ?>
      <?php if(supports_post_type($this->user, 'note')): ?>
        <li><a href="/new">✏️</a></li>
      <?php endif; ?>
      <?php if(supports_post_type($this->user, 'event')): ?>
        <li><a href="/event">📅</a></li>
      <?php endif; ?>
      <?php if(supports_post_type($this->user, 'bookmark')): ?>
        <li><a href="/bookmark">🔖</a></li>
      <?php endif; ?>
      <?php if(supports_post_type($this->user, 'like')): ?>
        <li><a href="/favorite">👍</a></li>
      <?php endif; ?>
      <?php if(supports_post_type($this->user, 'repost')): ?>
        <li><a href="/repost">♺</a></li>
      <?php endif; ?>
      <?php if(supports_post_type($this->user, 'itinerary')): ?>
        <li><a href="/itinerary">✈️</a></li>
      <?php endif; ?>
      <?php if(supports_post_type($this->user, 'review')): ?>
        <li><a href="/review">⭐️</a></li>
      <?php endif; ?>
      <li><a href="/settings">⚙</a></li>
    </ul>
    <div style="clear:both;"></div>
  </div>
</div>
<style type="text/css">
.post-type-icons {
  margin-top: 0;
  list-style-type: none;
  font-size: 39pt;
}
.post-type-icons li {
  float: left;
  margin-right: 12px;
}
</style>
