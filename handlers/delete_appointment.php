<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Appointment.php';

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not authenticated");
    }

    // Validate appointment ID
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        throw new Exception("Invalid appointment ID");
    }

    $db = new DatabaseConnection();
    $appointment = new Appointment($db);

    // Get current appointment to check if deletion is allowed
    $currentAppointment = $appointment->getAppointment($id);
    if (!$currentAppointment) {
        throw new Exception("Appointment not found");
    }

    // Only allow deletion of scheduled/confirmed appointments
    $allowedStatuses = ['scheduled', 'confirmed'];
    if (!in_array($currentAppointment['status'], $allowedStatuses)) {
        throw new Exception("Cannot delete appointment in current status");
    } 

    // Delete the appointment
    $appointment->deleteAppointment($id);

    $_SESSION['success'] = "Appointment deleted successfully";
    header('Location: ../views/appointments/list.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../views/appointments/list.php');
    exit;
}