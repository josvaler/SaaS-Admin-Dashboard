<?php

declare(strict_types=1);

function database_config(): array
{
    return [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => (int) env('DB_PORT', 3306),
        'database' => env('DB_NAME', ''),
        'username' => env('DB_USER', ''),
        'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ];
}

function database_dsn(array $config): string
{
    $host = $config['host'] ?: '127.0.0.1';
    $port = $config['port'] ?: 3306;
    $dbname = $config['database'];
    $charset = $config['charset'] ?? 'utf8mb4';

    return "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = database_config();

    if ($config['database'] === '' || $config['username'] === '') {
        throw new RuntimeException('Database configuration is incomplete.');
    }

    $pdo = new PDO(
        database_dsn($config),
        $config['username'],
        $config['password'],
        $config['options']
    );

    return $pdo;
}

