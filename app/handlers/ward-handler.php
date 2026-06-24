<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';

function ward_flash(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['ward_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function ward_get_flash(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['ward_flash']) || !is_array($_SESSION['ward_flash'])) {
        return null;
    }

    $flash = $_SESSION['ward_flash'];
    unset($_SESSION['ward_flash']);

    return $flash;
}

function assignAdmissionBed(array $data): int
{
    $errors = validate_required($data, ['admission_id', 'ward_id', 'room_id', 'bed_id', 'assigned_at']);
    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    $pdo = database_connection();
    $pdo->beginTransaction();

    try {
        $admissionStatement = $pdo->prepare(
            'SELECT ward_id, room_id, bed_id
             FROM admissions
             WHERE id = :id
             LIMIT 1'
        );
        $admissionStatement->execute(['id' => (int) $data['admission_id']]);
        $admission = $admissionStatement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($admission)) {
            throw new RuntimeException('Admission record not found.');
        }

        $bedStatement = $pdo->prepare(
            'SELECT status
             FROM beds
             WHERE id = :id
             LIMIT 1'
        );
        $bedStatement->execute(['id' => (int) $data['bed_id']]);
        $bedStatus = (string) $bedStatement->fetchColumn();

        if ($bedStatus === '') {
            throw new RuntimeException('Selected bed was not found.');
        }

        $currentBedId = isset($admission['bed_id']) ? (int) $admission['bed_id'] : 0;
        $newBedId = (int) $data['bed_id'];

        if ($bedStatus !== 'available' && $currentBedId !== $newBedId) {
            throw new RuntimeException('Selected bed is not currently available.');
        }

        $transferStatement = $pdo->prepare(
            'INSERT INTO bed_transfers (
                admission_id,
                from_ward_id,
                from_room_id,
                from_bed_id,
                to_ward_id,
                to_room_id,
                to_bed_id,
                transfer_reason,
                transferred_by,
                transferred_at
            ) VALUES (
                :admission_id,
                :from_ward_id,
                :from_room_id,
                :from_bed_id,
                :to_ward_id,
                :to_room_id,
                :to_bed_id,
                :transfer_reason,
                :transferred_by,
                :transferred_at
            )'
        );
        $transferStatement->execute([
            'admission_id' => (int) $data['admission_id'],
            'from_ward_id' => ($admission['ward_id'] ?? null) !== null ? (int) $admission['ward_id'] : null,
            'from_room_id' => ($admission['room_id'] ?? null) !== null ? (int) $admission['room_id'] : null,
            'from_bed_id' => ($admission['bed_id'] ?? null) !== null ? (int) $admission['bed_id'] : null,
            'to_ward_id' => (int) $data['ward_id'],
            'to_room_id' => (int) $data['room_id'],
            'to_bed_id' => $newBedId,
            'transfer_reason' => trim((string) ($data['notes'] ?? '')) !== '' ? trim((string) $data['notes']) : 'Bed assignment updated from Ward & Beds module.',
            'transferred_by' => null,
            'transferred_at' => str_replace('T', ' ', (string) $data['assigned_at']),
        ]);

        $updateAdmission = $pdo->prepare(
            'UPDATE admissions
             SET ward_id = :ward_id, room_id = :room_id, bed_id = :bed_id
             WHERE id = :id'
        );
        $updateAdmission->execute([
            'ward_id' => (int) $data['ward_id'],
            'room_id' => (int) $data['room_id'],
            'bed_id' => $newBedId,
            'id' => (int) $data['admission_id'],
        ]);

        if ($currentBedId > 0 && $currentBedId !== $newBedId) {
            $releaseBed = $pdo->prepare('UPDATE beds SET status = :status WHERE id = :id');
            $releaseBed->execute([
                'status' => 'available',
                'id' => $currentBedId,
            ]);
        }

        $occupyBed = $pdo->prepare('UPDATE beds SET status = :status WHERE id = :id');
        $occupyBed->execute([
            'status' => 'occupied',
            'id' => $newBedId,
        ]);

        $transferId = (int) $pdo->lastInsertId();
        $pdo->commit();

        return $transferId;
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function handle_ward_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (($GLOBALS['currentPage'] ?? '') !== 'ward-beds') {
        return;
    }

    if (($_POST['form_action'] ?? '') !== 'assign_bed') {
        return;
    }

    requirePermission('wards.view');

    try {
        $transferId = assignAdmissionBed($_POST);
        ward_flash('success', 'Bed assignment saved successfully. Transfer record ID: ' . $transferId . '.');
    } catch (Throwable $exception) {
        ward_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=ward-beds'));
}
