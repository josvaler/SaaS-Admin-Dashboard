<?php

declare(strict_types=1);

require __DIR__ . '/../../app/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = db();
    $repository = new UserRepository($pdo);
    $service = new UserService($repository);

    $termRaw = filter_input(INPUT_GET, 'q', FILTER_DEFAULT);
    $term = is_string($termRaw) ? trim($termRaw) : '';
    $page = (int) filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1]]);
    $perPage = (int) filter_input(INPUT_GET, 'per_page', FILTER_VALIDATE_INT, ['options' => ['default' => 10]]);

    $result = $service->search($term, $page, $perPage);

    echo json_encode([
        'success' => true,
        'data' => $result,
    ], JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    error_log('[users.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Unable to load users at the moment.',
        'debug' => $e->getMessage(),
    ]);
}

