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

if ($currentPage === 'outpatient') {
    require_once dirname(__DIR__) . '/app/handlers/visit-handler.php';
    handle_outpatient_visit_submission();
}

if ($currentPage === 'patients') {
    require_once dirname(__DIR__) . '/app/handlers/patient-handler.php';
    handle_patient_submission();
}

if ($currentPage === 'doctors') {
    require_once dirname(__DIR__) . '/app/handlers/doctor-handler.php';
    handle_doctor_submission();
}

if ($currentPage === 'wards') {
    require_once dirname(__DIR__) . '/app/handlers/ward-handler.php';
    handle_ward_submission();
}

if ($currentPage === 'inpatient') {
    require_once dirname(__DIR__) . '/app/handlers/admission-handler.php';
    handle_inpatient_admission_submission();
}

if ($currentPage === 'inpatient-admission') {
    require_once dirname(__DIR__) . '/app/handlers/admission-handler.php';
    handle_inpatient_admission_submission();
}

if ($currentPage === 'appointments') {
    require_once dirname(__DIR__) . '/app/handlers/appointment-handler.php';
    handle_appointment_submission();
}

if ($currentPage === 'consultations') {
    require_once dirname(__DIR__) . '/app/handlers/consultation-handler.php';
    handle_consultation_submission();
}

if ($currentPage === 'emergency') {
    require_once dirname(__DIR__) . '/app/handlers/emergency-handler.php';
    handle_emergency_submission();
}

if ($currentPage === 'triage' || $currentPage === 'emergency-triage') {
    require_once dirname(__DIR__) . '/app/handlers/triage-handler.php';
    handle_triage_submission();
}

if ($currentPage === 'vitals') {
    require_once dirname(__DIR__) . '/app/handlers/vitals-handler.php';
    handle_vitals_submission();
}

if ($currentPage === 'ward-beds') {
    require_once dirname(__DIR__) . '/app/handlers/ward-handler.php';
    handle_ward_submission();
}

if ($currentPage === 'nursing-notes') {
    require_once dirname(__DIR__) . '/app/handlers/nursing-handler.php';
    handle_nursing_submission();
}

if ($currentPage === 'discharge-referral') {
    require_once dirname(__DIR__) . '/app/handlers/discharge-handler.php';
    handle_discharge_submission();
}

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
    $viewKey = $pages[$currentPage]['view'];

    if ($viewKey === 'dashboard') {
        $viewFile = dirname(__DIR__) . '/app/views/dashboard.php';
    } elseif ($viewKey === 'module') {
        $viewFile = dirname(__DIR__) . '/app/views/module-page.php';
    } else {
        $viewFile = dirname(__DIR__) . '/app/views/' . ltrim($viewKey, '/') . '.php';
    }
}

require_once dirname(__DIR__) . '/app/includes/header.php';
require_once dirname(__DIR__) . '/app/includes/sidebar.php';
require_once dirname(__DIR__) . '/app/includes/topbar.php';
require_once $viewFile;
require_once dirname(__DIR__) . '/app/includes/footer.php';
