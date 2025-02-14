<?php
class ActivityLogger {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function logActivity($data) {
        try {
            $stmt = $this->db->conn->prepare(
                "INSERT INTO user_activities (
                    user_id, activity_type, activity_description,
                    ip_address, user_agent, entity_type,
                    entity_id, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssssss",
                $data['user_id'],
                $data['activity_type'],
                $data['activity_description'],
                $data['ip_address'],
                $data['user_agent'],
                $data['entity_type'],
                $data['entity_id'],
                $data['status']
            );

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Failed to log activity: " . $e->getMessage());
            return false;
        }
    }

    public function getClientIP() {
        $ipAddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        return $ipAddress;
    }

    public function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}