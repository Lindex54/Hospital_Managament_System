<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';

function admission_flash(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['admission_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function admission_get_flash(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['admission_flash']) || !is_array($_SESSION['admission_flash'])) {
        return null;
    }

    $flash = $_SESSION['admission_flash'];
    unset($_SESSION['admission_flash']);

    return $flash;
}

function generateAdmissionNumber(): string
{
    $pdo = database_connection();
    $dateSegment = date('Ymd');
    $pattern = 'ADM-' . $dateSegment . '-%';

    $statement = $pdo->prepare(
        'SELECT admission_number
         FROM admissions
         WHERE admission_number LIKE :pattern
         ORDER BY id DESC
         LIMIT 1'
    );
    $statement->execute(['pattern' => $pattern]);
    $lastAdmissionNumber = (string) $statement->fetchColumn();
    $nextSequence = 1;

    if ($lastAdmissionNumber !== '' && preg_match('/(\d{4})$/', $lastAdmissionNumber, $matches) === 1) {
        $nextSequence = ((int) $matches[1]) + 1;
    }

    return sprintf('ADM-%s-%04d', $dateSegment, $nextSequence);
}

function createAdmission(array $data): int
{
    $errors = validate_required($data, [
        'patient_id',
        'visit_id',
        'ward_id',
        'room_id',
        'bed_id',
        'admitted_by',
        'admission_date',
        'status',
        'reason',
    ]);

    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    $pdo = database_connection();
    $status = (string) ($data['status'] ?? 'active');
    $allowedStatuses = ['active', 'discharged', 'cancelled'];

    if (!in_array($status, $allowedStatuses, true)) {
        $status = 'active';
    }

    $admissionDate = str_replace('T', ' ', (string) $data['admission_date']);

    $statement = $pdo->prepare(
        'INSERT INTO admissions (
            patient_id,
            visit_id,
            ward_id,
            room_id,
            bed_id,
            admitted_by,
            admission_number,
            admission_date,
            reason,
            status,
            notes
        ) VALUES (
            :patient_id,
            :visit_id,
            :ward_id,
            :room_id,
            :bed_id,
            :admitted_by,
            :admission_number,
            :admission_date,
            :reason,
            :status,
            :notes
        )'
    );

    $statement->execute([
        'patient_id' => (int) $data['patient_id'],
        'visit_id' => (int) $data['visit_id'],
        'ward_id' => (int) $data['ward_id'],
        'room_id' => (int) $data['room_id'],
        'bed_id' => (int) $data['bed_id'],
        'admitted_by' => (int) $data['admitted_by'],
        'admission_number' => generateAdmissionNumber(),
        'admission_date' => $admissionDate,
        'reason' => trim((string) $data['reason']),
        'status' => $status,
        'notes' => trim((string) ($data['notes'] ?? '')) !== '' ? trim((string) $data['notes']) : null,
    ]);

    return (int) $pdo->lastInsertId();
}

function handle_inpatient_admission_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (($GLOBALS['currentPage'] ?? '') !== 'inpatient') {
        return;
    }

    if (($_POST['form_action'] ?? '') !== 'create_inpatient_admission') {
        return;
    }

    requirePermission('inpatient.admit');

    try {
        $admissionId = createAdmission($_POST);
        admission_flash('success', 'Admission created successfully. Admission record ID: ' . $admissionId . '.');
    } catch (Throwable $exception) {
        admission_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=inpatient'));
}
