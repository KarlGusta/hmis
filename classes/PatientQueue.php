<?php
require_once __DIR__ . '/../config/database.php';

class PatientQueue {
    private $db;
    private $table = 'patient_queue';

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    public function addToQueue($data) {
        try {
            // Generate queue number (format: YYYYMMDD-XXX)
            $query = "SELECT COUNT(*) + 1 as next_number
                      FROM {$this->table}
                      WHERE DATE(created_at) = CURDATE()";
            $count = $this->db->fetchOne($query);
            $queueNumber = date('Ymd') . '-' . str_pad($count['next_number'], 3, '0', STR_PAD_LEFT);
            
            // Calculate estimated wait time
            $estimatedWaitTime = $this->getEstimatedWaitTime($data['department_id']);

            // Insert into queue
            $query = "INSERT INTO {$this->table} (
                        id,
                        queue_number,
                        patient_id,
                        department_id,
                        priority,
                        symptoms,
                        notes,
                        status,
                        called_by,
                        estimated_wait_time,
                        check_in_time
                      ) VALUES (
                          UUID(),
                          ?,
                          ?,
                          ?,
                          ?,
                          ?,
                          ?,
                          'waiting',
                          (SELECT user_id FROM doctors WHERE id = ?),
                          ?,
                          NOW()
                      )";

            $params = [
                $queueNumber,
                $data['patient_id'],
                $data['department_id'],
                $data['priority'],
                $data['symptoms'],
                $data['notes'],
                $data['called_by'],
                $estimatedWaitTime
            ];   
            
            $success = $this->db->executeQuery($query, $params);

            if ($success) {
                // Log the initial queue status
                $this->logQueueHistory($queueNumber, 'waiting');
            }

            return $success;
        } catch (Exception $e) {
            throw new Exception("Failed to add patient to queue: " . $e->getMessage());
        }
    }

    public function getQueueByDepartment($departmentId) {
        try {
            $query = "SELECT q.*,
                             CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                             d.name as department_name,
                             CONCAT(doc.first_name, ' ', doc.last_name) as doctor_name,
                             TIMESTAMPDIFF(MINUTE, q.check_in_time, NOW()) as wait_time
                      FROM {$this->table} q
                      JOIN patients p ON q.patient_id = p.id
                      JOIN departments d ON q.department_id = d.id
                      JOIN doctors doc ON q.called_by = doc.id
                      WHERE q.department_id = ?
                      AND q.status = 'waiting'
                      AND DATE(q.created_at) = CURDATE()
                      ORDER BY
                          FIELD(q.priority, 'emergency', 'urgent', 'normal'),
                          q.check_in_time ASC";

            return $this->db->fetchAll($query, [$departmentId]);              
        } catch (Exception $e) {
            throw new Exception("Failed to fetch queue: " . $e->getMessage());
        }
    }

    public function getCurrentQueue() {
        try {
            $query = "SELECT q.*,
                             CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                             d.name as department_name,
                             CONCAT(doc.first_name, ' ', doc.last_name) as doctor_name,
                             TIMESTAMPDIFF(MINUTE, q.check_in_time, NOW()) as wait_time
                      FROM {$this->table} q
                      JOIN patients p ON q.patient_id = p.id
                      JOIN departments d ON q.department_id = d.id
                      LEFT JOIN users u ON q.called_by = u.id
                      LEFT JOIN doctors doc ON u.id = doc.user_id
                      WHERE q.status = 'waiting'
                      AND DATE(q.created_at) = CURDATE()
                      ORDER BY 
                          FIELD(q.priority, 'emergency', 'urgent', 'normal'),
                          q.check_in_time ASC";

            $result = $this->db->fetchAll($query);
            
            // Enhanced debug output
            if (empty($result)) {
                error_log("No queue data found for date: " . date('Y-m-d'));
                error_log("SQL Query: " . $query);
            } else {
                error_log("Found " . count($result) . " queue entries");
            }
            
            return $result;               
        } catch (Exception $e) {
            error_log("Failed to fetch current queue. Error: " . $e->getMessage());
            throw new Exception("Failed to fetch current queue: " . $e->getMessage());
        }
    }

    public function updateQueueStatus($queue_id, $status, $room_number = null) {
        try {
            $query = "UPDATE {$this->table} 
                      SET status = ?, 
                          room_number = ?,
                          updated_at = NOW()
                      WHERE id = ?";
            
            $params = [$status, $room_number, $queue_id];
            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error updating queue status: " . $e->getMessage());
            throw $e;
        }
    }

    private function logQueueHistory($queueId, $status) {
        try {
            $query = "INSERT INTO queue_history
                      (id, queue_id, status, changed_by)
                      VALUES (UUID(), ?, ?, ?)";

            $this->db->executeQuery($query, [
                $queueId,
                $status,
                $_SESSION['user_id']
            ]);          
        } catch (Exception $e) {
            error_log("Failed to log queue history: " . $e->getMessage());
            throw $e;
        }
    }

    public function getEstimatedWaitTime($departmentId) {
        try {
            $query = "SELECT AVG(TIMESTAMPDIFF(MINUTE, check_in_time, start_time)) as avg_wait_time
                      FROM {$this->table}
                      WHERE department_id = ?
                      AND status = 'completed'
                      AND DATE(created_at) = CURDATE()";

            $result = $this->db->fetchOne($query, [$departmentId]);
            return round($result['avg_wait_time'] ?? 30); // Default to 30 minutes if no data          
        } catch (Exception $e) {
            error_log("Failed to calculate wait time: " . $e->getMessage());
            return 30; // Default to 30 minutes on error 
        }
    }

    public function getNextPatientForDoctor($doctor_id) {
        try {
            $query = "SELECT q.*,
                             CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                             d.name as department_name,
                             TIMESTAMPDIFF(MINUTE, q.check_in_time, NOW()) as wait_time
                      FROM {$this->table} q
                      JOIN patients p ON q.patient_id = p.id
                      JOIN departments d ON q.department_id = d.id
                      JOIN doctors doc ON q.called_by = doc.user_id
                      WHERE q.called_by = ?
                      AND q.status = 'waiting'
                      AND DATE(q.created_at) = CURDATE()
                      ORDER BY 
                          FIELD(q.priority, 'emergency', 'urgent', 'normal'),
                          q.check_in_time ASC
                      LIMIT 1";

            $result = $this->db->fetchOne($query, [$doctor_id]);
            return $result;
        } catch (Exception $e) {
            error_log("Failed to get next patient: " . $e->getMessage());
            return null;
        }
    }
}