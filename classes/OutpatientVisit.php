<?php
require_once __DIR__ . '/../config/database.php';

class OutpatientVisit {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function registerVisit($data) {
        try {
            $this->db->conn->begin_transaction();

            // Generate UUID for the queue entry
            $queue_id = $this->generateUUID();
            $queue_number = $this->generateQueueNumber();

            // Insert into patient queue
            $stmt = $this->db->conn->prepare(
                "INSERT INTO patient_queue (
                    id, 
                    queue_number, 
                    patient_id, 
                    department_id,
                    priority, 
                    symptoms, 
                    notes, 
                    called_by, 
                    room_number,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'waiting')"
            );

            if ($stmt === false) {
                throw new Exception("Failed to prepare statement: " . $this->db->conn->error);
            }

            $room_number = $data['room_number'] ?? null; // Make room_number optional

            $stmt->bind_param(
                "ssissssss",
                $queue_id,
                $queue_number,
                $data['patient_id'],
                $data['department_id'],
                $data['priority'],
                $data['symptoms'],
                $data['notes'],
                $data['called_by'],
                $room_number
            );

            if (!$stmt->execute()) {
                throw new Exception("Failed to insert queue entry: " . $stmt->error);
            }

            // Log the queue entry
            $this->logQueueHistory($queue_id, 'waiting', $data['called_by'], 'Initial registration');

            $this->db->conn->commit();
            return $queue_id;
        } catch (Exception $e) {
            $this->db->conn->rollback();
            throw new Exception("Failed to register outpatient visit: " . $e->getMessage());
        }
    }

    private function generateQueueNumber() {
        // Format: OP-YYYYMMDD-XXX where XXX is sequential number for the day
        $date = date('Ymd');

        $stmt = $this->db->conn->prepare(
            "SELECT COUNT(*) as count FROM patient_queue
             WHERE DATE(created_at) = CURDATE()"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'] + 1;

        return sprintf("OP-%s-%03d", $date, $count);
    }

    private function logQueueHistory($queue_id, $status, $changed_by, $notes) {
        $stmt = $this->db->conn->prepare(
            "INSERT INTO queue_history (id, queue_id, status, changed_by, notes)
             VALUES (UUID(), ?, ?, ?, ?)"
        );

        if ($stmt === false) {
            throw new Exception("Failed to prepare log statement: " . $this->db->conn->error);
        }

        $stmt->bind_param("ssss", $queue_id, $status, $changed_by, $notes);
        $stmt->execute();
    }

    public function updateQueueStatus($queue_id, $status, $changed_by, $notes = '') {
        try {
            $this->db->conn->begin_transaction();

            $stmt = $this->db->conn->prepare(
                "UPDATE patient_queue SET status = ?,
                start_time = CASE
                    WHEN ? = 'in_progress' THEN CURRENT_TIMESTAMP
                    ELSE start_time
                END,
                end_time = CASE
                    WHEN ? IN ('completed, 'cancelled', 'no_show') THEN CURRENT_TIMESTAMP
                    ELSE end_time
                END
                WHERE id = ?"
            );

            $stmt->bind_param("ssss", $status, $status, $status, $queue_id);
            $stmt->execute();

            // Log the status change
            $this->logQueueHistory($queue_id, $status, $changed_by, $notes);

            $this->db->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->db->conn->rollback();
            throw new Exception("Failed to update queue status: " . $e->getMessage());
        }
    }

    public function getQueueByDepartment($department_id, $status = null) {
        $sql = "SELECT pq.*, p.first_name, p.last_name, p.phone
                FROM patient_queue pq
                JOIN patients p ON p.id = pq.patient_id
                WHERE pq.department_id = ? ";

        if ($status) {
            $sql .= "AND pq.status = ? ";
        }        

        $sql .= "ORDER BY
                 CASE pq.priority
                     WHEN 'emergency' THEN 1
                     WHEN 'urgent' THEN 2
                     ELSE 3
                 END,
                 pq.created_at ASC";
                 
        $stmt = $this->db->conn->prepare($sql);
        
        if ($status) {
            $stmt->bind_param("ss", $department_id, $status);
        } else {
            $stmt->bind_param("s", $department_id);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getVisitHistory($patient_id) {
        $stmt = $this->db->conn->prepare(
            "SELECT pq.*, d.name as department_name,
            qh.status as history_status, qh.notes as history_notes,
            qh.created_at as history_date
            FROM patient_queue pq
            JOIN departments d ON d.id = pq.department_id
            JOIN queue_history qh ON qh.queue_id = pq.id
            WHERE pq.patient_id = ?
            ORDER BY pq.created_at DESC"
        );

        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function generateUUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}