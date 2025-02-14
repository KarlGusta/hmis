<?php
require_once '../config/database.php';
require_once '../classes/Department.php';

try {
    if (!isset($_POST['department_id'])) {
        throw new Exception('Department ID is required');
    }

    $db = new DatabaseConnection();
    $department = new Department($db);

    $department_id = filter_input(INPUT_POST, 'department_id', FILTER_SANITIZE_STRING);
    
    if ($department->toggleStatus($department_id)) {
        $_SESSION['success'] = "Department status updated successfully";
    } else {
        $_SESSION['error'] = "Failed to update department status";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header('Location: ../views/departments/list.php');
exit;