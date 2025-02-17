<?php
require_once(__DIR__ . '/../config/database.php');

class Prescription {
    private $db;
    private $table = 'prescriptions';

    public function __construct($db) {
        $this->db = $db;
    }

    public function createPrescription($medicalRecordId, $prescriptionData) {
        try {
            $query = "INSERT INTO {$this->table} (
                medical_record_id,
                medication_id,
                dosage,
                frequency,
                duration,
                instructions,
                created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $medicalRecordId,
                $prescriptionData['medication_id'],
                $prescriptionData['dosage'],
                $prescriptionData['frequency'],
                $prescriptionData['duration'],
                $prescriptionData['instructions'],
                $_SESSION['user_id']
            ];

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error creating prescription: " . $e->getMessage());
            throw new Exception("Failed to create prescription");
        }
    }

    public function updatePrescription($prescriptionId, $data) {
        try {
            $query = "UPDATE {$this->table} SET
               medication_id = :medication_id,
               dosage = :dosage,
               frequency = :frequency,
               duration = :duration,
               instructions = :instructions,
               status = :status,
               updated_by = :updated_by
               WHERE id = :id";

            $params = [
                ':id' => $prescriptionId,
                ':medication_id' => $data['medication_id'],
                ':dosage' => $data['dosage'],
                ':frequency' => $data['frequency'],
                ':duration' => $data['duration'],
                ':instructions' => $data['instructions'],
                ':status' => $data['status'],
                ':updated_by' => $_SESSION['user_id']
            ];   

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error updating prescription: " . $e->getMessage());
            throw new Exception("Failed to update prescription");
        }
    }

    public function getPrescription($prescriptionId) {
        try {
            $query = "SELECT p.*, m.name as medication_name, m.form, m.strength
                      FROM {$this->table} p
                      JOIN medications m ON p.medication_id = m.id
                      WHERE p.id = ?";

            return $this->db->fetchOne($query, [$prescriptionId]);          
        } catch (Exception $e) {
            error_log("Error fetching prescription: " . $e->getMessage());
            throw new Exception("Failed to fetch prescription");
        }
    }

    public function getMedicalRecordPrescriptions($medicalRecordId) {
        try {
            $query = "SELECT p.*, m.name as medication_name, m.form, m.strength
                      FROM {$this->table} p
                      JOIN medications m ON p.medication_id = m.id
                      WHERE p.medical_record_id = ?
                      ORDER BY p.created_at DESC";

            return $this->db->fetchAll($query, [$medicalRecordId]);          
        } catch (Exception $e) {
            error_log("Error fetching prescriptions: " . $e->getMessage());
            throw new Exception("Failed to fetch prescriptions");
        }
    }

    public function deletePrescription($prescriptionId) {
        try {
            $query = "UPDATE {$this->table} SET status = 'cancelled' WHERE id = ?";
            return $this->db->executeQuery($query, [$prescriptionId]);
        } catch (Exception $e) {
            error_log("Error deleting prescription: " . $e->getMessage());
            throw new Exception("Failed to delete prescription");
        }
    }

    public function getAllMedications() {
        try {
            $query = "SELECT * FROM medications WHERE status = 'active' ORDER BY name";
            return $this->db->fetchAll($query);
        } catch (Exception $e) {
            error_log("Error fetching medications: " . $e->getMessage());
            throw new Exception("Failed to fetch medications");
        }
    }

    public function dispensePrescription($prescriptionId, $dispensingData) {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Update prescription status
            $query = "UPDATE {$this->table} SET
                status = :status,
                dispensed_quantity = :quantity,
                dispensing_notes = :notes,
                dispensed_by = :dispensed_by,
                dispensed_at = NOW()
                WHERE id = :id";

            $params = [
                ':id' => $prescriptionId,
                ':status' => $dispensingData['status'],
                ':quantity' => $dispensingData['quantity'],
                ':notes' => $dispensingData['notes'],
                ':dispensed_by' => $_SESSION['user_id']
            ];

            $this->db->executeQuery($query, $params);

            // Could add additional logic here like:
            // - Updating medication inventory
            // - Creating dispensing record
            // - Sending notifications

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error dispensing prescription: " . $e->getMessage());
            throw new Exception("Failed to dispense prescription");
        }
    }
}
