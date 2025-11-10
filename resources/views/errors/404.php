<div class="container-fluid">
  <div class="text-center py-5">
    <div class="display-1 text-muted">404</div>
    <p class="lead mb-4">Sorry, the page you are looking for could not be found.</p>
    <?php if (!empty($requestedPage)) : ?>
      <p class="text-muted">Requested page: <code><?= htmlspecialchars($requestedPage) ?></code></p>
    <?php endif; ?>
    <a class="btn btn-primary" href="<?= route_url('dashboard') ?>">Back to dashboard</a>
  </div>
</div>

