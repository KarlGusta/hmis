<?php
require_once __DIR__ . '/../config/database.php';

class Item {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addItem($data) {
        try {
            $this->db->conn->begin_transaction();

            $stmt = $this->db->conn->prepare(
                "INSERT INTO items (item_code, name, description, category_id, unit, current_price,
                reorder_level, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssissis",
                $data['item_code'],
                $data['name'],
                $data['description'],
                $data['category_id'],
                $data['unit'],
                $data['current_price'],
                $data['reorder_level'],
                $data['status']
            ); 

            $stmt->execute();
            $item_id = $this->db->conn->insert_id;

            // Log the initial price in price history
            $this->logPriceChange($item_id, 0, $data['current_price'], $data['user_id'], 'Initial price set');

            $this->db->conn->commit();
            return $item_id;

        } catch (Exception $e) {
            $this->db->conn->rollback();
            throw $e;
        }
    }

    public function updatePrice($item_id, $new_price, $user_id, $notes = '') {
        try {
            $this->db->conn->begin_transaction();

            // Get current price
            $stmt = $this->db->conn->prepare("SELECT current_price FROM items WHERE id = ?");
            $stmt->bind_param("i", $item_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_price = $result->fetch_assoc()['current_price'];

            // Update item price
            $stmt = $this->db->conn->prepare("UPDATE items SET current_price = ? WHERE id = ?");
            $stmt->bind_param("di", $new_price, $item_id);
            $stmt->execute();

            // Log price change
            $this->logPriceChange($item_id, $current_price, $new_price, $user_id, $notes);

            $this->db->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->db->conn->rollback();
            throw $e;
        } 
    }

    private function logPriceChange($item_id, $old_price, $new_price, $user_id, $notes) {
        $stmt = $this->db->conn->prepare(
            "INSERT INTO price_history (item_id, old_price, new_price, changed_by, notes)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("iddis", $item_id, $old_price, $new_price, $user_id, $notes);
        return $stmt->execute();
    }

    public function getItem($id) {
        $stmt = $this->db->conn->prepare(
            "SELECT i.*, c.name as category_name
            FROM items i
            LEFT JOIN item_categories c ON i.category_id = c.id
            WHERE i.id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getPriceHistory($item_id) {
        $stmt = $this->db->conn->prepare(
            "SELECT ph.*, u.username as changed_by_user
            FROM price_history ph
            LEFT JOIN users u ON ph.changed_by = u.id
            WHERE ph.item_id = ?
            ORDER BY ph.change_date DESC"
        );

        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC); 
    }

    public function getAllItems($category_id = null, $status = 'active') {
        $sql = "SELECT i.*, c.name as category_name
               FROM items i
               LEFT JOIN item_categories c ON i.category_id = c.id
               WHERE i.status = ?";

        if ($category_id) {
            $sql .= " AND i.category_id = ?";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bind_param("si", $status, $category_id);
        } else {
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bind_param("s", $status);
        }       

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateItem($data) {
        try {
            $stmt = $this->db->conn->prepare(
                "UPDATE items SET 
                    item_code = ?,
                    name = ?,
                    description = ?,
                    category_id = ?,
                    unit = ?,
                    current_price = ?,
                    reorder_level = ?,
                    status = ?
                WHERE id = ?"
            );

            $stmt->bind_param(
                "sssissisi",
                $data['item_code'],
                $data['name'],
                $data['description'],
                $data['category_id'],
                $data['unit'],
                $data['current_price'],
                $data['reorder_level'],
                $data['status'],
                $data['id']
            );

            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
}