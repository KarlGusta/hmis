<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Billing.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$db = new DatabaseConnection();
$billing = new Billing($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'record_payment':
                $billingId = $_POST['billing_id'] ?? 0;
                $data = [
                    'payment_method' => $_POST['payment_method'] ?? '',
                    'payment_reference' => $_POST['payment_reference'] ?? null,
                    'amount' => $_POST['amount'] ?? 0,
                    'notes' => $_POST['notes'] ?? null
                ];

                if (empty($billingId) || empty($data['payment_method']) || empty($data['amount'])) {
                    throw new Exception("Required fields are missing");
                }

                $billing->recordPayment($billingId, $data);
                $_SESSION['success'] = "Payment recorded successfully";
                header('Location: ../views/billing/pending_bills.php');
                exit;

            case 'cancel_bill':
                $billingId = $_POST['billing_id'] ?? 0;
                
                if (empty ($billingId)) {
                    throw new Exception("Billing ID is required");
                }

                $billing->cancelBilling($billingId);
                $_SESSION['success'] = "Bill has been cancelled successfully";
                header('Location: ../views/billing/pending_bills.php');
                exit;

            default:
                throw new Exception("Invalid action specified");    
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
} else {
    // Handle GET requests if needed (for reports, etc.)
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'view_receipt':
            $billingId = $_GET['id'] ?? 0;

            if (empty($billingId)) {
                $_SESSION['error'] = "Billing ID is required";
                header('Location: ../views/billing/paid_bills.php');
                exit;
            }

            // Redirect to receipt page
            header('LocationL ../views/billing/receipt.php?id=' . $billingId);
            exit;

        default:
            $_SESSION['error'] = "Invalid request";
            header('Location: ../index.php');
            exit;    
    }
}