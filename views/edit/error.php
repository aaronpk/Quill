<div class="narrow">
  <?= partial('partials/header') ?>

  <h1>Error</h1>

  <p><?= htmlspecialchars($this->summary) ?></p>

  <div class="bs-callout bs-callout-danger">
    <h4><?= htmlspecialchars($this->error) ?></h4>
    <?= htmlspecialchars($this->error_description) ?>
  </div>

</div>