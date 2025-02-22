<?php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/ActivityLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $db = new DatabaseConnection();
    $auth = new Auth($db);
    $activityLogger = new ActivityLogger($db);

    // Validate passwords match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        throw new Exception("Passwords do not match");
    }

    // Sanitize and validate input data
    $data = [
        'username' => filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING),
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
        'password' => $_POST['password'],
        'first_name' => filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING),
        'last_name' => filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING),
        'roles' => isset($_POST['roles']) ? $_POST['roles'] : [],
        'department_id' => filter_input(INPUT_POST, 'department_id', FILTER_SANITIZE_STRING)
    ];

    // Add doctor-specific fields if doctor role is selected
    if (in_array('doctor', $data['roles'])) {
        $data['gender'] = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
        $data['date_of_birth'] = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING);
        $data['national_id'] = filter_input(INPUT_POST, 'national_id', FILTER_SANITIZE_STRING);
        $data['license_number'] = filter_input(INPUT_POST, 'license_number', FILTER_SANITIZE_STRING);
        $data['specialization'] = filter_input(INPUT_POST, 'specialization', FILTER_SANITIZE_STRING);
        $data['qualification'] = filter_input(INPUT_POST, 'qualification', FILTER_SANITIZE_STRING);
        $data['experience_years'] = filter_input(INPUT_POST, 'experience_years', FILTER_VALIDATE_INT);
        $data['consultation_fee'] = filter_input(INPUT_POST, 'consultation_fee', FILTER_VALIDATE_FLOAT);
        $data['bio'] = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING);
        $data['office_number'] = filter_input(INPUT_POST, 'office_number', FILTER_SANITIZE_STRING);
        $data['available_days'] = filter_input(INPUT_POST, 'available_days', FILTER_SANITIZE_STRING);
        $data['available_times'] = filter_input(INPUT_POST, 'available_times', FILTER_SANITIZE_STRING);
        
        // Contact information
        $data['phone'] = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $data['address'] = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $data['city'] = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
        $data['country'] = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
        $data['emergency_contact_name'] = filter_input(INPUT_POST, 'emergency_contact_name', FILTER_SANITIZE_STRING);
        $data['emergency_contact_phone'] = filter_input(INPUT_POST, 'emergency_contact_phone', FILTER_SANITIZE_STRING);
        
        // Validate required doctor fields
        $required_fields = [
            'gender', 'date_of_birth', 'national_id', 'license_number',
            'specialization', 'qualification', 'experience_years',
            'consultation_fee', 'phone'
        ];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . " is required for doctors");
            }
        }
    }

    // Register the user
    $user_id = $auth->register($data);

    // Log the activity
    $activityLogger->logActivity([
        'user_id' => $_SESSION['user_id'],
        'activity_type' => 'user_registration',
        'activity_description' => "New user registered: {$data['username']} with roles: " . implode(', ', $data['roles']),
        'ip_address' => $activityLogger->getClientIP(),
        'user_agent' => $activityLogger->getUserAgent(),
        'entity_type' => 'user',
        'entity_id' => $user_id,
        'status' => 'success'
    ]);

    // Redirect with success message
    $_SESSION['success'] = "User registered successfully";
    header('Location: ../views/users/list.php');
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../views/users/register.php');
    exit;
} 