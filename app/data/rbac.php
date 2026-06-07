<?php

declare(strict_types=1);

return [
    /*
     |---------------------------------------------------------------------------
     | Roles seeded for the first RBAC rollout
     |---------------------------------------------------------------------------
     | Accountant and Receptionist are intentionally excluded for now.
     */
    'roles' => [
        ['name' => 'Administrator', 'description' => 'Full system administration access.'],
        ['name' => 'Doctor', 'description' => 'Clinical consultation and treatment access.'],
        ['name' => 'Emergency Doctor', 'description' => 'Emergency triage, consultation, and admission access.'],
        ['name' => 'Nurse', 'description' => 'Nursing, vitals, and inpatient support access.'],
        ['name' => 'Laboratory Technician', 'description' => 'Laboratory request, sample, and result access.'],
        ['name' => 'Radiologist', 'description' => 'Radiology request, results, and imaging report access.'],
        ['name' => 'Pharmacist', 'description' => 'Prescription, dispensing, and pharmacy inventory access.'],
    ],

    /*
     |---------------------------------------------------------------------------
     | Permission catalog
     |---------------------------------------------------------------------------
     | Each permission is stored once and later attached to one or more roles.
     | The "module" field helps keep related permissions grouped in the database.
     */
    'permissions' => [
        ['name' => 'dashboard.view', 'label' => 'View Dashboard', 'module' => 'dashboard', 'description' => 'Access the main dashboard overview.'],
        ['name' => 'patients.view', 'label' => 'View Patients', 'module' => 'patients', 'description' => 'Access patient records.'],
        ['name' => 'patients.create', 'label' => 'Create Patients', 'module' => 'patients', 'description' => 'Create new patient records.'],
        ['name' => 'patients.edit', 'label' => 'Edit Patients', 'module' => 'patients', 'description' => 'Update existing patient records.'],
        ['name' => 'outpatient.view', 'label' => 'View Outpatient', 'module' => 'outpatient', 'description' => 'Access outpatient workflows.'],
        ['name' => 'outpatient.create', 'label' => 'Create Outpatient Visit', 'module' => 'outpatient', 'description' => 'Create outpatient visits.'],
        ['name' => 'inpatient.view', 'label' => 'View Inpatient', 'module' => 'inpatient', 'description' => 'Access inpatient workflows.'],
        ['name' => 'inpatient.admit', 'label' => 'Admit Inpatient', 'module' => 'inpatient', 'description' => 'Admit a patient to inpatient care.'],
        ['name' => 'inpatient.discharge', 'label' => 'Discharge Inpatient', 'module' => 'inpatient', 'description' => 'Discharge or refer inpatients.'],
        ['name' => 'appointments.view', 'label' => 'View Appointments', 'module' => 'appointments', 'description' => 'Access appointment schedules.'],
        ['name' => 'consultations.view', 'label' => 'View Consultations', 'module' => 'consultations', 'description' => 'Access consultation records.'],
        ['name' => 'consultations.create', 'label' => 'Create Consultations', 'module' => 'consultations', 'description' => 'Create consultation records.'],
        ['name' => 'emergency.view', 'label' => 'View Emergency', 'module' => 'emergency', 'description' => 'Access emergency workflows.'],
        ['name' => 'emergency.create', 'label' => 'Create Emergency Case', 'module' => 'emergency', 'description' => 'Register emergency cases.'],
        ['name' => 'emergency.triage', 'label' => 'Emergency Triage', 'module' => 'emergency', 'description' => 'Triage emergency cases.'],
        ['name' => 'emergency.consult', 'label' => 'Emergency Consultation', 'module' => 'emergency', 'description' => 'Consult emergency cases.'],
        ['name' => 'emergency.admit', 'label' => 'Emergency Admission', 'module' => 'emergency', 'description' => 'Admit emergency cases to inpatient care.'],
        ['name' => 'emergency.discharge', 'label' => 'Emergency Discharge', 'module' => 'emergency', 'description' => 'Discharge emergency patients.'],
        ['name' => 'emergency.refer', 'label' => 'Emergency Referral', 'module' => 'emergency', 'description' => 'Refer emergency patients out.'],
        ['name' => 'vitals.view', 'label' => 'View Vitals', 'module' => 'vitals', 'description' => 'Access recorded vitals.'],
        ['name' => 'vitals.create', 'label' => 'Record Vitals', 'module' => 'vitals', 'description' => 'Record patient vitals.'],
        ['name' => 'wards.view', 'label' => 'View Wards & Beds', 'module' => 'wards', 'description' => 'Access ward and bed management.'],
        ['name' => 'nursing.view', 'label' => 'View Nursing Notes', 'module' => 'nursing', 'description' => 'Access nursing notes.'],
        ['name' => 'nursing.create', 'label' => 'Create Nursing Notes', 'module' => 'nursing', 'description' => 'Create nursing notes.'],
        ['name' => 'laboratory.view', 'label' => 'View Laboratory', 'module' => 'laboratory', 'description' => 'Access laboratory module overview.'],
        ['name' => 'laboratory.requests', 'label' => 'Laboratory Requests', 'module' => 'laboratory', 'description' => 'Access laboratory requests.'],
        ['name' => 'laboratory.sample_collection', 'label' => 'Sample Collection', 'module' => 'laboratory', 'description' => 'Access sample collection workflows.'],
        ['name' => 'laboratory.results', 'label' => 'Laboratory Results', 'module' => 'laboratory', 'description' => 'Access laboratory results.'],
        ['name' => 'laboratory.reports', 'label' => 'Laboratory Reports', 'module' => 'laboratory', 'description' => 'Access laboratory reports.'],
        ['name' => 'radiology.view', 'label' => 'View Radiology', 'module' => 'radiology', 'description' => 'Access radiology module overview.'],
        ['name' => 'radiology.requests', 'label' => 'Radiology Requests', 'module' => 'radiology', 'description' => 'Access radiology requests.'],
        ['name' => 'radiology.results', 'label' => 'Radiology Results', 'module' => 'radiology', 'description' => 'Access radiology results.'],
        ['name' => 'radiology.reports', 'label' => 'Imaging Reports', 'module' => 'radiology', 'description' => 'Access imaging reports.'],
        ['name' => 'pharmacy.view', 'label' => 'View Pharmacy', 'module' => 'pharmacy', 'description' => 'Access pharmacy module overview.'],
        ['name' => 'pharmacy.prescriptions', 'label' => 'Prescriptions', 'module' => 'pharmacy', 'description' => 'Access prescriptions.'],
        ['name' => 'pharmacy.dispense', 'label' => 'Medicine Dispensing', 'module' => 'pharmacy', 'description' => 'Dispense medicines.'],
        ['name' => 'pharmacy.stock', 'label' => 'Pharmacy Stock', 'module' => 'pharmacy', 'description' => 'Manage pharmacy stock.'],
        ['name' => 'pharmacy.reports', 'label' => 'Pharmacy Reports', 'module' => 'pharmacy', 'description' => 'Access pharmacy reports.'],
        ['name' => 'billing.view', 'label' => 'View Billing', 'module' => 'billing', 'description' => 'Access billing pages.'],
        ['name' => 'insurance.view', 'label' => 'View Insurance', 'module' => 'insurance', 'description' => 'Access insurance pages.'],
        ['name' => 'queue.view', 'label' => 'View Queue', 'module' => 'queue', 'description' => 'Access queue management pages.'],
        ['name' => 'noticeboard.view', 'label' => 'View Noticeboard', 'module' => 'noticeboard', 'description' => 'Access internal notices.'],
        ['name' => 'reports.view', 'label' => 'View Reports', 'module' => 'reports', 'description' => 'Access reporting pages.'],
        ['name' => 'settings.view', 'label' => 'View Settings', 'module' => 'settings', 'description' => 'Access application settings.'],
        ['name' => 'users.manage', 'label' => 'Manage Users & Roles', 'module' => 'users', 'description' => 'Manage user accounts, roles, and permissions.'],
    ],

    /*
     |---------------------------------------------------------------------------
     | Role to permission mapping
     |---------------------------------------------------------------------------
     | Administrator uses the wildcard marker and is granted every permission.
     */
    'role_permissions' => [
        'Administrator' => ['*'],
        'Doctor' => [
            'dashboard.view',
            'patients.view',
            'outpatient.view',
            'inpatient.view',
            'appointments.view',
            'consultations.view',
            'laboratory.results',
            'radiology.results',
            'pharmacy.prescriptions',
        ],
        'Emergency Doctor' => [
            'dashboard.view',
            'patients.view',
            'emergency.view',
            'emergency.triage',
            'consultations.view',
            'laboratory.requests',
            'laboratory.results',
            'radiology.requests',
            'radiology.results',
            'pharmacy.prescriptions',
            'inpatient.admit',
            'inpatient.discharge',
        ],
        'Nurse' => [
            'dashboard.view',
            'patients.view',
            'vitals.view',
            'vitals.create',
            'inpatient.view',
            'wards.view',
            'nursing.view',
            'nursing.create',
            'emergency.triage',
        ],
        'Laboratory Technician' => [
            'dashboard.view',
            'laboratory.requests',
            'laboratory.sample_collection',
            'laboratory.results',
            'laboratory.reports',
        ],
        'Radiologist' => [
            'dashboard.view',
            'radiology.requests',
            'radiology.results',
            'radiology.reports',
        ],
        'Pharmacist' => [
            'dashboard.view',
            'pharmacy.prescriptions',
            'pharmacy.dispense',
            'pharmacy.stock',
            'pharmacy.reports',
        ],
    ],
];

