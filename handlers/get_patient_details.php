<?php
require_once '../config/database.php';
require_once '../classes/Queue.php';

header('Content-Type: application/json');

try {
    $patient_id = filter_input(INPUT_GET, 'patient_id', FILTER_SANITIZE_STRING);
    
    if (!$patient_id) {
        throw new Exception('Patient ID is required');
    }

    $db = new DatabaseConnection();
    $queue = new Queue($db);
    
    $patient = $queue->getPatientDetails($patient_id);
    
    if (!$patient) {
        throw new Exception('Patient not found');
    }

    echo json_encode([
        'success' => true,
        'data' => $patient
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

?>