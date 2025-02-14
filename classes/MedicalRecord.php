<?php
require_once(__DIR__ . '/../config/database.php');
require_once('Patient.php');
require_once('Doctor.php');

class MedicalRecord
{
    private $db;
    private $table = 'medical_records';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createRecord($patientId, $doctorId, $consultationId, $data)
    {
        try {
            // First get the doctor's ID from the doctors table using the user_id
            $query = "SELECT id FROM doctors WHERE user_id = ?";
            $doctorResult = $this->db->fetchOne($query, [$doctorId]);
            
            if (!$doctorResult) {
                error_log("Could not find doctor with user_id: " . $doctorId);
                throw new Exception("Invalid doctor ID");
            }

            $actualDoctorId = $doctorResult['id'];

            $query = "INSERT INTO {$this->table} (
                      patient_id,
                      doctor_id,
                      consultation_id,
                      chief_complaint,
                      history_of_illness,
                      diagnosis,
                      treatment_plan,
                      prescription,
                      lab_requests,
                      follow_up_date,
                      consultation_notes,
                      status,
                      created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $patientId,
                $actualDoctorId,
                $consultationId,
                $data['chief_complaint'],
                $data['history_of_illness'],
                $data['diagnosis'],
                $data['treatment_plan'],
                $data['prescription'],
                $data['lab_requests'],
                $data['follow_up_date'],
                $data['consultation_notes'],
                'completed',
                $_SESSION['user_id']
            ];

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error creating medical record: " . $e->getMessage());
            error_log("SQL Error: " . $this->db->getErrorInfo());
            throw new Exception("Failed to create medical record: " . $e->getMessage());
        }
    }

    public function updateRecord($recordId, $data)
    {
        try {
            $query = "UPDATE {$this->table} 
                     SET history_of_illness = :history_of_illness,
                         diagnosis = :diagnosis,
                         treatment_plan = :treatment_plan,
                         prescription = :prescription,
                         lab_requests = :lab_requests,
                         follow_up_date = :follow_up_date,
                         consultation_notes = :consultation_notes,
                         updated_by = :updated_by
                     WHERE id = :id";

            $params = [
                ':id' => $recordId,
                ':history_of_illness' => $data['history_of_illness'],
                ':diagnosis' => $data['diagnosis'],
                ':treatment_plan' => $data['treatment_plan'],
                ':prescription' => $data['prescription'],
                ':lab_requests' => $data['lab_requests'],
                ':follow_up_date' => $data['follow_up_date'],
                ':consultation_notes' => $data['consultation_notes'],
                ':updated_by' => $_SESSION['user_id']
            ];

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error updating medical record: " . $e->getMessage());
            throw new Exception("Failed to update medical record");
        }
    }

    public function getRecord($recordId)
    {
        try {
            $query = "SELECT mr.*, 
                             CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                             CONCAT(d.first_name, ' ', d.last_name) as doctor_name 
                      FROM {$this->table} mr 
                      JOIN patients p ON mr.patient_id = p.id 
                      JOIN doctors d ON mr.doctor_id = d.id 
                      WHERE mr.id = :id";

            return $this->db->fetchOne($query, [':id' => $recordId]);
        } catch (Exception $e) {
            error_log("Error fetching medical record: " . $e->getMessage());
            throw new Exception("Failed to fetch medical record");
        }
    }

    public function getPatientHistory($patientId)
    {
        try {
            $query = "SELECT r.*, d.name as doctor_name FROM {$this->table} r JOIN doctors d ON r.doctor_id = d.id WHERE r.patient_id = :patient_id ORDER BY r.visit_date DESC";

            return $this->db->fetchAll($query, [':patient_id' => $patientId]);
        } catch (Exception $e) {
            error_log("Error fetching patient history: " . $e->getMessage());
            throw new Exception("Failed to fetch patient medical history");
        }
    }

    public function addAttachment($recordId, $fileData)
    {
        try {
            $query = "INSERT INTO record_attachments (record_id, file_name, file_type, file_path, uploaded_at) VALUES (:record_id, :file_name, :file_type, :file_path, NOW())";

            $params = [
                ':record_id' => $recordId,
                ':file_name' => $fileData['name'],
                ':file_type' => $fileData['type'],
                ':file_path' => $fileData['path']
            ];

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error adding attachment: " . $e->getMessage());
            throw new Exception("Failed to add attachment to medical record");
        }
    }

    public function getAttachments($recordId)
    {
        try {
            $query = "SELECT * FROM record_attachments WHERE record_id = :record_id ORDER BY uploaded_at DESC";

            return $this->db->fetchAll($query, [':record_id' => $recordId]);
        } catch (Exception $e) {
            error_log("Error fetching attachments: " . $e->getMessage());
            throw new Exception("Failed to fetch record attachments");
        }
    }
}
