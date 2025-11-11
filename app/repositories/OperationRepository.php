<?php

declare(strict_types=1);

class OperationRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function searchByUser(
        int $userId,
        string $operationType,
        int $limit,
        int $offset
    ): array {
        $sql = <<<SQL
            SELECT id,
                   user_id,
                   operation_type,
                   status,
                   file_size,
                   date,
                   created_at
            FROM operations
            WHERE user_id = :user_id
              AND (:type = '' OR operation_type LIKE :like)
            ORDER BY date DESC
            LIMIT :limit OFFSET :offset
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':type', $operationType);
        $stmt->bindValue(':like', '%' . $operationType . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByUser(int $userId, string $operationType): int
    {
        $sql = <<<SQL
            SELECT COUNT(*) AS total
            FROM operations
            WHERE user_id = :user_id
              AND (:type = '' OR operation_type LIKE :like)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':type', $operationType);
        $stmt->bindValue(':like', '%' . $operationType . '%');
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['total'] ?? 0);
    }
}

