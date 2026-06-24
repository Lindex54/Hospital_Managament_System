<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/database.php';

$pdo = database_connection();
$existingCount = (int) $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$timestamp = date('YmdHis');

$firstNames = [
    'Amina', 'Brian', 'Clara', 'David', 'Esther',
    'Frank', 'Grace', 'Henry', 'Irene', 'James',
    'Karen', 'Leon', 'Mary', 'Noah', 'Olive',
    'Peter', 'Queen', 'Robert', 'Sarah', 'Timothy',
];

$lastNames = [
    'Namusoke', 'Kato', 'Achieng', 'Okello', 'Nankya',
    'Mugisha', 'Atim', 'Wekesa', 'Asiimwe', 'Ssenfuma',
    'Nabirye', 'Tumusiime', 'Nabukenya', 'Ouma', 'Nakato',
    'Muwanga', 'Akello', 'Ssemanda', 'Nambooze', 'Byaruhanga',
];

$genders = ['female', 'male'];
$bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'O+', 'O-'];
$maritalStatuses = ['single', 'married', 'divorced'];
$districts = ['Kampala', 'Wakiso', 'Mukono', 'Jinja', 'Mbarara'];

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

$created = 0;

for ($index = 0; $index < 20; $index++) {
    $sequence = $existingCount + $index + 1;
    $firstName = $firstNames[$index];
    $lastName = $lastNames[$index];
    $gender = $genders[$index % count($genders)];
    $district = $districts[$index % count($districts)];
    $phoneSuffix = str_pad((string) (700000000 + $sequence), 9, '0', STR_PAD_LEFT);

    $statement->execute([
        'patient_number' => sprintf('PAT-%s-%04d', $timestamp, $sequence),
        'first_name' => $firstName,
        'middle_name' => chr(65 + ($index % 26)),
        'last_name' => $lastName,
        'date_of_birth' => date('Y-m-d', strtotime('-' . (20 + $index) . ' years -' . (($index % 12) + 1) . ' months')),
        'gender' => $gender,
        'blood_group' => $bloodGroups[$index % count($bloodGroups)],
        'marital_status' => $maritalStatuses[$index % count($maritalStatuses)],
        'national_id' => sprintf('CM%010d', $sequence),
        'phone' => '07' . substr($phoneSuffix, -8),
        'alternate_phone' => '07' . substr((string) (80000000 + $sequence), -8),
        'email' => strtolower($firstName . '.' . $lastName . $sequence . '@example.com'),
        'address_line_1' => 'Plot ' . (10 + $sequence) . ' Health Road',
        'address_line_2' => 'Near Community Clinic',
        'city' => $district,
        'district' => $district,
        'emergency_contact_name' => $lastName . ' Contact',
        'emergency_contact_phone' => '07' . substr((string) (81000000 + $sequence), -8),
        'allergies' => $index % 4 === 0 ? 'Penicillin' : null,
        'notes' => 'Dummy patient seeded for UI testing.',
        'status' => 'active',
    ]);

    $created++;
}

echo 'created:' . $created . PHP_EOL;
echo 'total_patients:' . ((int) $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn()) . PHP_EOL;
