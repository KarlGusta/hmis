<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/Billing.php');

class Dispensing {
    private $db;
    private $billing;

    public function __construct($db) {
        $this->db = $db;
        $this->billing = new Billing($db);
    }

    public function dispenseMedication($prescriptionId, $data) {
        try {
            $this->db->beginTransaction();

            // Update prescription status
            $query = "UPDATE prescriptions SET
                status = 'completed',
                dispensed_quantity = ?,
                dispensing_notes = ?,
                dispensed_by = ?,
                dispensed_at = NOW()
                WHERE id = ?";

            $params = [
                $data['quantity'],
                $data['notes'],
                $_SESSION['user_id'],
                $prescriptionId
            ];

            $this->db->executeQuery($query, $params);

            // Create dispensing record
            $query = "INSERT INTO medication_dispensing (
                prescription_id,
                quantity,
                notes,
                dispensed_by
            ) VALUES (?, ?, ?, ?)";

            $this->db->executeQuery($query, $params);

            // Update medication stock
            $prescriptionData = $this->getPrescriptionDetails($prescriptionId);
            $this->updateMedicationStock($prescriptionData['medication_id'], $data['quantity']);

            // Create billing record
            $billingData = $this->billing->createBillingRecord($prescriptionId, $data['quantity']);

            $this->db->commit();
            return [
                'success' => true,
                'billing' => $billingData
            ];

        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Failed to dispense medication: " . $e->getMessage());
        }
    }

    private function getPrescriptionDetails($prescriptionId) {
        $query = "SELECT p.*, m.name as medication_name, m.form, m.strength
                  FROM prescriptions p
                  JOIN medications m ON p.medication_id = m.id
                  WHERE p.id = ?";
        
        return $this->db->fetchOne($query, [$prescriptionId]);
    }

    private function updateMedicationStock($medicationId, $quantity) {
        // First check current stock
        $checkQuery = "SELECT current_stock FROM medications WHERE id = ?";
        $currentStock = $this->db->fetchOne($checkQuery, [$medicationId]);
        
        if (!$currentStock || $currentStock['current_stock'] < $quantity) {
            throw new Exception("Insufficient stock available");
        }

        // Proceed with update if sufficient stock exists
        $query = "UPDATE medications 
                  SET current_stock = current_stock - ?
                  WHERE id = ?";
        
        $this->db->executeQuery($query, [$quantity, $medicationId]);
    }

    public function getPendingPrescriptions() {
        $query = "SELECT p.*, m.name as medication_name, m.form, m.strength,
                         pat.first_name, pat.last_name, pat.patient_id as patient_number
                  FROM prescriptions p
                  JOIN medications m ON p.medication_id = m.id
                  JOIN medical_records mr ON p.medical_record_id = mr.id
                  JOIN patients pat ON mr.patient_id = pat.id
                  WHERE p.status = 'active'
                  ORDER BY p.created_at DESC";

        return $this->db->fetchAll($query);
    }
} 