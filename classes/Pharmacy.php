<?php

class Pharmacy {
    private $db;
    private $table = 'pharmacy_inventory';

    public function __construct($db) {
        $this->db = $db;
    }

    public function dispenseMedication($prescriptionId, $quantity, $notes = '') {
        try {
            // Start transaction
            $this->db->conn->begin_transaction();

            // Get prescription details
            $stmt = $this->db->conn->prepare(
                "SELECT p.*, m.name as medication_name, m.current_stock
                 FROM prescriptions p
                 JOIN medications m ON p.medication_id = m.id
                 WHERE p.id = ?"
            );
            $stmt->bind_param("i", $prescriptionId);
            $stmt->execute();
            $prescription = $stmt->get_result()->fetch_assoc();
            
            if (!$prescription) {
                throw new Exception("Prescription not found");
            }

            if ($prescription['current_stock'] < $quantity) {
                throw new Exception("Insufficient stock");
            }

            // Update medication stock
            $stmt = $this->db->conn->prepare(
                "UPDATE medications
                 SET current_stock = current_stock - ?
                 WHERE id = ?"
            );
            $stmt->bind_param("ii", $quantity, $prescription['medication_id']);
            $stmt->execute();
            
            // Record dispensing
            $stmt = $this->db->conn->prepare(
                "INSERT INTO medication_dispensing
                 (prescription_id, quantity, dispensed_by, notes)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("iiis", 
                $prescriptionId,
                $quantity,
                $_SESSION['user_id'],
                $notes
            );
            $stmt->execute();
            
            // Update prescription status
            $stmt = $this->db->conn->prepare(
                "UPDATE prescriptions
                 SET status = 'completed'
                 WHERE id = ?"
            );
            $stmt->bind_param("i", $prescriptionId);
            $stmt->execute();
            
            $this->db->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->db->conn->rollback();
            throw $e;
        }
    }

    public function getPendingPrescriptions() {
        $stmt = $this->db->conn->prepare(
            "SELECT p.*, m.name as medication_name, m.form, m.strength,
                    pat.first_name, pat.last_name,
                    u.first_name as prescribed_by_name
             FROM prescriptions p
             JOIN medications m ON p.medication_id = m.id
             JOIN medical_records mr ON p.medical_record_id = mr.id
             JOIN patients pat ON mr.patient_id = pat.id
             JOIN users u ON p.created_by = u.id
             WHERE p.status = 'active'
             ORDER BY p.created_at DESC"
        );
        
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $this->db->conn->error);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute query: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if ($result === false) {
            throw new Exception("Failed to get result: " . $stmt->error);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getDispensingHistory($prescriptionId) {
        $stmt = $this->db->conn->prepare(
            "SELECT md.*, u.first_name, u.last_name
             FROM medication_dispensing md
             JOIN users u ON md.dispensed_by = u.id
             WHERE md.prescription_id = ?
             ORDER BY md.dispensed_at DESC"
        );
        $stmt->bind_param("i", $prescriptionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function checkLowStock($threshold = 10) {
        $stmt = $this->db->conn->prepare(
            "SELECT * FROM medications
             WHERE current_stock <= ?
             ORDER BY current_stock ASC"
        );
        $stmt->bind_param("i", $threshold);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}