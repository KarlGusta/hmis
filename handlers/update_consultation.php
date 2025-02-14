<?php
session_start();
require_once '../classes/Consultation.php';
require_once '../config/database.php';

$db = new DatabaseConnection();
$consultation = new Consultation($db);

$consultationId = $_POST['consultation_id'] ?? null;
$action = $_POST['action'] ?? 'save';

try {
    if ($action === 'save') {
        // Update consultation details
        $consultationData = [
            'diagnosis' => $_POST['diagnosis'] ?? '',
            'treatment_plan' => $_POST['treatment_plan'] ?? '',
            'prescription' => $_POST['prescription'] ?? '',
            'lab_requests' => $_POST['lab_requests'] ?? '',
            'follow_up_date' => $_POST['follow_up_date'] ?? null,
            'consultation_notes' => $_POST['consultation_notes'] ?? '',
            'history_of_illness' => $_POST['history_of_illness'] ?? ''
        ];

        $consultation->updateConsultation($consultationId, $consultationData);
        $_SESSION['success'] = "Consultation updated successfully";

    } elseif ($action === 'complete') {
        // First update the consultation details
        $consultationData = [
            'diagnosis' => $_POST['diagnosis'] ?? '',
            'treatment_plan' => $_POST['treatment_plan'] ?? '',
            'prescription' => $_POST['prescription'] ?? '',
            'lab_requests' => $_POST['lab_requests'] ?? '',
            'follow_up_date' => $_POST['follow_up_date'] ?? null,
            'consultation_notes' => $_POST['consultation_notes'] ?? '',
            'history_of_illness' => $_POST['history_of_illness'] ?? ''
        ];

        // Validate required fields for completion
        if (empty($consultationData['diagnosis']) || empty($consultationData['treatment_plan'])) {
           throw new Exception("Diagnosis and Treatment Plan are required to complete the consultation"); 
        }

        // Update and complete the consultation
        $consultation->updateConsultation($consultationId, $consultationData);
        
        // Create medical record
        require_once '../classes/MedicalRecord.php';
        $medicalRecord = new MedicalRecord($db); // Pass database connection
        
        // Get consultation details to get patient_id and doctor_id
        $consultationDetails = $consultation->getConsultation($consultationId);
        
        // Validate consultation details
        if (!isset($consultationDetails['patient_id']) || !isset($consultationDetails['doctor_id'])) {
            throw new Exception("Missing required consultation details (patient_id or doctor_id)");
        }
        
        // Prepare data for medical record
        $medicalRecordData = array_merge($consultationData, [
            'chief_complaint' => $consultationDetails['chief_complaint'] ?? ''
        ]);
        
        try {

            
            // Create the medical record
            $medicalRecord->createRecord(
                $consultationDetails['patient_id'],
                $consultationDetails['doctor_id'],
                $consultationId,
                $medicalRecordData
            );
        } catch (Exception $e) {
            error_log("Failed to create medical record. Error details:");
            error_log("Exception message: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
            error_log("Medical Record Data: " . json_encode($medicalRecordData, JSON_PRETTY_PRINT));
            throw new Exception("Failed to create medical record: " . $e->getMessage());
        }

        // Complete the consultation
        $consultation->completeConsultation($consultationId, $consultationData);

        $_SESSION['success'] = "Consultation completed successfully";
    } else {
        throw new Exception("Invalid action"); 
    }

    // Redirect back to the consultation view
    header("Location: ../views/consultations/consultation.php?id=" . $consultationId);
    exit;

} catch (Exception $e) {
    error_log("Error in consultation handler: " . $e->getMessage());
    $_SESSION['error'] = "Error processing consultation: " . $e->getMessage();
    header("Location: ../views/consultations/consultation.php?id=" . $consultationId);
    exit;
}