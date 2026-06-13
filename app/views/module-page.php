<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/module-pages.php';
require_once dirname(__DIR__) . '/helpers/clinical-forms.php';
require_once __DIR__ . '/partials/clinical-modals.php';
require_once dirname(__DIR__) . '/handlers/visit-handler.php';
require_once dirname(__DIR__) . '/handlers/admission-handler.php';
require_once dirname(__DIR__) . '/handlers/appointment-handler.php';
require_once dirname(__DIR__) . '/handlers/consultation-handler.php';

$currentPage = $currentPage ?? '';
$pageFlash = null;

if ($currentPage === 'outpatient') {
    $pageFlash = visit_get_flash();
}

if ($currentPage === 'inpatient') {
    $pageFlash = admission_get_flash();
}

if ($currentPage === 'appointments') {
    $pageFlash = appointment_get_flash();
}

if ($currentPage === 'consultations') {
    $pageFlash = consultation_get_flash();
}

if (hospital_module_modal_config($currentPage) !== null) {
    $pageScripts = [versioned_asset_url('js/clinical-forms.js')];
}

if (is_array($pageFlash)) {
    $flashClass = ($pageFlash['type'] ?? '') === 'success' ? 'badge badge-success' : 'badge badge-danger';
    ?>
    <section class="module-page-shell">
        <div class="panel">
            <span class="<?= e($flashClass); ?>"><?= e(ucfirst((string) ($pageFlash['type'] ?? 'notice'))); ?></span>
            <p class="mt-3 text-sm text-hospital-secondary"><?= e((string) ($pageFlash['message'] ?? '')); ?></p>
        </div>
    </section>
    <?php
}

render_hospital_module_page($currentPage ?? '');
