<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';

function doctor_flash(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['doctor_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function doctor_get_flash(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['doctor_flash']) || !is_array($_SESSION['doctor_flash'])) {
        return null;
    }

    $flash = $_SESSION['doctor_flash'];
    unset($_SESSION['doctor_flash']);

    return $flash;
}

function generateDoctorStaffNumber(): string
{
    $pdo = database_connection();
    $dateSegment = date('Ymd');
    $pattern = 'DOC-' . $dateSegment . '-%';
    $statement = $pdo->prepare(
        'SELECT staff_number
         FROM staff
         WHERE staff_number LIKE :pattern
         ORDER BY id DESC
         LIMIT 1'
    );
    $statement->execute(['pattern' => $pattern]);
    $lastStaffNumber = (string) $statement->fetchColumn();
    $nextSequence = 1;

    if ($lastStaffNumber !== '' && preg_match('/(\d{4})$/', $lastStaffNumber, $matches) === 1) {
        $nextSequence = ((int) $matches[1]) + 1;
    }

    return sprintf('DOC-%s-%04d', $dateSegment, $nextSequence);
}

function createDoctorRecord(array $data): int
{
    $errors = validate_required($data, [
        'professional_title',
        'first_name',
        'last_name',
        'gender',
        'department_id',
        'doctor_grade',
        'phone',
        'hire_date',
        'status',
    ]);

    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    $specialtyFocus = trim((string) ($data['specialty_focus'] ?? ''));
    $jobTitleParts = [
        trim((string) $data['professional_title']),
        trim((string) $data['doctor_grade']),
    ];

    if ($specialtyFocus !== '') {
        $jobTitleParts[] = '- ' . $specialtyFocus;
    }

    $jobTitle = trim(implode(' ', array_filter($jobTitleParts, static fn ($value): bool => trim((string) $value) !== '')));
    $pdo = database_connection();
    $statement = $pdo->prepare(
        'INSERT INTO staff (
            user_id,
            department_id,
            staff_number,
            first_name,
            last_name,
            gender,
            phone,
            email,
            job_title,
            hire_date,
            status
        ) VALUES (
            :user_id,
            :department_id,
            :staff_number,
            :first_name,
            :last_name,
            :gender,
            :phone,
            :email,
            :job_title,
            :hire_date,
            :status
        )'
    );

    $statement->execute([
        'user_id' => null,
        'department_id' => (int) $data['department_id'],
        'staff_number' => generateDoctorStaffNumber(),
        'first_name' => trim((string) $data['first_name']),
        'last_name' => trim((string) $data['last_name']),
        'gender' => trim((string) $data['gender']),
        'phone' => trim((string) $data['phone']),
        'email' => trim((string) ($data['email'] ?? '')) !== '' ? trim((string) $data['email']) : null,
        'job_title' => $jobTitle !== '' ? $jobTitle : 'Doctor',
        'hire_date' => trim((string) $data['hire_date']),
        'status' => trim((string) $data['status']),
    ]);

    return (int) $pdo->lastInsertId();
}

function handle_doctor_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (($GLOBALS['currentPage'] ?? '') !== 'doctors') {
        return;
    }

    if (($_POST['form_action'] ?? '') !== 'create_doctor') {
        return;
    }

    requirePermission('users.manage');

    try {
        $doctorId = createDoctorRecord($_POST);
        doctor_flash('success', 'Doctor added successfully. Record ID: ' . $doctorId . '.');
    } catch (Throwable $exception) {
        doctor_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=doctors'));
}
