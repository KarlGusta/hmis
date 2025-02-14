<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';

try {
    $db = new DatabaseConnection();
    $auth = new Auth($db);

    // Validate and sanitize input
    $data = [
        'username' => filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING),
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
        'password' => $_POST['password'],
        'confirm_password' => $_POST['confirm_password']
    ];

    // Validate required fields
    foreach ($data as $key => $value) {
        if (empty($value)) {
            throw new Exception("All fields are required");
        }
    }

    // Validate password match
    if ($data['password'] !== $data['confirm_password']) {
        throw new Exception("Passwords do not match");
    }

    // Validate password strength
    if (strlen($data['password']) < 8) {
        throw new Exception("Password must be at least 8 characters long");
    }

    $user_id = $auth->register($data);

    $_SESSION['success'] = "Registration successful. Please login.";
    header('Location: ../views/auth/login.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../views/auth/register.php');
    exit;
}