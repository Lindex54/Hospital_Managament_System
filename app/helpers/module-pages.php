<?php

declare(strict_types=1);

function hospital_module_page_catalog(): array
{
    return [
        'patients' => [
            'title' => 'Patients',
            'description' => 'Manage registered patient records and medical profiles across every hospital encounter.',
            'icon' => 'patients',
            'actions' => ['Register Patient', 'Import Records', 'Export List'],
            'stats' => [
                ['Registered Patients', '2,543'],
                ['New This Month', '214'],
                ['Active Records', '2,501'],
                ['Archived Records', '42'],
            ],
            'columns' => ['Patient No', 'Name', 'Gender', 'Phone', 'Last Visit', 'Status'],
            'rows' => [
                ['PAT-0001', 'John Doe', 'Male', '+256 700 111 222', 'Today', 'Active'],
                ['PAT-0002', 'Mary Akello', 'Female', '+256 701 444 555', 'Yesterday', 'Active'],
                ['PAT-0003', 'Robert Ssenyonga', 'Male', '+256 702 333 444', '24 May 2026', 'Active'],
                ['PAT-0004', 'Linda Namatovu', 'Female', '+256 703 888 999', '20 May 2026', 'Active'],
            ],
        ],
        'outpatient' => [
            'title' => 'Outpatient',
            'description' => 'Track OPD visits from reception and triage through consultation, billing, and follow-up.',
            'icon' => 'outpatient',
            'actions' => ['Create OPD Visit', 'Send to Triage', 'View OPD Queue'],
            'stats' => [
                ['Today OPD Visits', '128'],
                ['Waiting', '34'],
                ['In Consultation', '19'],
                ['Completed', '75'],
            ],
            'columns' => ['Visit No', 'Patient', 'Doctor', 'Department', 'Stage', 'Time'],
            'rows' => [
                ['OPD-1021', 'John Doe', 'Dr. Sarah', 'General Medicine', 'Consultation', '09:00 AM'],
                ['OPD-1022', 'Mary Akello', 'Dr. Michael', 'Cardiology', 'Triage', '09:20 AM'],
                ['OPD-1023', 'Amina Kato', 'Dr. Emily', 'General Medicine', 'Billing', '10:00 AM'],
            ],
        ],
        'inpatient' => [
            'title' => 'Inpatient',
            'description' => 'Manage admissions, wards, beds, treatment notes, and discharge planning from a single view.',
            'icon' => 'inpatient',
            'actions' => ['Admit Patient', 'Transfer Bed', 'Prepare Discharge'],
            'stats' => [
                ['Current Admissions', '86'],
                ['Available Beds', '18'],
                ['Critical Patients', '4'],
                ['Discharges Today', '9'],
            ],
            'columns' => ['Admission No', 'Patient', 'Ward / Bed', 'Doctor', 'Status', 'Admitted'],
            'rows' => [
                ['IPD-301', 'James Anderson', 'Male Ward / Bed 3', 'Dr. Sarah', 'Stable', '22 May'],
                ['IPD-302', 'Patricia Johnson', 'Female Ward / Bed 1', 'Dr. Michael', 'Stable', '23 May'],
                ['IPD-303', 'Barbara White', 'ICU / Bed 2', 'Dr. Emily', 'Critical', '24 May'],
            ],
        ],
        'appointments' => [
            'title' => 'Appointments',
            'description' => 'Schedule patient appointments by doctor, department, and service time slots.',
            'icon' => 'appointments',
            'actions' => ['Book Appointment', 'Reschedule', 'Calendar View'],
            'stats' => [
                ['Today', '42'],
                ['Confirmed', '31'],
                ['Cancelled', '3'],
                ['Pending', '8'],
            ],
            'columns' => ['Time', 'Patient', 'Doctor', 'Department', 'Status', 'Type'],
            'rows' => [
                ['09:00 AM', 'John Doe', 'Dr. Sarah', 'Cardiology', 'Completed', 'OPD'],
                ['10:30 AM', 'Linda Wilson', 'Dr. Emily', 'Orthopedics', 'Upcoming', 'Review'],
                ['11:00 AM', 'William Taylor', 'Dr. Michael', 'General Medicine', 'Upcoming', 'Consultation'],
            ],
        ],
        'emergency' => [
            'title' => 'Emergency',
            'description' => 'Handle emergency registration, triage priority, stabilization, and urgent admissions.',
            'icon' => 'emergency',
            'actions' => ['Register Emergency', 'Assign Doctor', 'Open Triage'],
            'stats' => [
                ['Open Cases', '7'],
                ['Critical', '2'],
                ['Stabilized', '4'],
                ['Admitted', '1'],
            ],
            'columns' => ['Case No', 'Patient', 'Arrival Mode', 'Triage Level', 'Status', 'Time'],
            'rows' => [
                ['EMG-1001', 'Samuel Okello', 'Ambulance', 'Critical', 'Open', '08:42 AM'],
                ['EMG-1002', 'Grace Achieng', 'Walk In', 'Urgent', 'Stabilized', '09:15 AM'],
                ['EMG-1003', 'Peter Kato', 'Referral', 'Semi Urgent', 'Admitted', '10:05 AM'],
            ],
        ],
        'laboratory' => [
            'title' => 'Laboratory',
            'description' => 'Manage lab requests, sample collection, test workflows, and result completion.',
            'icon' => 'laboratory',
            'actions' => ['New Lab Request', 'Enter Result', 'Print Report'],
            'stats' => [
                ['Requests Today', '64'],
                ['Pending', '18'],
                ['Processing', '21'],
                ['Completed', '25'],
            ],
            'columns' => ['Request No', 'Patient', 'Test', 'Priority', 'Status', 'Requested By'],
            'rows' => [
                ['LAB-5001', 'John Doe', 'Full Blood Count', 'Normal', 'Completed', 'Dr. Sarah'],
                ['LAB-5002', 'Mary Akello', 'Malaria Test', 'Urgent', 'Processing', 'Dr. Michael'],
                ['LAB-5003', 'Amina Kato', 'Urinalysis', 'Normal', 'Pending', 'Dr. Emily'],
            ],
        ],
        'radiology' => [
            'title' => 'Radiology',
            'description' => 'Coordinate X-ray, ultrasound, CT scan, and imaging report workflows.',
            'icon' => 'radiology',
            'actions' => ['New Radiology Request', 'Upload Result', 'View Images'],
            'stats' => [
                ['Requests', '22'],
                ['Scheduled', '8'],
                ['Completed', '11'],
                ['Pending', '3'],
            ],
            'columns' => ['Request No', 'Patient', 'Test', 'Status', 'Radiologist', 'Date'],
            'rows' => [
                ['RAD-2001', 'Robert Davis', 'Chest X-Ray', 'Completed', 'Dr. Grace', 'Today'],
                ['RAD-2002', 'Linda Wilson', 'Ultrasound', 'Scheduled', 'Dr. Paul', 'Today'],
                ['RAD-2003', 'Michael Clark', 'CT Scan', 'Pending', 'Dr. Grace', 'Tomorrow'],
            ],
        ],
        'pharmacy' => [
            'title' => 'Pharmacy',
            'description' => 'Dispense prescriptions, monitor stock movement, and flag low inventory early.',
            'icon' => 'pharmacy',
            'actions' => ['Dispense Medicine', 'Add Stock', 'Stock Report'],
            'stats' => [
                ['Prescriptions', '58'],
                ['Dispensed', '41'],
                ['Low Stock', '6'],
                ['Expired Items', '2'],
            ],
            'columns' => ['Medicine', 'Category', 'Stock', 'Reorder Level', 'Expiry', 'Status'],
            'rows' => [
                ['Paracetamol', 'Painkiller', '24', '50', '12 Dec 2026', 'Low Stock'],
                ['Amoxicillin', 'Antibiotic', '130', '30', '20 Jan 2027', 'Available'],
                ['Artemether', 'Antimalarial', '80', '20', '18 Aug 2026', 'Available'],
            ],
        ],
        'billing' => [
            'title' => 'Billing',
            'description' => 'Generate invoices, receive payments, and monitor patient balances in one place.',
            'icon' => 'billing',
            'actions' => ['Create Invoice', 'Receive Payment', 'Print Receipt'],
            'stats' => [
                ['Invoices Today', '73'],
                ['Paid', '51'],
                ['Pending', '22'],
                ['Revenue', 'UGX 8.5M'],
            ],
            'columns' => ['Invoice No', 'Patient', 'Amount', 'Paid', 'Balance', 'Status'],
            'rows' => [
                ['INV-901', 'John Doe', 'UGX 85,000', 'UGX 85,000', 'UGX 0', 'Paid'],
                ['INV-902', 'Mary Akello', 'UGX 120,000', 'UGX 70,000', 'UGX 50,000', 'Partial'],
                ['INV-903', 'Robert Davis', 'UGX 40,000', 'UGX 0', 'UGX 40,000', 'Unpaid'],
            ],
        ],
        'insurance' => [
            'title' => 'Insurance',
            'description' => 'Manage providers, policy verification, and claims tracking from submission to approval.',
            'icon' => 'insurance',
            'actions' => ['Add Provider', 'Submit Claim', 'Verify Policy'],
            'stats' => [
                ['Providers', '12'],
                ['Active Policies', '418'],
                ['Claims Pending', '27'],
                ['Claims Approved', '64'],
            ],
            'columns' => ['Claim No', 'Patient', 'Provider', 'Claim Amount', 'Status', 'Submitted'],
            'rows' => [
                ['CLM-1001', 'John Doe', 'Jubilee Health', 'UGX 250,000', 'Approved', 'Today'],
                ['CLM-1002', 'Mary Akello', 'UAP Insurance', 'UGX 180,000', 'Review', 'Yesterday'],
                ['CLM-1003', 'James Anderson', 'Sanlam', 'UGX 470,000', 'Submitted', '22 May'],
            ],
        ],
        'nursing' => [
            'title' => 'Nursing',
            'description' => 'Capture nursing observations, medication notes, and ward handover activity.',
            'icon' => 'nursing',
            'actions' => ['Add Nursing Note', 'Medication Chart', 'Shift Report'],
            'stats' => [
                ['Notes Today', '46'],
                ['Medication Notes', '18'],
                ['Shift Reports', '9'],
                ['Care Plans', '19'],
            ],
            'columns' => ['Patient', 'Ward / Bed', 'Note Type', 'Nurse', 'Time', 'Status'],
            'rows' => [
                ['James Anderson', 'Ward 1 / Bed 3', 'Observation', 'Nurse Jane', '08:30 AM', 'Recorded'],
                ['Patricia Johnson', 'Ward 2 / Bed 1', 'Medication', 'Nurse Alice', '09:10 AM', 'Recorded'],
                ['Barbara White', 'ICU / Bed 2', 'Care Plan', 'Nurse Brian', '10:00 AM', 'Updated'],
            ],
        ],
        'reports' => [
            'title' => 'Reports',
            'description' => 'Review hospital analytics, department performance, and financial reporting snapshots.',
            'icon' => 'reports',
            'actions' => ['Generate Report', 'Export PDF', 'Export Excel'],
            'stats' => [
                ['Monthly Revenue', 'UGX 186M'],
                ['OPD Visits', '1,524'],
                ['Admissions', '230'],
                ['Lab Tests', '976'],
            ],
            'columns' => ['Report', 'Category', 'Period', 'Generated By', 'Status', 'Action'],
            'rows' => [
                ['Patient Visit Summary', 'Clinical', 'May 2026', 'Admin User', 'Ready', 'Download'],
                ['Revenue Report', 'Finance', 'May 2026', 'Accountant', 'Ready', 'Download'],
                ['Bed Occupancy', 'Inpatient', 'May 2026', 'Admin User', 'Ready', 'Download'],
            ],
            'highlights' => [
                ['Clinical Volume', '1,524', 'Outpatient visits remain the largest monthly demand center.'],
                ['Finance Yield', 'UGX 186M', 'Revenue is still led by consultations, lab work, and pharmacy services.'],
                ['Capacity Load', '230', 'Admissions remain steady enough to support predictable ward planning.'],
            ],
        ],
        'queue' => [
            'title' => 'Queue',
            'description' => 'Monitor patient flow across reception, triage, consultation, laboratory, and billing points.',
            'icon' => 'queue',
            'actions' => ['Issue Ticket', 'Call Next', 'Transfer Queue'],
            'stats' => [
                ['Waiting', '34'],
                ['Called', '7'],
                ['Serving', '12'],
                ['Served', '75'],
            ],
            'columns' => ['Ticket No', 'Patient', 'Queue', 'Priority', 'Status', 'Issued'],
            'rows' => [
                ['Q-001', 'John Doe', 'Consultation', 'Normal', 'Serving', '09:00 AM'],
                ['Q-002', 'Mary Akello', 'Triage', 'Urgent', 'Waiting', '09:10 AM'],
                ['Q-003', 'Amina Kato', 'Laboratory', 'Normal', 'Called', '09:30 AM'],
            ],
        ],
        'noticeboard' => [
            'title' => 'Noticeboard',
            'description' => 'Share internal announcements, clinical memos, shift reminders, and hospital updates.',
            'icon' => 'noticeboard',
            'actions' => ['Post Update', 'Pin Notice', 'Archive Bulletin'],
            'stats' => [
                ['Active Notices', '14'],
                ['Critical Alerts', '2'],
                ['Shift Memos', '5'],
                ['Policy Updates', '3'],
            ],
            'columns' => ['Title', 'Category', 'Audience', 'Priority', 'Status', 'Published'],
            'rows' => [
                ['Night Shift Handover', 'Nursing', 'Ward Teams', 'High', 'Live', 'Today'],
                ['Generator Maintenance', 'Operations', 'All Staff', 'Medium', 'Live', 'Today'],
                ['Updated Lab SOP', 'Clinical', 'Laboratory', 'High', 'Pinned', 'Yesterday'],
            ],
        ],
        'settings' => [
            'title' => 'Settings',
            'description' => 'Configure hospital profile, departments, wards, beds, service prices, and system preferences.',
            'icon' => 'settings',
            'actions' => ['Hospital Profile', 'Service Prices', 'System Preferences'],
            'stats' => [
                ['Departments', '8'],
                ['Wards', '6'],
                ['Rooms', '42'],
                ['Beds', '128'],
            ],
            'columns' => ['Setting', 'Description', 'Status', 'Last Updated'],
            'rows' => [
                ['Hospital Profile', 'Name, address, contacts and logo', 'Active', 'Today'],
                ['Departments', 'Clinical and operational departments', 'Active', 'Yesterday'],
                ['Wards & Beds', 'Ward, room and bed configuration', 'Active', '20 May'],
            ],
        ],
        'users' => [
            'title' => 'Users & Roles',
            'description' => 'Manage staff accounts, user access, and role-based permissions across the hospital system.',
            'icon' => 'users',
            'actions' => ['Add User', 'Create Role', 'Permission Matrix'],
            'stats' => [
                ['Users', '84'],
                ['Doctors', '18'],
                ['Nurses', '31'],
                ['Administrators', '4'],
            ],
            'columns' => ['User', 'Role', 'Department', 'Email', 'Status', 'Last Login'],
            'rows' => [
                ['Dr. Sarah Johnson', 'Doctor', 'General Medicine', 'sarah@hospital.test', 'Active', 'Today'],
                ['Nurse Jane', 'Nurse', 'Inpatient Ward', 'jane@hospital.test', 'Active', 'Today'],
                ['Paul Accountant', 'Accountant', 'Finance', 'paul@hospital.test', 'Active', 'Yesterday'],
            ],
        ],
    ];
}

function hospital_module_badge_class(string $value): string
{
    $value = strtolower($value);

    if (str_contains($value, 'paid') || str_contains($value, 'completed') || str_contains($value, 'active') || str_contains($value, 'stable') || str_contains($value, 'approved') || str_contains($value, 'available') || str_contains($value, 'ready') || str_contains($value, 'recorded') || str_contains($value, 'live') || str_contains($value, 'pinned')) {
        return 'status-success';
    }

    if (str_contains($value, 'pending') || str_contains($value, 'waiting') || str_contains($value, 'review') || str_contains($value, 'scheduled') || str_contains($value, 'partial') || str_contains($value, 'high')) {
        return 'status-warning';
    }

    if (str_contains($value, 'critical') || str_contains($value, 'low') || str_contains($value, 'urgent') || str_contains($value, 'unpaid') || str_contains($value, 'open')) {
        return 'status-danger';
    }

    return 'badge-info';
}

function render_hospital_module_page(string $key): void
{
    $catalog = hospital_module_page_catalog();
    $page = $catalog[$key] ?? null;

    if ($page === null) {
        echo '<section class="dashboard-surface"><p class="text-sm text-hospital-secondary">Module page not found.</p></section>';
        return;
    }
    ?>
    <section class="dashboard-grid">
        <div class="module-hero">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex items-start gap-4">
                    <div class="module-hero-icon">
                        <span class="h-7 w-7 [&_svg]:h-7 [&_svg]:w-7"><?= sidebar_icon($page['icon']); ?></span>
                    </div>
                    <div>
                        <h1 class="page-title"><?= e($page['title']); ?></h1>
                        <p class="mt-2 text-base font-medium text-hospital-secondary"><?= e($page['description']); ?></p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <?php foreach ($page['actions'] as $index => $action): ?>
                        <button class="btn <?= $index === 0 ? 'btn-primary' : 'btn-secondary'; ?>" type="button"><?= e($action); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2 2xl:grid-cols-4">
            <?php foreach ($page['stats'] as $stat): ?>
                <article class="dashboard-surface">
                    <p class="text-sm font-bold text-hospital-muted"><?= e($stat[0]); ?></p>
                    <p class="mt-3 font-display text-[2rem] font-extrabold text-hospital-ink"><?= e($stat[1]); ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="grid gap-6 <?= isset($page['highlights']) ? '2xl:grid-cols-[1.55fr_0.95fr]' : ''; ?>">
            <article class="chart-card">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="section-title"><?= e($page['title']); ?> Records</h3>
                    <a class="text-sm font-bold text-hospital-primary" href="#">View All</a>
                </div>

                <div class="mt-5 table-shell">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <?php foreach ($page['columns'] as $column): ?>
                                    <th><?= e($column); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($page['rows'] as $row): ?>
                                <tr>
                                    <?php foreach ($row as $index => $cell): ?>
                                        <td>
                                            <?php if ($index >= count($row) - 2): ?>
                                                <span class="status-pill <?= e(hospital_module_badge_class((string) $cell)); ?>"><?= e((string) $cell); ?></span>
                                            <?php else: ?>
                                                <?php if ($index === 0 || $index === 1): ?>
                                                    <span class="<?= $index === 1 ? 'font-semibold text-hospital-primary' : 'font-semibold text-hospital-ink'; ?>"><?= e((string) $cell); ?></span>
                                                <?php else: ?>
                                                    <?= e((string) $cell); ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </article>

            <?php if (isset($page['highlights'])): ?>
                <article class="chart-card">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="section-title">Performance Highlights</h3>
                        <span class="badge badge-info">Static View</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        <?php foreach ($page['highlights'] as $highlight): ?>
                            <div class="insight-card">
                                <p class="insight-label"><?= e($highlight[0]); ?></p>
                                <p class="insight-value"><?= e($highlight[1]); ?></p>
                                <p class="insight-copy"><?= e($highlight[2]); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

