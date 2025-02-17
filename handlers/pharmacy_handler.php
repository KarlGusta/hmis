<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Pharmacy.php';

$db = new DatabaseConnection();
$pharmacy = new Pharmacy($db);

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

            $pharmacy->dispenseMedication($prescriptionId, $quantity, $notes);
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