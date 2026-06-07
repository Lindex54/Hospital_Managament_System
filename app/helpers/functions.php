<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';

function config(string $key, mixed $default = null): mixed
{
    return APP_CONFIG[$key] ?? $default;
}

function base_url(string $path = ''): string
{
    $baseUrl = rtrim((string) config('base_url', ''), '/');
    $path = ltrim($path, '/');

    return $path === '' ? $baseUrl : $baseUrl . '/' . $path;
}

function asset_url(string $path = ''): string
{
    $path = ltrim($path, '/');

    return base_url('assets/' . $path);
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

