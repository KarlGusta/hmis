<?php
require_once __DIR__ . '/../config/database.php';

class Patient {
    private $db;
    private $patient_id;
    private $first_name;
    private $last_name;
    private $date_of_birth;
    private $gender;
    private $contact_number;
    private $email;

    public function __construct($db) {
        $this->db = $db;
    }

    // Patient Registration
    public function register($data) {
        try {
            $this->db->conn->begin_transaction();

            $stmt = $this->db->conn->prepare(
                "INSERT INTO patients (patient_id, first_name, last_name, date_of_birth, gender, blood_group, phone, email, 
                address, emergency_contact_name, emergency_contact_phone, photo, allergies, 
                current_medications, insurance_provider, insurance_id, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            if ($stmt === false) {
                throw new Exception("Failed to prepare statement: " . $this->db->conn->error);
            }

            $patient_id = $this->db->generatePatientID();
            $this->patient_id = $patient_id;
            $status = $data['status'] ?? 'active';
            
            $stmt->bind_param(
                "sssssssssssssssss",
                $patient_id,
                $data['first_name'],
                $data['last_name'],
                $data['date_of_birth'],
                $data['gender'],
                $data['blood_group'],
                $data['phone'],
                $data['email'],
                $data['address'],
                $data['emergency_contact_name'],
                $data['emergency_contact_phone'],
                $data['photo'],
                $data['allergies'],
                $data['current_medications'],
                $data['insurance_provider'],
                $data['insurance_id'],
                $status
            );

            $stmt->execute();

            // If medical history is provided
            if (!empty($data['medical_history'])) {
                $this->addMedicalHistory($this->patient_id, $data['medical_history']);
            }

            $this->db->conn->commit();
            return $this->patient_id;

        } catch (Exception $e) {
            $this->db->conn->rollback();
            $this->db->logError("Patient Registration Failed: " . $e->getMessage());
            throw $e;
        }
    }

    // Add Medical History
    private function addMedicalHistory($patient_id, $history) {
        $stmt = $this->db->conn->prepare(
            "INSERT INTO medical_history (patient_id, condition_name, diagnosis_date, notes, is_chronic) 
            VALUES (?, ?, ?, ?, ?)"
        );
        
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $this->db->conn->error);
        }

        foreach ($history as $condition) {
            $stmt->bind_param(
                "ssssi",
                $patient_id,
                $condition['name'],
                $condition['diagnosis_date'],
                $condition['notes'],
                $condition['is_chronic']
            );
            $stmt->execute();
        }
    }    

    // Search Patient
    public function searchPatient($searchTerm) {
        $stmt = $this->db->conn->prepare(
            "SELECT * FROM patients WHERE first_name LIKE ? OR last_name LIKE ? OR patient_id = ? OR phone = ?"            
        );

        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $this->db->conn->error);
        }

        $searchParam = "%$searchTerm%";
        $stmt->bind_param("ssss", $searchParam, $searchParam, $searchTerm, $searchTerm);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get Patient Medical History
    public function getMedicalHistory($patient_id) {
        $stmt = $this->db->conn->prepare(
            "SELECT * FROM medical_history WHERE patient_id = ? ORDER BY diagnosis_date DESC"            
        );

        $stmt->bind_param("s", $patient_id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update Patient Profile
    public function updateProfile($data) {
        try {
            $this->db->conn->begin_transaction();

            $stmt = $this->db->conn->prepare(
                "UPDATE patients SET first_name = ?, last_name = ?, contact_number = ?, email = ?, address = ?, blood_group = ?, emergency_contact_name = ?, emergency_contact_number = ? WHERE patient_id = ?"
            );

            $stmt->bind_param(
                "sssssssss",
                $data['first_name'],
                $data['last_name'],
                $data['contact_number'],
                $data['email'],
                $data['address'],
                $data['blood_group'],
                $data['emergency_contact_name'],
                $data['emergency_contact_number'],
                $data['patient_id']
            );

            $result = $stmt->execute();
            
            if ($result) {
                $this->db->conn->commit();
                return true;
            }

            $this->db->conn->rollback();
            return false; 
        } catch (Exception $e) {
            $this->db->conn->rollback();
            $this->db->logError("Patient Update Failed: " . $e->getMessage());
            throw $e;
        }
    }

    // Get Patient Details
    public function getPatientDetails($patient_id) {
        $stmt = $this->db->conn->prepare(
            "SELECT * FROM patients WHERE patient_id = ?"
        );

        $stmt->bind_param("s", $patient_id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAllPatients() {
        try {
            $stmt = $this->db->conn->prepare(
                "SELECT * FROM patients ORDER BY created_at DESC"
            );
            
            if ($stmt === false) {
                throw new Exception("Failed to prepare statement: " . $this->db->conn->error);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            $this->db->logError("Error fetching patients: " . $e->getMessage());
            throw $e;
        }
    }

    public function deletePatient($id) {
        try {
            $stmt = $this->db->conn->prepare(
                "UPDATE patients SET status = 'inactive' WHERE patient_id = ?"
            );
            
            if ($stmt === false) {
                throw new Exception("Failed to prepare statement: " . $this->db->conn->error);
            }

            $stmt->bind_param("s", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError("Error deleting patient: " . $e->getMessage());
            throw $e;
        }
    }

    // Delete Patient Record
    // public function deletePatient($patient_id) {
    //     try {
    //         $this->db->conn->begin_transaction();

    //         // Delete related records first
    //         $this->deleteRelatedRecords($patient_id);
            
    //         // Delete patient record
    //         $stmt = $this->db->conn->prepare(
    //             "DELETE FROM patients WHERE patient_id = ?"
    //         );

    //         $stmt->bind_param("s", $patient_id);
    //         $result = $stmt->execute();

    //         if ($result) {
    //             $this->db->conn->commit();
    //             return true;
    //         }

    //         $this->db->conn->rollback();
    //         return false;
    //     } catch (Exception $e) {
    //         $this->db->conn->rollback();
    //         $this->db->logError("Patient Deletion Failed: " . $e->getMessage());
    //         throw $e;
    //     }
    // }

    // Delete Related Records
    // private function deleteRelatedRecords($patient_id) {
    //     $tables = ['medical_history', 'appointments', 'billing', 'insurance_claims'];
        
    //     foreach ($tables as $table) {
    //         $stmt = $this->db->conn->prepare(
    //             "DELETE FROM $table WHERE patient_id = ?"
    //         );
    //         $stmt->bind_param("s", $patient_id);
    //         $stmt->execute(); 
    //     }
    // }    
}
?>