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
            $this->db->beginTransaction();

            // Get prescription details
            $query = "SELECT p.*, m.name as medication_name, m.current_stock
                      FROM prescriptions p
                      JOIN medications m ON p.medication_id = m.id
                      WHERE p.id = ?";
            $prescription = $this->db->fetchOne($query, [$prescriptionId]);
            
            if (!$prescription) {
                throw new Exception("Prescription not found");
            }

            if ($prescription['current_stock'] < $quantity) {
                throw new Exception("Insufficient stock");
            }

            // Update medication stock
            $updateStock = "UPDATE medications
                            SET current_stock = current_stock - ?
                            WHERE id = ?";
            $this->db->executeQuery($updateStock, [$quantity, $prescription['medication_id']]);
            
            // Record dispensing
            $insertDispensing = "INSERT INTO medication_dispensing
                                (prescription_id, quantity, dispensed_by, notes)
                                VALUES (?, ?, ?, ?)";
            $this->db->executeQuery($insertDispensing, [
                $prescriptionId,
                $quantity,
                $_SESSION['user_id'],
                $notes
            ]);            
            
            // Update prescription status if fully dispensed
            $updatePrescription = "UPDATE prescriptions
                                 SET status = 'completed'
                                 WHERE id = ?";
            $this->db->executeQuery($updatePrescription, [$prescriptionId]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getPendingPrescriptions() {
        $query = "SELECT p.*, m.name as medication_name, m.form, ,m.strength,
                         pat.first_name, pat.last_name,
                         u.first_name as prescribed_by_name
                  FROM prescriptions p
                  JOIN medications m ON p.medication_id = m.id
                  JOIN medical_records mr ON p.medical_record_id = mr.id
                  JOIN patients pat ON mr.patient_id = pat.id
                  JOIN users u ON p.created_by = u.id
                  WHERE p.status = 'active'
                  ORDER BY p.created_at DESC";

        return $this->db->fetchAll($query);          
    } 

    public function getDispensingHistory($prescriptionId) {
        $query = "SELECT md.*, u.first_name, u.last_name
                 FROM medication_dispensing md
                 JOIN users u ON md.dispensed_by = u.id
                 WHERE md.prescription_id = ?
                 ORDER BY md.dispensed_at DESC";

        return $this->db->fetchAll($query, [$prescriptionId]);         
    }

    public function checkLowStock($threshold = 10) {
        $query = "SELECT * FROM medications
                 WHERE current_stock <= ?
                 ORDER BY current_stock ASC";
                 
        return $this->db->fetchAll($query, [$threshold]);         
    }
}