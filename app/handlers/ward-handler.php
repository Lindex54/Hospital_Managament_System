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

function generateWardCode(string $name): string
{
    $normalized = strtoupper((string) preg_replace('/[^A-Za-z0-9]+/', '-', trim($name)));
    $normalized = trim($normalized, '-');

    if ($normalized === '') {
        $normalized = 'WARD';
    }

    $baseCode = substr($normalized, 0, 24);
    $pdo = database_connection();
    $candidate = $baseCode;
    $suffix = 1;

    while (true) {
        $statement = $pdo->prepare(
            'SELECT COUNT(*)
             FROM wards
             WHERE code = :code'
        );
        $statement->execute(['code' => $candidate]);

        if ((int) $statement->fetchColumn() === 0) {
            return $candidate;
        }

        $candidate = substr($baseCode, 0, max(1, 24 - strlen((string) $suffix) - 1)) . '-' . $suffix;
        $suffix++;
    }
}

function normalizeWardRooms(array $rooms): array
{
    $normalized = [];

    foreach ($rooms as $room) {
        if (!is_array($room)) {
            continue;
        }

        $roomNumber = trim((string) ($room['room_number'] ?? ''));
        $roomName = trim((string) ($room['name'] ?? ''));
        $roomType = trim((string) ($room['room_type'] ?? ''));
        $bedCount = (int) ($room['bed_count'] ?? 0);
        $normalizedRoomType = strtolower($roomType);

        if ($roomNumber === '' && $roomName === '' && $bedCount === 0) {
            continue;
        }

        if ($normalizedRoomType === '' || !in_array($normalizedRoomType, ['general', 'private', 'other'], true)) {
            throw new RuntimeException('Choose a valid room type for each room entry.');
        }

        if ($bedCount <= 0) {
            throw new RuntimeException('Each room must have at least one bed.');
        }

        $normalized[] = [
            'name' => $roomName !== '' ? $roomName : 'Room',
            'room_number' => $roomNumber,
            'room_type' => ucfirst($normalizedRoomType),
            'bed_count' => $bedCount,
        ];
    }

    if ($normalized === []) {
        throw new RuntimeException('Add at least one room before saving the ward.');
    }

    return $normalized;
}

function createWardRecord(array $data): int
{
    $errors = validate_required($data, ['name', 'ward_type', 'gender_policy', 'status']);
    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    $rooms = normalizeWardRooms($data['rooms'] ?? []);
    $capacity = array_sum(array_map(
        static fn (array $room): int => (int) $room['bed_count'],
        $rooms
    ));
    $pdo = database_connection();
    $name = trim((string) $data['name']);
    $code = trim((string) ($data['code'] ?? ''));
    $status = trim((string) $data['status']);

    $pdo->beginTransaction();

    try {
        $statement = $pdo->prepare(
            'INSERT INTO wards (
                name,
                code,
                ward_type,
                gender_policy,
                capacity,
                status
            ) VALUES (
                :name,
                :code,
                :ward_type,
                :gender_policy,
                :capacity,
                :status
            )'
        );
        $statement->execute([
            'name' => $name,
            'code' => $code !== '' ? strtoupper($code) : generateWardCode($name),
            'ward_type' => trim((string) $data['ward_type']),
            'gender_policy' => trim((string) $data['gender_policy']),
            'capacity' => $capacity,
            'status' => $status,
        ]);

        $wardId = (int) $pdo->lastInsertId();

        $roomStatement = $pdo->prepare(
            'INSERT INTO rooms (
                ward_id,
                name,
                room_number,
                room_type,
                status
            ) VALUES (
                :ward_id,
                :name,
                :room_number,
                :room_type,
                :status
            )'
        );
        $bedStatement = $pdo->prepare(
            'INSERT INTO beds (
                room_id,
                bed_number,
                bed_type,
                status
            ) VALUES (
                :room_id,
                :bed_number,
                :bed_type,
                :status
            )'
        );

        $roomSequence = 0;

        foreach ($rooms as $room) {
            $roomSequence++;
            $roomNumber = trim((string) $room['room_number']);
            if ($roomNumber === '') {
                $roomNumber = sprintf('R%02d', $roomSequence);
            }

            $roomName = trim((string) $room['name']);
            if ($roomName === '') {
                $roomName = 'Room ' . $roomNumber;
            }

            $roomStatement->execute([
                'ward_id' => $wardId,
                'name' => $roomName,
                'room_number' => $roomNumber,
                'room_type' => $room['room_type'],
                'status' => $status === 'inactive' ? 'inactive' : 'active',
            ]);

            $roomId = (int) $pdo->lastInsertId();
            for ($index = 1; $index <= (int) $room['bed_count']; $index++) {
                $bedStatement->execute([
                    'room_id' => $roomId,
                    'bed_number' => 'Bed ' . $index,
                    'bed_type' => $room['room_type'],
                    'status' => $status === 'inactive' ? 'maintenance' : 'available',
                ]);
            }
        }

        $pdo->commit();

        return $wardId;
    } catch (PDOException $exception) {
        $pdo->rollBack();

        if ($exception->getCode() === '23000') {
            throw new RuntimeException('Ward name, ward code, or room number already exists. Please use unique values.');
        }

        throw $exception;
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
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

        if (strtolower((string) ($resource['room_type'] ?? '')) === 'private' && (int) ($resource['occupied_beds'] ?? 0) > 0) {
            throw new RuntimeException('Selected private room is already occupied and cannot be assigned again.');
        }

        $currentBedId = isset($admission['bed_id']) ? (int) $admission['bed_id'] : 0;
        $newBedId = (int) $data['bed_id'];
        $bedStatus = (string) ($resource['bed_status'] ?? '');

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

    $currentPage = (string) ($GLOBALS['currentPage'] ?? '');

    if (!in_array($currentPage, ['wards', 'ward-beds'], true)) {
        return;
    }

    $formAction = (string) ($_POST['form_action'] ?? '');

    if (!in_array($formAction, ['create_ward', 'assign_bed'], true)) {
        return;
    }

    requirePermission('wards.view');

    try {
        if ($formAction === 'create_ward') {
            $wardId = createWardRecord($_POST);
            ward_flash('success', 'Ward added successfully. Record ID: ' . $wardId . '.');
        } else {
            $transferId = assignAdmissionBed($_POST);
            ward_flash('success', 'Bed assignment saved successfully. Transfer record ID: ' . $transferId . '.');
        }
    } catch (Throwable $exception) {
        ward_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=' . $currentPage));
}
