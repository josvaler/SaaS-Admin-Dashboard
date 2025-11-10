<?php

declare(strict_types=1);

function user_total_count(): int
{
    try {
        $stmt = db()->query('SELECT COUNT(*) AS total FROM users');
        $row = $stmt->fetch();
        if ($row === false) {
            return 0;
        }

        return (int) ($row['total'] ?? 0);
    } catch (Throwable $e) {
        return 0;
    }
}

function user_operations_metrics(): array
{
    try {
        $stmt = db()->query('SELECT SUM(lifetime_ops) AS total FROM users');
        $row = $stmt->fetch();
        $sum = $row === false ? 0.0 : (float) ($row['total'] ?? 0);

        return [
            'sum' => $sum,
        ];
    } catch (Throwable $e) {
        return [
            'sum' => 0.0,
        ];
    }
}

