<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Appointment.php';

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not authenticated");
    }

    // Validate input
    $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

    if (!$appointmentId || !$status) {
        throw new Exception("Invalid input parameters");
    }

    // Validate status value
    $validStatuses = ['scheduled', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception("Invalid status value");
    }

    // Update appointment status
    $db = new DatabaseConnection();
    $appointment = new Appointment($db);
    
    $result = $appointment->updateStatus($appointmentId, $status);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Failed to update appointment status");
    }
} catch (Exception $e) {
    error_log("Error updating appointment status: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}