<?php

declare(strict_types=1);

return [
    /*
     |---------------------------------------------------------------------------
     | Landing page meta
     |---------------------------------------------------------------------------
     | These labels are kept separate from the view so later we can localize or
     | replace marketing copy without rewriting the layout structure.
     */
    'meta' => [
        'title' => 'Hospital Management System',
        'eyebrow' => 'Connected care. Clear records. Faster service.',
        'headline' => 'Manage hospital operations from one clear, professional platform.',
        'description' => 'Coordinate patient registration, outpatient visits, inpatient admissions, diagnostics, pharmacy, billing and reports in one readable hospital workspace.',
        'topbar_text' => '24/7 Hospital Operations • OPD, IPD, Pharmacy, Laboratory & Billing',
        'support_text' => 'Emergency Support: +256 700 000 000',
    ],

    /*
     |---------------------------------------------------------------------------
     | Hero statistics
     |---------------------------------------------------------------------------
     | Short statistics make the hero feel active while still staying static for
     | now. Later these values can be loaded from real dashboard services.
     */
    'hero_stats' => [
        ['value' => 'OPD', 'label' => 'Outpatient Care'],
        ['value' => 'IPD', 'label' => 'Inpatient Flow'],
        ['value' => '24/7', 'label' => 'Hospital Access'],
    ],

    'hero_panel' => [
        'label' => 'Today\'s patient flow',
        'value' => '128 Patients',
        'breakdown' => [
            ['value' => '94', 'label' => 'OPD', 'tone' => 'patients'],
            ['value' => '34', 'label' => 'IPD', 'tone' => 'outpatient'],
        ],
    ],

    'features' => [
        [
            'step' => '01',
            'tone' => 'patients',
            'title' => 'Unified Patient Records',
            'description' => 'Keep one patient profile for every visit, consultation, admission, laboratory request and prescription across the hospital.',
        ],
        [
            'step' => '02',
            'tone' => 'info',
            'title' => 'Outpatient Coordination',
            'description' => 'Track registration, triage, consultation, investigations, billing and follow-up visits from one shared workflow.',
        ],
        [
            'step' => '03',
            'tone' => 'success',
            'title' => 'Inpatient Management',
            'description' => 'Organize admissions, wards, beds, nursing notes, treatment updates and discharge planning without losing patient history.',
        ],
        [
            'step' => '04',
            'tone' => 'reports',
            'title' => 'Billing & Oversight',
            'description' => 'Monitor invoices, services, medicine charges, payment status and hospital reporting from a clean finance trail.',
        ],
    ],

    'workflow' => [
        [
            'title' => '1. Registration & Visit Creation',
            'description' => 'Create the patient record once, then open each hospital encounter as a new visit linked to the correct department and clinician.',
        ],
        [
            'title' => '2. Clinical Review & Requests',
            'description' => 'Capture vitals, consultations, laboratory requests, radiology requests, prescriptions and treatment notes inside the same care journey.',
        ],
        [
            'title' => '3. Admission, Monitoring & Discharge',
            'description' => 'Convert qualifying visits into admissions, assign wards and beds, document inpatient care, then discharge or refer with a proper summary.',
        ],
    ],

    'modules' => [
        ['title' => 'Patient Registration', 'description' => 'Patient profiles, visit creation, appointment booking and identity tracking.'],
        ['title' => 'Clinical Consultation', 'description' => 'Vitals, doctor notes, diagnoses, treatment plans and referrals.'],
        ['title' => 'Laboratory Services', 'description' => 'Requests, sample collection, test results and report review.'],
        ['title' => 'Radiology & Imaging', 'description' => 'Imaging requests, result review and report workflows.'],
        ['title' => 'Pharmacy Operations', 'description' => 'Prescriptions, dispensing, stock movement and medicine control.'],
        ['title' => 'Billing & Reports', 'description' => 'Invoices, payments, service summaries and management reporting.'],
    ],

    'dashboard_preview' => [
        ['value' => '42', 'label' => 'Patients in queue'],
        ['value' => '18', 'label' => 'Beds available'],
        ['value' => '27', 'label' => 'Lab results pending'],
        ['value' => 'UGX 4.8M', 'label' => 'Today\'s revenue'],
    ],

    'cta' => [
        'eyebrow' => 'Hospital Management System',
        'headline' => 'Reliable digital care coordination for modern hospitals.',
        'description' => 'Give hospital teams a cleaner, faster and more connected way to manage care delivery from registration to discharge.',
    ],
];
