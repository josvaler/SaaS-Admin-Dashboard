<?php

declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

$routes = [
    'dashboard' => [
        'view' => 'pages/dashboard',
        'data' => static function () {
            $operations = user_operations_metrics();
            $bufferPool = database_buffer_pool_diagnostics();

            return [
                'pageTitle' => 'Dashboard',
                'active' => 'dashboard',
                'userTotal' => user_total_count(),
                'operationsTotal' => $operations['sum'] ?? 0,
                'bufferHitRatio' => $bufferPool['ratio'] ?? 0,
                'bufferPool' => $bufferPool,
                'connectionsTotal' => database_total_connections(),
            ];
        },
    ],
    'database' => [
        'view' => 'pages/database',
        'data' => [
            'pageTitle' => 'Database Page',
            'active' => 'database',
        ],
    ],
];

$page = strtolower((string) ($_GET['page'] ?? 'dashboard'));

if (!array_key_exists($page, $routes)) {
    http_response_code(404);
    render('errors/404', [
        'pageTitle' => 'Not Found',
        'active' => null,
        'requestedPage' => $page,
    ]);
    return;
}

$route = $routes[$page];
$data = is_callable($route['data']) ? ($route['data'])() : $route['data'];
render($route['view'], $data);
