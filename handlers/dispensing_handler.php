<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Dispensing.php';
require_once '../classes/ActivityLogger.php';

$db = new DatabaseConnection();
$dispensing = new Dispensing($db);
$activityLogger = new ActivityLogger($db);

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'dispense':
            $prescriptionId = $_POST['prescription_id'] ?? null;
            $quantity = $_POST['quantity'] ?? null;
            $notes = $_POST['notes'] ?? '';

            if (!$prescriptionId || !$quantity) {
                throw new Exception("Missing required fields");
            }

            $dispensing->dispenseMedication($prescriptionId, [
                'quantity' => $quantity,
                'notes' => $notes
            ]);

            // Log the activity
            $activityLogger->logActivity([
                'user_id' => $_SESSION['user_id'],
                'activity_type' => 'MEDICATION_DISPENSED',
                'activity_description' => "Dispensed medication for prescription #$prescriptionId",
                'entity_type' => 'prescription',
                'entity_id' => $prescriptionId,
                'status' => 'success'
            ]);

            $_SESSION['success'] = "Medication dispensed successfully";
            break;

        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: ../views/pharmacy/pending_prescriptions.php");
exit; 