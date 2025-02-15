<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Room.php';
require_once __DIR__ . '/MedicalRecord.php';

class Consultation {
    private $db;
    private $table = 'consultations';
    private $room;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
        $this->room = new Room($db);
    }

    public function getDoctorConsultations($doctorId, $dateFrom = null, $dateTo = null, $status = 'all') {
        try {
            $query = "SELECT c.*,
                             CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                             r.room_number
                      FROM {$this->table} c
                      JOIN patients p ON c.patient_id = p.id
                      LEFT JOIN room_history rh ON c.id = rh.consultation_id AND rh.end_time IS NULL
                      LEFT JOIN rooms r ON rh.room_id = r.id
                      WHERE c.doctor_id = ?";

            $params = [$doctorId];
            
            if ($dateFrom && $dateTo) {
                $query .= " AND DATE(c.created_at) BETWEEN ? AND ?";
                $params[] = $dateFrom;
                $params[] = $dateTo;
            }

            if ($status !== 'all') {
                $query .= " AND c.status = ?";
                $params[] = $status;
            }

            $query .= " ORDER BY c.created_at DESC";

            return $this->db->fetchAll($query, $params);
        } catch (Exception $e) {
            throw new Exception("Failed to fetch doctor consultations: " . $e->getMessage());
        }
    }


    public function startConsultation($queueId, $roomId, $medicalRecordId) {
        try {
            $this->db->beginTransaction();

            // Get queue details
            $query = "SELECT * FROM patient_queue WHERE id = ?";
            $queue = $this->db->fetchOne($query, [$queueId]);

            if (!$queue) {
                throw new Exception("Queue record not found");
            }

            // Create consultation record with medical_record_id
            $consultationId = $this->createConsultation($queue, $medicalRecordId);

            // Update queue status
            $query = "UPDATE patient_queue
                     SET status = 'in_progress',
                         start_time = NOW()
                     WHERE id = ?";

            $this->db->executeQuery($query, [$queueId]);

            // Assign room
            $this->room->assignRoom($roomId, $queue['patient_id'], $consultationId);
            
            // Log consultation history
            $this->logConsultationHistory($consultationId, 'in_progress');

            $this->db->commit();
            return $consultationId;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function completeConsultation($consultationId, $consultationData = []) {
        try {
            $this->db->beginTransaction();

            // Get consultation details
            $consultation = $this->getConsultation($consultationId);

            // Update consultation status
            $query = "UPDATE {$this->table}
                     SET status = 'completed',
                         end_time = NOW()
                     WHERE id = ?";

            $this->db->executeQuery($query, [$consultationId]);
            
            // Update queue status
            $query = "UPDATE patient_queue
                     SET status = 'completed',
                         end_time = NOW()
                     WHERE id = ?";

            $this->db->executeQuery($query, [$consultation['queue_id']]);
            
            // Release room
            $query = "SELECT room_id FROM room_history
                     WHERE consultation_id = ?
                     AND end_time IS NULL";
            $roomRecord = $this->db->fetchOne($query, [$consultationId]);
            
            if ($roomRecord) {
                $this->room->releaseRoom($roomRecord['room_id']);
            }

            // Log consultation history
            $this->logConsultationHistory($consultationId, 'completed');

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function createConsultation($queue, $medicalRecordId) {
        // Generate UUID using PHP's built-in functions
        $consultationId = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        
        $query = "INSERT INTO {$this->table} (
                    id,
                    queue_id,
                    patient_id,
                    doctor_id,
                    department_id,
                    medical_record_id,
                    chief_complaint,
                    status
                    ) VALUES (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    'in_progress' 
                    )";

        $params = [
            $consultationId,
            $queue['id'],
            $queue['patient_id'],
            $queue['called_by'],
            $queue['department_id'],
            $medicalRecordId,
            $queue['symptoms']
        ];            

        $success = $this->db->executeQuery($query, $params);
        if (!$success) {
            throw new Exception("Failed to create consultation record");
        }

        return $consultationId;
    }

    public function updateConsultation($consultationId, $data) {
        try {
            $query = "UPDATE {$this->table}
                     SET diagnosis = ?,
                         treatment_plan = ?,
                         prescription = ?,
                         lab_requests = ?,
                         follow_up_date = ?,
                         consultation_notes = ?,
                         history_of_illness = ?
                     WHERE id = ?";

            $params = [
                $data['diagnosis'],
                $data['treatment_plan'],
                $data['prescription'],
                $data['lab_requests'],
                $data['follow_up_date'],
                $data['consultation_notes'],
                $data['history_of_illness'],
                $consultationId
            ];         

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            throw new Exception("Failed to update consultation: " . $e->getMessage());
        }
    }

    public function getConsultation($consultationId) {
        $query = "SELECT c.*,
                  p.first_name, p.last_name, p.date_of_birth, p.gender,
                  CONCAT(p.first_name, ' ', p.last_name) as patient_name
                  FROM consultations c
                  JOIN patients p ON c.patient_id = p.id
                  WHERE c.id = ?";
                  
        $result = $this->db->fetchOne($query, [$consultationId]);
        
        if (!$result) {
            throw new Exception("Consultation not found");
        }
        
        return $result;
    }

    private function logConsultationHistory($consultationId, $status) {
        $query = "INSERT INTO consultation_history
                  (id, consultation_id, status, changed_by)
                  VALUES (UUID(), ?, ?, ?)";

        return $this->db->executeQuery($query, [
            $consultationId,
            $status,
            $_SESSION['user_id']
        ]);          
    }
}