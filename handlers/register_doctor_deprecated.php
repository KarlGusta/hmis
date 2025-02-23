<?php
require_once '../config/database.php';
require_once '../classes/Doctor.php';
require_once '../classes/ActivityLogger.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not authenticated");
    }

    $db = new DatabaseConnection();
    $doctor = new Doctor($db);
    $activityLogger = new ActivityLogger($db);

    // Debug: Log the incoming data
    error_log("Incoming POST data: " . print_r($_POST, true));
    error_log("Incoming FILES data: " . print_r($_FILES, true));

    // Sanitize and validate input data
    $data = [
        'first_name' => filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING),
        'last_name' => filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING),
        'gender' => filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING),
        'date_of_birth' => filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING),
        'national_id' => filter_input(INPUT_POST, 'national_id', FILTER_SANITIZE_STRING),
        'license_number' => filter_input(INPUT_POST, 'license_number', FILTER_SANITIZE_STRING),
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
        'phone' => filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING),
        'specialization' => filter_input(INPUT_POST, 'specialization', FILTER_SANITIZE_STRING),
        'department_id' => filter_input(INPUT_POST, 'department_id', FILTER_SANITIZE_STRING),
        'qualification' => filter_input(INPUT_POST, 'qualification', FILTER_SANITIZE_STRING),
        'experience_years' => filter_input(INPUT_POST, 'experience_years', FILTER_VALIDATE_INT),
        'consultation_fee' => filter_input(INPUT_POST, 'consultation_fee', FILTER_VALIDATE_FLOAT),
        'available_days' => isset($_POST['available_days']) ? $_POST['available_days'] : [],
    ];

    // Debug: Log the sanitized data
    error_log("Sanitized data: " . print_r($data, true));

    // Validate required fields
    $required_fields = ['first_name', 'last_name', 'gender', 'date_of_birth', 'license_number', 
                       'email', 'phone', 'specialization', 'qualification', 
                       'experience_years', 'consultation_fee'];
    
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            throw new Exception("Required field '$field' is missing or empty");
        }
    }

    // Handle file upload for photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/doctors/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            throw new Exception("Invalid file type. Only JPG, JPEG and PNG files are allowed.");
        }

        $photo_path = $upload_dir . uniqid('doctor_') . '.' . $file_extension;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            throw new Exception("Failed to upload photo: " . error_get_last()['message']);
        }
        
        $data['photo'] = basename($photo_path);
    }

    // Register the doctor
    $doctor_id = $doctor->register($data);

    // Handle profile image upload if present
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = $doctor->uploadProfileImage($doctor_id, $_FILES['profile_image']);
        if (!$upload_result['success']) {
            throw new Exception($upload_result['message']);
        }
    }

    // Log successful registration
    $activityLogger->logActivity([
        'user_id' => $_SESSION['user_id'],
        'activity_type' => 'DOCTOR_REGISTRATION',
        'activity_description' => "Registered new doctor: Dr. {$data['first_name']} {$data['last_name']} (ID: $doctor_id)",
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'entity_type' => 'doctor',
        'entity_id' => $doctor_id,
        'status' => 'success'
    ]);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Doctor registered successfully',
        'doctor_id' => $doctor_id
    ]);

} catch (Exception $e) {
    // Log the failed registration attempt
    if (isset($activityLogger)) {
        $activityLogger->logActivity([
            'user_id' => $_SESSION['user_id'] ?? null,
            'activity_type' => 'DOCTOR_REGISTRATION',
            'activity_description' => "Failed to register doctor: " . $e->getMessage(),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'entity_type' => 'doctor',
            'entity_id' => null,
            'status' => 'error'
        ]);
    }

    error_log("Registration failed: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    $_SESSION['error'] = "Registration failed: " . $e->getMessage();
    header('Location: ../views/doctors/register.php');
    exit;
}