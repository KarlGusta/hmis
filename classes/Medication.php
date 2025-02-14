<?php

class Medication {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addMedication($data) {
        try {
            $stmt = $this->db->conn->prepare(
                "INSERT INTO medications (
                    name, generic_name, category, unit,
                    unit_price, stock_quantity, reorder_level
                ) VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssdii",
                $data['name'],
                $data['generic_name'],
                $data['category'],
                $data['unit'],
                $data['unit_price'],
                $data['stock_quantity'],
                $data['reorder_level']
            );

            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Failed to add medication: " . $e->getMessage());
        }
    }

    public function updateMedication($id, $data) {
        try {
            $stmt = $this->db->conn->prepare(
                "UPDATE medications SET
                    name = ?, generic_name = ?, category = ?,
                    unit = ?, unit_price = ?, stock_quantity = ?,
                    reorder_level = ?, status = ?
                 WHERE id = ?"
            );
            
            $stmt->bind_param(
                "ssssdisi",
                $data['name'],
                $data['generic_name'],
                $data['category'],
                $data['unit'],
                $data['unit_price'],
                $data['stock_quantity'],
                $data['reorder_level'],
                $data['status'],
                $id
            );

            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Failed to update medication: " . $e->getMessage());
        }
    }

    public function getMedication($id) {
        $stmt = $this->db->conn->prepare(
            "SELECT * FROM medications WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAllMedications($status = null) {
        $sql = "SELECT * FROM medications";
        if ($status) {
            $sql .= " WHERE status = ?";
        }
        $sql .= " ORDER BY name ASC";

        $stmt = $this->db->conn->prepare($sql);
        if ($status) {
            $stmt->bind_param("s", $status);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStock($id, $quantity, $type = 'add') {
        try {
            $this->db->conn->begin_transaction();

            // Get current stock
            $stmt = $this->db->conn->prepare(
                "SELECT stock_quantity FROM medications WHERE id = ? FOR UPDATE"
            );
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            // Calculate new stock
            $newQuantity = $type === 'add'
                ? $result['stock_quantity'] + $quantity
                : $result['stock_quantity'] - $quantity;

            if ($newQuantity < 0) {
                throw new Exception("Insufficient stock");
            }    

            // Update stock
            $stmt = $this->db->conn->prepare(
                    "UPDATE medications SET stock_quantity = ? WHERE id = ?"
                );
                $stmt->bind_param("ii", $newQuantity, $id);
                $stmt->execute();
                
                $this->db->conn->commit();
                return true;
            } catch (Exception $e) {
                $this->db->conn->rollback();
                throw new Exception("Failed to update stock: " . $e->getMessage());
            }
        }

        public function getLowStockMedications() {
            $stmt = $this->db->conn->prepare(
                "SELECT * FROM medications
                WHERE stock_quantity <= reorder_level
                AND status = 'active'"
            );
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }   
}