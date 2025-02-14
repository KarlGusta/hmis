<?php
session_start();
require_once '../classes/Appointment.php';
require_once '../classes/PatientQueue.php';
require_once '../config/database.php';

$db = new DatabaseConnection();
$appointment = new Appointment($db);
$queue = new PatientQueue($db);

// Get form data
$appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
$vitalSigns = $_POST['vital_signs'] ?? [];
$priority = $_POST['priority'] ?? 'normal';
$symptoms = $_POST['symptoms'] ?? '';
$notes = $_POST['notes'] ?? '';

try {
    // Begin transaction
    $db->beginTransaction();

    // 1. Record vital signs
    $appointment->recordVitalSigns($appointmentId, $vitalSigns);

    // 2. Update appointment status to checked in
    $appointment->updateStatus($appointmentId, 'checked_in');
    
    // 3. Get appointment details for queue
    $appointmentDetails = $appointment->getAppointment($appointmentId);

    if (!$appointmentDetails) {
        throw new Exception("Could not retrieve appointment details");
    }

    // 4. Add to patient queue
    $queueData = [
        'patient_id' => $appointmentDetails['patient_id'],
        'department_id' => $appointmentDetails['department_id'],
        'priority' => $priority,
        'symptoms' => $symptoms,
        'notes' => $notes,
        'called_by' => $appointmentDetails['doctor_id'],
        'vital_signs' => $vitalSigns
    ];

    // Enhanced debug logging
    error_log("Appointment Details: " . print_r($appointmentDetails, true));
    error_log("Queue Data before insertion: " . print_r($queueData, true));
    
    $queueResult = $queue->addToQueue($queueData);
    
    error_log("Queue insertion result: " . ($queueResult ? "success" : "failed"));
    if (!$queueResult) {
        error_log("MySQL Error: " . $db->error);
        throw new Exception("Failed to add patient to queue");
    }

    // Commit transaction
    $db->commit();

    $_SESSION['success'] = "Patient successfully checked in and added to queue";
    header('Location: ../views/queue/waiting_room.php');
    exit;
} catch (Exception $e) {
    // Rollback transaction on error
    $db->rollback();
    $_SESSION['error'] = "Error during check-in: " . $e->getMessage();
    header('Location: ../views/appointments/check_in.php?id=' . $appointmentId);
    exit;
}