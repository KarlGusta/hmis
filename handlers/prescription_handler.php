<?php
require_once '../config/database.php';
require_once '../classes/Prescription.php';

$db = new DatabaseConnection();
$prescription = new Prescription($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    try {
        switch ($action) {
            case 'create':
                $consultationId = $_POST['consultation_id'];
                $medications = $_POST['medications'] ?? [];
                $dosages = $_POST['dosages'] ?? [];
                $frequencies = $_POST['frequencies'] ?? [];
                $durations = $_POST['durations'] ?? [];
                $quantities = $_POST['quantities'] ?? [];
                $specialInstructions = $_POST['special_instructions'] ?? [];
                $medicalRecordId = $_POST['medical_record_id'];

                $prescriptionData = [];
                for ($i = 0; $i < count($medications); $i++) {
                    $prescriptionData[] = [
                        'medical_record_id' => $medicalRecordId,
                        'medication_id' => $medications[$i],
                        'dosage' => $dosages[$i],
                        'frequency' => $frequencies[$i],
                        'duration' => $durations[$i],
                        'quantity' => $quantities[$i],
                        'special_instructions' => $specialInstructions[$i] ?? null,
                        'route' => $_POST['route'] ?? 'oral'
                    ];
                }

                foreach ($prescriptionData as $data) {
                    $prescription->createPrescription($data);
                }
                
                $_SESSION['success'] = "Prescription created successfully";
                header("Location: ../views/consultations/view.php?id=" . $consultationId);
                break;

            case 'edit':
                $prescriptionId = $_POST['prescription_id'];
                $medications = $_POST['medications'] ?? [];
                $dosages = $_POST['dosages'] ?? [];
                $frequencies = $_POST['frequencies'] ?? [];
                $durations = $_POST['durations'] ?? [];
                $notes = $_POST['notes'] ?? [];
                
                $prescriptionData = [];
                for ($i = 0; $i < count($medications); $i++) {
                    $prescriptionData[] = [
                        'medication_id' => $medications[$i],
                        'dosage' => $dosages[$i],
                        'frequency' => $frequencies[$i],
                        'duration' => $durations[$i],
                        'notes' => $notes[$i] ?? null
                    ];
                }

                $prescription->updatePrescription($prescriptionId, $prescriptionData);
                $_SESSION['success'] = "Prescription updated successfully";
                header("Location: prescriptions_list.php");
                break;

            default:
                throw new Exception("Invalid action");    
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: prescriptions_list.php");
    } 
    exit();
}