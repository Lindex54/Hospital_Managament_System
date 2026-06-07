<?php

declare(strict_types=1);

return [
    /*
     |---------------------------------------------------------------------------
     | Sidebar groups
     |---------------------------------------------------------------------------
     | Each item is filtered at render time using its required permission.
     */
    [
        'label' => 'Overview',
        'items' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'url' => 'dashboard', 'permission' => 'dashboard.view'],
        ],
    ],
    [
        'label' => 'Clinical',
        'items' => [
            ['label' => 'Patients', 'icon' => 'patients', 'url' => 'patients', 'permission' => 'patients.view'],
            ['label' => 'Outpatient', 'icon' => 'outpatient', 'url' => 'outpatient', 'permission' => 'outpatient.view'],
            ['label' => 'Inpatient', 'icon' => 'inpatient', 'url' => 'inpatient', 'permission' => 'inpatient.view'],
            ['label' => 'Appointments', 'icon' => 'appointments', 'url' => 'appointments', 'permission' => 'appointments.view'],
            ['label' => 'Consultations', 'icon' => 'consultations', 'url' => 'consultations', 'permission' => 'consultations.view'],
            ['label' => 'Emergency', 'icon' => 'emergency', 'url' => 'emergency', 'permission' => 'emergency.view'],
            ['label' => 'Triage', 'icon' => 'triage', 'url' => 'triage', 'permission' => 'emergency.triage'],
            ['label' => 'Emergency Triage', 'icon' => 'triage', 'url' => 'emergency-triage', 'permission' => 'emergency.triage'],
            ['label' => 'Vitals', 'icon' => 'vitals', 'url' => 'vitals', 'permission' => 'vitals.view'],
            ['label' => 'Ward & Beds', 'icon' => 'ward-beds', 'url' => 'ward-beds', 'permission' => 'wards.view'],
            ['label' => 'Nursing Notes', 'icon' => 'nursing', 'url' => 'nursing-notes', 'permission' => 'nursing.view'],
            ['label' => 'Inpatient Admission', 'icon' => 'inpatient', 'url' => 'inpatient-admission', 'permission' => 'inpatient.admit'],
            ['label' => 'Discharge / Referral', 'icon' => 'discharge', 'url' => 'discharge-referral', 'permission' => 'inpatient.discharge'],
        ],
    ],
    [
        'label' => 'Diagnostics',
        'items' => [
            ['label' => 'Laboratory Requests', 'icon' => 'lab-requests', 'url' => 'lab-requests', 'permission' => 'laboratory.requests'],
            ['label' => 'Sample Collection', 'icon' => 'sample-collection', 'url' => 'sample-collection', 'permission' => 'laboratory.sample_collection'],
            ['label' => 'Lab Results', 'icon' => 'lab-results', 'url' => 'lab-results', 'permission' => 'laboratory.results'],
            ['label' => 'Lab Reports', 'icon' => 'lab-reports', 'url' => 'lab-reports', 'permission' => 'laboratory.reports'],
            ['label' => 'Radiology Requests', 'icon' => 'radiology-requests', 'url' => 'radiology-requests', 'permission' => 'radiology.requests'],
            ['label' => 'Radiology Results', 'icon' => 'radiology-results', 'url' => 'radiology-results', 'permission' => 'radiology.results'],
            ['label' => 'Imaging Reports', 'icon' => 'imaging-reports', 'url' => 'imaging-reports', 'permission' => 'radiology.reports'],
        ],
    ],
    [
        'label' => 'Pharmacy',
        'items' => [
            ['label' => 'Prescriptions', 'icon' => 'prescriptions', 'url' => 'prescriptions', 'permission' => 'pharmacy.prescriptions'],
            ['label' => 'Medicine Dispensing', 'icon' => 'medicine-dispensing', 'url' => 'medicine-dispensing', 'permission' => 'pharmacy.dispense'],
            ['label' => 'Pharmacy Stock', 'icon' => 'pharmacy-stock', 'url' => 'pharmacy-stock', 'permission' => 'pharmacy.stock'],
            ['label' => 'Pharmacy Reports', 'icon' => 'pharmacy-reports', 'url' => 'pharmacy-reports', 'permission' => 'pharmacy.reports'],
        ],
    ],
    [
        'label' => 'Administration',
        'items' => [
            ['label' => 'Billing', 'icon' => 'billing', 'url' => 'billing', 'permission' => 'billing.view'],
            ['label' => 'Insurance', 'icon' => 'insurance', 'url' => 'insurance', 'permission' => 'insurance.view'],
            ['label' => 'Reports', 'icon' => 'reports', 'url' => 'reports', 'permission' => 'reports.view'],
            ['label' => 'Queue', 'icon' => 'queue', 'url' => 'queue', 'permission' => 'queue.view'],
            ['label' => 'Noticeboard', 'icon' => 'noticeboard', 'url' => 'noticeboard', 'permission' => 'noticeboard.view'],
            ['label' => 'Settings', 'icon' => 'settings', 'url' => 'settings', 'permission' => 'settings.view'],
            ['label' => 'Users & Roles', 'icon' => 'users', 'url' => 'users', 'permission' => 'users.manage'],
        ],
    ],
];

