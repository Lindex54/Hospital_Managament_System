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

function normalizeAppointmentDate(string $value): string
{
    $appointmentDate = str_replace('T', ' ', trim($value));
    $appointmentTimestamp = strtotime($appointmentDate);

    if ($appointmentTimestamp === false) {
        throw new RuntimeException('Appointment date and time is invalid.');
    }

    if ($appointmentTimestamp <= time()) {
        throw new RuntimeException('Appointment date and time must be in the future.');
    }

    return date('Y-m-d H:i:s', $appointmentTimestamp);
}

function normalizeAppointmentStatus(string $value): string
{
    $status = trim($value);
    $allowedStatuses = ['scheduled', 'confirmed', 'cancelled', 'completed', 'no_show'];

    return in_array($status, $allowedStatuses, true) ? $status : 'scheduled';
}

function resolveAppointmentCreatedBy(): ?int
{
    $currentUser = getCurrentUser();

    if (($currentUser['is_demo_user'] ?? false) === true || !isset($currentUser['id'])) {
        return null;
    }

    $currentUserId = (int) $currentUser['id'];

    return $currentUserId > 0 ? $currentUserId : null;
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
    $status = normalizeAppointmentStatus((string) ($data['status'] ?? 'scheduled'));
    $appointmentDate = normalizeAppointmentDate((string) $data['appointment_date']);
    $createdBy = resolveAppointmentCreatedBy();

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
        'created_by' => $createdBy,
    ]);

    return (int) $pdo->lastInsertId();
}

function rescheduleAppointment(array $data): int
{
    $errors = validate_required($data, [
        'appointment_id',
        'appointment_date',
        'status',
    ]);

    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    $pdo = database_connection();
    $appointmentId = (int) $data['appointment_id'];
    $status = normalizeAppointmentStatus((string) ($data['status'] ?? 'scheduled'));
    $appointmentDate = normalizeAppointmentDate((string) $data['appointment_date']);
    $notes = trim((string) ($data['reschedule_notes'] ?? ''));

    $lookupStatement = $pdo->prepare(
        'SELECT id, notes
         FROM appointments
         WHERE id = :id
         LIMIT 1'
    );
    $lookupStatement->execute([
        'id' => $appointmentId,
    ]);
    $appointment = $lookupStatement->fetch(PDO::FETCH_ASSOC);

    if (!is_array($appointment)) {
        throw new RuntimeException('Select an existing appointment to reschedule.');
    }

    $updatedNotes = trim((string) ($appointment['notes'] ?? ''));
    $rescheduleMessage = 'Rescheduled on ' . date('d M Y h:i A') . '.';

    if ($notes !== '') {
        $rescheduleMessage .= ' ' . $notes;
    }

    $updatedNotes = trim($updatedNotes !== '' ? $updatedNotes . PHP_EOL . $rescheduleMessage : $rescheduleMessage);

    $updateStatement = $pdo->prepare(
        'UPDATE appointments
         SET appointment_date = :appointment_date,
             status = :status,
             notes = :notes
         WHERE id = :id'
    );
    $updateStatement->execute([
        'appointment_date' => $appointmentDate,
        'status' => $status,
        'notes' => $updatedNotes,
        'id' => $appointmentId,
    ]);

    return $appointmentId;
}

function handle_appointment_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (($GLOBALS['currentPage'] ?? '') !== 'appointments') {
        return;
    }

    $formAction = (string) ($_POST['form_action'] ?? '');

    if (!in_array($formAction, ['create_appointment', 'reschedule_appointment'], true)) {
        return;
    }

    requirePermission('appointments.view');

    try {
        if ($formAction === 'create_appointment') {
            $appointmentId = createAppointment($_POST);
            appointment_flash('success', 'Appointment created successfully. Appointment record ID: ' . $appointmentId . '.');
        } else {
            $appointmentId = rescheduleAppointment($_POST);
            appointment_flash('success', 'Appointment rescheduled successfully. Appointment record ID: ' . $appointmentId . '.');
        }
    } catch (Throwable $exception) {
        appointment_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=appointments'));
}
