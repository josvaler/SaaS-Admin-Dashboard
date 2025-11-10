<?php require view_path('partials/head'); ?>
<div id="layout" class="w-100">
  <?php require view_path('partials/sidebar'); ?>
  <?php require view_path('partials/topbar'); ?>
  <main class="p-3">
    <?= $content ?>
  </main>
  <?php require view_path('partials/footer'); ?>
</div>
<?php require view_path('partials/scripts'); ?>

