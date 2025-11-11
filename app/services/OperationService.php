<?php

declare(strict_types=1);

class OperationService
{
    public function __construct(
        private OperationRepository $repository,
        private UserRepository $userRepository
    ) {
    }

    public function listByUser(int $userId, string $operationType, int $page, int $perPage): array
    {
        $operationType = trim($operationType);
        $page = max($page, 1);
        $perPage = max(min($perPage, 20), 5);

        $offset = ($page - 1) * $perPage;
        $items = $this->repository->searchByUser($userId, $operationType, $perPage, $offset);
        $total = $this->repository->countByUser($userId, $operationType);
        $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 1;

        return [
            'items' => $items,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1,
            ],
        ];
    }

    public function getUserSummary(int $userId): ?array
    {
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            return null;
        }

        return $user;
    }
}

