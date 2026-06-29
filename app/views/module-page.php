<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/module-pages.php';
require_once dirname(__DIR__) . '/helpers/clinical-forms.php';
require_once __DIR__ . '/partials/clinical-modals.php';
require_once dirname(__DIR__) . '/handlers/visit-handler.php';
require_once dirname(__DIR__) . '/handlers/patient-handler.php';
require_once dirname(__DIR__) . '/handlers/doctor-handler.php';
require_once dirname(__DIR__) . '/handlers/admission-handler.php';
require_once dirname(__DIR__) . '/handlers/appointment-handler.php';
require_once dirname(__DIR__) . '/handlers/consultation-handler.php';
require_once dirname(__DIR__) . '/handlers/emergency-handler.php';
require_once dirname(__DIR__) . '/handlers/triage-handler.php';
require_once dirname(__DIR__) . '/handlers/vitals-handler.php';
require_once dirname(__DIR__) . '/handlers/ward-handler.php';
require_once dirname(__DIR__) . '/handlers/nursing-handler.php';
require_once dirname(__DIR__) . '/handlers/discharge-handler.php';

$currentPage = $currentPage ?? '';
$pageFlash = null;

if ($currentPage === 'outpatient') {
    $pageFlash = visit_get_flash();
}

if ($currentPage === 'patients') {
    $pageFlash = patient_get_flash();
}

if ($currentPage === 'doctors') {
    $pageFlash = doctor_get_flash();
}

if ($currentPage === 'wards') {
    $pageFlash = ward_get_flash();
}

if ($currentPage === 'inpatient') {
    $pageFlash = admission_get_flash();
}

if ($currentPage === 'inpatient-admission') {
    $pageFlash = admission_get_flash();
}

if ($currentPage === 'appointments') {
    $pageFlash = appointment_get_flash();
}

if ($currentPage === 'consultations') {
    $pageFlash = consultation_get_flash();
}

if ($currentPage === 'emergency') {
    $pageFlash = emergency_get_flash();
}

if ($currentPage === 'triage' || $currentPage === 'emergency-triage') {
    $pageFlash = triage_get_flash();
}

if ($currentPage === 'vitals') {
    $pageFlash = vitals_get_flash();
}

if ($currentPage === 'ward-beds') {
    $pageFlash = ward_get_flash();
}

if ($currentPage === 'nursing-notes') {
    $pageFlash = nursing_get_flash();
}

if ($currentPage === 'discharge-referral') {
    $pageFlash = discharge_get_flash();
}

if (hospital_module_modal_config($currentPage) !== null) {
    $pageScripts = [versioned_asset_url('js/clinical-forms.js')];
}

if (is_array($pageFlash)) {
    ?>
    <div
        data-flash-message
        data-flash-type="<?= e((string) ($pageFlash['type'] ?? 'notice')); ?>"
        data-flash-text="<?= e((string) ($pageFlash['message'] ?? '')); ?>"
        hidden
    ></div>
    <?php
}

render_hospital_module_page($currentPage ?? '');
