<?php
require_once '../config/database.php';
require_once '../classes/Patient.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['patient_id'])) {
        throw new Exception('Patient ID is required');
    }

    $db = new DatabaseConnection();
    $patient = new Patient($db);

    $success = $patient->deletePatient($_POST['patient_id']);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Patient deleted successfully']);        
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete patient']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}