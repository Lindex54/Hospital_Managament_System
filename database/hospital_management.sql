-- Hospital Management System
-- Initial relational schema based on the patient journey through the hospital.
-- MySQL 8+ recommended.

CREATE DATABASE IF NOT EXISTS hospital_management
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE hospital_management;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS invoice_items;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS medicine_stock_movements;
DROP TABLE IF EXISTS prescription_items;
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS medicines;
DROP TABLE IF EXISTS medicine_categories;
DROP TABLE IF EXISTS lab_results;
DROP TABLE IF EXISTS lab_request_items;
DROP TABLE IF EXISTS lab_requests;
DROP TABLE IF EXISTS discharges;
DROP TABLE IF EXISTS bed_transfers;
DROP TABLE IF EXISTS admissions;
DROP TABLE IF EXISTS beds;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS wards;
DROP TABLE IF EXISTS consultations;
DROP TABLE IF EXISTS vitals;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS visits;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS staff;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS departments;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE departments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(30) DEFAULT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    label VARCHAR(150) NOT NULL,
    module VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_permissions_module (module)
) ENGINE=InnoDB;

CREATE TABLE role_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_role_permissions_role
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_permissions_permission
        FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY uq_role_permission (role_id, permission_id)
) ENGINE=InnoDB;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'locked') NOT NULL DEFAULT 'active',
    last_login_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE staff (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED DEFAULT NULL UNIQUE,
    department_id BIGINT UNSIGNED NOT NULL,
    staff_number VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender ENUM('male', 'female', 'other') DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    job_title VARCHAR(100) DEFAULT NULL,
    hire_date DATE DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_staff_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_staff_department
        FOREIGN KEY (department_id) REFERENCES departments(id)
) ENGINE=InnoDB;

CREATE TABLE patients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_number VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) DEFAULT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE DEFAULT NULL,
    gender ENUM('male', 'female', 'other') DEFAULT NULL,
    blood_group VARCHAR(10) DEFAULT NULL,
    marital_status VARCHAR(30) DEFAULT NULL,
    national_id VARCHAR(50) DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    alternate_phone VARCHAR(30) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    address_line_1 VARCHAR(255) DEFAULT NULL,
    address_line_2 VARCHAR(255) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    district VARCHAR(100) DEFAULT NULL,
    emergency_contact_name VARCHAR(150) DEFAULT NULL,
    emergency_contact_phone VARCHAR(30) DEFAULT NULL,
    allergies TEXT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('active', 'inactive', 'deceased') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_patients_name (last_name, first_name),
    INDEX idx_patients_phone (phone)
) ENGINE=InnoDB;

CREATE TABLE appointments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NOT NULL,
    doctor_id BIGINT UNSIGNED DEFAULT NULL,
    appointment_date DATETIME NOT NULL,
    reason TEXT DEFAULT NULL,
    status ENUM('scheduled', 'confirmed', 'cancelled', 'completed', 'no_show') NOT NULL DEFAULT 'scheduled',
    notes TEXT DEFAULT NULL,
    created_by BIGINT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_appointments_patient
        FOREIGN KEY (patient_id) REFERENCES patients(id),
    CONSTRAINT fk_appointments_department
        FOREIGN KEY (department_id) REFERENCES departments(id),
    CONSTRAINT fk_appointments_doctor
        FOREIGN KEY (doctor_id) REFERENCES staff(id) ON DELETE SET NULL,
    CONSTRAINT fk_appointments_created_by
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_appointments_date (appointment_date),
    INDEX idx_appointments_status (status)
) ENGINE=InnoDB;

CREATE TABLE visits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id BIGINT UNSIGNED NOT NULL,
    appointment_id BIGINT UNSIGNED DEFAULT NULL,
    department_id BIGINT UNSIGNED NOT NULL,
    doctor_id BIGINT UNSIGNED DEFAULT NULL,
    visit_number VARCHAR(50) NOT NULL UNIQUE,
    visit_type ENUM('outpatient', 'inpatient', 'emergency', 'follow_up') NOT NULL DEFAULT 'outpatient',
    visit_status ENUM('registered', 'triage', 'consulting', 'admitted', 'completed', 'cancelled') NOT NULL DEFAULT 'registered',
    visit_date DATETIME NOT NULL,
    chief_complaint TEXT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_by BIGINT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_visits_patient
        FOREIGN KEY (patient_id) REFERENCES patients(id),
    CONSTRAINT fk_visits_appointment
        FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    CONSTRAINT fk_visits_department
        FOREIGN KEY (department_id) REFERENCES departments(id),
    CONSTRAINT fk_visits_doctor
        FOREIGN KEY (doctor_id) REFERENCES staff(id) ON DELETE SET NULL,
    CONSTRAINT fk_visits_created_by
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_visits_patient_date (patient_id, visit_date),
    INDEX idx_visits_status (visit_status)
) ENGINE=InnoDB;

CREATE TABLE vitals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    visit_id BIGINT UNSIGNED NOT NULL,
    patient_id BIGINT UNSIGNED NOT NULL,
    recorded_by BIGINT UNSIGNED DEFAULT NULL,
    systolic_bp SMALLINT UNSIGNED DEFAULT NULL,
    diastolic_bp SMALLINT UNSIGNED DEFAULT NULL,
    temperature DECIMAL(5,2) DEFAULT NULL,
    pulse_rate SMALLINT UNSIGNED DEFAULT NULL,
    respiratory_rate SMALLINT UNSIGNED DEFAULT NULL,
    oxygen_saturation DECIMAL(5,2) DEFAULT NULL,
    weight_kg DECIMAL(6,2) DEFAULT NULL,
    height_cm DECIMAL(6,2) DEFAULT NULL,
    bmi DECIMAL(6,2) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_vitals_visit
        FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE CASCADE,
    CONSTRAINT fk_vitals_patient
        FOREIGN KEY (patient_id) REFERENCES patients(id),
    CONSTRAINT fk_vitals_recorded_by
        FOREIGN KEY (recorded_by) REFERENCES staff(id) ON DELETE SET NULL,
    INDEX idx_vitals_visit (visit_id, recorded_at)
) ENGINE=InnoDB;

CREATE TABLE consultations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    visit_id BIGINT UNSIGNED NOT NULL,
    patient_id BIGINT UNSIGNED NOT NULL,
    doctor_id BIGINT UNSIGNED DEFAULT NULL,
    symptoms TEXT DEFAULT NULL,
    diagnosis TEXT DEFAULT NULL,
    treatment_plan TEXT DEFAULT NULL,
    clinical_notes LONGTEXT DEFAULT NULL,
    follow_up_instructions TEXT DEFAULT NULL,
    consultation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_consultations_visit
        FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE CASCADE,
    CONSTRAINT fk_consultations_patient
        FOREIGN KEY (patient_id) REFERENCES patients(id),
    CONSTRAINT fk_consultations_doctor
        FOREIGN KEY (doctor_id) REFERENCES staff(id) ON DELETE SET NULL,
    INDEX idx_consultations_visit (visit_id, consultation_date)
) ENGINE=InnoDB;

CREATE TABLE wards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(30) DEFAULT NULL UNIQUE,
    ward_type VARCHAR(50) DEFAULT NULL,
    gender_policy ENUM('mixed', 'male', 'female', 'children') NOT NULL DEFAULT 'mixed',
    capacity INT UNSIGNED DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ward_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    room_number VARCHAR(30) NOT NULL,
    room_type VARCHAR(50) DEFAULT NULL,
    floor_label VARCHAR(30) DEFAULT NULL,
    status ENUM('active', 'inactive', 'maintenance') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rooms_ward
        FOREIGN KEY (ward_id) REFERENCES wards(id) ON DELETE CASCADE,
    UNIQUE KEY uq_rooms_ward_room_number (ward_id, room_number)
) ENGINE=InnoDB;

CREATE TABLE beds (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NOT NULL,
    bed_number VARCHAR(30) NOT NULL,
    bed_type VARCHAR(50) DEFAULT NULL,
    status ENUM('available', 'occupied', 'reserved', 'maintenance') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_beds_room
        FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    UNIQUE KEY uq_beds_room_bed_number (room_id, bed_number)
) ENGINE=InnoDB;

CREATE TABLE admissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id BIGINT UNSIGNED NOT NULL,
    visit_id BIGINT UNSIGNED NOT NULL UNIQUE,
    ward_id BIGINT UNSIGNED DEFAULT NULL,
    room_id BIGINT UNSIGNED DEFAULT NULL,
    bed_id BIGINT UNSIGNED DEFAULT NULL,
    admitted_by BIGINT UNSIGNED DEFAULT NULL,
    admission_number VARCHAR(50) NOT NULL UNIQUE,
    admission_date DATETIME NOT NULL,
    reason TEXT DEFAULT NULL,
    status ENUM('active', 'discharged', 'cancelled') NOT NULL DEFAULT 'active',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_admissions_patient
        FOREIGN KEY (patient_id) REFERENCES patients(id),
    CONSTRAINT fk_admissions_visit
        FOREIGN KEY (visit_id) REFERENCES visits(id),
    CONSTRAINT fk_admissions_ward
        FOREIGN KEY (ward_id) REFERENCES wards(id) ON DELETE SET NULL,
    CONSTRAINT fk_admissions_room
        FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    CONSTRAINT fk_admissions_bed
        FOREIGN KEY (bed_id) REFERENCES beds(id) ON DELETE SET NULL,
    CONSTRAINT fk_admissions_admitted_by
        FOREIGN KEY (admitted_by) REFERENCES staff(id) ON DELETE SET NULL,
    INDEX idx_admissions_status (status),
    INDEX idx_admissions_date (admission_date)
) ENGINE=InnoDB;

CREATE TABLE bed_transfers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admission_id BIGINT UNSIGNED NOT NULL,
    from_ward_id BIGINT UNSIGNED DEFAULT NULL,
    from_room_id BIGINT UNSIGNED DEFAULT NULL,
    from_bed_id BIGINT UNSIGNED DEFAULT NULL,
    to_ward_id BIGINT UNSIGNED DEFAULT NULL,
    to_room_id BIGINT UNSIGNED DEFAULT NULL,
    to_bed_id BIGINT UNSIGNED DEFAULT NULL,
    transfer_reason TEXT DEFAULT NULL,
    transferred_by BIGINT UNSIGNED DEFAULT NULL,
    transferred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bed_transfers_admission
        FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE CASCADE,
    CONSTRAINT fk_bed_transfers_from_ward
        FOREIGN KEY (from_ward_id) REFERENCES wards(id) ON DELETE SET NULL,
    CONSTRAINT fk_bed_transfers_from_room
        FOREIGN KEY (from_room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    CONSTRAINT fk_bed_transfers_from_bed
        FOREIGN KEY (from_bed_id) REFERENCES beds(id) ON DELETE SET NULL,
    CONSTRAINT fk_bed_transfers_to_ward
        FOREIGN KEY (to_ward_id) REFERENCES wards(id) ON DELETE SET NULL,
    CONSTRAINT fk_bed_transfers_to_room
        FOREIGN KEY (to_room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    CONSTRAINT fk_bed_transfers_to_bed
        FOREIGN KEY (to_bed_id) REFERENCES beds(id) ON DELETE SET NULL,
    CONSTRAINT fk_bed_transfers_transferred_by
        FOREIGN KEY (transferred_by) REFERENCES staff(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE discharges (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admission_id BIGINT UNSIGNED NOT NULL UNIQUE,
    discharged_by BIGINT UNSIGNED DEFAULT NULL,
    discharge_date DATETIME NOT NULL,
    discharge_condition VARCHAR(150) DEFAULT NULL,
    discharge_summary LONGTEXT DEFAULT NULL,
    instructions TEXT DEFAULT NULL,
    outcome VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_discharges_admission
        FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE CASCADE,
    CONSTRAINT fk_discharges_discharged_by
        FOREIGN KEY (discharged_by) REFERENCES staff(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE lab_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    visit_id BIGINT UNSIGNED NOT NULL,
    patient_id BIGINT UNSIGNED NOT NULL,
    requested_by BIGINT UNSIGNED DEFAULT NULL,
    request_number VARCHAR(50) NOT NULL UNIQUE,
    priority ENUM('routine', 'urgent', 'stat') NOT NULL DEFAULT 'routine',
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    clinical_notes TEXT DEFAULT NULL,
    requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_lab_requests_visit
        FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE CASCADE,
    CONSTRAINT fk_lab_requests_patient
        FOREIGN KEY (patient_id) REFERENCES patients(id),
    CONSTRAINT fk_lab_requests_requested_by
        FOREIGN KEY (requested_by) REFERENCES staff(id) ON DELETE SET NULL,
    INDEX idx_lab_requests_visit (visit_id, requested_at)
) ENGINE=InnoDB;

CREATE TABLE lab_request_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lab_request_id BIGINT UNSIGNED NOT NULL,
    test_name VARCHAR(150) NOT NULL,
    specimen_type VARCHAR(100) DEFAULT NULL,
    instructions TEXT DEFAULT NULL,
    status ENUM('pending', 'sample_collected', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_lab_request_items_request
        FOREIGN KEY (lab_request_id) REFERENCES lab_requests(id) ON DELETE CASCADE,
    INDEX idx_lab_request_items_request (lab_request_id)
) ENGINE=InnoDB;

CREATE TABLE lab_results (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lab_request_item_id BIGINT UNSIGNED NOT NULL UNIQUE,
    result_value TEXT DEFAULT NULL,
    reference_range VARCHAR(150) DEFAULT NULL,
    interpretation TEXT DEFAULT NULL,
    result_notes TEXT DEFAULT NULL,
    performed_by BIGINT UNSIGNED DEFAULT NULL,
    verified_by BIGINT UNSIGNED DEFAULT NULL,
    result_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_lab_results_item
        FOREIGN KEY (lab_request_item_id) REFERENCES lab_request_items(id) ON DELETE CASCADE,
    CONSTRAINT fk_lab_results_performed_by
        FOREIGN KEY (performed_by) REFERENCES staff(id) ON DELETE SET NULL,
    CONSTRAINT fk_lab_results_verified_by
        FOREIGN KEY (verified_by) REFERENCES staff(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE medicine_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE medicines (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED DEFAULT NULL,
    name VARCHAR(150) NOT NULL,
    generic_name VARCHAR(150) DEFAULT NULL,
    sku VARCHAR(50) DEFAULT NULL UNIQUE,
    unit_of_measure VARCHAR(50) DEFAULT NULL,
    dosage_form VARCHAR(100) DEFAULT NULL,
    strength VARCHAR(100) DEFAULT NULL,
    reorder_level INT UNSIGNED DEFAULT 0,
    selling_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_medicines_category
        FOREIGN KEY (category_id) REFERENCES medicine_categories(id) ON DELETE SET NULL,
    INDEX idx_medicines_name (name)
) ENGINE=InnoDB;

CREATE TABLE prescriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    visit_id BIGINT UNSIGNED NOT NULL,
    patient_id BIGINT UNSIGNED NOT NULL,
    prescribed_by BIGINT UNSIGNED DEFAULT NULL,
    prescription_number VARCHAR(50) NOT NULL UNIQUE,
    notes TEXT DEFAULT NULL,
    status ENUM('draft', 'prescribed', 'dispensed', 'cancelled') NOT NULL DEFAULT 'prescribed',
    prescribed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_prescriptions_visit
        FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE CASCADE,
    CONSTRAINT fk_prescriptions_patient
        FOREIGN KEY (patient_id) REFERENCES patients(id),
    CONSTRAINT fk_prescriptions_prescribed_by
        FOREIGN KEY (prescribed_by) REFERENCES staff(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE prescription_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    prescription_id BIGINT UNSIGNED NOT NULL,
    medicine_id BIGINT UNSIGNED NOT NULL,
    dosage VARCHAR(100) DEFAULT NULL,
    frequency VARCHAR(100) DEFAULT NULL,
    duration VARCHAR(100) DEFAULT NULL,
    route VARCHAR(100) DEFAULT NULL,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    instructions TEXT DEFAULT NULL,
    status ENUM('pending', 'dispensed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_prescription_items_prescription
        FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE,
    CONSTRAINT fk_prescription_items_medicine
        FOREIGN KEY (medicine_id) REFERENCES medicines(id),
    INDEX idx_prescription_items_prescription (prescription_id)
) ENGINE=InnoDB;

CREATE TABLE medicine_stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    medicine_id BIGINT UNSIGNED NOT NULL,
    prescription_item_id BIGINT UNSIGNED DEFAULT NULL,
    movement_type ENUM('purchase', 'dispense', 'adjustment', 'return', 'transfer') NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_cost DECIMAL(12,2) DEFAULT NULL,
    reference_number VARCHAR(100) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    performed_by BIGINT UNSIGNED DEFAULT NULL,
    movement_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_stock_movements_medicine
        FOREIGN KEY (medicine_id) REFERENCES medicines(id),
    CONSTRAINT fk_stock_movements_prescription_item
        FOREIGN KEY (prescription_item_id) REFERENCES prescription_items(id) ON DELETE SET NULL,
    CONSTRAINT fk_stock_movements_performed_by
        FOREIGN KEY (performed_by) REFERENCES staff(id) ON DELETE SET NULL,
    INDEX idx_stock_movements_medicine (medicine_id, movement_date)
) ENGINE=InnoDB;

CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id BIGINT UNSIGNED NOT NULL,
    visit_id BIGINT UNSIGNED DEFAULT NULL,
    admission_id BIGINT UNSIGNED DEFAULT NULL,
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    invoice_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    due_date DATETIME DEFAULT NULL,
    status ENUM('draft', 'unpaid', 'partially_paid', 'paid', 'cancelled') NOT NULL DEFAULT 'unpaid',
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    notes TEXT DEFAULT NULL,
    created_by BIGINT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_invoices_patient
        FOREIGN KEY (patient_id) REFERENCES patients(id),
    CONSTRAINT fk_invoices_visit
        FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE SET NULL,
    CONSTRAINT fk_invoices_admission
        FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE SET NULL,
    CONSTRAINT fk_invoices_created_by
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_invoices_status (status),
    INDEX idx_invoices_date (invoice_date)
) ENGINE=InnoDB;

CREATE TABLE invoice_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    item_type ENUM('consultation', 'lab', 'admission', 'bed', 'medicine', 'procedure', 'other') NOT NULL,
    reference_table VARCHAR(100) DEFAULT NULL,
    reference_id BIGINT UNSIGNED DEFAULT NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_invoice_items_invoice
        FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice_items_invoice (invoice_id)
) ENGINE=InnoDB;

CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    payment_reference VARCHAR(50) NOT NULL UNIQUE,
    amount_paid DECIMAL(12,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'mobile_money', 'bank_transfer', 'insurance', 'other') NOT NULL DEFAULT 'cash',
    payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    notes TEXT DEFAULT NULL,
    received_by BIGINT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_invoice
        FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    CONSTRAINT fk_payments_received_by
        FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_payments_invoice (invoice_id, payment_date)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id BIGINT UNSIGNED DEFAULT NULL,
    description TEXT DEFAULT NULL,
    old_values JSON DEFAULT NULL,
    new_values JSON DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_logs_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_logs_entity (entity_type, entity_id),
    INDEX idx_audit_logs_created_at (created_at)
) ENGINE=InnoDB;

INSERT INTO roles (name, description) VALUES
('Administrator', 'Full system administration access'),
('Doctor', 'Clinical consultation and treatment access'),
('Emergency Doctor', 'Emergency triage, consultation and admission access'),
('Nurse', 'Nursing and vital signs workflow access'),
('Laboratory Technician', 'Laboratory request and result access'),
('Radiologist', 'Radiology request and imaging result access'),
('Pharmacist', 'Pharmacy and dispensing access');

INSERT INTO permissions (name, label, module, description) VALUES
('dashboard.view', 'View Dashboard', 'dashboard', 'Access the main dashboard overview'),
('patients.view', 'View Patients', 'patients', 'Access patient records'),
('patients.create', 'Create Patients', 'patients', 'Create new patient records'),
('patients.edit', 'Edit Patients', 'patients', 'Edit existing patient records'),
('outpatient.view', 'View Outpatient', 'outpatient', 'Access outpatient workflows'),
('outpatient.create', 'Create Outpatient Visit', 'outpatient', 'Create outpatient visits'),
('inpatient.view', 'View Inpatient', 'inpatient', 'Access inpatient workflows'),
('inpatient.admit', 'Admit Inpatient', 'inpatient', 'Admit inpatients'),
('inpatient.discharge', 'Discharge Inpatient', 'inpatient', 'Discharge or refer inpatients'),
('appointments.view', 'View Appointments', 'appointments', 'Access appointments'),
('consultations.view', 'View Consultations', 'consultations', 'Access consultation records'),
('consultations.create', 'Create Consultations', 'consultations', 'Create consultation records'),
('emergency.view', 'View Emergency', 'emergency', 'Access emergency module'),
('emergency.create', 'Create Emergency', 'emergency', 'Create emergency cases'),
('emergency.triage', 'Emergency Triage', 'emergency', 'Triage emergency patients'),
('emergency.consult', 'Emergency Consult', 'emergency', 'Consult emergency cases'),
('emergency.admit', 'Emergency Admit', 'emergency', 'Admit emergency cases'),
('emergency.discharge', 'Emergency Discharge', 'emergency', 'Discharge emergency cases'),
('emergency.refer', 'Emergency Refer', 'emergency', 'Refer emergency cases'),
('vitals.view', 'View Vitals', 'vitals', 'Access recorded vitals'),
('vitals.create', 'Record Vitals', 'vitals', 'Create vitals records'),
('wards.view', 'View Wards & Beds', 'wards', 'Access ward and bed pages'),
('nursing.view', 'View Nursing Notes', 'nursing', 'Access nursing notes'),
('nursing.create', 'Create Nursing Notes', 'nursing', 'Create nursing notes'),
('laboratory.view', 'View Laboratory', 'laboratory', 'Access laboratory overview'),
('laboratory.requests', 'Laboratory Requests', 'laboratory', 'Access laboratory requests'),
('laboratory.sample_collection', 'Sample Collection', 'laboratory', 'Access sample collection'),
('laboratory.results', 'Laboratory Results', 'laboratory', 'Access laboratory results'),
('laboratory.reports', 'Laboratory Reports', 'laboratory', 'Access laboratory reports'),
('radiology.view', 'View Radiology', 'radiology', 'Access radiology overview'),
('radiology.requests', 'Radiology Requests', 'radiology', 'Access radiology requests'),
('radiology.results', 'Radiology Results', 'radiology', 'Access radiology results'),
('radiology.reports', 'Imaging Reports', 'radiology', 'Access imaging reports'),
('pharmacy.view', 'View Pharmacy', 'pharmacy', 'Access pharmacy overview'),
('pharmacy.prescriptions', 'Prescriptions', 'pharmacy', 'Access prescriptions'),
('pharmacy.dispense', 'Medicine Dispensing', 'pharmacy', 'Dispense medicines'),
('pharmacy.stock', 'Pharmacy Stock', 'pharmacy', 'Access pharmacy stock'),
('pharmacy.reports', 'Pharmacy Reports', 'pharmacy', 'Access pharmacy reports'),
('billing.view', 'View Billing', 'billing', 'Access billing pages'),
('insurance.view', 'View Insurance', 'insurance', 'Access insurance pages'),
('queue.view', 'View Queue', 'queue', 'Access queue pages'),
('noticeboard.view', 'View Noticeboard', 'noticeboard', 'Access noticeboard pages'),
('reports.view', 'View Reports', 'reports', 'Access reporting pages'),
('settings.view', 'View Settings', 'settings', 'Access settings pages'),
('users.manage', 'Manage Users & Roles', 'users', 'Manage users and roles');

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'Administrator';

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
INNER JOIN permissions p ON p.name IN (
    'dashboard.view',
    'patients.view',
    'outpatient.view',
    'inpatient.view',
    'appointments.view',
    'consultations.view',
    'laboratory.results',
    'radiology.results',
    'pharmacy.prescriptions'
)
WHERE r.name = 'Doctor';

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
INNER JOIN permissions p ON p.name IN (
    'dashboard.view',
    'patients.view',
    'emergency.view',
    'emergency.triage',
    'consultations.view',
    'laboratory.requests',
    'laboratory.results',
    'radiology.requests',
    'radiology.results',
    'pharmacy.prescriptions',
    'inpatient.admit',
    'inpatient.discharge'
)
WHERE r.name = 'Emergency Doctor';

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
INNER JOIN permissions p ON p.name IN (
    'dashboard.view',
    'patients.view',
    'vitals.view',
    'vitals.create',
    'inpatient.view',
    'wards.view',
    'nursing.view',
    'nursing.create',
    'emergency.triage'
)
WHERE r.name = 'Nurse';

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
INNER JOIN permissions p ON p.name IN (
    'dashboard.view',
    'laboratory.requests',
    'laboratory.sample_collection',
    'laboratory.results',
    'laboratory.reports'
)
WHERE r.name = 'Laboratory Technician';

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
INNER JOIN permissions p ON p.name IN (
    'dashboard.view',
    'radiology.requests',
    'radiology.results',
    'radiology.reports'
)
WHERE r.name = 'Radiologist';

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
INNER JOIN permissions p ON p.name IN (
    'dashboard.view',
    'pharmacy.prescriptions',
    'pharmacy.dispense',
    'pharmacy.stock',
    'pharmacy.reports'
)
WHERE r.name = 'Pharmacist';

INSERT INTO departments (name, code, description) VALUES
('Administration', 'ADMIN', 'Administrative services'),
('Outpatient', 'OPD', 'Outpatient care and triage'),
('Inpatient', 'IPD', 'Admission and ward management'),
('Laboratory', 'LAB', 'Laboratory investigations'),
('Pharmacy', 'PHARM', 'Medication dispensing and inventory'),
('Billing', 'BILL', 'Billing and finance'),
('Nursing', 'NURS', 'Nursing services');

INSERT INTO wards (name, code, ward_type, gender_policy, capacity, status) VALUES
('St. Luke Ward', 'ST-LUKE', 'General', 'male', 24, 'active'),
('St. Joseph Ward', 'ST-JOSEPH', 'Surgical', 'male', 20, 'active'),
('St. Mary Ward', 'ST-MARY', 'General', 'female', 24, 'active'),
('St. Theresa Ward', 'ST-THERESA', 'General', 'female', 20, 'active'),
('St. Anne Maternity Ward', 'ST-ANNE', 'Maternity', 'female', 18, 'active'),
('St. Clare Ward', 'ST-CLARE', 'Medical', 'female', 16, 'active');

INSERT INTO medicine_categories (name, description) VALUES
('Antibiotics', 'Antibacterial medicines'),
('Painkillers', 'Analgesic medicines'),
('Vaccines', 'Preventive immunization medicines');
