<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/helpers/functions.php';

$pages = [
    'dashboard' => ['title' => 'Dashboard', 'view' => dirname(__DIR__) . '/app/views/dashboard.php'],
    'patients' => ['title' => 'Patients', 'view' => dirname(__DIR__) . '/app/views/patients/index.php'],
    'outpatient' => ['title' => 'Outpatient', 'view' => dirname(__DIR__) . '/app/views/outpatient/index.php'],
    'inpatient' => ['title' => 'Inpatient', 'view' => dirname(__DIR__) . '/app/views/inpatient/index.php'],
    'appointments' => ['title' => 'Appointments', 'view' => dirname(__DIR__) . '/app/views/appointments/index.php'],
    'emergency' => ['title' => 'Emergency', 'view' => dirname(__DIR__) . '/app/views/emergency/index.php'],
    'laboratory' => ['title' => 'Laboratory', 'view' => dirname(__DIR__) . '/app/views/laboratory/index.php'],
    'radiology' => ['title' => 'Radiology', 'view' => dirname(__DIR__) . '/app/views/radiology/index.php'],
    'pharmacy' => ['title' => 'Pharmacy', 'view' => dirname(__DIR__) . '/app/views/pharmacy/index.php'],
    'billing' => ['title' => 'Billing', 'view' => dirname(__DIR__) . '/app/views/billing/index.php'],
    'insurance' => ['title' => 'Insurance', 'view' => dirname(__DIR__) . '/app/views/insurance/index.php'],
    'nursing' => ['title' => 'Nursing', 'view' => dirname(__DIR__) . '/app/views/nursing/index.php'],
    'reports' => ['title' => 'Reports', 'view' => dirname(__DIR__) . '/app/views/reports/index.php'],
    'queue' => ['title' => 'Queue', 'view' => dirname(__DIR__) . '/app/views/queue/index.php'],
    'noticeboard' => ['title' => 'Noticeboard', 'view' => dirname(__DIR__) . '/app/views/noticeboard/index.php'],
    'settings' => ['title' => 'Settings', 'view' => dirname(__DIR__) . '/app/views/settings/index.php'],
    'users' => ['title' => 'Users & Roles', 'view' => dirname(__DIR__) . '/app/views/users/index.php'],
];

$requestedPage = strtolower(trim((string) ($_GET['page'] ?? 'dashboard')));
$currentPage = array_key_exists($requestedPage, $pages) ? $requestedPage : 'dashboard';
$pageTitle = $pages[$currentPage]['title'];
$viewFile = $pages[$currentPage]['view'];

require_once dirname(__DIR__) . '/app/includes/header.php';
require_once dirname(__DIR__) . '/app/includes/sidebar.php';
require_once dirname(__DIR__) . '/app/includes/topbar.php';
require_once $viewFile;
require_once dirname(__DIR__) . '/app/includes/footer.php';
