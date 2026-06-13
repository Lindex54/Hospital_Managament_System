<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';

function appointment_flash(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['appointment_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function appointment_get_flash(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['appointment_flash']) || !is_array($_SESSION['appointment_flash'])) {
        return null;
    }

    $flash = $_SESSION['appointment_flash'];
    unset($_SESSION['appointment_flash']);

    return $flash;
}

function createAppointment(array $data): int
{
    $errors = validate_required($data, [
        'patient_id',
        'department_id',
        'appointment_date',
        'status',
        'reason',
    ]);

    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    $pdo = database_connection();
    $currentUser = getCurrentUser();
    $status = (string) ($data['status'] ?? 'scheduled');
    $allowedStatuses = ['scheduled', 'confirmed', 'cancelled', 'completed', 'no_show'];

    if (!in_array($status, $allowedStatuses, true)) {
        $status = 'scheduled';
    }

    $appointmentDate = str_replace('T', ' ', (string) $data['appointment_date']);

    $statement = $pdo->prepare(
        'INSERT INTO appointments (
            patient_id,
            department_id,
            doctor_id,
            appointment_date,
            reason,
            status,
            notes,
            created_by
        ) VALUES (
            :patient_id,
            :department_id,
            :doctor_id,
            :appointment_date,
            :reason,
            :status,
            :notes,
            :created_by
        )'
    );

    $statement->execute([
        'patient_id' => (int) $data['patient_id'],
        'department_id' => (int) $data['department_id'],
        'doctor_id' => ($data['doctor_id'] ?? '') !== '' ? (int) $data['doctor_id'] : null,
        'appointment_date' => $appointmentDate,
        'reason' => trim((string) $data['reason']),
        'status' => $status,
        'notes' => trim((string) ($data['notes'] ?? '')) !== '' ? trim((string) $data['notes']) : null,
        'created_by' => isset($currentUser['id']) ? (int) $currentUser['id'] : null,
    ]);

    return (int) $pdo->lastInsertId();
}

function handle_appointment_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (($GLOBALS['currentPage'] ?? '') !== 'appointments') {
        return;
    }

    if (($_POST['form_action'] ?? '') !== 'create_appointment') {
        return;
    }

    requirePermission('appointments.view');

    try {
        $appointmentId = createAppointment($_POST);
        appointment_flash('success', 'Appointment created successfully. Appointment record ID: ' . $appointmentId . '.');
    } catch (Throwable $exception) {
        appointment_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=appointments'));
}
