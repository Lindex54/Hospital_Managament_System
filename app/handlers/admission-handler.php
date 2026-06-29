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

    $pdo->beginTransaction();

    try {
        $resourceStatement = $pdo->prepare(
            'SELECT rooms.id AS room_id,
                    rooms.ward_id,
                    rooms.status AS room_status,
                    rooms.room_type,
                    (
                        SELECT COUNT(*)
                        FROM beds occupied_beds
                        WHERE occupied_beds.room_id = rooms.id
                          AND occupied_beds.status = \'occupied\'
                    ) AS occupied_beds,
                    beds.id AS bed_id,
                    beds.status AS bed_status
             FROM beds
             INNER JOIN rooms ON rooms.id = beds.room_id
             WHERE beds.id = :bed_id
               AND rooms.id = :room_id
             LIMIT 1'
        );
        $resourceStatement->execute([
            'bed_id' => (int) $data['bed_id'],
            'room_id' => (int) $data['room_id'],
        ]);
        $resource = $resourceStatement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($resource)) {
            throw new RuntimeException('Selected room and bed do not match.');
        }

        if ((int) ($resource['ward_id'] ?? 0) !== (int) $data['ward_id']) {
            throw new RuntimeException('Selected room does not belong to the chosen ward.');
        }

        if ((string) ($resource['room_status'] ?? '') !== 'active') {
            throw new RuntimeException('Selected room is not currently available.');
        }

        if (strtolower((string) ($resource['room_type'] ?? '')) === 'private' && (int) ($resource['occupied_beds'] ?? 0) > 0) {
            throw new RuntimeException('Selected private room is already occupied and cannot be assigned again.');
        }

        if ((string) ($resource['bed_status'] ?? '') !== 'available') {
            throw new RuntimeException('Selected bed is no longer available.');
        }

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

        $admissionId = (int) $pdo->lastInsertId();

        $bedUpdate = $pdo->prepare(
            'UPDATE beds
             SET status = :status
             WHERE id = :bed_id'
        );
        $bedUpdate->execute([
            'status' => $status === 'active' ? 'occupied' : 'reserved',
            'bed_id' => (int) $data['bed_id'],
        ]);

        $pdo->commit();

        return $admissionId;
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function handle_inpatient_admission_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (!in_array(($GLOBALS['currentPage'] ?? ''), ['inpatient', 'inpatient-admission'], true)) {
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

    $redirectPage = ($GLOBALS['currentPage'] ?? '') === 'inpatient-admission' ? 'inpatient-admission' : 'inpatient';
    redirect(base_url('index.php?page=' . $redirectPage));
}
