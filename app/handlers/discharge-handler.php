<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';

function discharge_flash(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['discharge_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function discharge_get_flash(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['discharge_flash']) || !is_array($_SESSION['discharge_flash'])) {
        return null;
    }

    $flash = $_SESSION['discharge_flash'];
    unset($_SESSION['discharge_flash']);

    return $flash;
}

function createDischargeRecord(array $data): int
{
    $errors = validate_required($data, [
        'admission_id',
        'discharged_by',
        'discharge_date',
        'discharge_condition',
        'discharge_summary',
        'outcome',
    ]);
    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    $pdo = database_connection();
    $pdo->beginTransaction();

    try {
        $admissionStatement = $pdo->prepare(
            'SELECT id, bed_id, status
             FROM admissions
             WHERE id = :id
             LIMIT 1'
        );
        $admissionStatement->execute(['id' => (int) $data['admission_id']]);
        $admission = $admissionStatement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($admission)) {
            throw new RuntimeException('Admission record not found.');
        }

        if ((string) ($admission['status'] ?? '') === 'discharged') {
            throw new RuntimeException('This admission has already been discharged.');
        }

        $statement = $pdo->prepare(
            'INSERT INTO discharges (
                admission_id,
                discharged_by,
                discharge_date,
                discharge_condition,
                discharge_summary,
                instructions,
                outcome
            ) VALUES (
                :admission_id,
                :discharged_by,
                :discharge_date,
                :discharge_condition,
                :discharge_summary,
                :instructions,
                :outcome
            )'
        );
        $statement->execute([
            'admission_id' => (int) $data['admission_id'],
            'discharged_by' => (int) $data['discharged_by'],
            'discharge_date' => str_replace('T', ' ', (string) $data['discharge_date']),
            'discharge_condition' => trim((string) $data['discharge_condition']),
            'discharge_summary' => trim((string) $data['discharge_summary']),
            'instructions' => trim((string) ($data['instructions'] ?? '')) !== '' ? trim((string) $data['instructions']) : null,
            'outcome' => trim((string) $data['outcome']),
        ]);

        $updateAdmission = $pdo->prepare(
            'UPDATE admissions
             SET status = :status
             WHERE id = :id'
        );
        $updateAdmission->execute([
            'status' => 'discharged',
            'id' => (int) $data['admission_id'],
        ]);

        if (($admission['bed_id'] ?? null) !== null) {
            $releaseBed = $pdo->prepare('UPDATE beds SET status = :status WHERE id = :id');
            $releaseBed->execute([
                'status' => 'available',
                'id' => (int) $admission['bed_id'],
            ]);
        }

        $dischargeId = (int) $pdo->lastInsertId();
        $pdo->commit();

        return $dischargeId;
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function handle_discharge_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (($GLOBALS['currentPage'] ?? '') !== 'discharge-referral') {
        return;
    }

    if (($_POST['form_action'] ?? '') !== 'create_discharge_record') {
        return;
    }

    requirePermission('inpatient.discharge');

    try {
        $dischargeId = createDischargeRecord($_POST);
        discharge_flash('success', 'Discharge prepared successfully. Record ID: ' . $dischargeId . '.');
    } catch (Throwable $exception) {
        discharge_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=discharge-referral'));
}
