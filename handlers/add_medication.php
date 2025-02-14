<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Medication.php';
require_once '../classes/ActivityLogger.php';

try {
    $db = new DatabaseConnection();
    $medication = new Medication($db);
    $activityLogger = new ActivityLogger($db);

    // Sanitize and validate input data
    $data = [
        'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
        'generic_name' => filter_input(INPUT_POST, 'generic_name', FILTER_SANITIZE_STRING),
        'category' => filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING),
        'unit' => filter_input(INPUT_POST, 'unit', FILTER_SANITIZE_STRING),
        'unit_price' => filter_input(INPUT_POST, 'unit_price', FILTER_VALIDATE_FLOAT),
        'stock_quantity' => filter_input(INPUT_POST, 'stock_quantity', FILTER_VALIDATE_INT),
        'reorder_level' => filter_input(INPUT_POST, 'reorder_level', FILTER_VALIDATE_INT)
    ];
    
    // Validate required fields
    if (empty($data['name']) || empty($data['unit_price']) || empty($data['stock_quantity'])) {
        throw new Exception("Required fields are missing");
    }

    // Add the medication
    $medicationId = $medication->addMedication($data);

    // Log successful medication addition
    $activityLogger->logActivity([
        'user_id' => $_SESSION['user_id'] ?? null,
        'activity_type' => 'MEDICATION_ADD',
        'activity_description' => "Added new medication: {$data['name']} (Generic: {$data['generic_name']}) with initial stock of {$data['stock_quantity']} {$data['unit']}",
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'entity_type' => 'medication',
        'entity_id' => $medicationId,
        'status' => 'success'
    ]);

    $_SESSION['success'] = "Medication added successfully";
    header('Location: ../views/medications/list.php');
    exit;
} catch (Exception $e) {
    // Log failed medication addition
    if (isset($activityLogger)) {
        $activityLogger->logActivity([
            'user_id' => $_SESSION['user_id'] ?? null,
            'activity_type' => 'MEDICATION_ADD',
            'activity_description' => "Failed to add medication: " . $e->getMessage(),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'entity_type' => 'medication',
            'entity_id' => null,
            'status' => 'error'
        ]);
    }

    $_SESSION['error'] = "Failed to add medication: " . $e->getMessage();
    header('Location: ../views/medications/add.php');
    exit;
}