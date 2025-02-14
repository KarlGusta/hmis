<?php
require_once '../config/database.php';
require_once '../classes/InsuranceProvider.php';

header('Content-Type: application/json');

try {
    $provider_id = filter_input(INPUT_POST, 'provider_id', FILTER_SANITIZE_STRING);

    $db = new DatabaseConnection();
    $provider = new InsuranceProvider($db);

    $result = $provider->toggleStatus($provider_id);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}