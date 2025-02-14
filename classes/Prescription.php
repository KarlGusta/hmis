<?php
require_once __DIR__ . '/../config/database.php';

class Prescription {
    private $db;
    private $table = 'prescriptions';

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    // Create a new prescription with stock validation
    public function createPrescription($prescriptionData) {
        try {
            $this->db->beginTransaction();

            // Validate medication stock
            if (!$this->validateMedicationStock($prescriptionData['medication_id'], $prescriptionData['quantity'])) {
                throw new Exception("Insufficient stock for prescribed medication");
            }

            // Create prescription record
            $query = "INSERT INTO {$this->table} (
                medical_record_id,
                medication_id,
                dosage,
                frequency,
                duration,
                route,
                special_instructions,
                quantity,
                prescribed_by,
                prescribed_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $params = [
                $prescriptionData['medical_record_id'],
                $prescriptionData['medication_id'],
                $prescriptionData['dosage'],
                $prescriptionData['frequency'],
                $prescriptionData['duration'],
                $prescriptionData['route'],
                $prescriptionData['special_instructions'],
                $prescriptionData['quantity'],
                $_SESSION['user_id']
            ];

            $prescriptionId = $this->db->executeQuery($query, $params);

            // Update medication stock
            $this->updateMedicationStock($prescriptionData['medication_id'], $prescriptionData['quantity']);

            // Log prescription history
            $this->logPrescriptionHistory($prescriptionId, 'created');

            $this->db->commit();
            return $prescriptionId;

        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Failed to create prescription: " . $e->getMessage());
        }
    } 

    // Get prescriptions for a medical record with medication details
    public function getPrescriptionsByMedicalRecord($medicalRecordId) {
        $query = "SELECT p.*,
                         m.name as medication_name,
                         m.generic_name,
                         m.form,
                         m.strength,
                         CONCAT(u.first_name, ' ', u.last.name) as prescribed_by_name
                  FROM {$this->table} p
                  JOIN medications m ON p.medication_id = m.id
                  JOIN users u ON p.prescribed_by = u.id
                  WHERE p.medical_record_id = ?
                  ORDER BY p.prescribed_at DESC";

        return $this->db->fetchAll($query, [$medicalRecordId]);          
    }

    // Get active prescriptions for a patient
    public function getActivePrescriptions($patientId) {
        $query = "SELECT p.*,
                         m.name as medication_name,
                         mr.consultation_date
                  FROM {$this->table} p
                  JOIN medical_records mr ON p.medical_record_id = mr.id
                  JOIN medications m ON p.medication_id = m.id
                  WHERE mr.patient_id = ?
                  AND DATE_ADD(p.prescribed_at, INTERVAL p.duration DAY) >= CURRENT_DATE
                  ORDER BY p.prescribed_at DESC";

        return $this->db->fetchAll($query, [$patientId]);          
    }

    // Validate if sufficient stock exists
    private function validateMedicationStock($medicationId, $quantity) {
        $query = "SELECT stock_quantity
                  FROM medications
                  WHERE id = ?
                  AND stock_quantity >= ?";

        return $this->db->fetchOne($query, [$medicationId, $quantity]) !== false;          
    }

    // Update medication stock levels
    private function updateMedicationStock($medicationId, $quantity) {
        $query = "UPDATE medications
                 SET stock_quantity = stock_quantity - ?,
                     updated_at = NOW()
                 WHERE id = ?";

        return $this->db->executeQuery($query, [$quantity, $medicationId]);         
    }

    // Log prescription history
    private function logPrescriptionHistory($prescriptionId, $action, $notes = null) {
        $query = "INSERT INTO prescription_history (
                    prescription_id,
                    action,
                    performed_by,
                    notes,
                    created_at
                ) VALUES (?, ?, ?, ?, NOW())";

        return $this->db->executeQuery($query, [
            $prescriptionId,
            $action,
            $_SESSION['user_id'],
            $notes
        ]);        
    }

    // Calculate medication duration coverage
    public function calculateMedicationCoverage($dosage, $frequency, $quantity) {
        // Convert frequency to daily doses
        $dailyDoses = $this->calculateDailyDoses($frequency);

        // Calculate daily quantity needed
        $dailyQuantity = $dosage * $dailyDoses;

        // Calculate days covered
        return floor($quantity / $dailyQuantity);
    }

    // Convert frequency to number of daily doses
    private function calculateDailyDoses($frequency) {
        $frequencyMap = [
            'OD' => 1, // Once daily
            'BD' => 2, // Twice daily
            'TDS' => 3, // Three times daily
            'QDS' => 4, // Four times daily
            'Q4H' => 6, // Every 4 hours
            'Q6H' => 4, // Every 6 hours
            'Q8H' => 3, // Every 8 hours
            'Q12H' => 2, // Every 12 hours
            'STAT' => 1, // Immediately/once
            'PRN' => 1 // As needed
        ];

        return $frequencyMap[$frequency] ?? 1;
    }
}