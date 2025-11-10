<?php
$pageTitle ??= '';
?>
<?php $dbHost = env('DB_HOST', 'localhost'); ?>
<header class="border-bottom bg-body z-1">
  <div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between py-2">
      <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>
      <div class="d-flex align-items-center gap-2">
        <span class="fw-semibold"><?= htmlspecialchars($pageTitle) ?></span>
        <div class="d-none d-md-inline-flex align-items-center gap-2">
          <?php if (database_is_connected()) : ?>
            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle align-items-center gap-1 px-3 py-2 rounded-pill shadow-sm">
              <i class="bi bi-database-check"></i>
              Connected
            </span>
          <?php else : ?>
            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle align-items-center gap-1 px-3 py-2 rounded-pill shadow-sm">
              <i class="bi bi-database-x"></i>
              Offline
            </span>
          <?php endif; ?>
          <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-2 rounded-pill shadow-sm">
            <i class="bi bi-hdd-network"></i>
            <?= htmlspecialchars($dbHost) ?>
          </span>
        </div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-outline-secondary" id="themeToggle" title="Toggle theme">
          <i class="bi bi-moon-stars"></i>
        </button>
        <div class="dropdown">
          <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><h6 class="dropdown-header">Account</h6></li>
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</header>

