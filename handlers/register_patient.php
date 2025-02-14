<?php
require_once '../config/database.php';
require_once '../classes/Patient.php';
require_once '../classes/ActivityLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $db = new DatabaseConnection();
    $patient = new Patient($db);
    $activityLogger = new ActivityLogger($db);

    // Sanitize and validate input data
    $data = [
        'first_name' => filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING),
        'last_name' => filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING),
        'date_of_birth' => filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING),
        'gender' => filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING),
        'blood_group' => filter_input(INPUT_POST, 'blood_group', FILTER_SANITIZE_STRING),
        'phone' => filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING),
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
        'address' => filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING),
        'emergency_contact_name' => filter_input(INPUT_POST, 'emergency_contact_name', FILTER_SANITIZE_STRING),
        'emergency_contact_phone' => filter_input(INPUT_POST, 'emergency_contact_phone', FILTER_SANITIZE_STRING),
        'photo' => filter_input(INPUT_POST, 'photo', FILTER_SANITIZE_STRING),
        'allergies' => filter_input(INPUT_POST, 'allergies', FILTER_SANITIZE_STRING),
        'current_medications' => filter_input(INPUT_POST, 'current_medications', FILTER_SANITIZE_STRING),
        'insurance_provider' => filter_input(INPUT_POST, 'insurance_provider', FILTER_SANITIZE_STRING),
        'insurance_id' => filter_input(INPUT_POST, 'insurance_id', FILTER_SANITIZE_STRING),
        'status' => filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING) ?: 'active'
    ];
    
    // Handle medical history if provided
    if (isset($_POST['medical_history'])) {
        $data['medical_history'] = array_map(function($history) {
            return [
                'name' => filter_var($history['name'], FILTER_SANITIZE_STRING),
                'diagnosis_date' => filter_var($history['diagnosis_date'], FILTER_SANITIZE_STRING),
                'notes' => filter_var($history['notes'], FILTER_SANITIZE_STRING),
                'is_chronic' => (int)filter_var($history['is_chronic'], FILTER_SANITIZE_NUMBER_INT) 
            ];
        }, $_POST['medical_history']);
    }

    // Handle file upload for photo
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/patients/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_path = $upload_dir . uniqid('patient_') . '.' . $file_extension;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            $data['photo'] = basename($photo_path);
        }
    }

    // Update the data array with the photo path
    $data['photo'] = $photo_path;

    // Register the patient
    $patient_id = $patient->register($data);

    // Log successful registration
    $activityLogger->logActivity([
        'user_id' => $_SESSION['user_id'] ?? null,
        'activity_type' => 'PATIENT_REGISTRATION',
        'activity_description' => "Registered new patient: {$data['first_name']} {$data['last_name']} (ID: $patient_id)",
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'entity_type' => 'patient',
        'entity_id' => $patient_id,
        'status' => 'success'
    ]);

    if ($photo_path) {
        // Log photo upload if successful
        $activityLogger->logActivity([
            'user_id' => $_SESSION['user_id'] ?? null,
            'activity_type' => 'PATIENT_PHOTO_UPLOAD',
            'activity_description' => "Uploaded photo for patient ID: $patient_id",
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'entity_type' => 'patient',
            'entity_id' => $patient_id,
            'status' => 'success'
        ]);
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Patient registered successfully',
        'patient_id' => $patient_id
    ]);

} catch (Exception $e) {
    // Log failed registration
    if (isset($activityLogger)) {
        $activityLogger->logActivity([
            'user_id' => $_SESSION['user_id'] ?? null,
            'activity_type' => 'PATIENT_REGISTRATION',
            'activity_description' => "Failed to register patient: " . $e->getMessage(),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'entity_type' => 'patient',
            'entity_id' => null,
            'status' => 'error'
        ]);
    }

    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>