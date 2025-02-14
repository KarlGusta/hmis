<?php
require_once '../config/database.php';
require_once '../classes/InsuranceProvider.php';

try {
    $db = new DatabaseConnection();
    $provider = new InsuranceProvider($db);

    $data = [
        'provider_name' => filter_input(INPUT_POST, 'provider_name', FILTER_SANITIZE_STRING),
        'contact_number' => filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING),
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
        'website' => filter_input(INPUT_POST, 'website', FILTER_SANITIZE_URL),
        'address' => filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING),
        'coverage_types' => filter_input(INPUT_POST, 'coverage_types', FILTER_SANITIZE_STRING),
        'policy_details' => filter_input(INPUT_POST, 'policy_details', FILTER_SANITIZE_STRING)
    ];

    $provider_id = $provider->register($data);

    $_SESSION['success'] = "Insurance provider registered successfully with ID: $provider_id";
    header('Location: ../views/insurance/list.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = "Registration failed: " . $e->getMessage();
    header('Location: ../views/insurance/register.php');
}