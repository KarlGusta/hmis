<?php
session_start();
require_once '../classes/Room.php';
require_once '../config/database.php';

class RoomHandler {
    private $db;
    private $table = 'rooms';

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    public function addRoom($data) {
        try {
            // Validate room number is unique
            $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE room_number = ?";
            $result = $this->db->fetchOne($query, [$data['room_number']]);

            if ($result['count'] > 0) {
                throw new Exception("Room number already exists");
            }

            // Insert new room
            $query = "INSERT INTO {$this->table} (
                        id,
                        room_number,
                        department_id,
                        room_type,
                        capacity,
                        features,
                        notes
                    ) VALUES (
                        UUID(),
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                    )";

            $params = [
                $data['room_number'],
                $data['department_id'],
                $data['room_type'],
                $data['capacity'] ?? 1,
                $data['features'] ?? null,
                $data['notes'] ?? null
            ];        

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            throw new Exception("Failed to add room: " . $e->getMessage());
        }
    }
}

// Handle the form submission
try {
    // Validate required fields
    $requiredFields = ['room_number', 'department_id', 'room_type'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Sanitize input
    $roomData = [
        'room_number' => trim($_POST['room_number']),
        'department_id' => trim($_POST['department_id']),
        'room_type' => trim($_POST['room_type']),
        'capacity' => isset($_POST['capacity']) ? (int)$_POST['capacity'] : 1,
        'features' => trim($_POST['features'] ?? ''),
        'notes' => trim($_POST['notes'] ?? '')
    ];

    // Create room
    $db = new DatabaseConnection();
    $handler = new RoomHandler($db);
    $handler->addRoom($roomData);

    $_SESSION['success'] = "Room added successfully";
    header('Location: ../views/room/list.php');
    exit;

} catch (Exception $e) {
    error_log("Error adding room: " . $e->getMessage());
    $_SESSION['error'] = "Error adding room: " . $e->getMessage();
    header('Location: ../views/room/add_room.php');
    exit; 
}
