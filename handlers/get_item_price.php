<?php
require_once '../config/database.php';
require_once '../classes/Billing.php';

try {
    $type = $_GET['type'] ?? '';
    $id = $_GET['id'] ?? '';

    if (!$type || !$id) {
        throw new Exception('Invalid parameters'); 
    }

    $db = new DatabaseConnection();
    $billing = new Billing($db);

    $price = $billing->getItemPrice($type, $id);

    header('Content-type: application/json');
    echo json_encode(['price' => $price]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}