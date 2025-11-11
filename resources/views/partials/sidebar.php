<?php
$active ??= 'dashboard';
?>
<nav id="sidebar" class="border-end bg-body-tertiary" style="min-width:260px;max-width:260px;">
  <div class="p-3 d-flex align-items-center gap-2 border-bottom">
    <i class="bi bi-grid-1x2-fill fs-4"></i>
    <span class="fw-bold">VV</span>
  </div>
  <div class="list-group list-group-flush rounded-0">
    <a class="list-group-item list-group-item-action d-flex align-items-center gap-2 <?= active_class('dashboard', $active) ?>"
       href="<?= route_url('dashboard') ?>">
      <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>
    <a class="list-group-item list-group-item-action d-flex align-items-center gap-2 <?= active_class('database', $active) ?>"
       href="<?= route_url('database') ?>#users-section">
      <i class="bi bi-people"></i><span>Users</span>
    </a>
    <a class="list-group-item list-group-item-action d-flex align-items-center gap-2 <?= active_class('database', $active) ?>"
       href="<?= route_url('database') ?>#operations-section">
      <i class="bi bi-diagram-3"></i><span>Operations</span>
    </a>
  </div>
</nav>

