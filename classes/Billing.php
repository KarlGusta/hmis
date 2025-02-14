<?php
require_once(__DIR__ . '/../config/database.php');

class Billing
{
    private $db;
    private $table = 'bills';

    public function __construct(DatabaseConnection $db)
    {
        $this->db = $db;
    }

    public function createBill($data)
    {
        try {
            $this->db->beginTransaction();

            // Create main bill entry
            $query = "INSERT INTO {$this->table} (patient_id, appointment_id, bill_date, total_amount, status, notes, created_at)
            VALUES (:patient_id, :appointment_id, CURDATE(), :total_amount, 'pending', :notes, NOW())";

            $params = [
                ':patient_id' => $data['patient_id'],
                ':appointment_id' => $data['appointment_id'],
                ':total_amount' => $data['total_amount'],
                ':notes' => $data['notes']
            ];

            $bill_id = $this->db->executeQuery($query, $params);

            // Insert bill items
            foreach ($data['items'] as $item) {
                $query = "INSERT INTO bill_items (bill_id, item_type, item_id, quantity, unit_price, total_price)
                          VALUES (:bill_id, :item_type, :item_id, :quantity, :unit_price, :total_price)";

                $params = [
                    ':bill_id' => $bill_id,
                    ':item_type' => $item['type'],
                    ':item_id' => $item['item_id'],
                    ':quantity' => $item['quantity'],
                    ':unit_price' => $item['unit_price'],
                    ':total_price' => $item['quantity'] * $item['unit_price']
                ];

                $this->db->executeQuery($query, $params);
            }

            $this->db->commit();
            return $bill_id;
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Failed to create bill: " . $e->getMessage());
        }
    }

    public function getItemPrice($type, $id)
    {
        $table = $this->getTableByType($type);
        $priceColumn = $this->getPriceColumnByType($type);

        $query = "SELECT $priceColumn as price FROM $table WHERE id = :id";
        $result = $this->db->fetchOne($query, [':id' => $id]);

        return $result ? $result['price'] : 0;
    }

    private function getTableByType($type)
    {
        switch ($type) {
            case 'consultation':
                return 'doctors';
            case 'laboratory':
                return 'laboratory_tests';
            case 'medication':
                return 'medications';
            case 'procedure':
                return 'procedures';
            default:
                throw new Exception('Invalid item type');
        }
    }

    private function getPriceColumnByType($type)
    {
        switch ($type) {
            case 'consultation':
                return 'consultation_fee';
            case 'laboratory':
            case 'medication':
            case 'procedure':
                return 'cost';
            default:
                throw new Exception('Invalid item type');
        }
    }

    public function getAllBills() {
        try {
            $query = "SELECT b.*, CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                             a.appointment_datetime
                      FROM {$this->table} b 
                      JOIN patients p ON b.patient_id = p.id
                      LEFT JOIN appointments a ON b.appointment_id = a.id
                      ORDER BY b.created_at DESC";
                      
            return $this->db->fetchAll($query);
        } catch (Exception $e) {
            throw new Exception("Failed to fetch bills: " . $e->getMessage());
        }
    }
    
    public function getBill($id) {
        try {
            // Get bill details
            $query = "SELECT b.*, p.first_name || ' ' || p.last_name as patient_name,
                             a.appointment_datetime, i.provider_name as insurance_provider
                      FROM {$this->table} b 
                      JOIN patients p ON b.patient_id = p.id
                      LEFT JOIN appointments a ON b.appointment_id = a.id
                      LEFT JOIN insurance_providers i ON p.insurance_provider = i.id
                      WHERE b.id = :id";
                      
            $bill = $this->db->fetchOne($query, [':id' => $id]);
            
            if (!$bill) {
                throw new Exception("Bill not found");
            }
            
            // Get bill items
            $query = "SELECT bi.*, 
                             CASE bi.item_type 
                                 WHEN 'consultation' THEN d.first_name || ' ' || d.last_name
                                 WHEN 'laboratory' THEN lt.name
                                 WHEN 'medication' THEN m.name
                                 WHEN 'procedure' THEN p.name
                             END as item_name
                      FROM bill_items bi
                      LEFT JOIN doctors d ON bi.item_type = 'consultation' AND bi.item_id = d.id
                      LEFT JOIN laboratory_tests lt ON bi.item_type = 'laboratory' AND bi.item_id = lt.id
                      LEFT JOIN medications m ON bi.item_type = 'medication' AND bi.item_id = m.id
                      LEFT JOIN procedures p ON bi.item_type = 'procedure' AND bi.item_id = p.id
                      WHERE bi.bill_id = :bill_id";
                      
            $bill['items'] = $this->db->fetchAll($query, [':bill_id' => $id]);
            
            // Get payment history
            $query = "SELECT * FROM payments WHERE bill_id = :bill_id ORDER BY payment_date";
            $bill['payments'] = $this->db->fetchAll($query, [':bill_id' => $id]);
            
            return $bill;
        } catch (Exception $e) {
            throw new Exception("Failed to fetch bill details: " . $e->getMessage());
        }
    }

    public function processPayment($billId, $amount, $paymentMethod) {
        try {
            $this->db->beginTransaction();

            // Get current bill details
            $query = "SELECT total_amount, paid_amount FROM {$this->table} WHERE id = :bill_id";
            $bill = $this->db->fetchOne($query, [':bill_id' => $billId]);

            if (!$bill) {
                throw new Exception("Bill not found");
            }

            $newPaidAmount = $bill['paid_amount'] + $amount;
            if ($newPaidAmount > $bill['total_amount']) {
                throw new Exception("Payment amount exceeds remaining balance");
            }

            // Record payment
            $query = "INSERT INTO payments (bill_id, amount, payment_method, payment_date) 
                     VALUES (:bill_id, :amount, :payment_method, NOW())";

            $params = [
                ':bill_id' => $billId,
                ':amount' => $amount,
                ':payment_method' => $paymentMethod
            ];

            $this->db->executeQuery($query, $params);

            // Update bill status and paid amount
            $status = $newPaidAmount >= $bill['total_amount'] ? 'paid' : 'partially_paid';

            $query = "UPDATE {$this->table} 
                     SET paid_amount = :paid_amount, 
                         status = :status,
                         payment_method = :payment_method,
                         payment_date = NOW(),
                         updated_at = NOW()
                     WHERE id = :bill_id";

            $params = [
                ':paid_amount' => $newPaidAmount,
                ':status' => $status,
                ':payment_method' => $paymentMethod,
                ':bill_id' => $billId
            ];

            $this->db->executeQuery($query, $params);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Failed to process payment: " . $e->getMessage());
        }
    }

    public function generateBillNumber() {
        $prefix = date('Ym');
        $query = "SELECT MAX(CAST(SUBSTRING(bill_number, 9) AS UNSIGNED)) as last_number 
                 FROM {$this->table} 
                 WHERE bill_number LIKE :prefix";
        
        $result = $this->db->fetchOne($query, [':prefix' => $prefix . '%']);
        $lastNumber = $result['last_number'] ?? 0;
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        
        return $prefix . $nextNumber;
    }

    public function getPaymentHistory($billId) {
        $query = "SELECT p.*, u.username as processed_by 
                 FROM payments p
                 LEFT JOIN users u ON p.processed_by = u.id
                 WHERE p.bill_id = :bill_id 
                 ORDER BY p.payment_date DESC";
                 
        return $this->db->fetchAll($query, [':bill_id' => $billId]);
    }

    public function voidBill($billId, $reason) {
        try {
            $this->db->beginTransaction();

            $query = "UPDATE {$this->table} SET status = 'cancelled', notes = :reason WHERE id = :bill_id";
            $this->db->executeQuery($query, [':bill_id' => $billId, ':reason' => $reason]);

            // Log the void operation
            $query = "INSERT INTO bill_history (bill_id, action, notes, created_by) 
                     VALUES (:bill_id, 'void', :notes, :user_id)";
            $this->db->executeQuery($query, [
                ':bill_id' => $billId,
                ':notes' => $reason,
                ':user_id' => $_SESSION['user_id'] ?? null
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Failed to void bill: " . $e->getMessage());
        }
    }

    public function getBillsByPatient($patientId) {
        $query = "SELECT b.*, COUNT(p.id) as payment_count
                 FROM {$this->table} b
                 LEFT JOIN payments p ON b.id = p.bill_id
                 WHERE b.patient_id = :patient_id
                 GROUP BY b.id
                 ORDER BY b.created_at DESC";
                 
        return $this->db->fetchAll($query, [':patient_id' => $patientId]);
    }

    public function getBillsByDateRange($startDate, $endDate) {
        $query = "SELECT b.*, p.first_name || ' ' || p.last_name as patient_name
                 FROM {$this->table} b
                 JOIN patients p ON b.patient_id = p.id
                 WHERE DATE(b.created_at) BETWEEN :start_date AND :end_date
                 ORDER BY b.created_at DESC";
                 
        return $this->db->fetchAll($query, [
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);
    }

    public function recordPayment($data)
    {
        try {
            $this->db->beginTransaction();

            // Get current bill details
            $query = "SELECT total_amount, paid_amount FROM {$this->table} WHERE id = :bill_id";
            $bill = $this->db->fetchOne($query, [':bill_id' => $data['bill_id']]);

            if (!$bill) {
                throw new Exception("Bill not found");
            }

            $newPaidAmount = $bill['paid_amount'] + $data['amount'];
            if ($newPaidAmount > $bill['total_amount']) {
                throw new Exception("Payment amount exceeds remaining balance");
            }

            // Record payment
            $query = "INSERT INTO payments (bill_id, amount, payment_method, notes, payment_date) 
                     VALUES (:bill_id, :amount, :payment_method, :notes, NOW())";

            $params = [
                ':bill_id' => $data['bill_id'],
                ':amount' => $data['amount'],
                ':payment_method' => $data['payment_method'],
                ':notes' => $data['notes']
            ];

            $this->db->executeQuery($query, $params);

            // Update bill status and paid amount
            $status = $newPaidAmount >= $bill['total_amount'] ? 'paid' : 'partially_paid';

            $query = "UPDATE {$this->table} 
                     SET paid_amount = :paid_amount, 
                         status = :status,
                         payment_method = :payment_method,
                         payment_date = NOW(),
                         updated_at = NOW()
                     WHERE id = :bill_id";

            $params = [
                ':paid_amount' => $newPaidAmount,
                ':status' => $status,
                ':payment_method' => $data['payment_method'],
                ':bill_id' => $data['bill_id']
            ];

            $this->db->executeQuery($query, $params);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Failed to record payment: " . $e->getMessage());
        }
    }
}
