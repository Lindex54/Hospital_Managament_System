<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';

function vitals_flash(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['vitals_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function vitals_get_flash(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['vitals_flash']) || !is_array($_SESSION['vitals_flash'])) {
        return null;
    }

    $flash = $_SESSION['vitals_flash'];
    unset($_SESSION['vitals_flash']);

    return $flash;
}

function createVitalsRecord(array $data): int
{
    $errors = validate_required($data, ['visit_id', 'patient_id', 'recorded_by', 'recorded_at']);
    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    $pdo = database_connection();
    $recordedAt = str_replace('T', ' ', (string) $data['recorded_at']);
    $statement = $pdo->prepare(
        'INSERT INTO vitals (
            visit_id,
            patient_id,
            recorded_by,
            systolic_bp,
            diastolic_bp,
            temperature,
            pulse_rate,
            respiratory_rate,
            oxygen_saturation,
            weight_kg,
            height_cm,
            notes,
            recorded_at
        ) VALUES (
            :visit_id,
            :patient_id,
            :recorded_by,
            :systolic_bp,
            :diastolic_bp,
            :temperature,
            :pulse_rate,
            :respiratory_rate,
            :oxygen_saturation,
            :weight_kg,
            :height_cm,
            :notes,
            :recorded_at
        )'
    );

    $statement->execute([
        'visit_id' => (int) $data['visit_id'],
        'patient_id' => (int) $data['patient_id'],
        'recorded_by' => (int) $data['recorded_by'],
        'systolic_bp' => ($data['systolic_bp'] ?? '') !== '' ? (int) $data['systolic_bp'] : null,
        'diastolic_bp' => ($data['diastolic_bp'] ?? '') !== '' ? (int) $data['diastolic_bp'] : null,
        'temperature' => ($data['temperature'] ?? '') !== '' ? (float) $data['temperature'] : null,
        'pulse_rate' => ($data['pulse_rate'] ?? '') !== '' ? (int) $data['pulse_rate'] : null,
        'respiratory_rate' => ($data['respiratory_rate'] ?? '') !== '' ? (int) $data['respiratory_rate'] : null,
        'oxygen_saturation' => ($data['oxygen_saturation'] ?? '') !== '' ? (float) $data['oxygen_saturation'] : null,
        'weight_kg' => ($data['weight_kg'] ?? '') !== '' ? (float) $data['weight_kg'] : null,
        'height_cm' => ($data['height_cm'] ?? '') !== '' ? (float) $data['height_cm'] : null,
        'notes' => trim((string) ($data['notes'] ?? '')) !== '' ? trim((string) $data['notes']) : null,
        'recorded_at' => $recordedAt,
    ]);

    return (int) $pdo->lastInsertId();
}

function handle_vitals_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (($GLOBALS['currentPage'] ?? '') !== 'vitals') {
        return;
    }

    if (($_POST['form_action'] ?? '') !== 'record_vitals') {
        return;
    }

    requirePermission('vitals.create');

    try {
        $vitalsId = createVitalsRecord($_POST);
        vitals_flash('success', 'Vitals recorded successfully. Record ID: ' . $vitalsId . '.');
    } catch (Throwable $exception) {
        vitals_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=vitals'));
}
