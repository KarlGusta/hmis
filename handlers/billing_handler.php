<?php
require_once(__DIR__ . '/../classes/Billing.php');
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../classes/ActivityLogger.php');

class BillingHandler {
    private $billing;
    private $db;
    private $activityLogger;

    public function __construct() {
        $this->db = new DatabaseConnection();
        $this->billing = new Billing($this->db);
        $this->activityLogger = new ActivityLogger($this->db);
    }

    public function handleRequest() {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        switch ($action) {
            case 'create':
                $this->handleCreateBill();
                break;
            case 'get':
                $this->handleGetBill();
                break;
            case 'list':
                $this->handleListBills();
                break;
            case 'record_payment':
                $this->handleRecordPayment();
                break;
            case 'get_item_price':
                $this->handleGetItemPrice();
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
        }
    }

    private function handleCreateBill() {
        try {
            $data = [
                'patient_id' => $_POST['patient_id'],
                'appointment_id' => $_POST['appointment_id'] ?? null,
                'total_amount' => $_POST['total_amount'],
                'notes' => $_POST['notes'] ?? '',
                'items' => json_decode($_POST['items'], true)
            ];

            $billId = $this->billing->createBill($data);
            
            // Log activity
            $this->activityLogger->logActivity([
                'user_id' => $_SESSION['user_id'] ?? null,
                'activity_type' => 'BILLING_CREATE',
                'activity_description' => "Created new bill #$billId for patient #" . $data['patient_id'],
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'entity_type' => 'bill',
                'entity_id' => $billId,
                'status' => 'success'
            ]);

            echo json_encode(['success' => true, 'bill_id' => $billId]);
        } catch (Exception $e) {
            // Log failed activity
            $this->activityLogger->logActivity([
                'user_id' => $_SESSION['user_id'] ?? null,
                'activity_type' => 'BILLING_CREATE',
                'activity_description' => "Failed to create bill: " . $e->getMessage(),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'entity_type' => 'bill',
                'entity_id' => null,
                'status' => 'error'
            ]);
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function handleGetBill() {
        try {
            $billId = $_GET['bill_id'];
            $bill = $this->billing->getBill($billId);
            echo json_encode(['success' => true, 'bill' => $bill]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function handleListBills() {
        try {
            $bills = $this->billing->getAllBills();
            echo json_encode(['success' => true, 'bills' => $bills]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function handleRecordPayment() {
        try {
            $data = [
                'bill_id' => $_POST['bill_id'],
                'amount' => $_POST['amount'],
                'payment_method' => $_POST['payment_method'],
                'payment_date' => $_POST['payment_date'] ?? date('Y-m-d'),
                'notes' => $_POST['notes'] ?? ''
            ];

            $paymentId = $this->billing->recordPayment($data);

            // Log activity
            $this->activityLogger->logActivity([
                'user_id' => $_SESSION['user_id'] ?? null,
                'activity_type' => 'PAYMENT_RECORD',
                'activity_description' => "Recorded payment of " . $data['amount'] . " for bill #" . $data['bill_id'],
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'entity_type' => 'payment',
                'entity_id' => $paymentId,
                'status' => 'success'
            ]);

            echo json_encode(['success' => true, 'payment_id' => $paymentId]);
        } catch (Exception $e) {
            // Log failed activity
            $this->activityLogger->logActivity([
                'user_id' => $_SESSION['user_id'] ?? null,
                'activity_type' => 'PAYMENT_RECORD',
                'activity_description' => "Failed to record payment: " . $e->getMessage(),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'entity_type' => 'payment',
                'entity_id' => null,
                'status' => 'error'
            ]);
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function handleGetItemPrice() {
        try {
            $type = $_GET['type'];
            $id = $_GET['id'];
            $price = $this->billing->getItemPrice($type, $id);
            echo json_encode(['success' => true, 'price' => $price]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

// Initialize and run the handler
$handler = new BillingHandler();
$handler->handleRequest();
