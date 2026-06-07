<?php

declare(strict_types=1);

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__, 2));
}

if (!defined('APP_CONFIG')) {
    define('APP_CONFIG', [
        'app_name' => 'Hospital Management System',
        'base_url' => 'http://localhost/hospital-management-system/public',
        'timezone' => 'Africa/Kampala',
        'environment' => 'development',
    ]);
}

date_default_timezone_set(APP_CONFIG['timezone']);

