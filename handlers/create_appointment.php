<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Appointment.php';

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not authenticated");
    }

    $db = new DatabaseConnection();
    $appointment = new Appointment($db);

    // Sanitize and validate input data
    $data = [
        'patient_id' => filter_input(INPUT_POST, 'patient_id', FILTER_VALIDATE_INT),
        'doctor_id' => filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT),
        'department_id' => filter_input(INPUT_POST, 'department_id', FILTER_SANITIZE_STRING),
        'appointment_datetime' => filter_input(INPUT_POST, 'appointment_datetime', FILTER_SANITIZE_STRING),
        'reason' => filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING),
        'status' => 'scheduled',
        'notes' => filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING)
    ];

    // Validate required fields
    if (empty($data['patient_id']) || empty($data['doctor_id']) || 
        empty($data['department_id']) || empty($data['appointment_datetime'])) {
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

    // Check doctor availability
    if (!$appointment->checkAvailability($data['doctor_id'], $data['appointment_datetime'])) {
        throw new Exception("Doctor is not available at the selected time");
    }

    // Create the appointment
    $appointment->createAppointment($data);

    $_SESSION['success'] = "Appointment created successfully";
    header('Location: ../views/appointments/list.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../views/appointments/schedule.php');
    exit;
}