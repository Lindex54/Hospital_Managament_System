<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';

function nursing_flash(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['nursing_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function nursing_get_flash(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['nursing_flash']) || !is_array($_SESSION['nursing_flash'])) {
        return null;
    }

    $flash = $_SESSION['nursing_flash'];
    unset($_SESSION['nursing_flash']);

    return $flash;
}

function ensure_nursing_notes_table(): void
{
    database_connection()->exec(
        'CREATE TABLE IF NOT EXISTS nursing_notes (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            patient_id BIGINT UNSIGNED NOT NULL,
            admission_id BIGINT UNSIGNED DEFAULT NULL,
            staff_id BIGINT UNSIGNED DEFAULT NULL,
            note_type VARCHAR(50) NOT NULL,
            note_body LONGTEXT NOT NULL,
            plan TEXT DEFAULT NULL,
            recorded_at DATETIME NOT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_nursing_notes_patient
                FOREIGN KEY (patient_id) REFERENCES patients(id),
            CONSTRAINT fk_nursing_notes_admission
                FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE SET NULL,
            CONSTRAINT fk_nursing_notes_staff
                FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE SET NULL
        ) ENGINE=InnoDB'
    );
}

function createNursingNote(array $data): int
{
    $errors = validate_required($data, ['patient_id', 'staff_id', 'note_type', 'recorded_at', 'note_body']);
    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    ensure_nursing_notes_table();
    $pdo = database_connection();
    $statement = $pdo->prepare(
        'INSERT INTO nursing_notes (
            patient_id,
            admission_id,
            staff_id,
            note_type,
            note_body,
            plan,
            recorded_at
        ) VALUES (
            :patient_id,
            :admission_id,
            :staff_id,
            :note_type,
            :note_body,
            :plan,
            :recorded_at
        )'
    );

    $statement->execute([
        'patient_id' => (int) $data['patient_id'],
        'admission_id' => ($data['admission_id'] ?? '') !== '' ? (int) $data['admission_id'] : null,
        'staff_id' => (int) $data['staff_id'],
        'note_type' => trim((string) $data['note_type']),
        'note_body' => trim((string) $data['note_body']),
        'plan' => trim((string) ($data['plan'] ?? '')) !== '' ? trim((string) $data['plan']) : null,
        'recorded_at' => str_replace('T', ' ', (string) $data['recorded_at']),
    ]);

    return (int) $pdo->lastInsertId();
}

function handle_nursing_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (($GLOBALS['currentPage'] ?? '') !== 'nursing-notes') {
        return;
    }

    if (($_POST['form_action'] ?? '') !== 'create_nursing_note') {
        return;
    }

    requirePermission('nursing.create');

    try {
        $noteId = createNursingNote($_POST);
        nursing_flash('success', 'Nursing note saved successfully. Record ID: ' . $noteId . '.');
    } catch (Throwable $exception) {
        nursing_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=nursing-notes'));
}
