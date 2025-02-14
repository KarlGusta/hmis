<?php
require_once '../config/database.php';
require_once '../classes/Billing.php';

try {
    $data = [
        'bill_id' => filter_input(INPUT_POST, 'bill_id', FILTER_SANITIZE_NUMBER_INT),
        'amount' => filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
        'payment_method' => filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING)
    ];

    $db = new DatabaseConnection();
    $billing = new Billing($db);

    $billing->processPayment($data['bill_id'], $data['amount'], $data['payment_method']);

    $_SESSION['success'] = "Payment processed successfully";
} catch (Exception $e) {
    $_SESSION['error'] = "Payment processing failed: " . $e->getMessage();
}

header('Location: ../views/billing/view.php?id=' . $data['bill_id']);
exit;