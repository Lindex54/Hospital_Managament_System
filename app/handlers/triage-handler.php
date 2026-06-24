<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';

function triage_flash(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['triage_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function triage_get_flash(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['triage_flash']) || !is_array($_SESSION['triage_flash'])) {
        return null;
    }

    $flash = $_SESSION['triage_flash'];
    unset($_SESSION['triage_flash']);

    return $flash;
}

function ensure_triage_records_table(): void
{
    database_connection()->exec(
        'CREATE TABLE IF NOT EXISTS triage_records (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            page_key VARCHAR(50) NOT NULL,
            visit_id BIGINT UNSIGNED DEFAULT NULL,
            patient_id BIGINT UNSIGNED NOT NULL,
            staff_id BIGINT UNSIGNED DEFAULT NULL,
            triage_level VARCHAR(50) NOT NULL,
            queue_status VARCHAR(50) DEFAULT NULL,
            outcome VARCHAR(100) DEFAULT NULL,
            complaint_summary TEXT DEFAULT NULL,
            observations LONGTEXT DEFAULT NULL,
            findings LONGTEXT DEFAULT NULL,
            action_taken LONGTEXT DEFAULT NULL,
            notes LONGTEXT DEFAULT NULL,
            triage_time DATETIME NOT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_triage_records_visit
                FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE SET NULL,
            CONSTRAINT fk_triage_records_patient
                FOREIGN KEY (patient_id) REFERENCES patients(id),
            CONSTRAINT fk_triage_records_staff
                FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE SET NULL
        ) ENGINE=InnoDB'
    );
}

function createTriageRecord(array $data, string $pageKey): int
{
    $required = $pageKey === 'emergency-triage'
        ? ['patient_id', 'nurse_id', 'priority', 'outcome', 'triage_time', 'findings', 'action_taken']
        : ['patient_id', 'nurse_id', 'triage_level', 'status', 'triage_time', 'complaint_summary', 'observations'];
    $errors = validate_required($data, $required);

    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    ensure_triage_records_table();
    $pdo = database_connection();
    $triageTime = str_replace('T', ' ', (string) $data['triage_time']);
    $statement = $pdo->prepare(
        'INSERT INTO triage_records (
            page_key,
            visit_id,
            patient_id,
            staff_id,
            triage_level,
            queue_status,
            outcome,
            complaint_summary,
            observations,
            findings,
            action_taken,
            notes,
            triage_time
        ) VALUES (
            :page_key,
            :visit_id,
            :patient_id,
            :staff_id,
            :triage_level,
            :queue_status,
            :outcome,
            :complaint_summary,
            :observations,
            :findings,
            :action_taken,
            :notes,
            :triage_time
        )'
    );

    $statement->execute([
        'page_key' => $pageKey,
        'visit_id' => ($data['visit_id'] ?? '') !== '' ? (int) $data['visit_id'] : null,
        'patient_id' => (int) $data['patient_id'],
        'staff_id' => (int) $data['nurse_id'],
        'triage_level' => $pageKey === 'emergency-triage' ? trim((string) $data['priority']) : trim((string) $data['triage_level']),
        'queue_status' => $pageKey === 'emergency-triage' ? null : trim((string) $data['status']),
        'outcome' => $pageKey === 'emergency-triage' ? trim((string) $data['outcome']) : null,
        'complaint_summary' => trim((string) ($data['complaint_summary'] ?? '')) !== '' ? trim((string) $data['complaint_summary']) : null,
        'observations' => trim((string) ($data['observations'] ?? '')) !== '' ? trim((string) $data['observations']) : null,
        'findings' => trim((string) ($data['findings'] ?? '')) !== '' ? trim((string) $data['findings']) : null,
        'action_taken' => trim((string) ($data['action_taken'] ?? '')) !== '' ? trim((string) $data['action_taken']) : null,
        'notes' => trim((string) ($data['notes'] ?? '')) !== '' ? trim((string) $data['notes']) : null,
        'triage_time' => $triageTime,
    ]);

    return (int) $pdo->lastInsertId();
}

function handle_triage_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $currentPage = (string) ($GLOBALS['currentPage'] ?? '');
    if (!in_array($currentPage, ['triage', 'emergency-triage'], true)) {
        return;
    }

    $expectedAction = $currentPage === 'emergency-triage'
        ? 'create_emergency_triage_record'
        : 'create_triage_record';

    if (($_POST['form_action'] ?? '') !== $expectedAction) {
        return;
    }

    requirePermission('emergency.triage');

    try {
        $recordId = createTriageRecord($_POST, $currentPage);
        $message = $currentPage === 'emergency-triage'
            ? 'Emergency triage saved successfully. Record ID: ' . $recordId . '.'
            : 'Triage saved successfully. Record ID: ' . $recordId . '.';
        triage_flash('success', $message);
    } catch (Throwable $exception) {
        triage_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=' . $currentPage));
}
