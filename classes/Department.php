<?php
class Department {
    private $db;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    public function register($data) {
        try {
            $this->db->conn->begin_transaction();

            // Generate department code if not provided
            if (empty($data['code'])) {
                $data['code'] = $this->generateDepartmentCode($data['name']);
            }

            $stmt = $this->db->conn->prepare(
                "INSERT INTO departments (id, code, name, description, status) 
                 VALUES (UUID(), ?, ?, ?, ?)"
            );

            $status = 'active';
            $stmt->bind_param(
                "ssss",
                $data['code'],
                $data['name'],
                $data['description'],
                $status
            );

            $stmt->execute();
            $this->db->conn->commit();

            return $this->db->conn->insert_id;
        } catch (Exception $e) {
            $this->db->conn->rollback();
            throw $e;
        }
    }

    private function generateDepartmentCode($name) {
        // Generate a code based on the first 3 letters of the department name
        $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        
        // Add a random number to ensure uniqueness
        $code .= str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        
        return $code;
    }

    public function getAllDepartments() {
        $stmt = $this->db->conn->prepare(
            "SELECT id, code, name, description, status 
             FROM departments 
             ORDER BY name ASC"
        );

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getActiveDepartments() {
        $stmt = $this->db->conn->prepare(
            "SELECT id, code, name 
             FROM departments 
             WHERE status = 'active' 
             ORDER BY name ASC"
        );

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function toggleStatus($id) {
        try {
            $this->db->conn->begin_transaction();

            $stmt = $this->db->conn->prepare(
                "UPDATE departments 
                 SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END 
                 WHERE id = ?"
            );
            
            $stmt->bind_param("s", $id);
            $stmt->execute();

            $this->db->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->db->conn->rollback();
            throw $e;
        }
    }
}