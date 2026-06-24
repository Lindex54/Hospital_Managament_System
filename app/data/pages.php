<?php

declare(strict_types=1);

return [
    /*
     |---------------------------------------------------------------------------
     | Route registry
     |---------------------------------------------------------------------------
     | Each page declares the title used in the browser, the view file to render,
     | and the permission required to access it from the backend.
     */
    'dashboard' => ['title' => 'Dashboard', 'view' => 'dashboard', 'permission' => 'dashboard.view'],
    'patients' => ['title' => 'Patients', 'view' => 'module', 'permission' => 'patients.view'],
    'doctors' => ['title' => 'Doctors', 'view' => 'module', 'permission' => 'users.manage'],
    'outpatient' => ['title' => 'Outpatient', 'view' => 'module', 'permission' => 'outpatient.view'],
    'inpatient' => ['title' => 'Inpatient', 'view' => 'module', 'permission' => 'inpatient.view'],
    'appointments' => ['title' => 'Appointments', 'view' => 'module', 'permission' => 'appointments.view'],
    'consultations' => ['title' => 'Consultations', 'view' => 'module', 'permission' => 'consultations.view'],
    'emergency' => ['title' => 'Emergency', 'view' => 'module', 'permission' => 'emergency.view'],
    'triage' => ['title' => 'Triage', 'view' => 'module', 'permission' => 'emergency.triage'],
    'emergency-triage' => ['title' => 'Emergency Triage', 'view' => 'module', 'permission' => 'emergency.triage'],
    'laboratory' => ['title' => 'Laboratory', 'view' => 'module', 'permission' => 'laboratory.view'],
    'lab-requests' => ['title' => 'Laboratory Requests', 'view' => 'module', 'permission' => 'laboratory.requests'],
    'sample-collection' => ['title' => 'Sample Collection', 'view' => 'module', 'permission' => 'laboratory.sample_collection'],
    'lab-results' => ['title' => 'Lab Results', 'view' => 'module', 'permission' => 'laboratory.results'],
    'lab-reports' => ['title' => 'Lab Reports', 'view' => 'module', 'permission' => 'laboratory.reports'],
    'radiology' => ['title' => 'Radiology', 'view' => 'module', 'permission' => 'radiology.view'],
    'radiology-requests' => ['title' => 'Radiology Requests', 'view' => 'module', 'permission' => 'radiology.requests'],
    'radiology-results' => ['title' => 'Radiology Results', 'view' => 'module', 'permission' => 'radiology.results'],
    'imaging-reports' => ['title' => 'Imaging Reports', 'view' => 'module', 'permission' => 'radiology.reports'],
    'pharmacy' => ['title' => 'Pharmacy', 'view' => 'module', 'permission' => 'pharmacy.view'],
    'prescriptions' => ['title' => 'Prescriptions', 'view' => 'module', 'permission' => 'pharmacy.prescriptions'],
    'medicine-dispensing' => ['title' => 'Medicine Dispensing', 'view' => 'module', 'permission' => 'pharmacy.dispense'],
    'pharmacy-stock' => ['title' => 'Pharmacy Stock', 'view' => 'module', 'permission' => 'pharmacy.stock'],
    'pharmacy-reports' => ['title' => 'Pharmacy Reports', 'view' => 'module', 'permission' => 'pharmacy.reports'],
    'inpatient-admission' => ['title' => 'Inpatient Admission', 'view' => 'module', 'permission' => 'inpatient.admit'],
    'discharge-referral' => ['title' => 'Discharge / Referral', 'view' => 'module', 'permission' => 'inpatient.discharge'],
    'vitals' => ['title' => 'Vitals', 'view' => 'module', 'permission' => 'vitals.view'],
    'ward-beds' => ['title' => 'Ward & Beds', 'view' => 'module', 'permission' => 'wards.view'],
    'nursing' => ['title' => 'Nursing', 'view' => 'module', 'permission' => 'nursing.view'],
    'nursing-notes' => ['title' => 'Nursing Notes', 'view' => 'module', 'permission' => 'nursing.view'],
    'billing' => ['title' => 'Billing', 'view' => 'module', 'permission' => 'billing.view'],
    'insurance' => ['title' => 'Insurance', 'view' => 'module', 'permission' => 'insurance.view'],
    'reports' => ['title' => 'Reports', 'view' => 'module', 'permission' => 'reports.view'],
    'queue' => ['title' => 'Queue', 'view' => 'module', 'permission' => 'queue.view'],
    'noticeboard' => ['title' => 'Noticeboard', 'view' => 'module', 'permission' => 'noticeboard.view'],
    'settings' => ['title' => 'Settings', 'view' => 'module', 'permission' => 'settings.view'],
    'users' => ['title' => 'Users & Roles', 'view' => 'module', 'permission' => 'users.manage'],
];
