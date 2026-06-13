<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';

function visit_flash(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['visit_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function visit_get_flash(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['visit_flash']) || !is_array($_SESSION['visit_flash'])) {
        return null;
    }

    $flash = $_SESSION['visit_flash'];
    unset($_SESSION['visit_flash']);

    return $flash;
}

function generateVisitNumber(string $visitType = 'outpatient'): string
{
    $pdo = database_connection();
    $prefixMap = [
        'outpatient' => 'OPD',
        'inpatient' => 'IPD',
        'emergency' => 'EMR',
        'follow_up' => 'FUP',
    ];
    $prefix = $prefixMap[$visitType] ?? 'VIS';
    $dateSegment = date('Ymd');
    $pattern = $prefix . '-' . $dateSegment . '-%';

    $statement = $pdo->prepare(
        'SELECT visit_number
         FROM visits
         WHERE visit_number LIKE :pattern
         ORDER BY id DESC
         LIMIT 1'
    );
    $statement->execute(['pattern' => $pattern]);
    $lastVisitNumber = (string) $statement->fetchColumn();
    $nextSequence = 1;

    if ($lastVisitNumber !== '' && preg_match('/(\d{4})$/', $lastVisitNumber, $matches) === 1) {
        $nextSequence = ((int) $matches[1]) + 1;
    }

    return sprintf('%s-%s-%04d', $prefix, $dateSegment, $nextSequence);
}

function createOutpatientVisit(array $data): int
{
    $errors = validate_required($data, [
        'patient_id',
        'department_id',
        'visit_date',
        'visit_type',
        'visit_status',
        'chief_complaint',
    ]);

    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    $pdo = database_connection();
    $currentUser = getCurrentUser();
    $visitType = (string) ($data['visit_type'] ?? 'outpatient');
    $visitStatus = (string) ($data['visit_status'] ?? 'registered');
    $allowedVisitTypes = ['outpatient', 'inpatient', 'emergency', 'follow_up'];
    $allowedVisitStatuses = ['registered', 'triage', 'consulting', 'admitted', 'completed', 'cancelled'];

    if (!in_array($visitType, $allowedVisitTypes, true)) {
        $visitType = 'outpatient';
    }

    if (!in_array($visitStatus, $allowedVisitStatuses, true)) {
        $visitStatus = 'registered';
    }

    $statement = $pdo->prepare(
        'INSERT INTO visits (
            patient_id,
            appointment_id,
            department_id,
            doctor_id,
            visit_number,
            visit_type,
            visit_status,
            visit_date,
            chief_complaint,
            notes,
            created_by
        ) VALUES (
            :patient_id,
            :appointment_id,
            :department_id,
            :doctor_id,
            :visit_number,
            :visit_type,
            :visit_status,
            :visit_date,
            :chief_complaint,
            :notes,
            :created_by
        )'
    );

    $visitDate = str_replace('T', ' ', (string) $data['visit_date']);

    $statement->execute([
        'patient_id' => (int) $data['patient_id'],
        'appointment_id' => ($data['appointment_id'] ?? '') !== '' ? (int) $data['appointment_id'] : null,
        'department_id' => (int) $data['department_id'],
        'doctor_id' => ($data['doctor_id'] ?? '') !== '' ? (int) $data['doctor_id'] : null,
        'visit_number' => generateVisitNumber($visitType),
        'visit_type' => $visitType,
        'visit_status' => $visitStatus,
        'visit_date' => $visitDate,
        'chief_complaint' => trim((string) $data['chief_complaint']),
        'notes' => trim((string) ($data['notes'] ?? '')) !== '' ? trim((string) $data['notes']) : null,
        'created_by' => isset($currentUser['id']) ? (int) $currentUser['id'] : null,
    ]);

    return (int) $pdo->lastInsertId();
}

function handle_outpatient_visit_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (($GLOBALS['currentPage'] ?? '') !== 'outpatient') {
        return;
    }

    if (($_POST['form_action'] ?? '') !== 'create_outpatient_visit') {
        return;
    }

    requirePermission('outpatient.create');

    try {
        $visitId = createOutpatientVisit($_POST);
        visit_flash('success', 'Outpatient visit created successfully. Visit record ID: ' . $visitId . '.');
    } catch (Throwable $exception) {
        visit_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=outpatient'));
}
