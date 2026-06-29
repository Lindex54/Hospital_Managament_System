<?php

declare(strict_types=1);

require_once __DIR__ . '/data.php';
require_once __DIR__ . '/functions.php';
require_once dirname(__DIR__) . '/config/database.php';

function hospital_module_page_catalog(): array
{
    return load_app_data('modules.php');
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

function hospital_module_page_data(string $key, array $page, ?int $limit = 50): array
{
    return match ($key) {
        'patients' => hospital_module_patients_page_data($page, $limit),
        'doctors' => hospital_module_doctors_page_data($page, $limit),
        'wards' => hospital_module_wards_page_data($page, $limit),
        'outpatient' => hospital_module_outpatient_page_data($page, $limit),
        'inpatient' => hospital_module_inpatient_page_data($page, $limit),
        'appointments' => hospital_module_appointments_page_data($page, $limit),
        'consultations' => hospital_module_consultations_page_data($page, $limit),
        'emergency' => hospital_module_emergency_page_data($page, $limit),
        'triage' => hospital_module_triage_page_data($page, 'triage', $limit),
        'emergency-triage' => hospital_module_triage_page_data($page, 'emergency-triage', $limit),
        'vitals' => hospital_module_vitals_page_data($page, $limit),
        'ward-beds' => hospital_module_ward_beds_page_data($page, $limit),
        'nursing-notes' => hospital_module_nursing_notes_page_data($page, $limit),
        'inpatient-admission' => hospital_module_inpatient_admission_page_data($page, $limit),
        'discharge-referral' => hospital_module_discharge_page_data($page, $limit),
        default => $page,
    };
}

function hospital_module_table_exists(string $table): bool
{
    static $cache = [];

    if (array_key_exists($table, $cache)) {
        return $cache[$table];
    }

    $statement = database_connection()->prepare(
        'SELECT COUNT(*)
         FROM information_schema.tables
         WHERE table_schema = DATABASE()
           AND table_name = :table_name'
    );
    $statement->execute(['table_name' => $table]);
    $cache[$table] = ((int) $statement->fetchColumn()) > 0;

    return $cache[$table];
}

function hospital_module_count(string $sql): string
{
    return (string) database_connection()->query($sql)->fetchColumn();
}

function hospital_module_patient_display_name(array $record): string
{
    $name = trim(clinical_form_patient_name($record));
    return $name !== '' ? $name : 'Unknown Patient';
}

function hospital_module_staff_display_name(array $record): string
{
    $name = trim(implode(' ', array_filter([
        $record['staff_first_name'] ?? $record['first_name'] ?? '',
        $record['staff_last_name'] ?? $record['last_name'] ?? '',
    ], static fn ($value): bool => trim((string) $value) !== '')));

    return $name !== '' ? $name : 'Unassigned';
}

function hospital_module_doctor_filter_sql(string $alias = 'staff'): string
{
    $column = $alias . '.job_title';

    return "(
        LOWER(COALESCE({$column}, '')) LIKE '%doctor%'
        OR LOWER(COALESCE({$column}, '')) LIKE '%consultant%'
        OR LOWER(COALESCE({$column}, '')) LIKE '%surgeon%'
        OR LOWER(COALESCE({$column}, '')) LIKE '%physician%'
        OR LOWER(COALESCE({$column}, '')) LIKE '%specialist%'
        OR LOWER(COALESCE({$column}, '')) LIKE '%medical officer%'
        OR LOWER(COALESCE({$column}, '')) LIKE '%registrar%'
        OR LOWER(COALESCE({$column}, '')) LIKE '%resident%'
    )";
}

function hospital_module_format_count(int|string $value): string
{
    return number_format((int) $value);
}

function hospital_module_format_last_visit(mixed $value): string
{
    if (!is_string($value) || trim($value) === '') {
        return 'No Visit';
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return 'No Visit';
    }

    $visitDate = date('Y-m-d', $timestamp);
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    if ($visitDate === $today) {
        return 'Today';
    }

    if ($visitDate === $yesterday) {
        return 'Yesterday';
    }

    return date('d M Y', $timestamp);
}

function hospital_module_format_time(mixed $value): string
{
    if (!is_string($value) || trim($value) === '') {
        return '-';
    }

    $timestamp = strtotime($value);
    return $timestamp === false ? '-' : date('h:i A', $timestamp);
}

function hospital_module_format_short_date(mixed $value): string
{
    if (!is_string($value) || trim($value) === '') {
        return '-';
    }

    $timestamp = strtotime($value);
    return $timestamp === false ? '-' : date('d M', $timestamp);
}

function hospital_module_apply_limit(string $sql, ?int $limit): string
{
    if ($limit === null) {
        return $sql;
    }

    return rtrim($sql) . ' LIMIT ' . max(1, $limit);
}

function hospital_module_note_value(?string $notes, string $label): string
{
    if (!is_string($notes) || trim($notes) === '') {
        return '-';
    }

    foreach (preg_split('/\r\n|\r|\n/', $notes) ?: [] as $line) {
        if (stripos($line, $label . ':') === 0) {
            return trim(substr($line, strlen($label) + 1));
        }
    }

    return '-';
}

function hospital_module_render_records_table(array $page): string
{
    $badgeColumns = isset($page['badge_columns']) && is_array($page['badge_columns'])
        ? array_map('intval', $page['badge_columns'])
        : null;

    ob_start();
    ?>
    <div class="table-shell">
        <table class="table-clean">
            <thead>
                <tr>
                    <?php foreach ($page['columns'] as $column): ?>
                        <th><?= e($column); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (($page['rows'] ?? []) === []): ?>
                    <tr>
                        <td colspan="<?= e((string) count($page['columns'])); ?>" class="text-center text-hospital-secondary"><?= e((string) ($page['empty_message'] ?? 'No records found.')); ?></td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($page['rows'] as $row): ?>
                    <tr>
                        <?php foreach ($row as $index => $cell): ?>
                            <td>
                                <?php
                                $isBadgeColumn = $badgeColumns !== null
                                    ? in_array($index, $badgeColumns, true)
                                    : $index >= count($row) - 2;
                                ?>
                                <?php if ($isBadgeColumn): ?>
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
    <?php

    return (string) ob_get_clean();
}

function hospital_module_render_bed_room_overview(array $roomRows, array $bedRows): string
{
    ob_start();
    ?>
    <section class="grid gap-6 xl:grid-cols-2">
        <article class="chart-card">
            <div class="flex items-center justify-between gap-4">
                <h3 class="section-title">Room Availability</h3>
                <span class="badge badge-info">Live Database</span>
            </div>
            <div class="mt-5 space-y-3">
                <?php if ($roomRows === []): ?>
                    <p class="text-sm text-hospital-secondary">No rooms found yet.</p>
                <?php endif; ?>
                <?php foreach ($roomRows as $room): ?>
                    <div class="rounded-xl border border-hospital-borderSoft bg-white px-4 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold text-hospital-ink"><?= e((string) $room['label']); ?></p>
                                <p class="mt-1 text-sm text-hospital-secondary"><?= e((string) $room['subtext']); ?></p>
                            </div>
                            <span class="status-pill <?= e((string) $room['badge_class']); ?>"><?= e((string) $room['status']); ?></span>
                        </div>
                        <p class="mt-3 text-sm font-medium text-hospital-secondary"><?= e((string) $room['occupancy']); ?></p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-lg bg-hospital-primary/5 px-3 py-3">
                                <p class="text-xs font-bold uppercase tracking-wide text-hospital-muted">Total Beds</p>
                                <p class="mt-2 text-lg font-extrabold text-hospital-ink"><?= e((string) $room['total_beds']); ?></p>
                            </div>
                            <div class="rounded-lg bg-emerald-50 px-3 py-3">
                                <p class="text-xs font-bold uppercase tracking-wide text-hospital-muted">Available</p>
                                <p class="mt-2 text-lg font-extrabold text-emerald-600"><?= e((string) $room['available_beds']); ?></p>
                            </div>
                            <div class="rounded-lg bg-rose-50 px-3 py-3">
                                <p class="text-xs font-bold uppercase tracking-wide text-hospital-muted">Taken</p>
                                <p class="mt-2 text-lg font-extrabold text-rose-600"><?= e((string) $room['occupied_beds']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="chart-card">
            <div class="flex items-center justify-between gap-4">
                <h3 class="section-title">Bed Availability</h3>
                <span class="badge badge-info">Live Database</span>
            </div>
            <div class="mt-5 space-y-3">
                <?php if ($bedRows === []): ?>
                    <p class="text-sm text-hospital-secondary">No beds found yet.</p>
                <?php endif; ?>
                <?php foreach ($bedRows as $bed): ?>
                    <div class="rounded-xl border border-hospital-borderSoft bg-white px-4 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold text-hospital-ink"><?= e((string) $bed['label']); ?></p>
                                <p class="mt-1 text-sm text-hospital-secondary"><?= e((string) $bed['subtext']); ?></p>
                            </div>
                            <span class="status-pill <?= e((string) $bed['badge_class']); ?>"><?= e((string) $bed['status']); ?></span>
                        </div>
                        <p class="mt-3 text-sm font-medium text-hospital-secondary"><?= e((string) $bed['occupancy']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
    </section>
    <?php

    return (string) ob_get_clean();
}

function hospital_module_room_availability_status(string $roomType, int $availableBeds, int $occupiedBeds): array
{
    $normalizedType = strtolower(trim($roomType));

    if ($normalizedType === 'private') {
        return $occupiedBeds > 0
            ? ['Occupied', 'status-danger']
            : ['Available', 'status-success'];
    }

    if ($occupiedBeds > 0 && $availableBeds > 0) {
        return ['Partially Occupied', 'status-warning'];
    }

    if ($occupiedBeds > 0) {
        return ['Occupied', 'status-danger'];
    }

    return ['Available', 'status-success'];
}

function hospital_module_patients_page_data(array $page, ?int $limit = 50): array
{
    $pdo = database_connection();

    $stats = [
        ['Registered Patients', hospital_module_format_count((int) $pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn())],
        ['New This Month', hospital_module_format_count((int) $pdo->query("SELECT COUNT(*) FROM patients WHERE created_at >= DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01 00:00:00')")->fetchColumn())],
        ['Active Records', hospital_module_format_count((int) $pdo->query("SELECT COUNT(*) FROM patients WHERE status = 'active'")->fetchColumn())],
        ['Archived Records', hospital_module_format_count((int) $pdo->query("SELECT COUNT(*) FROM patients WHERE status <> 'active'")->fetchColumn())],
    ];

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT patients.patient_number,
                patients.first_name,
                patients.middle_name,
                patients.last_name,
                patients.gender,
                patients.phone,
                patients.status,
                MAX(visits.visit_date) AS last_visit
         FROM patients
         LEFT JOIN visits ON visits.patient_id = patients.id
         GROUP BY patients.id, patients.patient_number, patients.first_name, patients.middle_name, patients.last_name, patients.gender, patients.phone, patients.status
         ORDER BY patients.created_at DESC, patients.id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $patient) {
        $rows[] = [
            (string) ($patient['patient_number'] ?? '-'),
            clinical_form_patient_name($patient),
            ucfirst((string) ($patient['gender'] ?? '')),
            (string) ($patient['phone'] ?? '-'),
            hospital_module_format_last_visit($patient['last_visit'] ?? null),
            ucfirst((string) ($patient['status'] ?? '')),
        ];
    }

    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No patient records found yet.';

    return $page;
}

function hospital_module_outpatient_page_data(array $page, ?int $limit = 50): array
{
    $pdo = database_connection();
    $stats = [
        ['Today OPD Visits', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM visits WHERE visit_type IN ('outpatient', 'follow_up') AND DATE(visit_date) = CURRENT_DATE()"))],
        ['Waiting', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM visits WHERE visit_type IN ('outpatient', 'follow_up') AND visit_status IN ('registered', 'triage')"))],
        ['In Consultation', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM visits WHERE visit_type IN ('outpatient', 'follow_up') AND visit_status = 'consulting'"))],
        ['Completed', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM visits WHERE visit_type IN ('outpatient', 'follow_up') AND visit_status = 'completed'"))],
    ];

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT visits.visit_number, visits.visit_status, visits.visit_date,
                patients.first_name, patients.middle_name, patients.last_name,
                staff.first_name AS staff_first_name, staff.last_name AS staff_last_name,
                departments.name AS department_name
         FROM visits
         INNER JOIN patients ON patients.id = visits.patient_id
         LEFT JOIN staff ON staff.id = visits.doctor_id
         LEFT JOIN departments ON departments.id = visits.department_id
         WHERE visits.visit_type IN ('outpatient', 'follow_up')
         ORDER BY visits.visit_date DESC, visits.id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $visit) {
        $rows[] = [
            (string) ($visit['visit_number'] ?? '-'),
            hospital_module_patient_display_name($visit),
            hospital_module_staff_display_name($visit),
            (string) ($visit['department_name'] ?? '-'),
            ucfirst((string) ($visit['visit_status'] ?? '-')),
            hospital_module_format_time($visit['visit_date'] ?? null),
        ];
    }

    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No outpatient visits found yet.';

    return $page;
}

function hospital_module_doctors_page_data(array $page, ?int $limit = 50): array
{
    $pdo = database_connection();
    $doctorFilter = hospital_module_doctor_filter_sql('staff');
    $stats = [
        ['Registered Doctors', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM staff WHERE {$doctorFilter}"))],
        ['Active Roster', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM staff WHERE status = 'active' AND {$doctorFilter}"))],
        ['Consultants', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM staff WHERE LOWER(COALESCE(job_title, '')) LIKE '%consultant%'"))],
        ['Departments Covered', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(DISTINCT department_id) FROM staff WHERE {$doctorFilter}"))],
    ];

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT staff.staff_number, staff.first_name, staff.last_name, staff.job_title, staff.status, staff.hire_date,
                departments.name AS department_name
         FROM staff
         INNER JOIN departments ON departments.id = staff.department_id
         WHERE {$doctorFilter}
         ORDER BY staff.created_at DESC, staff.id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $doctor) {
        $rows[] = [
            (string) ($doctor['staff_number'] ?? '-'),
            'Dr. ' . hospital_module_staff_display_name($doctor),
            trim((string) ($doctor['job_title'] ?? '')) !== '' ? (string) $doctor['job_title'] : 'Doctor',
            (string) ($doctor['department_name'] ?? '-'),
            ucfirst((string) ($doctor['status'] ?? '-')),
            hospital_module_format_short_date($doctor['hire_date'] ?? null),
        ];
    }

    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No doctor records found yet. Add a doctor to start populating assignment dropdowns.';

    return $page;
}

function hospital_module_wards_page_data(array $page, ?int $limit = 50): array
{
    $pdo = database_connection();
    $stats = [
        ['Registered Wards', hospital_module_format_count((int) hospital_module_count('SELECT COUNT(*) FROM wards'))],
        ['Active Wards', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM wards WHERE status = 'active'"))],
        ['Total Capacity', hospital_module_format_count((int) hospital_module_count('SELECT COALESCE(SUM(capacity), 0) FROM wards'))],
        ['Mixed / Special Units', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM wards WHERE gender_policy IN ('mixed', 'children')"))],
    ];

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT code, name, ward_type, gender_policy, status, capacity
         FROM wards
         ORDER BY created_at DESC, id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $ward) {
        $rows[] = [
            (string) ($ward['code'] ?? '-'),
            (string) ($ward['name'] ?? '-'),
            trim((string) ($ward['ward_type'] ?? '')) !== '' ? (string) $ward['ward_type'] : 'General',
            ucfirst((string) ($ward['gender_policy'] ?? 'mixed')),
            ucfirst((string) ($ward['status'] ?? '-')),
            (string) ($ward['capacity'] ?? '0'),
        ];
    }

    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No ward records found yet. Add a ward to start assigning admissions properly.';

    return $page;
}

function hospital_module_inpatient_page_data(array $page, ?int $limit = 50): array
{
    $pdo = database_connection();
    $stats = [
        ['Current Admissions', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM admissions WHERE status = 'active'"))],
        ['Available Beds', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM beds WHERE status = 'available'"))],
        ['Critical Patients', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM admissions INNER JOIN wards ON wards.id = admissions.ward_id WHERE admissions.status = 'active' AND (LOWER(COALESCE(wards.name, '')) LIKE '%icu%' OR LOWER(COALESCE(wards.ward_type, '')) LIKE '%icu%')"))],
        ['Discharges Today', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM discharges WHERE DATE(discharge_date) = CURRENT_DATE()"))],
    ];

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT admissions.admission_number, admissions.status, admissions.admission_date,
                patients.first_name, patients.middle_name, patients.last_name,
                wards.name AS ward_name, rooms.room_number, rooms.room_type, beds.bed_number,
                staff.first_name AS staff_first_name, staff.last_name AS staff_last_name
         FROM admissions
         INNER JOIN patients ON patients.id = admissions.patient_id
         LEFT JOIN wards ON wards.id = admissions.ward_id
         LEFT JOIN rooms ON rooms.id = admissions.room_id
         LEFT JOIN beds ON beds.id = admissions.bed_id
         LEFT JOIN staff ON staff.id = admissions.admitted_by
         ORDER BY admissions.admission_date DESC, admissions.id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $admission) {
        $roomLabel = trim(implode(' / ', array_filter([
            (string) ($admission['ward_name'] ?? '-'),
            trim((string) ($admission['room_number'] ?? '')) !== '' ? (string) $admission['room_number'] : null,
            trim((string) ($admission['bed_number'] ?? '')) !== '' ? (string) $admission['bed_number'] : 'No Bed',
        ], static fn ($value): bool => $value !== null && trim((string) $value) !== '')));

        $rows[] = [
            (string) ($admission['admission_number'] ?? '-'),
            hospital_module_patient_display_name($admission),
            $roomLabel,
            trim((string) ($admission['room_type'] ?? 'General')) !== '' ? (string) $admission['room_type'] : 'General',
            hospital_module_staff_display_name($admission),
            ucfirst((string) ($admission['status'] ?? '-')),
            hospital_module_format_short_date($admission['admission_date'] ?? null),
        ];
    }

    $page['columns'] = ['Admission No', 'Patient', 'Ward / Room / Bed', 'Room Type', 'Doctor', 'Status', 'Admitted'];
    $page['badge_columns'] = [5];
    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No inpatient admissions found yet.';

    return $page;
}

function hospital_module_appointments_page_data(array $page, ?int $limit = 50): array
{
    $pdo = database_connection();
    $stats = [
        ['Today', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM appointments WHERE DATE(appointment_date) = CURRENT_DATE()"))],
        ['Confirmed', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM appointments WHERE status = 'confirmed'"))],
        ['Cancelled', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM appointments WHERE status = 'cancelled'"))],
        ['Pending', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM appointments WHERE status = 'scheduled'"))],
    ];

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT appointments.appointment_date, appointments.status, appointments.reason,
                patients.first_name, patients.middle_name, patients.last_name,
                staff.first_name AS staff_first_name, staff.last_name AS staff_last_name,
                departments.name AS department_name
         FROM appointments
         INNER JOIN patients ON patients.id = appointments.patient_id
         LEFT JOIN staff ON staff.id = appointments.doctor_id
         LEFT JOIN departments ON departments.id = appointments.department_id
         ORDER BY appointments.appointment_date DESC, appointments.id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $appointment) {
        $rows[] = [
            hospital_module_format_time($appointment['appointment_date'] ?? null),
            hospital_module_patient_display_name($appointment),
            hospital_module_staff_display_name($appointment),
            (string) ($appointment['department_name'] ?? '-'),
            ucfirst(str_replace('_', ' ', (string) ($appointment['status'] ?? '-'))),
            trim((string) ($appointment['reason'] ?? '')) !== '' ? 'Reasoned' : 'Appointment',
        ];
    }

    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No appointments booked yet.';

    return $page;
}

function hospital_module_consultations_page_data(array $page, ?int $limit = 50): array
{
    $pdo = database_connection();
    $stats = [
        ['Today', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM consultations WHERE DATE(consultation_date) = CURRENT_DATE()"))],
        ['Open Notes', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM consultations"))],
        ['Follow Ups', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM consultations WHERE follow_up_instructions IS NOT NULL AND TRIM(follow_up_instructions) <> ''"))],
        ['Closed', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM consultations WHERE diagnosis IS NOT NULL AND TRIM(diagnosis) <> ''"))],
    ];

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT visits.visit_number, consultations.diagnosis, consultations.follow_up_instructions, consultations.consultation_date,
                patients.first_name, patients.middle_name, patients.last_name,
                staff.first_name AS staff_first_name, staff.last_name AS staff_last_name
         FROM consultations
         INNER JOIN visits ON visits.id = consultations.visit_id
         INNER JOIN patients ON patients.id = consultations.patient_id
         LEFT JOIN staff ON staff.id = consultations.doctor_id
         ORDER BY consultations.consultation_date DESC, consultations.id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $consultation) {
        $rows[] = [
            (string) ($consultation['visit_number'] ?? '-'),
            hospital_module_patient_display_name($consultation),
            hospital_module_staff_display_name($consultation),
            trim((string) ($consultation['diagnosis'] ?? '')) !== '' ? trim((string) $consultation['diagnosis']) : 'Pending Diagnosis',
            trim((string) ($consultation['follow_up_instructions'] ?? '')) !== '' ? 'Review' : 'Recorded',
            hospital_module_format_time($consultation['consultation_date'] ?? null),
        ];
    }

    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No consultations saved yet.';

    return $page;
}

function hospital_module_emergency_page_data(array $page, ?int $limit = 50): array
{
    $pdo = database_connection();
    $stats = [
        ['Open Cases', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM visits WHERE visit_type = 'emergency' AND visit_status IN ('registered', 'triage', 'consulting')"))],
        ['Critical', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM visits WHERE visit_type = 'emergency' AND LOWER(COALESCE(notes, '')) LIKE '%priority level: critical%'"))],
        ['Stabilized', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM visits WHERE visit_type = 'emergency' AND visit_status = 'consulting'"))],
        ['Admitted', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM visits WHERE visit_type = 'emergency' AND visit_status = 'admitted'"))],
    ];

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT visits.visit_number, visits.notes, visits.visit_status, visits.visit_date,
                patients.first_name, patients.middle_name, patients.last_name
         FROM visits
         INNER JOIN patients ON patients.id = visits.patient_id
         WHERE visits.visit_type = 'emergency'
         ORDER BY visits.visit_date DESC, visits.id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $visit) {
        $rows[] = [
            (string) ($visit['visit_number'] ?? '-'),
            hospital_module_patient_display_name($visit),
            hospital_module_note_value($visit['notes'] ?? null, 'Arrival Mode'),
            hospital_module_note_value($visit['notes'] ?? null, 'Priority Level'),
            ucfirst((string) ($visit['visit_status'] ?? '-')),
            hospital_module_format_time($visit['visit_date'] ?? null),
        ];
    }

    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No emergency cases recorded yet.';

    return $page;
}

function hospital_module_triage_page_data(array $page, string $pageKey, ?int $limit = 50): array
{
    if (!hospital_module_table_exists('triage_records')) {
        $page['stats'] = array_map(static fn (array $stat): array => [$stat[0], '0'], $page['stats']);
        $page['rows'] = [];
        $page['empty_message'] = $pageKey === 'emergency-triage'
            ? 'No emergency triage records found yet.'
            : 'No triage records found yet.';
        return $page;
    }

    $pdo = database_connection();
    $recordFilter = $pageKey === 'emergency-triage' ? "page_key = 'emergency-triage'" : "page_key = 'triage'";

    if ($pageKey === 'emergency-triage') {
        $stats = [
            ['Screened', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM triage_records WHERE {$recordFilter}"))],
            ['Critical', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM triage_records WHERE {$recordFilter} AND triage_level = 'critical'"))],
            ['Urgent', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM triage_records WHERE {$recordFilter} AND triage_level = 'urgent'"))],
            ['Transferred', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM triage_records WHERE {$recordFilter} AND outcome = 'transfer'"))],
        ];
    } else {
        $stats = [
            ['Waiting', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM triage_records WHERE {$recordFilter} AND queue_status = 'queued'"))],
            ['Critical', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM triage_records WHERE {$recordFilter} AND triage_level = 'red'"))],
            ['Urgent', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM triage_records WHERE {$recordFilter} AND triage_level IN ('orange', 'yellow')"))],
            ['Stable', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM triage_records WHERE {$recordFilter} AND triage_level IN ('green', 'blue')"))],
        ];
    }

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT triage_records.id, triage_records.triage_level, triage_records.queue_status, triage_records.outcome, triage_records.triage_time,
                patients.first_name, patients.middle_name, patients.last_name,
                staff.first_name AS staff_first_name, staff.last_name AS staff_last_name
         FROM triage_records
         INNER JOIN patients ON patients.id = triage_records.patient_id
         LEFT JOIN staff ON staff.id = triage_records.staff_id
         WHERE {$recordFilter}
         ORDER BY triage_records.triage_time DESC, triage_records.id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $record) {
        if ($pageKey === 'emergency-triage') {
            $rows[] = [
                'ET-' . str_pad((string) $record['id'], 4, '0', STR_PAD_LEFT),
                hospital_module_patient_display_name($record),
                ucfirst((string) ($record['triage_level'] ?? '-')),
                hospital_module_staff_display_name($record),
                ucfirst(str_replace('_', ' ', (string) ($record['outcome'] ?? '-'))),
                hospital_module_format_time($record['triage_time'] ?? null),
            ];
        } else {
            $rows[] = [
                'TR-' . str_pad((string) $record['id'], 4, '0', STR_PAD_LEFT),
                hospital_module_patient_display_name($record),
                strtoupper((string) ($record['triage_level'] ?? '-')),
                hospital_module_staff_display_name($record),
                ucfirst((string) ($record['queue_status'] ?? '-')),
                hospital_module_format_time($record['triage_time'] ?? null),
            ];
        }
    }

    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = $pageKey === 'emergency-triage'
        ? 'No emergency triage records found yet.'
        : 'No triage records found yet.';

    return $page;
}

function hospital_module_vitals_page_data(array $page, ?int $limit = 50): array
{
    $pdo = database_connection();
    $stats = [
        ['Recorded Today', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM vitals WHERE DATE(recorded_at) = CURRENT_DATE()"))],
        ['Awaiting Review', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM vitals WHERE DATE(recorded_at) = CURRENT_DATE()"))],
        ['Abnormal Flags', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM vitals WHERE (systolic_bp >= 140 OR diastolic_bp >= 90 OR temperature >= 37.5 OR pulse_rate >= 100 OR oxygen_saturation < 92)"))],
        ['Completed', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM vitals"))],
    ];

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT vitals.systolic_bp, vitals.diastolic_bp, vitals.pulse_rate, vitals.temperature, vitals.recorded_at,
                patients.first_name, patients.middle_name, patients.last_name,
                staff.first_name AS staff_first_name, staff.last_name AS staff_last_name
         FROM vitals
         INNER JOIN patients ON patients.id = vitals.patient_id
         LEFT JOIN staff ON staff.id = vitals.recorded_by
         ORDER BY vitals.recorded_at DESC, vitals.id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $vitals) {
        $bp = (($vitals['systolic_bp'] ?? null) !== null && ($vitals['diastolic_bp'] ?? null) !== null)
            ? ((string) $vitals['systolic_bp'] . '/' . (string) $vitals['diastolic_bp'])
            : '-';
        $temperature = ($vitals['temperature'] ?? null) !== null ? (string) $vitals['temperature'] . ' C' : '-';

        $rows[] = [
            hospital_module_patient_display_name($vitals),
            $bp,
            ($vitals['pulse_rate'] ?? null) !== null ? (string) $vitals['pulse_rate'] : '-',
            $temperature,
            hospital_module_staff_display_name($vitals),
            hospital_module_format_time($vitals['recorded_at'] ?? null),
        ];
    }

    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No vitals records found yet.';

    return $page;
}

function hospital_module_ward_beds_page_data(array $page, ?int $limit = 50): array
{
    $pdo = database_connection();
    $stats = [
        ['Occupied Beds', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM beds WHERE status = 'occupied'"))],
        ['Available Beds', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM beds WHERE status = 'available'"))],
        ['Taken Rooms', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM (SELECT rooms.id FROM rooms INNER JOIN beds ON beds.room_id = rooms.id WHERE rooms.status = 'active' GROUP BY rooms.id HAVING SUM(CASE WHEN beds.status = 'available' THEN 1 ELSE 0 END) = 0) AS taken_rooms"))],
        ['Available Rooms', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM (SELECT rooms.id FROM rooms INNER JOIN beds ON beds.room_id = rooms.id WHERE rooms.status = 'active' GROUP BY rooms.id HAVING SUM(CASE WHEN beds.status = 'available' THEN 1 ELSE 0 END) > 0) AS available_rooms"))],
    ];

    $bedSql = "SELECT wards.name AS ward_name, rooms.room_number, rooms.room_type, beds.bed_number, beds.status, beds.updated_at,
                      patients.first_name, patients.middle_name, patients.last_name
               FROM beds
               INNER JOIN rooms ON rooms.id = beds.room_id
               INNER JOIN wards ON wards.id = rooms.ward_id
               LEFT JOIN admissions ON admissions.bed_id = beds.id AND admissions.status = 'active'
               LEFT JOIN patients ON patients.id = admissions.patient_id
               ORDER BY wards.name ASC, rooms.room_number ASC, beds.bed_number ASC";

    $statement = $pdo->query(hospital_module_apply_limit(
        $bedSql,
        $limit
    ));

    $bedRecords = $statement->fetchAll(PDO::FETCH_ASSOC);
    $bedOverviewRecords = $pdo->query($bedSql)->fetchAll(PDO::FETCH_ASSOC);
    $rows = [];
    foreach ($bedRecords as $bed) {
        $occupancyName = trim(clinical_form_patient_name($bed));
        $rows[] = [
            (string) ($bed['ward_name'] ?? '-'),
            (string) ($bed['room_number'] ?? '-'),
            trim((string) ($bed['room_type'] ?? 'Other')) !== '' ? (string) $bed['room_type'] : 'Other',
            (string) ($bed['bed_number'] ?? '-'),
            $occupancyName !== '' ? $occupancyName : '-',
            ucfirst((string) ($bed['status'] ?? '-')),
            hospital_module_format_last_visit($bed['updated_at'] ?? null),
        ];
    }

    $roomStatement = $pdo->query(
        "SELECT wards.name AS ward_name,
                rooms.name AS room_name,
                rooms.room_number,
                rooms.room_type,
                COUNT(beds.id) AS total_beds,
                SUM(CASE WHEN beds.status = 'available' THEN 1 ELSE 0 END) AS available_beds,
                SUM(CASE WHEN beds.status = 'occupied' THEN 1 ELSE 0 END) AS occupied_beds,
                GROUP_CONCAT(
                    DISTINCT CASE
                        WHEN admissions.status = 'active'
                            THEN TRIM(CONCAT_WS(' ', patients.first_name, patients.middle_name, patients.last_name))
                        ELSE NULL
                    END
                    ORDER BY patients.first_name, patients.last_name
                    SEPARATOR ', '
                ) AS patient_names
         FROM rooms
         INNER JOIN wards ON wards.id = rooms.ward_id
         LEFT JOIN beds ON beds.room_id = rooms.id
         LEFT JOIN admissions ON admissions.bed_id = beds.id AND admissions.status = 'active'
         LEFT JOIN patients ON patients.id = admissions.patient_id
         WHERE rooms.status = 'active'
         GROUP BY rooms.id, wards.name, rooms.name, rooms.room_number, rooms.room_type
         ORDER BY wards.name ASC, rooms.room_number ASC"
    );

    $roomOverview = [];
    foreach ($roomStatement->fetchAll(PDO::FETCH_ASSOC) as $room) {
        $availableBeds = (int) ($room['available_beds'] ?? 0);
        $occupiedBeds = (int) ($room['occupied_beds'] ?? 0);
        [$statusLabel, $statusClass] = hospital_module_room_availability_status(
            (string) ($room['room_type'] ?? 'Other'),
            $availableBeds,
            $occupiedBeds
        );
        $patientNames = trim((string) ($room['patient_names'] ?? ''));
        $roomOverview[] = [
            'label' => (string) (($room['ward_name'] ?? '-') . ' / ' . ($room['room_number'] ?? '-') . ' - ' . ($room['room_name'] ?? 'Room')),
            'subtext' => trim((string) (($room['room_type'] ?? 'General') . ' room')),
            'status' => $statusLabel,
            'badge_class' => $statusClass,
            'total_beds' => (int) ($room['total_beds'] ?? 0),
            'available_beds' => $availableBeds,
            'occupied_beds' => $occupiedBeds,
            'occupancy' => $patientNames !== '' ? 'Assigned patient(s): ' . $patientNames : 'No patient is currently assigned to this room.',
        ];
    }

    $bedOverview = [];
    foreach ($bedOverviewRecords as $bed) {
        $occupancyName = trim(clinical_form_patient_name($bed));
        $status = ucfirst((string) ($bed['status'] ?? 'Unknown'));
        $bedOverview[] = [
            'label' => (string) (($bed['ward_name'] ?? '-') . ' / ' . ($bed['room_number'] ?? '-') . ' / ' . ($bed['bed_number'] ?? '-')),
            'subtext' => trim((string) (($bed['room_type'] ?? 'Other') . ' room')) . ' • Updated ' . hospital_module_format_last_visit($bed['updated_at'] ?? null),
            'status' => $status,
            'badge_class' => hospital_module_badge_class($status),
            'occupancy' => $occupancyName !== '' ? 'Assigned to ' . $occupancyName : 'No active admission on this bed.',
        ];
    }

    $page['columns'] = ['Ward', 'Room', 'Room Type', 'Bed', 'Assigned Patient', 'Bed Status', 'Updated'];
    $page['badge_columns'] = [5];
    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No ward or bed records found yet.';
    $page['supplementary_content_html'] = hospital_module_render_bed_room_overview($roomOverview, $bedOverview);

    return $page;
}

function hospital_module_nursing_notes_page_data(array $page, ?int $limit = 50): array
{
    if (!hospital_module_table_exists('nursing_notes')) {
        $page['stats'] = array_map(static fn (array $stat): array => [$stat[0], '0'], $page['stats']);
        $page['rows'] = [];
        $page['empty_message'] = 'No nursing notes found yet.';
        return $page;
    }

    $pdo = database_connection();
    $stats = [
        ['Notes Today', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM nursing_notes WHERE DATE(recorded_at) = CURRENT_DATE()"))],
        ['Observations', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM nursing_notes WHERE note_type = 'observation'"))],
        ['Medication Notes', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM nursing_notes WHERE note_type = 'medication'"))],
        ['Care Plans', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM nursing_notes WHERE note_type = 'care_plan'"))],
    ];

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT nursing_notes.note_type, nursing_notes.recorded_at,
                patients.first_name, patients.middle_name, patients.last_name,
                wards.name AS ward_name, beds.bed_number,
                staff.first_name AS staff_first_name, staff.last_name AS staff_last_name
         FROM nursing_notes
         INNER JOIN patients ON patients.id = nursing_notes.patient_id
         LEFT JOIN admissions ON admissions.id = nursing_notes.admission_id
         LEFT JOIN wards ON wards.id = admissions.ward_id
         LEFT JOIN beds ON beds.id = admissions.bed_id
         LEFT JOIN staff ON staff.id = nursing_notes.staff_id
         ORDER BY nursing_notes.recorded_at DESC, nursing_notes.id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $note) {
        $rows[] = [
            hospital_module_patient_display_name($note),
            trim((string) (($note['ward_name'] ?? '-') . ' / ' . ($note['bed_number'] ?? 'No Bed'))),
            ucfirst(str_replace('_', ' ', (string) ($note['note_type'] ?? '-'))),
            hospital_module_staff_display_name($note),
            'Recorded',
            hospital_module_format_time($note['recorded_at'] ?? null),
        ];
    }

    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No nursing notes found yet.';

    return $page;
}

function hospital_module_inpatient_admission_page_data(array $page, ?int $limit = 50): array
{
    $pdo = database_connection();
    $stats = [
        ['Pending Admissions', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM visits LEFT JOIN admissions ON admissions.visit_id = visits.id WHERE visits.visit_status = 'admitted' AND admissions.id IS NULL"))],
        ['Approved Today', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM admissions WHERE DATE(admission_date) = CURRENT_DATE()"))],
        ['Awaiting Bed', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM admissions WHERE bed_id IS NULL AND status = 'active'"))],
        ['Emergency Transfers', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM admissions INNER JOIN visits ON visits.id = admissions.visit_id WHERE visits.visit_type = 'emergency'"))],
    ];

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT admissions.admission_number, admissions.status, admissions.admission_date,
                patients.first_name, patients.middle_name, patients.last_name,
                wards.name AS ward_name, beds.bed_number
         FROM admissions
         INNER JOIN patients ON patients.id = admissions.patient_id
         LEFT JOIN wards ON wards.id = admissions.ward_id
         LEFT JOIN beds ON beds.id = admissions.bed_id
         ORDER BY admissions.admission_date DESC, admissions.id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $admission) {
        $rows[] = [
            (string) ($admission['admission_number'] ?? '-'),
            hospital_module_patient_display_name($admission),
            (string) ($admission['ward_name'] ?? 'Unassigned'),
            (string) ($admission['bed_number'] ?? 'No Bed'),
            ucfirst((string) ($admission['status'] ?? '-')),
            hospital_module_format_time($admission['admission_date'] ?? null),
        ];
    }

    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No inpatient admission records found yet.';

    return $page;
}

function hospital_module_discharge_page_data(array $page, ?int $limit = 50): array
{
    $pdo = database_connection();
    $stats = [
        ['Ready Today', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM discharges WHERE DATE(discharge_date) = CURRENT_DATE()"))],
        ['Referred', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM discharges WHERE LOWER(COALESCE(outcome, '')) LIKE '%refer%'"))],
        ['Pending Signoff', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM admissions WHERE status = 'active'"))],
        ['Completed', hospital_module_format_count((int) hospital_module_count("SELECT COUNT(*) FROM discharges"))],
    ];

    $statement = $pdo->query(hospital_module_apply_limit(
        "SELECT admissions.admission_number, discharges.outcome, discharges.discharge_date,
                patients.first_name, patients.middle_name, patients.last_name,
                staff.first_name AS staff_first_name, staff.last_name AS staff_last_name
         FROM discharges
         INNER JOIN admissions ON admissions.id = discharges.admission_id
         INNER JOIN patients ON patients.id = admissions.patient_id
         LEFT JOIN staff ON staff.id = discharges.discharged_by
         ORDER BY discharges.discharge_date DESC, discharges.id DESC",
        $limit
    ));

    $rows = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $discharge) {
        $outcome = trim((string) ($discharge['outcome'] ?? ''));
        $rows[] = [
            (string) ($discharge['admission_number'] ?? '-'),
            hospital_module_patient_display_name($discharge),
            $outcome !== '' ? ucfirst(str_replace('_', ' ', $outcome)) : 'Discharge',
            hospital_module_staff_display_name($discharge),
            'Completed',
            hospital_module_format_time($discharge['discharge_date'] ?? null),
        ];
    }

    $page['stats'] = $stats;
    $page['rows'] = $rows;
    $page['empty_message'] = 'No discharge or referral records found yet.';

    return $page;
}

function render_hospital_module_page(string $key, array $actionAttributes = []): void
{
    $catalog = hospital_module_page_catalog();
    $page = $catalog[$key] ?? null;
    $modalConfig = hospital_module_modal_config($key);

    if ($modalConfig !== null) {
        $actionAttributes[$modalConfig['action_label']] = [
            'data-modal-open' => $modalConfig['modal_id'],
        ];
    }

    if ($page === null) {
        echo '<section class="dashboard-surface"><p class="text-sm text-hospital-secondary">Module page not found.</p></section>';
        return;
    }

    $page = hospital_module_page_data($key, $page, 50);
    $fullRecordsPage = hospital_module_page_data($key, $page, null);
    $recordsModalId = 'module-records-' . $key;
    ?>
    <section class="module-page-shell">
        <div class="module-hero">
            <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_auto] xl:items-center xl:gap-6">
                <div class="flex min-w-0 items-start gap-4">
                    <div class="module-hero-icon">
                        <span class="h-7 w-7 [&_svg]:h-7 [&_svg]:w-7"><?= sidebar_icon($page['icon']); ?></span>
                    </div>
                    <div class="min-w-0 xl:max-w-[720px]">
                        <h1 class="page-title"><?= e($page['title']); ?></h1>
                        <p class="mt-2 text-base font-medium text-hospital-secondary"><?= e($page['description']); ?></p>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3 xl:justify-self-end">
                    <?php foreach ($page['actions'] as $index => $action): ?>
                        <?php
                        $attributes = $actionAttributes[$action] ?? [];
                        $attributeHtml = '';

                        foreach ($attributes as $attributeName => $attributeValue) {
                            $attributeHtml .= ' ' . $attributeName . '="' . e((string) $attributeValue) . '"';
                        }
                        ?>
                        <button class="btn min-w-[170px] justify-center whitespace-nowrap <?= $index === 0 ? 'btn-primary' : 'btn-secondary'; ?>" type="button"<?= $attributeHtml; ?>><?= e($action); ?></button>
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
            <article class="chart-card" id="module-records">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="section-title"><?= e($page['title']); ?> Records</h3>
                    <button class="text-sm font-bold text-hospital-primary" type="button" data-modal-open="<?= e($recordsModalId); ?>">View All</button>
                </div>

                <div class="mt-5">
                    <?= hospital_module_render_records_table($page); ?>
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

        <?php if (isset($page['supplementary_content_html'])): ?>
            <div class="mt-6">
                <?= (string) $page['supplementary_content_html']; ?>
            </div>
        <?php endif; ?>
    </section>
    <?php

    if ($modalConfig !== null && is_callable($modalConfig['content'])) {
        render_clinical_modal(
            $modalConfig['modal_id'],
            $modalConfig['title'],
            $modalConfig['subtitle'],
            (string) call_user_func($modalConfig['content'])
        );
    }

    render_clinical_modal(
        $recordsModalId,
        $page['title'] . ' Records',
        'Showing all available records for this module.',
        hospital_module_render_records_table($fullRecordsPage)
    );
}

function hospital_module_modal_config(string $key): ?array
{
    return match ($key) {
        'patients' => [
            'action_label' => 'Register Patient',
            'modal_id' => 'patient-registration',
            'title' => 'Register Patient',
            'subtitle' => 'Patient fields from the `patients` table.',
            'content' => static fn (): string => render_patient_modal_form(),
        ],
        'doctors' => [
            'action_label' => 'Add Doctor',
            'modal_id' => 'doctor-registration',
            'title' => 'Add Doctor',
            'subtitle' => 'Doctor fields saved into the `staff` table for live assignment dropdowns.',
            'content' => static fn (): string => render_doctor_modal_form(
                clinical_form_fetch_doctor_departments()
            ),
        ],
        'wards' => [
            'action_label' => 'Add Ward',
            'modal_id' => 'ward-registration',
            'title' => 'Add Ward',
            'subtitle' => 'Ward fields saved into the `wards` table for live admission and bed assignment dropdowns.',
            'content' => static fn (): string => render_ward_modal_form(),
        ],
        'outpatient' => [
            'action_label' => 'Create OPD Visit',
            'modal_id' => 'outpatient-visit',
            'title' => 'Create OPD Visit',
            'subtitle' => 'Visit fields from the `visits` table.',
            'content' => static fn (): string => render_outpatient_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_departments(),
                clinical_form_fetch_doctors(),
                clinical_form_fetch_appointments()
            ),
        ],
        'inpatient' => [
            'action_label' => 'Admit Patient',
            'modal_id' => 'inpatient-admission',
            'title' => 'Admit Patient',
            'subtitle' => 'Admission fields from the `admissions` table.',
            'content' => static fn (): string => render_inpatient_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_visits(),
                clinical_form_fetch_wards(true),
                clinical_form_fetch_rooms(true),
                clinical_form_fetch_beds(true),
                clinical_form_fetch_doctors()
            ),
        ],
        'appointments' => [
            'action_label' => 'Book Appointment',
            'modal_id' => 'appointment-booking',
            'title' => 'Book Appointment',
            'subtitle' => 'Appointment fields from the `appointments` table.',
            'content' => static fn (): string => render_appointment_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_departments(),
                clinical_form_fetch_doctors()
            ),
        ],
        'consultations' => [
            'action_label' => 'New Consultation',
            'modal_id' => 'consultation-notes',
            'title' => 'New Consultation',
            'subtitle' => 'Consultation fields from the `consultations` table.',
            'content' => static fn (): string => render_consultation_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_visits(),
                clinical_form_fetch_doctors()
            ),
        ],
        'emergency' => [
            'action_label' => 'Register Emergency',
            'modal_id' => 'emergency-registration',
            'title' => 'Register Emergency',
            'subtitle' => 'Capture emergency intake details for urgent care handling.',
            'content' => static fn (): string => render_emergency_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_departments(),
                clinical_form_fetch_doctors()
            ),
        ],
        'triage' => [
            'action_label' => 'Record Triage',
            'modal_id' => 'triage-record',
            'title' => 'Record Triage',
            'subtitle' => 'Capture queue priority and first clinical observations.',
            'content' => static fn (): string => render_triage_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_visits(),
                clinical_form_fetch_doctors()
            ),
        ],
        'emergency-triage' => [
            'action_label' => 'Start Triage',
            'modal_id' => 'emergency-triage-record',
            'title' => 'Start Emergency Triage',
            'subtitle' => 'Document emergency prioritization and immediate handoff actions.',
            'content' => static fn (): string => render_emergency_triage_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_visits(),
                clinical_form_fetch_doctors()
            ),
        ],
        'vitals' => [
            'action_label' => 'Record Vitals',
            'modal_id' => 'record-vitals',
            'title' => 'Record Vitals',
            'subtitle' => 'Capture patient observations for the selected visit.',
            'content' => static fn (): string => render_vitals_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_visits(),
                clinical_form_fetch_doctors()
            ),
        ],
        'ward-beds' => [
            'action_label' => 'Assign Bed',
            'modal_id' => 'assign-bed',
            'title' => 'Assign Bed',
            'subtitle' => 'Link an admission to the selected ward, room, and bed.',
            'content' => static fn (): string => render_ward_bed_modal_form(
                clinical_form_fetch_admissions(),
                clinical_form_fetch_wards(),
                clinical_form_fetch_rooms(),
                clinical_form_fetch_beds()
            ),
        ],
        'nursing-notes' => [
            'action_label' => 'Add Note',
            'modal_id' => 'nursing-note',
            'title' => 'Add Nursing Note',
            'subtitle' => 'Capture bedside observations and nursing updates.',
            'content' => static fn (): string => render_nursing_notes_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_admissions(),
                clinical_form_fetch_doctors()
            ),
        ],
        'inpatient-admission' => [
            'action_label' => 'Admit Patient',
            'modal_id' => 'inpatient-admission-panel',
            'title' => 'Admit Patient',
            'subtitle' => 'Admission fields from the `admissions` table.',
            'content' => static fn (): string => render_inpatient_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_visits(),
                clinical_form_fetch_wards(true),
                clinical_form_fetch_rooms(true),
                clinical_form_fetch_beds(true),
                clinical_form_fetch_doctors()
            ),
        ],
        'discharge-referral' => [
            'action_label' => 'Prepare Discharge',
            'modal_id' => 'prepare-discharge',
            'title' => 'Prepare Discharge',
            'subtitle' => 'Discharge fields from the `discharges` table.',
            'content' => static fn (): string => render_discharge_modal_form(
                clinical_form_fetch_admissions(),
                clinical_form_fetch_doctors()
            ),
        ],
        default => null,
    };
}
