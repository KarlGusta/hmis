<?php
session_start();
require_once '../classes/Prescription.php';
require_once '../config/database.php';

$db = new DatabaseConnection();
$prescription = new Prescription($db);

$action = $_POST['action'] ?? '';
$prescriptionId = $_POST['prescription_id'] ?? null;
$medicalRecordId = $_POST['medical_record_id'] ?? null;

try {
    switch ($action) {
        case 'create':
            // Validate required fields
            $requiredFields = ['medication_id', 'dosage', 'frequency', 'duration'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("All prescription details are required"); 
                }
            }

            $prescriptionData = [
                'medication_id' => $_POST['medication_id'],
                'dosage' => $_POST['dosage'],
                'frequency' => $_POST['frequency'],
                'duration' => $_POST['duration'],
                'instructions' => $_POST['instructions'] ?? ''
            ];

            $prescription->createPrescription($medicalRecordId, $prescriptionData);
            $_SESSION['success'] = "Prescription added successfully";
            break;
        
        case 'update':
            if (!$prescriptionId) {
                throw new Exception("Prescription ID is required");
            }    

            $prescriptionData = [
                'medication_id' => $_POST['medication_id'],
                'dosage' => $_POST['dosage'],
                'frequency' => $_POST['frequency'],
                'duration' => $_POST['duration'],
                'instructions' => $_POST['instructions'] ?? '',
                'status' => $_POST['status'] ?? 'active'
            ];

            $prescription->updatePrescription($prescriptionId, $prescriptionData);
            $_SESSION['success'] = "Prescription updated successfully";
            break;

        case 'delete':
            if (!$prescriptionId) {
                throw new Exception("Prescription ID is required");
            }    

            $prescription->deletePrescription($prescriptionId);
            $_SESSION['success'] = "Prescription cancelled successfully";
            break;

        default:
            throw new Exception("Invalid action");    
    }

    // Redirect back to the medical record view
    header("Location: ../views/medical_records/view.php?id=" . $medicalRecordId);
    exit;
} catch (Exception $e) {
    error_log("Error in prescription handler: " . $e->getMessage());
    $_SESSION['error'] = "Error processing prescription: " . $e->getMessage();
    header("Location: ../views/medical_records/view.php?id=" . $medicalRecordId);
    exit;
}