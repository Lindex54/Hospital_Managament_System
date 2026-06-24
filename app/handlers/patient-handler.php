<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';

function patient_flash(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['patient_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function patient_get_flash(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['patient_flash']) || !is_array($_SESSION['patient_flash'])) {
        return null;
    }

    $flash = $_SESSION['patient_flash'];
    unset($_SESSION['patient_flash']);

    return $flash;
}

function generatePatientNumber(): string
{
    $pdo = database_connection();
    $dateSegment = date('Ymd');
    $pattern = 'PAT-' . $dateSegment . '-%';

    $statement = $pdo->prepare(
        'SELECT patient_number
         FROM patients
         WHERE patient_number LIKE :pattern
         ORDER BY id DESC
         LIMIT 1'
    );
    $statement->execute(['pattern' => $pattern]);
    $lastPatientNumber = (string) $statement->fetchColumn();
    $nextSequence = 1;

    if ($lastPatientNumber !== '' && preg_match('/(\d{4})$/', $lastPatientNumber, $matches) === 1) {
        $nextSequence = ((int) $matches[1]) + 1;
    }

    return sprintf('PAT-%s-%04d', $dateSegment, $nextSequence);
}

function createPatient(array $data): int
{
    $errors = validate_required($data, [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'marital_status',
        'status',
        'phone',
        'address_line_1',
        'city',
        'district',
        'emergency_contact_name',
        'emergency_contact_phone',
    ]);

    if ($errors !== []) {
        throw new RuntimeException((string) reset($errors));
    }

    $pdo = database_connection();
    $statement = $pdo->prepare(
        'INSERT INTO patients (
            patient_number,
            first_name,
            middle_name,
            last_name,
            date_of_birth,
            gender,
            blood_group,
            marital_status,
            national_id,
            phone,
            alternate_phone,
            email,
            address_line_1,
            address_line_2,
            city,
            district,
            emergency_contact_name,
            emergency_contact_phone,
            allergies,
            notes,
            status
        ) VALUES (
            :patient_number,
            :first_name,
            :middle_name,
            :last_name,
            :date_of_birth,
            :gender,
            :blood_group,
            :marital_status,
            :national_id,
            :phone,
            :alternate_phone,
            :email,
            :address_line_1,
            :address_line_2,
            :city,
            :district,
            :emergency_contact_name,
            :emergency_contact_phone,
            :allergies,
            :notes,
            :status
        )'
    );

    $statement->execute([
        'patient_number' => generatePatientNumber(),
        'first_name' => trim((string) $data['first_name']),
        'middle_name' => trim((string) ($data['middle_name'] ?? '')) !== '' ? trim((string) $data['middle_name']) : null,
        'last_name' => trim((string) $data['last_name']),
        'date_of_birth' => (string) $data['date_of_birth'],
        'gender' => (string) $data['gender'],
        'blood_group' => trim((string) ($data['blood_group'] ?? '')) !== '' ? trim((string) $data['blood_group']) : null,
        'marital_status' => trim((string) $data['marital_status']),
        'national_id' => trim((string) ($data['national_id'] ?? '')) !== '' ? trim((string) $data['national_id']) : null,
        'phone' => trim((string) $data['phone']),
        'alternate_phone' => trim((string) ($data['alternate_phone'] ?? '')) !== '' ? trim((string) $data['alternate_phone']) : null,
        'email' => trim((string) ($data['email'] ?? '')) !== '' ? trim((string) $data['email']) : null,
        'address_line_1' => trim((string) $data['address_line_1']),
        'address_line_2' => trim((string) ($data['address_line_2'] ?? '')) !== '' ? trim((string) $data['address_line_2']) : null,
        'city' => trim((string) $data['city']),
        'district' => trim((string) $data['district']),
        'emergency_contact_name' => trim((string) $data['emergency_contact_name']),
        'emergency_contact_phone' => trim((string) $data['emergency_contact_phone']),
        'allergies' => trim((string) ($data['allergies'] ?? '')) !== '' ? trim((string) $data['allergies']) : null,
        'notes' => trim((string) ($data['notes'] ?? '')) !== '' ? trim((string) $data['notes']) : null,
        'status' => (string) $data['status'],
    ]);

    return (int) $pdo->lastInsertId();
}

function handle_patient_submission(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (($GLOBALS['currentPage'] ?? '') !== 'patients') {
        return;
    }

    if (($_POST['form_action'] ?? '') !== 'create_patient') {
        return;
    }

    requirePermission('patients.create');

    try {
        $patientId = createPatient($_POST);
        patient_flash('success', 'Patient registered successfully. Record ID: ' . $patientId . '.');
    } catch (Throwable $exception) {
        patient_flash('error', $exception->getMessage());
    }

    redirect(base_url('index.php?page=patients'));
}
