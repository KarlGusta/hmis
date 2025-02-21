<?php
require_once(__DIR__ . '/../config/database.php');

class Billing {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createBillingRecord($prescriptionId, $quantityDispensed) {
        try {
            // Get prescription and medication details
            $query = "SELECT p.*, m.name as medication_name, m.unit_price,
                      mr.patient_id, pat.first_name, pat.last_name
                      FROM prescriptions p
                      JOIN medications m ON p.medication_id = m.id
                      JOIN medical_records mr ON p.medical_record_id = mr.id
                      JOIN patients pat ON mr.patient_id = pat.id
                      WHERE p.id = ?";
                      
            $prescriptionData = $this->db->fetchOne($query, [$prescriptionId]);
            
            if (!$prescriptionData) {
                throw new Exception("Prescription not found");
            }

            // Calculate total amount
            $totalAmount = $prescriptionData['unit_price'] * $quantityDispensed;

            // Create billing record
            $query = "INSERT INTO billing (
                prescription_id,
                patient_id,
                medication_id,
                quantity,
                unit_price,
                total_amount,
                billing_type,
                reference_id,
                created_by
            ) VALUES (?, ?, ?, ?, ?, ?, 'prescription', NULL, ?)";

            $params = [
                $prescriptionId,
                $prescriptionData['patient_id'],
                $prescriptionData['medication_id'],
                $quantityDispensed,
                $prescriptionData['unit_price'],
                $totalAmount,
                $_SESSION['user_id']
            ];

            $billingId = $this->db->executeQuery($query, $params, true);

            return [
                'id' => $billingId,
                'patient_name' => $prescriptionData['first_name'] . ' ' . $prescriptionData['last_name'],
                'medication_name' => $prescriptionData['medication_name'],
                'quantity' => $quantityDispensed,
                'unit_price' => $prescriptionData['unit_price'],
                'total_amount' => $totalAmount,
                'type' => 'prescription'
            ];

        } catch (Exception $e) {
            throw new Exception("Failed to create billing record: " . $e->getMessage());
        }
    }

    public function createConsultationBill($consultationId) {
        try {
            // Get consultation and doctor details
            $query = "SELECT c.*, d.consultation_fee, p.id as patient_id,
                      CONCAT(p.first_name, ' ', p.last_name) as patient_name
                      FROM consultations c
                      JOIN doctors d ON c.doctor_id = d.user_id
                      JOIN patients p ON c.patient_id = p.id
                      WHERE c.id = ?";

            $consultationData = $this->db->fetchOne($query, [$consultationId]);
            
            if (!$consultationData) {
                throw new Exception("Consultation not found");
            }

            // Create billing record for consultation
            $query = "INSERT INTO billing (
                prescription_id,
                patient_id,
                medication_id,
                quantity,
                unit_price,
                total_amount,
                billing_type,
                reference_id,
                created_by
            ) VALUES (
                NULL,
                ?,
                NULL,
                1,
                ?,
                ?,
                'consultation',
                ?,
                ?
            )";

            $params = [
                $consultationData['patient_id'],
                $consultationData['consultation_fee'], // unit_price
                $consultationData['consultation_fee'], // total_amount (same as unit_price since quantity is 1)
                $consultationId,
                $_SESSION['user_id']
            ];

            $billingId = $this->db->executeQuery($query, $params, true);

            return [
                'id' => $billingId,
                'patient_name' => $consultationData['patient_name'],
                'type' => 'consultation',
                'amount' => $consultationData['consultation_fee']
            ];
        } catch (Exception $e) {
            throw new Exception("Failed to create consultation billing: " . $e->getMessage());
        }
    }

    public function getPendingBills() {
        $query = "SELECT b.*,
                 p.first_name, p.last_name, p.patient_id as patient_number,
                 CASE
                   WHEN b.billing_type = 'consultation' THEN 'Consultation Fee'
                   ELSE m.name
                 END as medication_name,
                 m.form, m.strength
                 FROM billing b
                 JOIN patients p ON b.patient_id = p.id
                 LEFT JOIN medications m ON b.medication_id = m.id
                 WHERE b.status = 'pending'
                 ORDER BY b.created_at DESC";
                 
        return $this->db->fetchAll($query);
    }

    public function getPaidBills($startDate, $endDate, $patientId = null) {
        $query = "SELECT b.*, 
                 p.first_name, p.last_name, p.patient_id as patient_number,
                 CASE 
                    WHEN b.billing_type = 'consultation' THEN 'Consultation Fee'
                    ELSE COALESCE(m.name, 'Unknown Medication')
                 END as medication_name,
                 COALESCE(m.form, '') as form, 
                 COALESCE(m.strength, '') as strength,
                 pt.payment_method,
                 pt.payment_reference,
                 pt.transaction_date as payment_date
                 FROM billing b
                 JOIN patients p ON b.patient_id = p.id
                 LEFT JOIN medications m ON b.medication_id = m.id
                 JOIN payment_transactions pt ON b.id = pt.billing_id
                 WHERE b.status = 'paid'
                 AND DATE(pt.transaction_date) BETWEEN ? AND ? ";

        $params = [$startDate, $endDate];     

        if ($patientId) {
            $query .= " AND p.patient_id = ?";
            $params[] = $patientId;
        }

        $query .= " ORDER BY pt.transaction_date DESC";

        return $this->db->fetchAll($query, $params);
    }

    public function getBillingDetails($billingId) {
        $query = "SELECT b.*, pat.first_name, pat.last_name, pat.patient_id as patient_number,
                 CASE
                    WHEN b.billing_type = 'prescription' THEN m.name
                    ELSE 'Consultation Fee'
                 END as medication_name,
                 m.form, m.strength
                 FROM billing b
                 JOIN patients pat ON b.patient_id = pat.id
                 LEFT JOIN medications m ON b.medication_id = m.id
                 LEFT JOIN prescriptions p ON b.prescription_id = p.id
                 WHERE b.id = ?";

        return $this->db->fetchOne($query, [$billingId]);         
    }

    public function recordPayment($billingId, $data) {
        try {
            $this->db->beginTransaction();

            // Update billing status
            $updateQuery = "UPDATE billing SET
                status = 'paid',
                payment_method = ?,
                payment_reference = ?,
                payment_date = NOW(),
                updated_by = ?
                WHERE id = ?";

            $updateParams = [
                $data['payment_method'],
                $data['payment_reference'] ?? null,
                $_SESSION['user_id'],
                $billingId
            ];
            
            $this->db->executeQuery($updateQuery, $updateParams);

            // Create payment transaction record
            $transactionQuery = "INSERT INTO payment_transactions (
                billing_id,
                amount,
                payment_method,
                payment_reference,
                transaction_date,
                notes,
                created_by
            ) VALUES (?, ?, ?, ?, NOW(), ?, ?)";

            $transactionParams = [
                $billingId,
                $data['amount'],
                $data['payment_method'],
                $data['payment_reference'] ?? null,
                $data['notes'] ?? null,
                $_SESSION['user_id']
            ];

            $this->db->executeQuery($transactionQuery, $transactionParams);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Failed to record payment: " . $e->getMessage());
        }
    }

    public function getPaymentHistory($billingId) {
        $query = "SELECT pt.*, u.first_name as staff_first_name, u.last_name as staff_last_name
                 FROM payment_transactions pt
                 JOIN users u ON pt.created_by = u.id
                 WHERE pt.billing_id = ?
                 ORDER BY pt.transaction_date DESC";

        return $this->db->fetchAll($query, [$billingId]);         
    }

    public function getBillingReport($startDate, $endDate) {
        $query = "SELECT
                    SUM(CASE WHEN b.status = 'paid' THEN b.total_amount ELSE 0 END) as total_paid,
                    SUM(CASE WHEN b.status = 'pending' THEN b.total_amount ELSE 0 END) as total_pending,
                    COUNT(CASE WHEN b.status = 'paid' THEN 1 END) as paid_count,
                    COUNT(CASE WHEN b.status = 'pending' THEN 1 END) as pending_count
                  FROM billing b
                  WHERE b.created_at BETWEEN ? AND ?";

        $params = [
            $startDate . " 00:00:00",
            $endDate . " 23:59:59"
        ];          

        return $this->db->fetchOne($query, $params);
    }

    public function cancelBilling($billingId) {
        $query = "UPDATE billing SET
                  status = 'cancelled',
                  updated_by = ?,
                  updated_at = NOW()
                  WHERE id = ?";

        return $this->db->executeQuery($query, [$_SESSION['user_id'], $billingId]);          
    }
}