<?php
require_once '../config/database.php';
require_once '../classes/Department.php';

try {
    $db = new DatabaseConnection();
    $department = new Department($db);

    // Sanitize and validate input data
    $data = [
        'code' => filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING),
        'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
        'description' => filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING)
    ];

    // Register the department
    $department_id = $department->register($data);

    $_SESSION['success'] = "Department registered successfully";
    header('Location: ../views/departments/list.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = "Registration failed: " . $e->getMessage();
    header('Location: ../views/departments/register.php');
    exit;
}