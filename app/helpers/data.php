<?php

declare(strict_types=1);

function app_data_path(string $file): string
{
    return dirname(__DIR__) . '/data/' . ltrim($file, '/');
}

function load_app_data(string $file): array
{
    $path = app_data_path($file);

    if (!is_file($path)) {
        return [];
    }

    $data = require $path;

    return is_array($data) ? $data : [];
}

