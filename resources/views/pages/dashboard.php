<div class="container-fluid">
  <div class="row g-3">
    <div class="col-12 col-md-6 col-xl-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <p class="text-secondary text-uppercase fw-semibold small mb-1">Users</p>
              <h3 class="mb-0"><?= number_format((int)($userTotal ?? 0)) ?></h3>
              <small class="text-secondary">Target: 100 users</small>
            </div>
            <span class="badge bg-primary">
              <i class="bi bi-people"></i>
              Total
            </span>
          </div>
          <div>
            <canvas id="metricGauge"
                    class="chart-gauge"
                    data-total="<?= (int)($userTotal ?? 0) ?>"
                    data-capacity="100"
                    data-caption="Initial Users Capacity"
                    data-summary="<?= htmlspecialchars(number_format((int)($userTotal ?? 0)) . ' / 100 users', ENT_QUOTES) ?>"
                    height="160"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <p class="text-secondary text-uppercase fw-semibold small mb-1">Lifetime Operations</p>
              <h3 class="mb-0"><?= number_format((int) round($operationsTotal ?? 0)) ?></h3>
              <small class="text-secondary">Target: 100 operations</small>
            </div>
            <span class="badge bg-warning text-dark">
              <i class="bi bi-rocket-takeoff"></i>
              Ops
            </span>
          </div>
          <div>
            <canvas id="operationsGauge"
                    class="chart-gauge"
                    data-total="<?= max(0, (int) round($operationsTotal ?? 0)) ?>"
                    data-capacity="100"
                    data-caption="Total vs target (100 ops)"
                    data-summary="<?= htmlspecialchars(number_format((int) round($operationsTotal ?? 0)) . ' / 100 ops', ENT_QUOTES) ?>"
                    height="160"></canvas>
          </div>
        </div>
      </div>
    </div>
    <?php $bufferStats = $bufferPool ?? []; ?>
    <?php $hitRatioValue = (float) ($bufferStats['ratio'] ?? 0); ?>
    <?php $threads = $bufferStats['threads'] ?? []; ?>
    <?php $topTables = array_slice($bufferStats['top_tables'] ?? [], 0, 3); ?>
    <div class="col-12 col-md-6 col-xl-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <p class="text-secondary text-uppercase fw-semibold small mb-1">Buffer Pool Hit Ratio</p>
              <h3 class="mb-0"><?= number_format($hitRatioValue, 2) ?>%</h3>
              <small class="text-secondary">Target: 100%</small>
            </div>
            <span class="badge bg-success">
              <i class="bi bi-speedometer2"></i>
              Buffer
            </span>
          </div>
          <div>
            <canvas id="hitRatioGauge"
                    class="chart-gauge"
                    data-total="<?= max(0, min(100, $hitRatioValue)) ?>"
                    data-capacity="100"
                    data-caption="InnoDB buffer pool hit ratio"
                    data-summary="<?= htmlspecialchars(number_format($hitRatioValue, 2) . ' / 100 %', ENT_QUOTES) ?>"
                    height="160"></canvas>
          </div>
          <div class="mt-3">
            <small class="text-secondary text-uppercase fw-semibold d-block mb-2">Runtime Metrics</small>
            <ul class="list-unstyled text-secondary small mb-3">
              <li>
                Threads running: <?= number_format((int)($threads['Threads_running'] ?? 0)) ?>
                · connected: <?= number_format((int)($threads['Threads_connected'] ?? 0)) ?>
              </li>
              <li>Slow queries: <?= number_format((int)($bufferStats['slow_queries'] ?? 0)) ?></li>
              <li>Max connections: <?= number_format((int)($bufferStats['max_connections'] ?? 0)) ?></li>
              <li>
                Pool reads: <?= number_format((float)($bufferStats['buffer_pool_reads'] ?? 0), 0) ?>
                · requests: <?= number_format((float)($bufferStats['buffer_pool_read_requests'] ?? 0), 0) ?>
              </li>
            </ul>
            <?php if (!empty($topTables)) : ?>
              <small class="text-secondary text-uppercase fw-semibold d-block mb-2">Largest Tables</small>
              <ul class="list-unstyled text-secondary small mb-0">
                <?php foreach ($topTables as $table) : ?>
                  <li>
                    <?= htmlspecialchars(($table['table_schema'] ?? '') . '.' . ($table['table_name'] ?? '')) ?>
                    · <?= number_format((float)($table['size_mb'] ?? 0), 2) ?> MB
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php $connectionsValue = (int) ($connectionsTotal ?? 0); ?>
    <div class="col-12 col-md-6 col-xl-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <p class="text-secondary text-uppercase fw-semibold small mb-1">Total Users Connected</p>
              <h3 class="mb-0"><?= number_format($connectionsValue) ?></h3>
              <small class="text-secondary">Target: 151 connections</small>
            </div>
            <span class="badge bg-info">
              <i class="bi bi-people-fill"></i>
              Connections
            </span>
          </div>
          <div>
            <canvas id="connectionsGauge"
                    class="chart-gauge"
                    data-total="<?= max(0, $connectionsValue) ?>"
                    data-capacity="151"
                    data-caption="Concurrent connections"
                    data-summary="<?= htmlspecialchars(number_format($connectionsValue) . ' / 151 connections', ENT_QUOTES) ?>"
                    data-direction="down"
                    data-label="count"
                    height="160"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-8">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Weekly Overview</span>
          <div class="btn-group btn-group-sm" role="group" aria-label="Range">
            <button class="btn btn-outline-secondary" disabled>Day</button>
            <button class="btn btn-outline-secondary active">Week</button>
            <button class="btn btn-outline-secondary" disabled>Month</button>
          </div>
        </div>
        <div class="card-body">
          <canvas id="overviewChart" height="160"></canvas>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-4">
      <div class="card h-100">
        <div class="card-header">Channel Mix</div>
        <div class="card-body d-flex justify-content-center align-items-center">
          <div class="w-100" style="max-width:320px;">
            <canvas id="channelsChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

