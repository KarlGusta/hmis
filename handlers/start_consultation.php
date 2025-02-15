<?php
session_start();
require_once '../classes/Consultation.php';
require_once '../classes/PatientQueue.php';
require_once '../config/database.php';
require_once '../classes/ActivityLogger.php';
require_once '../classes/MedicalRecord.php';

$db = new DatabaseConnection();
$consultation = new Consultation($db);
$queue = new PatientQueue($db);
$activityLogger = new ActivityLogger($db);

$patientId = $_POST['patient_id'] ?? null;
$queueId = $_POST['queue_id'] ?? null;
$roomId = $_POST['room_id'] ?? null;

try {
    // Validate inputs
    if (!$patientId || !$queueId || !$roomId) {
        throw new Exception("Missing required parameters");
    }

    // Get queue details for symptoms/chief complaint
    $queueDetails = $queue->getQueueById($queueId);
    
    // Create initial medical record
    $medicalRecord = new MedicalRecord($db);
    $medicalRecordData = [
        'chief_complaint' => $queueDetails['symptoms'],
        'history_of_illness' => '',
        'diagnosis' => '',
        'treatment_plan' => '',
        'prescription' => '',
        'lab_requests' => '',
        'follow_up_date' => null,
        'consultation_notes' => '',
        'status' => 'in_progress'
    ];
    
    $medicalRecordId = $medicalRecord->createRecord($patientId, $_SESSION['user_id'], null, $medicalRecordData);
    
    // Start the consultation with medical record ID
    $consultationId = $consultation->startConsultation($queueId, $roomId, $medicalRecordId);

    // Update the medical record with the consultation ID
    // $medicalRecord->updateConsultationId($medicalRecordId, $consultationId);

    if (!$consultationId) {
        throw new Exception("Failed to start consultation");
    }

    // Log successful consultation start
    $activityLogger->logActivity([
        'user_id' => $_SESSION['user_id'] ?? null,
        'activity_type' => 'CONSULTATION_START',
        'activity_description' => "Started consultation #$consultationId for patient #$patientId in room #$roomId",
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'entity_type' => 'consultation',
        'entity_id' => $consultationId,
        'status' => 'success'
    ]);

    $_SESSION['success'] = "Consultation started successfully";
    header("Location: ../views/consultations/consultation.php?id=" . $consultationId);
    exit;

} catch (Exception $e) {
    // Log failed consultation start
    $activityLogger->logActivity([
        'user_id' => $_SESSION['user_id'] ?? null,
        'activity_type' => 'CONSULTATION_START',
        'activity_description' => "Failed to start consultation for patient #$patientId: " . $e->getMessage(),
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'entity_type' => 'consultation',
        'entity_id' => null,
        'status' => 'error'
    ]);

    error_log("Error starting consultation: " . $e->getMessage());
    $_SESSION['error'] = "Error starting consultation: " . $e->getMessage();
    header("Location: ../views/queue/waiting_room.php");
    exit;
}