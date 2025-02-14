<?php
require_once(__DIR__ . '/../config/database.php');

class Laboratory {
    private $db;
    private $table = 'lab_tests';

    public function __construct() {
        $this->db = new Database();
    }

    public function orderTest($patientId, $doctorId, $testData) {
        try {
            $this->db->beginTransaction();

            $query = "INSERT INTO {$this->table} (patient_id, doctor_id, test_type, priority, status, notes, ordered_at) VALUES (:patient_id, :doctor_id, :test_type, :priority, 'pending', :notes, NOW())";

            $params = [
                ':patient_id' => $patientId,
                ':doctor_id' => $doctorId,
                ':test_type' => $testData['test_type'],
                ':priority' => $testData['priority'] ?? 'normal',
                ':notes' => $testData['notes'] ?? null
            ];

            $testId = $this->db->executeQuery($query, $params);

            // Create billing entry
            $billingQuery = "INSERY INTO lab_test_billing (test_id, cost, status) VALUES (:test_id, :cost, 'pending')";

            $this->db->executeQuery($billingQuery, [
                ':test_id' => $testId,
                ':cost' => $testData['cost']
            ]);

            $this->db->commit();
            return $testId;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error ordering lab test: " . $e->getMessage());
            throw new Exception("Failed to order laboratory test");
        } 
    }

    public function updateTestResult($testId, $resultData) {
        try {
            $query = "UPDATE {$this->table} SET result = :result, result_notes = :result_notes, status = :status, completed_at = NOW(), updated_at = NOW() WHERE id = :id";

            $params = [
                ':id' => $testId,
                ':result' => $resultData['result'],
                ':result_notes' => $resultData['notes'] ?? null,
                ':status' => 'completed'
            ];

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error updating test result: " . $e->getMessage());
            throw new Exception("Failed to update test result"); 
        }
    }

    public function getTest($testId) {
        try {
            $query = "SELECT t.*, p.name as patient_name, d.name as doctor_name FROM {$this->table} t JOIN patients p ON t.patient_id = p.id JOIN doctors d ON t.doctor_id = d.id WHERE t.id = :id";
            
            return $this->db->fetchOne($query, [':id' => $testId]);
        } catch (Exception $e) {
            error_log("Error fetching test details: " . $e->getMessage());
            throw new Exception("Failed to fetch test details");
        }
    }

    public function getPatientTests($patientId) {
        try {
            $query = "SELECT t.*, d.name as doctor_name FROM {$this->table} t JOIN doctors d ON t.doctor_id = d.id WHERE t.patient_id = :patient_id ORDER BY t.ordered_at DESC";

            return $this->db->fetchAll($query, [':patient_id' => $patientId]);
        } catch (Exception $e) {
            error_log("Error fetching patient tests: " . $e->getMessage());
            throw new Exception("Failed to fetch patient test history"); 
        }
    }

    public function addAttachment($testId, $fileData) {
        try {
            $query = "INSERT INTO lab_test_attachments (test_id, file_name, file_type, file_path, uploaded_at) VALUES (:test_id, :file_name, :file_type, :file_path, NOW())";

            $params = [
                ':test_id' => $testId,
                ':file_name' => $fileData['name'],
                ':file_type' => $fileData['type'],
                ':file_path' => $fileData['path']
            ];

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error adding test attachment: " . $e->getMessage());
            throw new Exception("Failed to add test attachment");
        }
    }

    public function getPendingTests() {
        try {
            $query = "SELECT t.*, p.name as patient_name, d.name as doctor_name FROM {$this->table} t JOIN patients p ON t.patient_id = p.id JOIN doctors d ON t.doctor_id = d.id WHERE t.status = 'pending' ORDER BY CASE t.priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 ELSE 3 END, t.ordered_at ASC";

            return $this->db->fetchAll($query);
        } catch (Exception $e) {
            error_log("Error fetching pending tests: " . $e->getMessage());
            throw new Exception("Failed to fetch pending tests"); 
        }
    }
}