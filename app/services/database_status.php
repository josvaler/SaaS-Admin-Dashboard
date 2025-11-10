<?php

declare(strict_types=1);

function database_is_connected(): bool
{
    static $status = null;

    if ($status !== null) {
        return $status;
    }

    try {
        db()->query('SELECT 1');
        $status = true;
    } catch (Throwable $e) {
        $status = false;
    }

    return $status;
}

function database_buffer_pool_diagnostics(): array
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    $cache = [
        'timestamp' => null,
        'ratio' => 0.0,
        'threads' => [],
        'slow_queries' => 0,
        'max_connections' => 0,
        'buffer_pool_reads' => 0.0,
        'buffer_pool_read_requests' => 0.0,
        'replica' => null,
        'top_tables' => [],
    ];

    try {
        $pdo = db();

        // Timestamp
        $timestampStmt = $pdo->query("SELECT NOW() AS ts");
        if ($timestampStmt !== false) {
            $cache['timestamp'] = (string) ($timestampStmt->fetchColumn() ?: '');
        }

        // Threads status
        $threadsStmt = $pdo->query("SHOW GLOBAL STATUS LIKE 'Threads%';");
        if ($threadsStmt !== false) {
            foreach ($threadsStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $cache['threads'][$row['Variable_name']] = (int) $row['Value'];
            }
        }

        // Slow queries
        $slowStmt = $pdo->query("SHOW GLOBAL STATUS LIKE 'Slow_queries';");
        if ($slowStmt !== false) {
            $slowRow = $slowStmt->fetch(PDO::FETCH_ASSOC);
            if ($slowRow) {
                $cache['slow_queries'] = (int) ($slowRow['Value'] ?? 0);
            }
        }

        // Max connections
        $maxConnStmt = $pdo->query("SHOW VARIABLES LIKE 'max_connections';");
        if ($maxConnStmt !== false) {
            $maxConnRow = $maxConnStmt->fetch(PDO::FETCH_ASSOC);
            if ($maxConnRow) {
                $cache['max_connections'] = (int) ($maxConnRow['Value'] ?? 0);
            }
        }

        // Buffer pool reads stats
        $readsStmt = $pdo->query("SHOW GLOBAL STATUS LIKE 'Innodb_buffer_pool_read%';");
        $bufferReads = 0.0;
        $bufferReadRequests = 0.0;
        if ($readsStmt !== false) {
            foreach ($readsStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $name = $row['Variable_name'] ?? '';
                $value = (float) ($row['Value'] ?? 0);
                if ($name === 'Innodb_buffer_pool_reads') {
                    $bufferReads = $value;
                }
                if ($name === 'Innodb_buffer_pool_read_requests') {
                    $bufferReadRequests = $value;
                }
            }
        }
        $cache['buffer_pool_reads'] = $bufferReads;
        $cache['buffer_pool_read_requests'] = $bufferReadRequests;
        if ($bufferReadRequests > 0) {
            $ratio = (1 - ($bufferReads / $bufferReadRequests)) * 100;
            $cache['ratio'] = round(max(0.0, min($ratio, 100.0)), 2);
        }

        // Replica status
        $replicaStmt = $pdo->query('SHOW SLAVE STATUS;');
        if ($replicaStmt !== false) {
            $replicaRow = $replicaStmt->fetch(PDO::FETCH_ASSOC);
            $cache['replica'] = $replicaRow ?: null;
        }

        // Largest tables
        $tablesStmt = $pdo->query("
            SELECT table_schema, table_name,
                   ROUND((data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.TABLES
            ORDER BY (data_length + index_length) DESC
            LIMIT 10;
        ");
        if ($tablesStmt !== false) {
            $cache['top_tables'] = $tablesStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Throwable $e) {
        // Ignore and return whatever we managed to gather
    }

    return $cache;
}

function database_hit_ratio(): float
{
    $stats = database_buffer_pool_diagnostics();
    return (float) ($stats['ratio'] ?? 0.0);
}

function database_total_connections(): int
{
    try {
        $stmt = db()->query('SELECT COUNT(*) AS total_connections FROM information_schema.PROCESSLIST;');
        $row = $stmt->fetch();
        if ($row === false) {
            return 0;
        }

        return (int) ($row['total_connections'] ?? 0);
    } catch (Throwable $e) {
        return 0;
    }
}

