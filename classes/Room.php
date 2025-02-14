<?php
require_once __DIR__ . '/../config/database.php';

class Room {
    private $db;
    private $table = 'rooms';

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    public function getAvailableRooms($departmentId) {
        try {
            $query = "SELECT r.*, d.name as department_name
                     FROM {$this->table} r
                     JOIN departments d ON r.department_id = d.id
                     WHERE r.department_id = ?
                     AND r.status = 'available'
                     ORDER BY r.room_number";

            return $this->db->fetchAll($query, [$departmentId]);         
        } catch (Exception $e) {
            throw new Exception("Failed to fetch available rooms: " . $e->getMessage()); 
        }
    }

    public function getAllRooms($departmentId = 'all', $status = 'all', $search = '') {
        try {
            $query = "SELECT r.*,
                             d.name as department_name,
                             CONCAT(p.first_name, ' ', p.last_name) as patient_name
                      FROM {$this->table} r
                      JOIN departments d ON r.department_id = d.id
                      LEFT JOIN patients p ON r.current_patient_id = p.id
                      WHERE 1=1";       
                      
            $params = [];
            
            if ($departmentId !== 'all') {
                $query .= " AND r.department_id = ?";
                $params[] = $departmentId;
            }

            if ($status !== 'all') {
                $query .= " AND r.status = ?";
                $params[] = $status;
            }

            if ($search) {
                $query .= " AND (r.room_number LIKE ? OR r.features LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            $query .= " ORDER BY r.room_number";

            return $this->db->fetchAll($query, $params);            
        } catch (Exception $e) {
            throw new Exception("Failed to fetch rooms: " . $e->getMessage());
        }
    }

    public function assignRoom($roomId, $patientId, $consultationId) {
        try {
            $this->db->beginTransaction();

            // Check if room is available
            $query = "SELECT * FROM {$this->table} WHERE id = ? AND status = 'available'";
            $room = $this->db->fetchOne($query, [$roomId]);

            if (!$room) {
                throw new Exception("Room is not available"); 
            }

            // Update room status
            $query = "UPDATE {$this->table}
                    SET status = 'occupied',
                        current_patient_id = ?,
                        current_consultation_id = ?
                    WHERE id = ?";
                    
            $this->db->executeQuery($query, [$patientId, $consultationId, $roomId]);
            
            // Create room history record
            $query = "INSERT INTO room_history (
                        id,
                        room_id,
                        patient_id,
                        consultation_id,
                        status,
                        start_time,
                        created_by
                     ) VALUES (
                        UUID(),
                        ?,
                        ?,
                        ?,
                        'occupied',
                        NOW(),
                        ?
                     )";

            $this->db->executeQuery($query, [
                $roomId,
                $patientId,
                $consultationId,
                $_SESSION['user_id']
            ]);         

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Failed to assign room: " . $e->getMessage());
        }
    }

    public function releaseRoom($roomId) {
        try {
            $this->db->beginTransaction();

            // Get current room status
            $query = "SELECT * FROM {$this->table} WHERE id = ?";
            $room = $this->db->fetchOne($query, [$roomId]);

            if (!$room) {
                throw new Exception("Room not found");
            }

            // Update room history end time
            $query = "UPDATE room_history
                     SET end_time = NOW()
                     WHERE room_id = ?
                     AND consultation_id = ?
                     AND end_time IS NULL";

            $this->db->executeQuery($query, [$roomId, $room['current_consultation_id']]);
            
            // Update room status
            $query = "UPDATE {$this->table}
                     SET status = 'available',
                         current_patient_id = NULL,
                         current_consultation_id = NULL
                     WHERE id = ?";

            $this->db->executeQuery($query, [$roomId]);
            
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Failed to release room: " . $e->getMessage());
        }
    } 

    public function getRoomStatus($roomId) {
        try {
            $query = "SELECT r.*,
                            d.name as department_name,
                            CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                            c.id as consultation_id
                      FROM {$this->table} r
                      JOIN departments d ON r.department_id = d.id
                      LEFT JOIN patients p ON r.current_patient_id = p.id
                      LEFT JOIN consultations c ON r.current_consultation_id = c.id
                      WHERE r.id = ?";

            return $this->db->fetchOne($query, [$roomId]);          
        } catch (Exception $e) {
            throw new Exception("Failed to fetch room status: " . $e->getMessage());
        }
    }

    public function updateRoomMaintenance($roomId, $status, $notes = '') {
        try {
            $query = "UPDATE {$this->table}
                     SET status = ?,
                         notes = ?
                     WHERE id = ?";

            return $this->db->executeQuery($query, [$status, $notes, $roomId]);         
        } catch (Exception $e) {
            throw new Exception("Failed to update room maintenance status: " . $e->getMessage());
        }
    }

    public function getRoomHistory($roomId, $startDate = null, $endDate = null) {
        try {
            $query = "SELECT rh.*,
                             CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                             CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                      FROM room_history rh
                      JOIN patients p ON rh.patient_id = p.id
                      JOIN users u ON rh.created_by = u.id
                      WHERE rh.room_id = ?";

            $params = [$roomId];
            
            if ($startDate) {
                $query .= " AND DATE(rh.start_time) >= ?";
                $params[] = $startDate;
            }

            if ($endDate) {
                $query .= " AND DATE(rh.start_time) <= ?";
                $params[] = $endDate;
            }

            $query .= " ORDER BY rh.start_time DESC";

            return $this->db->fetchAll($query, $params);
        } catch (Exception $e) {
            throw new Exception("Failed to fetch room history: " . $e->getMessage());
        }
    }
}