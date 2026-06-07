<?php

declare(strict_types=1);

require_once __DIR__ . '/app.php';

function database_config(): array
{
    return [
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'hospital_management',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ];
}

function database_connection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = database_config();
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['dbname'],
        $config['charset']
    );

    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

