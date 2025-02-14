<?php
session_start();
require_once '../config/database.php';
require_once '../classes/OutpatientVisit.php';
require_once '../classes/ActivityLogger.php';

try {
    $db = new DatabaseConnection();
    $outpatient = new OutpatientVisit($db);
    $activityLogger = new ActivityLogger($db);

    // Sanitize and validate input data
    $data = [
        'patient_id' => filter_input(INPUT_POST, 'patient_id', FILTER_SANITIZE_NUMBER_INT),
        'department_id' => filter_input(INPUT_POST, 'department_id', FILTER_SANITIZE_STRING),
        'priority' => filter_input(INPUT_POST, 'priority', FILTER_SANITIZE_STRING) ?? 'normal',
        'symptoms' => filter_input(INPUT_POST, 'symptoms', FILTER_SANITIZE_STRING),
        'notes' => filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING),
        'called_by' => $_SESSION['user_id'],
        'room_number' => filter_input(INPUT_POST, 'room_number', FILTER_SANITIZE_STRING)
    ];

    // Add validation for required fields
    if (empty($data['patient_id']) || empty($data['department_id']) || empty($data['symptoms'])) {
        throw new Exception("Required fields are missing");
    }

    // Register the outpatient visit
    $queue_id = $outpatient->registerVisit($data);

    // Log successful outpatient registration
    $activityLogger->logActivity([
        'user_id' => $_SESSION['user_id'] ?? null,
        'activity_type' => 'OUTPATIENT_REGISTRATION',
        'activity_description' => "Registered outpatient visit for patient #{$data['patient_id']} in department #{$data['department_id']} (Priority: {$data['priority']})",
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'entity_type' => 'outpatient_visit',
        'entity_id' => $queue_id,
        'status' => 'success'
    ]);

    $_SESSION['success'] = "Outpatient visit registered successfully";
    header('Location: ../views/outpatient/queue.php');
    exit;
} catch (Exception $e) {
    // Log failed outpatient registration
    if (isset($activityLogger)) {
        $activityLogger->logActivity([
            'user_id' => $_SESSION['user_id'] ?? null,
            'activity_type' => 'OUTPATIENT_REGISTRATION',
            'activity_description' => "Failed to register outpatient visit: " . $e->getMessage(),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'entity_type' => 'outpatient_visit',
            'entity_id' => null,
            'status' => 'error'
        ]);
    }

    $_SESSION['error'] = "Registration failed: " . $e->getMessage();
    header('Location: ../views/outpatient/register.php');
    exit;
}