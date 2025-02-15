-- Database: hospital_management_system
-- Create database
CREATE DATABASE IF NOT EXISTS hospital_management;
USE hospital_management;

-- Table for managing departments (needs to be created BEFORE users table)
CREATE TABLE IF NOT EXISTS departments (
    id VARCHAR(36) PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Users table (create AFTER departments table)
CREATE TABLE users (
    id VARCHAR(36) PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    department_id VARCHAR(36) NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

CREATE TABLE permissions (
    permission_id INT PRIMARY KEY AUTO_INCREMENT,
    permission_name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id)
);

CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    success TINYINT(1) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Patients Table
CREATE TABLE patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    blood_group VARCHAR(5),
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    address TEXT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    photo VARCHAR(255),
    medical_history TEXT,
    allergies TEXT,
    current_medications TEXT,
    insurance_provider VARCHAR(100),
    insurance_id VARCHAR(50),
    status ENUM('active', 'inactive', 'admitted') DEFAULT 'active',
    last_visit DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE medical_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id VARCHAR(20) NOT NULL,
    condition_name VARCHAR(100) NOT NULL,
    diagnosis_date DATE,
    notes TEXT,
    is_chronic TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
);

-- Doctors table - core information about doctors
CREATE TABLE doctors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(36) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    date_of_birth DATE NOT NULL,
    national_id VARCHAR(20) UNIQUE,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    qualification VARCHAR(100) NOT NULL,
    experience_years INT,
    consultation_fee DECIMAL(10,2),
    bio TEXT,
    photo VARCHAR(255),
    department_id VARCHAR(36),
    office_number VARCHAR(20),
    available_days VARCHAR(100),
    available_times JSON,
    status ENUM('active', 'inactive', 'on_leave', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- First create the patient_queue table
CREATE TABLE IF NOT EXISTS patient_queue (
    id VARCHAR(36) PRIMARY KEY,
    queue_number VARCHAR(20) NOT NULL UNIQUE,
    patient_id INT NOT NULL,
    department_id VARCHAR(36) NOT NULL,
    priority ENUM('normal', 'urgent', 'emergency') DEFAULT 'normal',
    symptoms TEXT,
    notes TEXT,
    status ENUM('waiting', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'waiting',
    called_by VARCHAR(36) NOT NULL,
    room_number VARCHAR(20),
    estimated_wait_time INT,
    check_in_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    start_time TIMESTAMP NULL,
    end_time TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (called_by) REFERENCES users(id)
);

-- First create the medical_records table
CREATE TABLE medical_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    consultation_id VARCHAR(36) NULL,
    chief_complaint TEXT NOT NULL,
    history_of_illness TEXT,
    diagnosis TEXT,
    treatment_plan TEXT,
    prescription TEXT,
    lab_requests TEXT,
    follow_up_date DATE,
    consultation_notes TEXT,
    status ENUM('in_progress', 'completed', 'cancelled') DEFAULT 'in_progress',
    created_by VARCHAR(36) NOT NULL,
    updated_by VARCHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Then create the consultations table
CREATE TABLE IF NOT EXISTS consultations (
    id VARCHAR(36) PRIMARY KEY,
    queue_id VARCHAR(36) NOT NULL,
    patient_id INT NOT NULL,
    doctor_id VARCHAR(36) NOT NULL,
    department_id VARCHAR(36) NOT NULL,
    medical_record_id INT NULL,
    chief_complaint TEXT NOT NULL,
    history_of_illness TEXT,
    diagnosis TEXT,
    treatment_plan TEXT,
    prescription TEXT,
    lab_requests TEXT,
    follow_up_date DATE,
    consultation_notes TEXT,
    status ENUM('in_progress', 'completed', 'cancelled') DEFAULT 'in_progress',
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (queue_id) REFERENCES patient_queue(id),
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id),
    FOREIGN KEY (medical_record_id) REFERENCES medical_records(id),
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Appointments table (place this AFTER doctors table creation)
CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_datetime DATETIME NOT NULL,
    appointment_type ENUM('first_visit', 'follow_up', 'consultation', 'procedure', 'routine_checkup') NOT NULL,
    reason TEXT,
    status ENUM('scheduled', 'confirmed', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
    duration INT DEFAULT 30, -- Duration in minutes
    priority ENUM('normal', 'urgent', 'emergency') DEFAULT 'normal',
    blood_pressure VARCHAR(10),
    temperature DECIMAL(4,1),
    heart_rate INT,
    weight DECIMAL(5,2),
    symptoms TEXT,
    notes TEXT,
    cancellation_reason TEXT,
    cancelled_by VARCHAR(36),
    cancelled_at DATETIME,
    reminder_sent TINYINT(1) DEFAULT 0,
    last_reminder_date DATETIME,
    check_in_time DATETIME,
    start_time DATETIME,
    end_time DATETIME,
    created_by VARCHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (cancelled_by) REFERENCES users(id),
    department_id VARCHAR(36),
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Appointment reminders table
CREATE TABLE appointment_reminders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    reminder_type ENUM('email', 'sms', 'whatsapp') NOT NULL,
    scheduled_time DATETIME NOT NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    sent_time DATETIME,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);

-- Appointment history/logs table
CREATE TABLE appointment_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    action ENUM('created', 'updated', 'status_changed', 'cancelled', 'rescheduled') NOT NULL,
    previous_status VARCHAR(50),
    new_status VARCHAR(50),
    previous_datetime DATETIME,
    new_datetime DATETIME,
    notes TEXT,
    performed_by VARCHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id),
    FOREIGN KEY (performed_by) REFERENCES users(id) 
);

-- Laboratory Tests table
CREATE TABLE laboratory_tests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    cost DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Lab Orders table
CREATE TABLE lab_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    test_id INT NOT NULL,
    ordered_at DATETIME NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    results TEXT,
    notes TEXT,
    completed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id),
    FOREIGN KEY (test_id) REFERENCES laboratory_tests(id) 
);

-- Medications table
CREATE TABLE medications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    generic_name VARCHAR(100),
    category VARCHAR(50),
    unit VARCHAR(20),
    unit_price DECIMAL(10,2) NOT NULL,
    current_stock INT DEFAULT 0,
    minimum_stock INT DEFAULT 10,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create prescriptions table to link medical records with medications
CREATE TABLE prescriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medical_record_id INT NOT NULL,
    medication_id INT NOT NULL,
    dosage VARCHAR(100) NOT NULL, -- e.g., "1 tablet"
    frequency VARCHAR(100) NOT NULL, -- e.g., "3 times a day"
    duration VARCHAR(100) NOT NULL, -- e.g., "7 days"
    instructions TEXT,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_by VARCHAR(36) NOT NULL,
    updated_by VARCHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medical_record_id) REFERENCES medical_records(id),
    FOREIGN KEY (medication_id) REFERENCES medications(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)  
);

-- Medication Dispensing table
CREATE TABLE medication_dispensing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prescription_id INT NOT NULL,
    quantity INT NOT NULL,
    notes TEXT,
    dispensed_by VARCHAR(36) NOT NULL,
    dispensed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id),
    FOREIGN KEY (dispensed_by) REFERENCES users(id) 
);

-- Bills table
CREATE TABLE bills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    appointment_id INT,
    bill_date DATE NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    paid_amount DECIMAL(10,2) DEFAULT 0,
    payment_method VARCHAR(50),
    payment_date DATETIME,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);

-- Bill Items table
CREATE TABLE bill_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bill_id INT NOT NULL,
    item_type ENUM('consultation', 'laboratory', 'medication', 'procedure') NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (bill_id) REFERENCES bills(id) 
);

-- Inventory Management
CREATE TABLE inventory (
    item_id VARCHAR(20) PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    category ENUM('Medicine', 'Medical Supply', 'Equipment') NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    expiry_date DATE,
    reorder_level INT,
    last_restocked DATE      
);

-- Insurance Providers
CREATE TABLE insurance_providers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id VARCHAR(50) UNIQUE,
    provider_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    website VARCHAR(255),
    address TEXT NOT NULL,
    coverage_types TEXT,
    policy_details TEXT,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
);

-- Table for queue history/logs
CREATE TABLE IF NOT EXISTS queue_history (
    id VARCHAR(36) PRIMARY KEY,
    queue_id VARCHAR(36) NOT NULL,
    status ENUM('waiting', 'in_progress', 'completed', 'cancelled', 'no_show') NOT NULL,
    changed_by VARCHAR(36) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (queue_id) REFERENCES patient_queue(id),
    FOREIGN KEY (changed_by) REFERENCES users(id)
);

-- Procedures table for billing
CREATE TABLE procedures (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    cost DECIMAL(10,2) NOT NULL,
    duration INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Modify bills table
ALTER TABLE bills
ADD insurance_clam_id INT NULL,
ADD insurance_id INT NULL,
ADD payment_reference VARCHAR(50),
ADD due_date DATE,
MODIFY status ENUM('pending', 'partially_paid', 'paid', 'cancelled', 'insurance_pending', 'insurance_rejected') DEFAULT 'pending'; 

-- Bill payments table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bill_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'mpesa', 'insurance', 'bank_transfer') NOT NULL,
    payment_reference VARCHAR(50),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'completed',
    notes TEXT,
    created_by VARCHAR(36) NOT NULL,
    FOREIGN KEY (bill_id) REFERENCES bills(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Insurance claims table
CREATE TABLE insurance_claims (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bill_id INT NOT NULL,
    insurance_id INT NOT NULL,
    claim_number VARCHAR(50),
    claim_amount DECIMAL(10,2) NOT NULL,
    approved_amount DECIMAL(10,2),
    status ENUM('submitted', 'pending', 'approved', 'rejected', 'paid') DEFAULT 'submitted',
    submitted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    response_date TIMESTAMP NULL DEFAULT NULL,
    notes TEXT,
    FOREIGN KEY (bill_id) REFERENCES bills(id),
    FOREIGN KEY (insurance_id) REFERENCES insurance_providers(id)
);

-- Medication categories table
CREATE TABLE medication_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP    
);

-- Modify existing medications table to include more fields
ALTER TABLE medications
ADD COLUMN manufacturer VARCHAR(100),
ADD COLUMN strength VARCHAR(50),
ADD COLUMN form VARCHAR(50),
ADD COLUMN storage_conditions TEXT,
ADD COLUMN barcode VARCHAR(50) UNIQUE,
ADD COLUMN category_id INT,
ADD COLUMN expiry_alert_days INT DEFAULT 30,
ADD FOREIGN KEY (category_id) REFERENCES medication_categories(id);

-- First, create the suppliers table (if it doesn't exist)
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Then create medication_batches table
CREATE TABLE medication_batches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medication_id INT NOT NULL,
    batch_number VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    manufacturing_date DATE,
    expiring_date DATE NOT NULL,
    supplier_id INT NOT NULL,
    purchase_price DECIMAL(10,2),
    status ENUM('active', 'expired', 'depleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medication_id) REFERENCES medications(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    UNIQUE KEY (medication_id, batch_number)
);

-- Table for tracking stock movements
CREATE TABLE stock_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medication_id INT NOT NULL,
    batch_id INT NOT NULL,
    movement_type ENUM('in', 'out', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    reference_type VARCHAR(50), /* e.g., 'prescription', 'purchase', 'return' */
    reference_id VARCHAR(36), /* ID of the related record */
    notes TEXT,
    performed_by VARCHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medication_id) REFERENCES medications(id),
    FOREIGN KEY (batch_id) REFERENCES medication_batches(id),
    FOREIGN KEY (performed_by) REFERENCES users(id) 
);

-- Table for purchase orders
CREATE TABLE purchase_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    po_number VARCHAR(50) UNIQUE NOT NULL,
    supplier_id INT NOT NULL,
    order_date DATE NOT NULL,
    expected_delivery_date DATE,
    status ENUM('draft', 'pending', 'approved', 'ordered', 'received', 'cancelled') DEFAULT 'draft',
    total_amount DECIMAL(10,2),
    notes TEXT,
    created_by VARCHAR(36) NOT NULL,
    approved_by VARCHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id) 
);

-- Table for purchase order items
CREATE TABLE purchase_order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    po_id INT NOT NULL,
    medication_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(id),
    FOREIGN KEY (medication_id) REFERENCES medications(id) 
);

-- Table for returns/damaged items
CREATE TABLE medication_returns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medication_id INT NOT NULL,
    batch_id INT NOT NULL,
    quantity INT NOT NULL,
    reason TEXT NOT NULL,
    return_type ENUM('supplier_return', 'damage', 'expiry') NOT NULL,
    status ENUM('pending', 'approved', 'completed', 'rejected') DEFAULT 'pending',
    processed_by VARCHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medication_id) REFERENCES medications(id),
    FOREIGN KEY (batch_id) REFERENCES medication_batches(id),
    FOREIGN KEY (processed_by) REFERENCES users(id)  
);

-- Doctor contact information
CREATE TABLE doctor_contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    country VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Doctor schedules
CREATE TABLE doctor_schedules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    day_of_week TINYINT NOT NULL, -- 0 = Sunday, 1 = Monday, etc.
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    break_start TIME,
    break_end TIME,
    max_appointments INT DEFAULT 20,
    slot_duration INT DEFAULT 30, -- Duration in minutes
    is_available BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_doctor_schedule (doctor_id, day_of_week)
);

-- Doctor leaves/unavailability
CREATE TABLE doctor_leaves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    leave_type ENUM('sick', 'vacation', 'conference', 'other') NOT NULL,
    reason TEXT,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    approved_by VARCHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) 
);

-- Doctor specialties (many-to-many relationship)
CREATE TABLE doctor_specialties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    specialty_name VARCHAR(100) NOT NULL,
    is_primary BOOLEAN DEFAULT false,
    certification_date DATE,
    certification_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Doctor education history
CREATE TABLE doctor_education (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    degree VARCHAR(100) NOT NULL,
    institution VARCHAR(200) NOT NULL,
    country VARCHAR(100),
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Doctor work history
CREATE TABLE doctor_work_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    institution VARCHAR(200) NOT NULL,
    position VARCHAR(100) NOT NULL,
    start_date DATE,
    end_date DATE,
    responsibilities TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Performance metrics and ratings
CREATE TABLE doctor_ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    appointment_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);

-- Create table for user activity tracking
CREATE TABLE user_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(36) NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    activity_description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    entity_type VARCHAR(50), -- e.g., 'doctor', 'patient', etc.
    entity_id VARCHAR(36), -- ID of the affected record
    status VARCHAR(20), -- success, failure, etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table for tracking consultation status history
CREATE TABLE IF NOT EXISTS consultation_history (
    id VARCHAR(36) PRIMARY KEY,
    consultation_id VARCHAR(36) NOT NULL,
    status VARCHAR(50) NOT NULL,
    changed_by VARCHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (consultation_id) REFERENCES consultations(id),
    FOREIGN KEY (changed_by) REFERENCES users(id) 
);

-- Table for managing rooms
CREATE TABLE IF NOT EXISTS rooms (
    id VARCHAR(36) PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL UNIQUE,
    department_id VARCHAR(36) NOT NULL,
    room_type ENUM('consultation', 'emergency', 'procedure', 'ward') NOT NULL,
    status ENUM('available', 'occupied', 'maintenance', 'reserved') DEFAULT 'available',
    capacity INT DEFAULT 1,
    current_patient_id INT NULL,
    current_consultation_id VARCHAR(36) NULL,
    features TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (current_patient_id) REFERENCES patients(id),
    FOREIGN KEY (current_consultation_id) REFERENCES consultations(id) 
);

-- Table for tracking room usage history
CREATE TABLE IF NOT EXISTS room_history (
    id VARCHAR(36) PRIMARY KEY,
    room_id VARCHAR(36) NOT NULL,
    patient_id INT NOT NULL,
    consultation_id VARCHAR(36) NOT NULL,
    status VARCHAR(50) NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NULL,
    created_by VARCHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (consultation_id) REFERENCES consultations(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Indexes for performance
CREATE INDEX idx_bills_insurance ON bills(insurance_id);
CREATE INDEX idx_bills_status ON bills(status);
CREATE INDEX idx_payments_bill ON payments(bill_id);
CREATE INDEX idx_insurance_claims_bill ON insurance_claims(bill_id);
CREATE INDEX idx_insurance_claims_status ON insurance_claims(status);

-- Index for improving query performance
CREATE INDEX idx_patient_queue_status ON patient_queue(status);
CREATE INDEX idx_patient_queue_priority ON patient_queue(priority);
CREATE INDEX idx_patient_queue_department ON patient_queue(department_id);
CREATE INDEX idx_patient_queue_date ON patient_queue(created_at);
CREATE INDEX idx_queue_history_queue ON queue_history(queue_id);

-- Create indexes for performance
CREATE INDEX idx_patient_search ON patients(first_name, last_name, email, phone);
CREATE INDEX idx_appointment_date ON appointments(appointment_datetime);
CREATE INDEX idx_bills_date ON bills(bill_date);
CREATE INDEX idx_medical_records_status ON medical_records(status);

-- Add indexes for performance
CREATE INDEX idx_medications_category ON medications(category_id);
CREATE INDEX idx_medications_status ON medications(status);
CREATE INDEX idx_batches_medication ON medication_batches(medication_id);
CREATE INDEX idx_batches_expiry ON medication_batches(expiring_date);
CREATE INDEX idx_stock_movements_medication ON stock_movements(medication_id);
CREATE INDEX idx_stock_movements_batch ON stock_movements(batch_id);
CREATE INDEX idx_stock_movements_date ON stock_movements(created_at);
CREATE INDEX idx_purchase_orders_supplier ON purchase_orders(supplier_id);
CREATE INDEX idx_purchase_orders_status ON purchase_orders(status);

-- Create indexes for better query performance
CREATE INDEX idx_appointments_datetime ON appointments(appointment_datetime);
CREATE INDEX idx_appointments_patient ON appointments(patient_id);
CREATE INDEX idx_appointments_doctor ON appointments(doctor_id);
CREATE INDEX idx_appointments_status ON appointments(status);
CREATE INDEX idx_doctor_leaves_dates ON doctor_leaves(doctor_id, start_date, end_date);
CREATE INDEX idx_appointment_reminders_status ON appointment_reminders(status, scheduled_time);  

-- Create indexes for better query performance for doctors tables
CREATE INDEX idx_doctors_user ON doctors(user_id);
CREATE INDEX idx_doctors_department ON doctors(department_id);
CREATE INDEX idx_doctors_status ON doctors(status);
CREATE INDEX idx_doctor_specialities_name ON doctor_specialties(specialty_name);
CREATE INDEX idx_doctor_ratings_score ON doctor_ratings(doctor_id, rating); 

-- Create indexes for better query performance of the user activities table
CREATE INDEX idx_user_activities_user ON user_activities(user_id);
CREATE INDEX idx_user_activities_type ON user_activities(activity_type);
CREATE INDEX idx_user_activities_entity  ON user_activities(entity_type, entity_id);
CREATE INDEX idx_user_activities_date ON user_activities(created_at);

-- Insert insurance providers
INSERT INTO insurance_providers (provider_id, provider_name, contact_number, email, website, address, coverage_types, policy_details, status)
VALUES
    ('AAR001', 'AAR Insurance', '0722000000', 'info@aar-insurance.com', 'https://aar-insurance.com', 'Nairobi, Kenya', 'Inpatient, Outpatient, Maternity, Dental, Optical', 'Family plans and specialized covers', 'active'),
    ('JUB001', 'Jubilee Health Insurance', '0722111111', 'health@jubileeinsurance.com', 'https://jubileeinsurance.com', 'Nairobi, Kenya', 'Inpatient, Outpatient, Critical Illness', 'Plans tailored to age groups and needs', 'active'),
    ('BRT001', 'Britam Insurance', '0722333333', 'info@britam.com', 'https://britam.com', 'Nairobi, Kenya', 'Inpatient, Outpatient', 'Various coverage options', 'active'),
    ('OLD001', 'Old Mutual Insurance', '0722444444', 'customercare@oldmutualkenya.com', 'https://oldmutualkenya.com', 'Nairobi, Kenya', 'Inpatient, Outpatient', 'Afya Imara and Afya County plans', 'active'),
    ('APA001', 'APA Insurance', '0722555555', 'info@apainsurance.com', 'https://apainsurance.com', 'Nairobi, Kenya', 'Inpatient, Outpatient', 'Jamii Plus and Afya Nafuu', 'active'),
    ('CIC001', 'CIC Insurance Group', '0722666666', 'info@cic.co.ke', 'https://cic.co.ke', 'Nairobi, Kenya', 'Inpatient, Outpatient', 'Solutions for individuals and families', 'active'),
    ('FAS001', 'First Assurance Kenya Limited', '0722777777', 'info@firstassurance.co.ke', 'https://firstassurance.co.ke', 'Nairobi, Kenya', 'Inpatient, Outpatient', 'Medical insurance products', 'active'),
    ('GA001', 'GA Insurance Company', '0722888888', 'info@gakenya.com', 'https://gakenya.com', 'Nairobi, Kenya', 'Inpatient, Outpatient', 'Health insurance coverage', 'active'),
    ('HER001', 'Heritage Insurance Company', '0722999999', 'info@heritageinsurance.co.ke', 'https://heritageinsurance.co.ke', 'Nairobi, Kenya', 'Inpatient, Outpatient', 'Medical insurance plans', 'active'),
    ('MAD001', 'Madison Insurance', '0723000000', 'info@madison.co.ke', 'https://madison.co.ke', 'Nairobi, Kenya', 'Inpatient, Outpatient', 'Health insurance products', 'active'),
    ('KAI001', 'Kenyan Alliance Insurance', '0723111111', 'info@kenyanalliance.com', 'https://kenyanalliance.com', 'Nairobi, Kenya', 'Inpatient, Outpatient', 'Medical insurance solutions', 'active'),
    ('MUA001', 'MUA Insurance', '0723222222', 'info@mua.co.ke', 'https://mua.co.ke', 'Nairobi, Kenya', 'Inpatient, Outpatient', 'Health insurance coverage', 'active'),
    ('PAC001', 'Pacis Insurance', '0723333333', 'info@paciskenya.com', 'https://paciskenya.com', 'Nairobi, Kenya', 'Inpatient, Outpatient', 'Health insurance plans', 'active');

-- Insert some sample medication categories
INSERT INTO medication_categories (name, description) VALUES
('Antibiotics', 'Medications used to treat bacterial infections'),
('Analgesics', 'Pain relieving medications'),
('Antihypertensives', 'Medications for high blood pressure'),
('Antidiabetics', 'Medications for diabetes management'),
('Antihistamines', 'Medications for allergies'),
('Antidepressants', 'Medications for depression and mood disorders'),
('Antacids', 'Medications for acid reflux and stomach issues'),
('Vitamins', 'Nutritional supplements'),
('Antiseptics', 'Medications for preventing infection in wounds'),
('Bronchodilators', 'Medications for respiratory conditions');

-- Insert sample suppliers
INSERT INTO suppliers (name, contact_person, phone, email, address) VALUES
('PharmaCare Supplies', 'John Doe', '+254722111111', 'john@pharmacare.com', 'Nairobi, Kenya'),
('MediSource Ltd', 'Jane Smith', '+254722222222', 'jane@medisource.com', 'Mombasa, Kenya'),
('HealthPlus Distributors', 'Mark Johnson', '+254722333333', 'mark@healthplus.com', 'Kisumu, Kenya');

-- Insert into users table
INSERT INTO users (id, username, email, password_hash, role) VALUES
(UUID(), 'Karl', 'karlgustaesimit@gmail.com', '$2a$12$vNII2SyMqyEtcAUcQxCc6.nysW4s2LCaGpMsM3d6UvCBDIM/3EbRq', 'admin');

-- Insert departments into the departments table
INSERT INTO departments (id, code, name, description, status) VALUES
(UUID(), 'CARDIO', 'Cardiology', 'Deals with heart-related conditions and treatments.', 'active'),
(UUID(), 'ORTHO', 'Orthopedics', 'Focuses on bones, joints, muscles, and injuries.', 'active'),
(UUID(), 'PED', 'Pediatrics', 'Specializes in medical care for children.', 'active'),
(UUID(), 'RAD', 'Radiology', 'Provides imaging services like X-rays, CT scans, and MRIs.', 'active'),
(UUID(), 'NEURO', 'Neurology', 'Treats disorders of the nervous system, including the brain and spinal cord.', 'active'),
(UUID(), 'OBGYN', 'Obstetrics and Gynecology', 'Focuses on women\'s health, pregnancy, and childbirth.', 'active'),
(UUID(), 'ER', 'Emergency Medicine', 'Provides immediate care for acute illnesses and injuries.', 'active'),
(UUID(), 'ONCO', 'Oncology', 'Specializes in cancer treatment and management.', 'active'),
(UUID(), 'DERM', 'Dermatology', 'Treats skin-related conditions.', 'active'),
(UUID(), 'GASTRO', 'Gastroenterology', 'Focuses on the digestive system and its disorders.', 'active'),
(UUID(), 'URO', 'Urology', 'Specializes in urinary tract and male reproductive system issues.', 'active'),
(UUID(), 'ANES', 'Anesthesiology', 'Manages anesthesia and pain relief during surgeries.', 'active'),
(UUID(), 'PULMO', 'Pulmonology', 'Focuses on lung and respiratory system conditions.', 'active'),
(UUID(), 'PSYCH', 'Psychiatry', 'Treats mental health and emotional disorders.', 'active'),
(UUID(), 'NEPH', 'Nephrology', 'Specializes in kidney-related diseases.', 'active'),
(UUID(), 'ENDO', 'Endocrinology', 'Focuses on hormonal and metabolic disorders.', 'active'),
(UUID(), 'PATH', 'Pathology', 'Conducts lab tests to diagnose diseases.', 'active'),
(UUID(), 'SURG', 'Surgery', 'Performs surgical procedures across various specialties.', 'active'),
(UUID(), 'OPHTH', 'Ophthalmology', 'Specializes in eye care and vision health.', 'active'),
(UUID(), 'PHYSIO', 'Physical Therapy', 'Helps patients recover physical functionality through therapy.', 'active');

-- Insert common hospital departments (inpatient, outpatient, MCH, etc.)
INSERT INTO departments (id, code, name, description, status) VALUES
(UUID(), 'INPATIENT', 'Inpatient Services', 'Manages patients who are admitted to the hospital for overnight stays.', 'active'),
(UUID(), 'OUTPATIENT', 'Outpatient Services', 'Provides medical care and treatment without overnight stays.', 'active'),
(UUID(), 'MCH', 'Maternal and Child Health', 'Focuses on the health of mothers, infants, and children.', 'active'),
(UUID(), 'LAB', 'Laboratory Services', 'Conducts diagnostic tests and analyses.', 'active'),
(UUID(), 'PHARM', 'Pharmacy', 'Dispenses medications and provides pharmaceutical care.', 'active'),
(UUID(), 'GENMED', 'General Medicine', 'Provides primary and non-specialized care for general medical conditions.', 'active');

-- Sample trigger to update doctor status when leave is approved
DELIMITER //
CREATE TRIGGER update_doctor_status_on_leave
AFTER UPDATE ON doctor_leaves
FOR EACH ROW
BEGIN
    IF NEW.status = 'approved' AND NEW.start_date <= CURDATE() AND NEW.end_date >= CURDATE() THEN
        UPDATE doctors SET status = 'on_leave' WHERE id = NEW.doctor_id;
    END IF;
END;//
DELIMITER ;