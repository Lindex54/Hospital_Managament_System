<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/helpers/functions.php';
require_once dirname(__DIR__) . '/app/helpers/auth.php';
require_once dirname(__DIR__) . '/app/helpers/data.php';

/*
|--------------------------------------------------------------------------
| Central page registry
|--------------------------------------------------------------------------
| Every screen that can be opened through public/index.php is registered in
| one place. This lets us keep the title, view mapping, and permission rule
| together instead of scattering access logic across many files.
*/
$pages = load_app_data('pages.php');

$requestedPage = strtolower(trim((string) ($_GET['page'] ?? 'dashboard')));
$currentPage = array_key_exists($requestedPage, $pages) ? $requestedPage : 'dashboard';
$pageTitle = $pages[$currentPage]['title'];
$requiredPermission = $pages[$currentPage]['permission'] ?? null;

/*
|--------------------------------------------------------------------------
| Backend permission gate
|--------------------------------------------------------------------------
| Sidebar filtering is only the first layer. The route itself must also check
| the required permission so users cannot bypass access rules by typing a URL
| directly into the browser.
*/
if ($requiredPermission !== null && !hasPermission($requiredPermission)) {
    http_response_code(403);
    $pageTitle = 'Access Denied';
    $viewFile = dirname(__DIR__) . '/app/views/errors/403.php';
} else {
    /*
    |--------------------------------------------------------------------------
    | View resolution
    |--------------------------------------------------------------------------
    | The dashboard keeps its dedicated layout, while all other static module
    | pages are rendered through the shared module page template.
    */
    $viewFile = $pages[$currentPage]['view'] === 'dashboard'
        ? dirname(__DIR__) . '/app/views/dashboard.php'
        : dirname(__DIR__) . '/app/views/module-page.php';
}

require_once dirname(__DIR__) . '/app/includes/header.php';
require_once dirname(__DIR__) . '/app/includes/sidebar.php';
require_once dirname(__DIR__) . '/app/includes/topbar.php';
require_once $viewFile;
require_once dirname(__DIR__) . '/app/includes/footer.php';
