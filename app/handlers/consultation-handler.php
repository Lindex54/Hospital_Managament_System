<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';

function consultation_flash(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['consultation_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function consultation_get_flash(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['consultation_flash']) || !is_array($_SESSION['consultation_flash'])) {
        return null;
    }

    $flash = $_SESSION['consultation_flash'];
    unset($_SESSION['consultation_flash']);

    return $flash;
}

function createConsultation(array $data): int
{
    $errors = validate_required($data, [
        'visit_id',
        'patient_id',
        'doctor_id',
        'consultation_date',
        'symptoms',
        'diagnosis',
        'treatment_plan',
    ]);

    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    $pdo = database_connection();
    $consultationDate = str_replace('T', ' ', (string) $data['consultation_date']);

    $statement = $pdo->prepare(
        'INSERT INTO consultations (
            visit_id,
            patient_id,
            doctor_id,
            symptoms,
            diagnosis,
            treatment_plan,
            clinical_notes,
            follow_up_instructions,
            consultation_date
        ) VALUES (
            :visit_id,
            :patient_id,
            :doctor_id,
            :symptoms,
            :diagnosis,
            :treatment_plan,
            :clinical_notes,
            :follow_up_instructions,
            :consultation_date
        )'
    );

    $statement->execute([
        'visit_id' => (int) $data['visit_id'],
        'patient_id' => (int) $data['patient_id'],
        'doctor_id' => (int) $data['doctor_id'],
        'symptoms' => trim((string) $data['symptoms']),
        'diagnosis' => trim((string) $data['diagnosis']),
        'treatment_plan' => trim((string) $data['treatment_plan']),
        'clinical_notes' => trim((string) ($data['clinical_notes'] ?? '')) !== '' ? trim((string) $data['clinical_notes']) : null,
        'follow_up_instructions' => trim((string) ($data['follow_up_instructions'] ?? '')) !== '' ? trim((string) $data['follow_up_instructions']) : null,
        'consultation_date' => $consultationDate,
    ]);

    return (int) $pdo->lastInsertId();
}

function handle_consultation_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (($GLOBALS['currentPage'] ?? '') !== 'consultations') {
        return;
    }

    if (($_POST['form_action'] ?? '') !== 'create_consultation') {
        return;
    }

    requirePermission('consultations.create');

    try {
        $consultationId = createConsultation($_POST);
        consultation_flash('success', 'Consultation saved successfully. Consultation record ID: ' . $consultationId . '.');
    } catch (Throwable $exception) {
        consultation_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=consultations'));
}
