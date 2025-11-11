<?php

declare(strict_types=1);

require __DIR__ . '/../../app/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $userId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
    if ($userId === null || $userId === false || $userId <= 0) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'error' => 'A valid user_id is required.',
        ]);
        return;
    }

    $operationTypeRaw = filter_input(INPUT_GET, 'operation_type', FILTER_DEFAULT);
    $operationType = is_string($operationTypeRaw) ? trim($operationTypeRaw) : '';
    $page = (int) filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
    $perPage = (int) filter_input(INPUT_GET, 'per_page', FILTER_VALIDATE_INT, ['options' => ['default' => 10, 'min_range' => 1]]);

    $pdo = db();
    $userRepository = new UserRepository($pdo);
    $operationRepository = new OperationRepository($pdo);
    $service = new OperationService($operationRepository, $userRepository);

    $user = $service->getUserSummary($userId);
    if ($user === null) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'User not found.',
        ]);
        return;
    }

    $result = $service->listByUser($userId, $operationType, $page, $perPage);

    echo json_encode([
        'success' => true,
        'data' => [
            'user' => $user,
            'operations' => $result['items'],
            'meta' => $result['meta'],
        ],
    ], JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    error_log('[operations.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Unable to load operations at the moment.',
        'debug' => $e->getMessage(),
    ]);
}

