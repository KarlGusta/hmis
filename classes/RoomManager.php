<?php
require_once __DIR__ . '/../config/database.php';

class RoomManager {
    private $db;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    // Gets the first available consultation room
    public function getAvailableRoom() {
        $stmt = $this->db->conn->prepare("SELECT room_number FROM consultation_rooms WHERE status = 'available' LIMIT 1");
        $stmt->execute();
        
        // Get the result set from the prepared statement
        $result = $stmt->get_result();
        
        // Fetch the room number from the result
        $room = $result->fetch_assoc();
        
        $stmt->close();
        
        if ($room) {
            // Mark the room as occupied
            $this->updateRoomStatus($room['room_number'], 'occupied');
            return $room['room_number'];
        }

        return null;
    }

    // Updates the status of a consultation room
    public function updateRoomStatus($room_number, $status) {
        $query = "UPDATE consultation_rooms
                 SET status = ?
                 WHERE room_number = ?";
                 
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param('si', $status, $room_number);
        
        return $stmt->execute();
    }
}