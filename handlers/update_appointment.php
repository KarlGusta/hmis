<?php

session_start();
require_once '../config/database.php';
require_once '../classes/Appointment.php';

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not authenticated");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    $db = new DatabaseConnection();
    $appointment = new Appointment($db);

    // Validate appointment ID
    $id = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    if (!$id) {
        throw new Exception("Invalid appointment ID");
    }

    // Get current appointment to check if modification is allowed
    $currentAppointment = $appointment->getAppointment($id);
    if (!$currentAppointment) {
        throw new Exception("Appointment not found");
    } 

    // Only allow updates to scheduled/confirmed appointments
    $allowedStatuses = ['scheduled', 'confirmed'];
    if (!in_array($currentAppointment['status'], $allowedStatuses)) {
        throw new Exception("Cannot modify appointment in current status");
    } 

    // Sanitize and validate input data
    $data = [
        'patient_id' => filter_input(INPUT_POST, 'patient_id', FILTER_VALIDATE_INT),
        'doctor_id' => filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT),
        'appointment_datetime' => filter_input(INPUT_POST, 'appointment_datetime', FILTER_SANITIZE_STRING),
        'reason' => filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING),
        'status' => filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING),
        'notes' => filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING)
    ];

    // Validate required fields
    if (empty($data['patient_id']) || empty($data['doctor_id']) || empty($data['appointment_datetime'])) {
        throw new Exception("Required fields are missing");
    }

    // Validate datetime format
    $datetime = DateTime::createFromFormat('Y-m-d\TH:i', $data['appointment_datetime']);
    if (!$datetime) {
        throw new Exception("Invalid datetime format");
    }

    // Check if the appointment time is in the future
    if ($datetime < new DateTime()) {
        throw new Exception("Appointment time must be in the future");
    }

    // Check doctor availability (exluding current appointment)
    if (!$appointment->checkAvailability($data['doctor_id'], $data['appointment_datetime'], $id)) {
        throw new Exception("Doctor is not available at the selected time"); 
    }

    // Update the appointment
    $appointment->updateAppointment($id, $data);

    $_SESSION['success'] = "Appointment updated successfully";
    header('Location: ../views/appointments/list.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../views/appointments/edit.php?id=' . $id);
    exit;
}