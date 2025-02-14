<?php
require_once '../config/database.php';
require_once '../classes/Billing.php';

try {
    $db = new DatabaseConnection();
    $billing = new Billing($db);
    
    // Sanitize and validate input
    $data = [
        'patient_id' => filter_input(INPUT_POST, 'patient_id', FILTER_SANITIZE_NUMBER_INT),
        'appointment_id' => filter_input(INPUT_POST, 'appointment_id', FILTER_SANITIZE_NUMBER_INT),
        'total_amount' => filter_input(INPUT_POST, 'total_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
        'notes' => filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING),
        'items' => []
    ];

    // Validate and sanitize bill items
    if (isset($_POST['items'])) {
        foreach ($_POST['items'] as $item) {
            $data['items'][] = [
                'type' => filter_var($item['type'], FILTER_SANITIZE_STRING),
                'item_id' => filter_var($item['item_id'], FILTER_SANITIZE_NUMBER_INT),
                'quantity' => filter_var($item['quantity'], FILTER_SANITIZE_NUMBER_INT),
                'unit_price' => filter_var($item['unit_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)                
            ];
        }
    }

    // Create the bill
    $bill_id = $billing->createBill($data);
    
    $_SESSION['success'] = "Bill created successfully with ID: $bill_id";
    header('Location: ../views/billing/view.php?id=' . $bill_id);
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = "Failed to create bill: " . $e->getMessage();
    header('Location: ../views/billing/create.php');
    exit;
}