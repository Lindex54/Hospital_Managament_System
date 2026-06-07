<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/helpers/functions.php';

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
$viewFile = dirname(__DIR__) . '/app/views/dashboard.php';

require_once dirname(__DIR__) . '/app/includes/header.php';
require_once dirname(__DIR__) . '/app/includes/sidebar.php';
require_once dirname(__DIR__) . '/app/includes/topbar.php';
require_once $viewFile;
require_once dirname(__DIR__) . '/app/includes/footer.php';

