<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';
require_once __DIR__ . '/visit-handler.php';

function emergency_flash(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['emergency_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function emergency_get_flash(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['emergency_flash']) || !is_array($_SESSION['emergency_flash'])) {
        return null;
    }

    $flash = $_SESSION['emergency_flash'];
    unset($_SESSION['emergency_flash']);

    return $flash;
}

function createEmergencyVisit(array $data): int
{
    $errors = validate_required($data, [
        'patient_id',
        'department_id',
        'arrival_mode',
        'priority_level',
        'arrival_time',
        'presenting_complaint',
        'status',
    ]);

    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    $pdo = database_connection();
    $currentUser = getCurrentUser();
    $createdBy = null;

    if (
        is_array($currentUser)
        && (($currentUser['is_demo_user'] ?? false) !== true)
        && isset($currentUser['id'])
        && (int) $currentUser['id'] > 0
    ) {
        $createdBy = (int) $currentUser['id'];
    }

    $statusMap = [
        'open' => 'registered',
        'stabilized' => 'consulting',
        'admitted' => 'admitted',
        'closed' => 'completed',
    ];
    $visitStatus = $statusMap[(string) ($data['status'] ?? '')] ?? 'registered';
    $arrivalTime = str_replace('T', ' ', (string) $data['arrival_time']);
    $extraNotes = [
        'Emergency Intake',
        'Arrival Mode: ' . trim((string) $data['arrival_mode']),
        'Priority Level: ' . trim((string) $data['priority_level']),
    ];

    if (trim((string) ($data['notes'] ?? '')) !== '') {
        $extraNotes[] = 'Notes: ' . trim((string) $data['notes']);
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

    $statement->execute([
        'patient_id' => (int) $data['patient_id'],
        'appointment_id' => null,
        'department_id' => (int) $data['department_id'],
        'doctor_id' => ($data['doctor_id'] ?? '') !== '' ? (int) $data['doctor_id'] : null,
        'visit_number' => generateVisitNumber('emergency'),
        'visit_type' => 'emergency',
        'visit_status' => $visitStatus,
        'visit_date' => $arrivalTime,
        'chief_complaint' => trim((string) $data['presenting_complaint']),
        'notes' => implode(PHP_EOL, $extraNotes),
        'created_by' => $createdBy,
    ]);

    return (int) $pdo->lastInsertId();
}

function handle_emergency_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (($GLOBALS['currentPage'] ?? '') !== 'emergency') {
        return;
    }

    if (($_POST['form_action'] ?? '') !== 'create_emergency_visit') {
        return;
    }

    requirePermission('emergency.create');

    try {
        $visitId = createEmergencyVisit($_POST);
        emergency_flash('success', 'Emergency case registered successfully. Visit record ID: ' . $visitId . '.');
    } catch (Throwable $exception) {
        emergency_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=emergency'));
}
