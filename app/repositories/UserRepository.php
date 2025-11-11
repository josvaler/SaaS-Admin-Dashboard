<?php

declare(strict_types=1);

class UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function search(string $term, int $limit, int $offset): array
    {
        $sql = <<<SQL
            SELECT id, name, email, created_at
            FROM users
            WHERE (:term = '' OR name LIKE :like_name OR email LIKE :like_email)
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':term', $term);
        $stmt->bindValue(':like_name', '%' . $term . '%');
        $stmt->bindValue(':like_email', '%' . $term . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(string $term): int
    {
        $sql = <<<SQL
            SELECT COUNT(*) AS total
            FROM users
            WHERE (:term = '' OR name LIKE :like_name OR email LIKE :like_email)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':term', $term);
        $stmt->bindValue(':like_name', '%' . $term . '%');
        $stmt->bindValue(':like_email', '%' . $term . '%');
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['total'] ?? 0);
    }

    public function findById(int $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, created_at FROM users WHERE id = :id');
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }
}

