<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once dirname(__DIR__) . '/config/database.php';

function clinical_forms_pdo(): PDO
{
    return database_connection();
}

function clinical_form_fetch_departments(): array
{
    $statement = clinical_forms_pdo()->query(
        "SELECT id, name, code
         FROM departments
         WHERE status = 'active'
         ORDER BY name ASC"
    );

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function clinical_form_fetch_doctor_departments(): array
{
    $statement = clinical_forms_pdo()->query(
        "SELECT id, name, code
         FROM departments
         WHERE status = 'active'
           AND LOWER(name) IN ('outpatient', 'inpatient', 'laboratory')
         ORDER BY FIELD(LOWER(name), 'outpatient', 'inpatient', 'laboratory')"
    );

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function clinical_form_fetch_patients(int $limit = 100): array
{
    $statement = clinical_forms_pdo()->prepare(
        'SELECT id, patient_number, first_name, middle_name, last_name, phone
         FROM patients
         ORDER BY created_at DESC, id DESC
         LIMIT :limit_value'
    );
    $statement->bindValue(':limit_value', $limit, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function clinical_form_fetch_doctors(int $limit = 100): array
{
    $statement = clinical_forms_pdo()->prepare(
        "SELECT staff.id, staff.staff_number, staff.first_name, staff.last_name, staff.job_title, departments.name AS department_name
         FROM staff
         INNER JOIN departments ON departments.id = staff.department_id
         WHERE staff.status = 'active'
           AND (
                LOWER(COALESCE(staff.job_title, '')) LIKE '%doctor%'
                OR LOWER(COALESCE(staff.job_title, '')) LIKE '%consultant%'
                OR LOWER(COALESCE(staff.job_title, '')) LIKE '%surgeon%'
                OR LOWER(COALESCE(staff.job_title, '')) LIKE '%physician%'
                OR LOWER(COALESCE(staff.job_title, '')) LIKE '%specialist%'
                OR LOWER(COALESCE(staff.job_title, '')) LIKE '%medical officer%'
                OR LOWER(COALESCE(staff.job_title, '')) LIKE '%registrar%'
                OR LOWER(COALESCE(staff.job_title, '')) LIKE '%resident%'
           )
         ORDER BY staff.first_name ASC, staff.last_name ASC
         LIMIT :limit_value"
    );
    $statement->bindValue(':limit_value', $limit, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function clinical_form_fetch_appointments(int $limit = 100): array
{
    $statement = clinical_forms_pdo()->prepare(
        'SELECT appointments.id, appointments.appointment_date, appointments.status, patients.patient_number,
                patients.first_name, patients.middle_name, patients.last_name, departments.name AS department_name
         FROM appointments
         INNER JOIN patients ON patients.id = appointments.patient_id
         INNER JOIN departments ON departments.id = appointments.department_id
         ORDER BY appointments.appointment_date DESC, appointments.id DESC
         LIMIT :limit_value'
    );
    $statement->bindValue(':limit_value', $limit, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function clinical_form_fetch_visits(int $limit = 100): array
{
    $statement = clinical_forms_pdo()->prepare(
        'SELECT visits.id, visits.visit_number, visits.visit_type, visits.visit_status, visits.visit_date,
                patients.patient_number, patients.first_name, patients.middle_name, patients.last_name,
                departments.name AS department_name
         FROM visits
         INNER JOIN patients ON patients.id = visits.patient_id
         INNER JOIN departments ON departments.id = visits.department_id
         ORDER BY visits.visit_date DESC, visits.id DESC
         LIMIT :limit_value'
    );
    $statement->bindValue(':limit_value', $limit, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function clinical_form_fetch_admissions(int $limit = 100): array
{
    $statement = clinical_forms_pdo()->prepare(
        'SELECT admissions.id, admissions.admission_number, admissions.status, admissions.admission_date,
                patients.patient_number, patients.first_name, patients.middle_name, patients.last_name,
                wards.name AS ward_name, rooms.room_number, beds.bed_number
         FROM admissions
         INNER JOIN patients ON patients.id = admissions.patient_id
         LEFT JOIN wards ON wards.id = admissions.ward_id
         LEFT JOIN rooms ON rooms.id = admissions.room_id
         LEFT JOIN beds ON beds.id = admissions.bed_id
         ORDER BY admissions.admission_date DESC, admissions.id DESC
         LIMIT :limit_value'
    );
    $statement->bindValue(':limit_value', $limit, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function clinical_form_fetch_wards(bool $onlyAvailableForAdmission = false): array
{
    $sql = "SELECT wards.id,
                   wards.name,
                   wards.code,
                   wards.ward_type,
                   COALESCE(SUM(
                       CASE
                           WHEN room_stats.is_admission_available = 1 THEN room_stats.available_beds
                           ELSE 0
                       END
                   ), 0) AS available_beds
            FROM wards
            LEFT JOIN (
                SELECT rooms.id,
                       rooms.ward_id,
                       LOWER(COALESCE(rooms.room_type, 'other')) AS room_type,
                       COALESCE(SUM(CASE WHEN beds.status = 'available' THEN 1 ELSE 0 END), 0) AS available_beds,
                       COALESCE(SUM(CASE WHEN beds.status = 'occupied' THEN 1 ELSE 0 END), 0) AS occupied_beds,
                       CASE
                           WHEN LOWER(COALESCE(rooms.room_type, 'other')) = 'private'
                               THEN CASE
                                   WHEN COALESCE(SUM(CASE WHEN beds.status = 'occupied' THEN 1 ELSE 0 END), 0) = 0
                                        AND COALESCE(SUM(CASE WHEN beds.status = 'available' THEN 1 ELSE 0 END), 0) > 0
                                       THEN 1
                                   ELSE 0
                               END
                           ELSE CASE
                               WHEN COALESCE(SUM(CASE WHEN beds.status = 'available' THEN 1 ELSE 0 END), 0) > 0
                                   THEN 1
                               ELSE 0
                           END
                       END AS is_admission_available
                FROM rooms
                LEFT JOIN beds ON beds.room_id = rooms.id
                WHERE rooms.status = 'active'
                GROUP BY rooms.id, rooms.ward_id, rooms.room_type
            ) AS room_stats ON room_stats.ward_id = wards.id
            WHERE wards.status = 'active'
            GROUP BY wards.id, wards.name, wards.code, wards.ward_type";

    if ($onlyAvailableForAdmission) {
        $sql .= '
            HAVING COALESCE(SUM(room_stats.is_admission_available), 0) > 0';
    }

    $sql .= '
            ORDER BY wards.name ASC';

    $statement = clinical_forms_pdo()->query($sql);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function clinical_form_fetch_rooms(bool $onlyAvailableForAdmission = false): array
{
    $sql = "SELECT rooms.id,
                   rooms.ward_id,
                   rooms.name,
                   rooms.room_number,
                   rooms.room_type,
                   rooms.status,
                   COALESCE(SUM(CASE WHEN beds.status = 'available' THEN 1 ELSE 0 END), 0) AS available_beds,
                   COALESCE(SUM(CASE WHEN beds.status = 'occupied' THEN 1 ELSE 0 END), 0) AS occupied_beds
            FROM rooms
            LEFT JOIN beds ON beds.room_id = rooms.id
            WHERE rooms.status = 'active'
            GROUP BY rooms.id, rooms.ward_id, rooms.name, rooms.room_number, rooms.room_type, rooms.status";

    if ($onlyAvailableForAdmission) {
        $sql .= "
            HAVING (
                LOWER(COALESCE(rooms.room_type, 'other')) = 'private'
                AND COALESCE(SUM(CASE WHEN beds.status = 'occupied' THEN 1 ELSE 0 END), 0) = 0
                AND COALESCE(SUM(CASE WHEN beds.status = 'available' THEN 1 ELSE 0 END), 0) > 0
            ) OR (
                LOWER(COALESCE(rooms.room_type, 'other')) <> 'private'
                AND COALESCE(SUM(CASE WHEN beds.status = 'available' THEN 1 ELSE 0 END), 0) > 0
            )";
    }

    $sql .= '
            ORDER BY rooms.room_number ASC, rooms.name ASC';

    $statement = clinical_forms_pdo()->query($sql);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function clinical_form_fetch_beds(bool $onlyAvailableForAdmission = false): array
{
    $sql = "SELECT beds.id,
                   beds.room_id,
                   rooms.ward_id,
                   beds.bed_number,
                   beds.bed_type,
                   beds.status
            FROM beds
            INNER JOIN rooms ON rooms.id = beds.room_id";

    if ($onlyAvailableForAdmission) {
        $sql .= "
            WHERE beds.status = 'available'
              AND rooms.status = 'active'";
    }

    $sql .= '
            ORDER BY beds.bed_number ASC';

    $statement = clinical_forms_pdo()->query($sql);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function clinical_form_patient_name(array $patient): string
{
    return trim(implode(' ', array_filter([
        $patient['first_name'] ?? '',
        $patient['middle_name'] ?? '',
        $patient['last_name'] ?? '',
    ], static fn ($value): bool => trim((string) $value) !== '')));
}

function clinical_form_doctor_name(array $staff): string
{
    return trim(implode(' ', array_filter([
        $staff['first_name'] ?? '',
        $staff['last_name'] ?? '',
    ], static fn ($value): bool => trim((string) $value) !== '')));
}
