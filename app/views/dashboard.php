<?php

declare(strict_types=1);

$metrics = [
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
];

$appointments = [
    ['09:00 AM', 'John Doe', 'Dr. Sarah Johnson', 'Cardiology', 'OPD', 'Completed', 'status-success'],
    ['09:30 AM', 'Mary Smith', 'Dr. Michael Brown', 'General Medicine', 'OPD', 'Completed', 'status-success'],
    ['10:00 AM', 'Robert Davis', 'Dr. Sarah Johnson', 'Cardiology', 'OPD', 'Upcoming', 'status-warning'],
    ['10:30 AM', 'Linda Wilson', 'Dr. Emily Clark', 'Orthopedics', 'OPD', 'Upcoming', 'status-warning'],
    ['11:00 AM', 'William Taylor', 'Dr. Michael Brown', 'General Medicine', 'OPD', 'Upcoming', 'status-warning'],
];

$ipdPatients = [
    ['James Anderson', 'Ward 1 / Bed 3', 'May 22, 2025', 'Stable', 'status-success'],
    ['Patricia Johnson', 'Ward 2 / Bed 1', 'May 23, 2025', 'Stable', 'status-success'],
    ['Michael Clark', 'Ward 1 / Bed 7', 'May 21, 2025', 'Under Care', 'status-warning'],
    ['Barbara White', 'Ward 3 / Bed 2', 'May 24, 2025', 'Critical', 'status-danger'],
    ['David Harris', 'Ward 2 / Bed 5', 'May 20, 2025', 'Stable', 'status-success'],
];

$services = [
    ['Consultation', 1524, 78, 'bg-hospital-patients'],
    ['Laboratory Tests', 976, 58, 'bg-hospital-outpatient'],
    ['Pharmacy Sales', 865, 49, 'bg-[#8B5CF6]'],
    ['Radiology', 450, 34, 'bg-hospital-laboratory'],
    ['Admissions', 230, 18, 'bg-hospital-danger'],
];

$notifications = [
    ['Emergency Case', 'New emergency case registered', '10 min ago', 'bg-red-100 text-red-500', '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.5 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.5a2 2 0 0 0-3.4 0z"/></svg>'],
    ['Lab Result Ready', '5 lab results are ready to review', '25 min ago', 'bg-blue-100 text-blue-600', '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 2v6l-5 9a3 3 0 0 0 2.6 4.5h8.8A3 3 0 0 0 19 17l-5-9V2"/><path d="M8 12h8"/></svg>'],
    ['Low Stock Alert', 'Paracetamol stock is running low', '1 hr ago', 'bg-green-100 text-green-600', '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 7h4"/><path d="M12 5v4"/><rect x="5" y="7" width="14" height="13" rx="3"/><path d="M9 14h6"/></svg>'],
    ['Appointment Reminder', '12 appointments scheduled today', '2 hr ago', 'bg-orange-100 text-orange-500', '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M3 10h18"/></svg>'],
];
?>

<section class="dashboard-grid">
    <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="mt-2 text-base font-medium text-hospital-secondary">Welcome back, Admin User! Here's what's happening in your hospital today.</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <button class="btn btn-secondary min-w-[190px] justify-between">
                <span class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-hospital-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M16 3v4"/>
                        <path d="M8 3v4"/>
                        <path d="M3 10h18"/>
                    </svg>
                    May 24, 2025
                </span>
                <svg class="h-4 w-4 text-hospital-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 9l6 6 6-6"/>
                </svg>
            </button>
            <button class="btn btn-primary min-w-[180px] gap-2">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3v12"/>
                    <path d="M7 10l5 5 5-5"/>
                    <path d="M5 21h14"/>
                </svg>
                Export Report
            </button>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5">
        <?php foreach ($metrics as $metric): ?>
            <article class="metric-card">
                <div class="flex items-start gap-4">
                    <div class="metric-icon <?= e($metric['icon_bg']); ?>">
                        <span class="h-7 w-7 [&_svg]:h-7 [&_svg]:w-7"><?= $metric['icon']; ?></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[15px] font-semibold text-hospital-secondary"><?= e($metric['label']); ?></p>
                        <h3 class="stat-number mt-2"><?= e($metric['value']); ?></h3>
                        <p class="mt-2 <?= e($metric['trend_class']); ?>"><?= e($metric['trend']); ?></p>
                    </div>
                </div>
                <svg class="mini-sparkline" viewBox="0 0 180 36" fill="none" aria-hidden="true">
                    <path d="M2 34 C 30 34, 150 34, 178 34" stroke="<?= e($metric['spark']); ?>" stroke-opacity="0.08" stroke-width="10" stroke-linecap="round"/>
                    <polyline points="<?= e($metric['points']); ?>" stroke="<?= e($metric['spark']); ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="grid gap-6 xl:grid-cols-2 2xl:grid-cols-[1.7fr_1fr_0.95fr]">
        <article class="chart-card">
            <div class="flex items-center justify-between gap-4">
                <h3 class="section-title">Monthly Overview</h3>
                <div class="flex flex-wrap gap-4">
                    <span class="chart-legend"><span class="legend-dot bg-hospital-patients"></span>OPD Visits</span>
                    <span class="chart-legend"><span class="legend-dot bg-hospital-outpatient"></span>IPD Admissions</span>
                    <span class="chart-legend"><span class="legend-dot bg-[#8B5CF6]"></span>Revenue (USD)</span>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <svg width="100%" height="220" viewBox="0 0 680 220" fill="none" aria-hidden="true">
                    <g stroke="#E8EEF6" stroke-width="1">
                        <path d="M56 24H650"/><path d="M56 58H650"/><path d="M56 92H650"/><path d="M56 126H650"/><path d="M56 160H650"/><path d="M56 194H650"/>
                        <path d="M56 24V194"/><path d="M108 24V194"/><path d="M160 24V194"/><path d="M212 24V194"/><path d="M264 24V194"/><path d="M316 24V194"/><path d="M368 24V194"/><path d="M420 24V194"/><path d="M472 24V194"/><path d="M524 24V194"/><path d="M576 24V194"/><path d="M628 24V194"/>
                    </g>
                    <g fill="#64748B" font-size="12" font-family="Inter, sans-serif">
                        <text x="14" y="198">0</text>
                        <text x="7" y="164">300</text>
                        <text x="7" y="130">600</text>
                        <text x="7" y="96">900</text>
                        <text x="0" y="62">1200</text>
                        <text x="0" y="28">1500</text>
                        <text x="286" y="214">Jan</text><text x="338" y="214">Feb</text><text x="390" y="214">Mar</text><text x="442" y="214">Apr</text><text x="494" y="214">May</text><text x="546" y="214">Jun</text>
                    </g>
                    <polyline points="58,120 110,112 162,88 214,84 266,110 318,89 370,72 422,76 474,97 526,86 578,101 630,88" stroke="#2563EB" stroke-width="3" fill="none"/>
                    <polyline points="58,170 110,176 162,160 214,156 266,171 318,148 370,144 422,148 474,168 526,152 578,162 630,149" stroke="#22C55E" stroke-width="3" fill="none"/>
                    <polyline points="58,144 110,145 162,130 214,126 266,134 318,118 370,106 422,114 474,130 526,136 578,145 630,130" stroke="#8B5CF6" stroke-width="3" fill="none"/>
                    <g fill="#2563EB"><circle cx="58" cy="120" r="4"/><circle cx="162" cy="88" r="4"/><circle cx="370" cy="72" r="4"/><circle cx="630" cy="88" r="4"/></g>
                    <g fill="#22C55E"><circle cx="58" cy="170" r="4"/><circle cx="214" cy="156" r="4"/><circle cx="370" cy="144" r="4"/><circle cx="630" cy="149" r="4"/></g>
                    <g fill="#8B5CF6"><circle cx="58" cy="144" r="4"/><circle cx="214" cy="126" r="4"/><circle cx="370" cy="106" r="4"/><circle cx="630" cy="130" r="4"/></g>
                </svg>
            </div>

            <div class="insight-grid">
                <div class="insight-card">
                    <p class="insight-label">Peak Month</p>
                    <p class="insight-value">March</p>
                    <p class="insight-copy">OPD activity reached its highest level, suggesting stronger outpatient demand and faster triage throughput.</p>
                </div>
                <div class="insight-card">
                    <p class="insight-label">Revenue Efficiency</p>
                    <p class="insight-value">$31.8K Avg</p>
                    <p class="insight-copy">Monthly revenue stayed ahead of admission growth, showing billing is capturing more value per encounter.</p>
                </div>
                <div class="insight-card">
                    <p class="insight-label">Operational Signal</p>
                    <p class="insight-value">82% Stable Flow</p>
                    <p class="insight-copy">Most months follow a steady pattern, which is good for staffing plans, bed forecasting, and supply preparation.</p>
                </div>
            </div>
        </article>

        <article class="chart-card">
            <h3 class="section-title">Patient Visit Distribution</h3>
            <div class="mt-5 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="donut-chart">
                    <div class="donut-center">
                        <p class="font-display text-[2rem] font-extrabold text-hospital-ink">2,543</p>
                        <p class="text-sm font-medium text-hospital-muted">Total</p>
                    </div>
                </div>
                <div class="space-y-5">
                    <div class="chart-legend justify-between gap-6"><span class="flex items-center gap-2"><span class="legend-dot bg-hospital-patients"></span>OPD</span><span class="font-semibold text-hospital-ink">65% (1,653)</span></div>
                    <div class="chart-legend justify-between gap-6"><span class="flex items-center gap-2"><span class="legend-dot bg-[#53C57B]"></span>IPD</span><span class="font-semibold text-hospital-ink">25% (635)</span></div>
                    <div class="chart-legend justify-between gap-6"><span class="flex items-center gap-2"><span class="legend-dot bg-[#FB923C]"></span>Emergency</span><span class="font-semibold text-hospital-ink">7% (178)</span></div>
                    <div class="chart-legend justify-between gap-6"><span class="flex items-center gap-2"><span class="legend-dot bg-[#9B5DE5]"></span>Others</span><span class="font-semibold text-hospital-ink">3% (77)</span></div>
                </div>
            </div>

            <div class="distribution-summary">
                <div class="distribution-row">
                    <div>
                        <p class="insight-label">Most Demanding Channel</p>
                        <p class="mt-1 text-sm font-semibold text-hospital-ink">Outpatient care dominates daily operations</p>
                    </div>
                    <p class="text-base font-extrabold text-hospital-patients">65%</p>
                </div>
                <div class="distribution-row">
                    <div>
                        <p class="insight-label">Admission Pressure</p>
                        <p class="mt-1 text-sm font-semibold text-hospital-ink">One in four encounters may require bed planning</p>
                    </div>
                    <p class="text-base font-extrabold text-[#53C57B]">25%</p>
                </div>
                <div class="distribution-row">
                    <div>
                        <p class="insight-label">Rapid Response Load</p>
                        <p class="mt-1 text-sm font-semibold text-hospital-ink">Emergency and other cases still need fast cross-team coordination</p>
                    </div>
                    <p class="text-base font-extrabold text-hospital-laboratory">10%</p>
                </div>
            </div>
        </article>

        <article class="chart-card">
            <div class="flex items-center justify-between gap-4">
                <h3 class="section-title">Notifications</h3>
                <a class="text-sm font-bold text-hospital-primary" href="#">View All</a>
            </div>
            <div class="mt-5 space-y-3">
                <?php foreach ($notifications as $item): ?>
                    <div class="notification-item">
                        <div class="notification-icon <?= e($item[3]); ?>">
                            <span class="h-5 w-5 [&_svg]:h-5 [&_svg]:w-5"><?= $item[4]; ?></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[15px] font-bold text-hospital-ink"><?= e($item[0]); ?></p>
                                    <p class="mt-1 text-sm leading-6 text-hospital-secondary"><?= e($item[1]); ?></p>
                                </div>
                                <span class="whitespace-nowrap text-xs font-medium text-hospital-muted"><?= e($item[2]); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
    </div>

    <div class="grid gap-6 xl:grid-cols-2 2xl:grid-cols-[1.35fr_1fr_0.9fr]">
        <article class="chart-card">
            <div class="flex items-center justify-between gap-4">
                <h3 class="section-title">Recent Appointments</h3>
                <a class="text-sm font-bold text-hospital-primary" href="#">View All</a>
            </div>
            <div class="mt-5 table-shell">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Department</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $row): ?>
                            <tr>
                                <td><?= e($row[0]); ?></td>
                                <td><a class="font-semibold text-hospital-primary" href="#"><?= e($row[1]); ?></a></td>
                                <td><?= e($row[2]); ?></td>
                                <td><?= e($row[3]); ?></td>
                                <td><?= e($row[4]); ?></td>
                                <td><span class="status-pill <?= e($row[6]); ?>"><?= e($row[5]); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="chart-card">
            <div class="flex items-center justify-between gap-4">
                <h3 class="section-title">Current IPD Patients</h3>
                <a class="text-sm font-bold text-hospital-primary" href="#">View All</a>
            </div>
            <div class="mt-5 table-shell">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Ward / Bed</th>
                            <th>Admitted On</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ipdPatients as $row): ?>
                            <tr>
                                <td><?= e($row[0]); ?></td>
                                <td><?= e($row[1]); ?></td>
                                <td><?= e($row[2]); ?></td>
                                <td><span class="status-pill <?= e($row[4]); ?>"><?= e($row[3]); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="chart-card">
            <div class="flex items-center justify-between gap-4">
                <h3 class="section-title">Top Services (This Month)</h3>
                <a class="text-sm font-bold text-hospital-primary" href="#">View All</a>
            </div>
            <div class="mt-5 space-y-6">
                <?php foreach ($services as $service): ?>
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-4">
                            <p class="text-[15px] font-bold text-hospital-ink"><?= e($service[0]); ?></p>
                            <p class="text-sm font-semibold text-hospital-secondary"><?= number_format($service[1]); ?></p>
                        </div>
                        <div class="progress-track">
                            <div class="progress-bar <?= e($service[3]); ?>" style="width: <?= e((string) $service[2]); ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
    </div>
</section>
