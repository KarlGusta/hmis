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

        // Get consultation details to get the medical_record_id
        $consultationDetails = $consultation->getConsultation($consultationId);
        
        if (!$consultationDetails['medical_record_id']) {
            throw new Exception("No medical record found for this consultation");
        }

        // Update the existing medical record
        require_once '../classes/MedicalRecord.php';
        $medicalRecord = new MedicalRecord($db);
        
        try {
            // Update the medical record with completed status and consultation ID
            $medicalRecordData = array_merge($consultationData, [
                'status' => 'completed',
                'consultation_id' => $consultationId
            ]);
            
            $medicalRecord->updateRecord($consultationDetails['medical_record_id'], $medicalRecordData);
        } catch (Exception $e) {
            error_log("Failed to update medical record. Error details:");
            error_log("Exception message: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
            throw new Exception("Failed to update medical record: " . $e->getMessage());
        }

        // Update the consultation
        $consultation->updateConsultation($consultationId, $consultationData);

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