<?php

declare(strict_types=1);

return [
    'metrics' => [
        [
            'label' => 'Total Patients',
            'value' => '2,543',
            'trend' => '12.5% from last month',
            'trend_class' => 'metric-trend-up',
            'icon_bg' => 'bg-hospital-patients',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/></svg>',
            'spark' => '#2563EB',
            'points' => '2,32 18,20 34,26 50,17 66,25 82,16 98,24 114,12 130,11 146,7 162,9 178,3',
        ],
        [
            'label' => 'OPD Visits (Today)',
            'value' => '128',
            'trend' => '8.3% from yesterday',
            'trend_class' => 'metric-trend-up',
            'icon_bg' => 'bg-hospital-outpatient',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M5 5l4 4"/><path d="M19 5l-4 4"/><path d="M5 19l4-4"/><path d="M19 19l-4-4"/></svg>',
            'spark' => '#22C55E',
            'points' => '2,24 18,22 34,11 50,14 66,6 82,17 98,22 114,9 130,15 146,1 162,5 178,12',
        ],
        [
            'label' => 'IPD Patients',
            'value' => '86',
            'trend' => '4.4% from yesterday',
            'trend_class' => 'metric-trend-down',
            'icon_bg' => 'bg-[#8B5CF6]',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18"/><path d="M5 12V8h6a2 2 0 0 1 2 2v2"/><path d="M21 16v-5a2 2 0 0 0-2-2h-4"/><path d="M5 12v4"/><path d="M19 12v4"/></svg>',
            'spark' => '#A855F7',
            'points' => '2,25 18,26 34,24 50,10 66,22 82,15 98,20 114,8 130,22 146,15 162,0 178,12',
        ],
        [
            'label' => 'Lab Tests (Today)',
            'value' => '64',
            'trend' => '15.2% from yesterday',
            'trend_class' => 'metric-trend-up',
            'icon_bg' => 'bg-hospital-laboratory',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 2v6l-5 9a3 3 0 0 0 2.6 4.5h8.8A3 3 0 0 0 19 17l-5-9V2"/><path d="M8 12h8"/></svg>',
            'spark' => '#F97316',
            'points' => '2,20 18,21 34,9 50,18 66,12 82,5 98,15 114,20 130,4 146,11 162,6 178,10',
        ],
        [
            'label' => 'Revenue (Today)',
            'value' => '$8,540',
            'trend' => '10.7% from yesterday',
            'trend_class' => 'metric-trend-up',
            'icon_bg' => 'bg-hospital-billing',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14.5a3.5 3.5 0 0 1 0 7H6"/><path d="M12 1v22"/></svg>',
            'spark' => '#06B6D4',
            'points' => '2,26 18,20 34,34 50,18 66,29 82,33 98,25 114,12 130,26 146,28 162,4 178,12',
        ],
    ],
    'appointments' => [
        ['09:00 AM', 'John Doe', 'Dr. Sarah Johnson', 'Cardiology', 'OPD', 'Completed', 'status-success'],
        ['09:30 AM', 'Mary Smith', 'Dr. Michael Brown', 'General Medicine', 'OPD', 'Completed', 'status-success'],
        ['10:00 AM', 'Robert Davis', 'Dr. Sarah Johnson', 'Cardiology', 'OPD', 'Upcoming', 'status-warning'],
        ['10:30 AM', 'Linda Wilson', 'Dr. Emily Clark', 'Orthopedics', 'OPD', 'Upcoming', 'status-warning'],
        ['11:00 AM', 'William Taylor', 'Dr. Michael Brown', 'General Medicine', 'OPD', 'Upcoming', 'status-warning'],
    ],
    'ipdPatients' => [
        ['James Anderson', 'Ward 1 / Bed 3', 'May 22, 2025', 'Stable', 'status-success'],
        ['Patricia Johnson', 'Ward 2 / Bed 1', 'May 23, 2025', 'Stable', 'status-success'],
        ['Michael Clark', 'Ward 1 / Bed 7', 'May 21, 2025', 'Under Care', 'status-warning'],
        ['Barbara White', 'Ward 3 / Bed 2', 'May 24, 2025', 'Critical', 'status-danger'],
        ['David Harris', 'Ward 2 / Bed 5', 'May 20, 2025', 'Stable', 'status-success'],
    ],
    'services' => [
        ['Consultation', 1524, 78, 'bg-hospital-patients'],
        ['Laboratory Tests', 976, 58, 'bg-hospital-outpatient'],
        ['Pharmacy Sales', 865, 49, 'bg-[#8B5CF6]'],
        ['Radiology', 450, 34, 'bg-hospital-laboratory'],
        ['Admissions', 230, 18, 'bg-hospital-danger'],
    ],
    'notifications' => [
        ['Emergency Case', 'New emergency case registered', '10 min ago', 'bg-red-100 text-red-500', '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.5 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.5a2 2 0 0 0-3.4 0z"/></svg>'],
        ['Lab Result Ready', '5 lab results are ready to review', '25 min ago', 'bg-blue-100 text-blue-600', '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 2v6l-5 9a3 3 0 0 0 2.6 4.5h8.8A3 3 0 0 0 19 17l-5-9V2"/><path d="M8 12h8"/></svg>'],
        ['Low Stock Alert', 'Paracetamol stock is running low', '1 hr ago', 'bg-green-100 text-green-600', '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 7h4"/><path d="M12 5v4"/><rect x="5" y="7" width="14" height="13" rx="3"/><path d="M9 14h6"/></svg>'],
        ['Appointment Reminder', '12 appointments scheduled today', '2 hr ago', 'bg-orange-100 text-orange-500', '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M3 10h18"/></svg>'],
    ],
];

