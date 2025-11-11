<?php

declare(strict_types=1);

class UserService
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function search(string $term, int $page, int $perPage): array
    {
        $term = trim($term);
        $page = max($page, 1);
        $perPage = max(min($perPage, 10), 5);

        $offset = ($page - 1) * $perPage;
        $items = $this->repository->search($term, $perPage, $offset);
        $total = $this->repository->count($term);
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

    public function findById(int $userId): ?array
    {
        return $this->repository->findById($userId);
    }
}

