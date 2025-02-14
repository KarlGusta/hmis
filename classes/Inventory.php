<?php
require_once(__DIR__ . '/../config/database.php');

class Inventory {
    private $db;
    private $table = 'inventory_items';

    public function __construct() {
        $this->db = new Database();
    }

    public function addItem($data) {
        try {
            $query = "INSERT INTO {$this->table} (name, category, quantity, unit, unit_price, reorder_level, supplier_id, expiry_date, created_at) VALUES (:name, :category, :quantity, :unit, :unit_price, :reorder_level, :supplier_id, :expiry_date, NOW())";

            $params = [
                ':name' => $data['name'],
                ':category' => $data['category'],
                ':quantity' => $data['quantity'],
                ':unit' => $data['unit'],
                ':unit_price' => $data['unit_price'],
                ':reorder_level' => $data['reorder_level'],
                ':supplier_id' => $data['supplier_id'],
                ':expiry_date' => $data['expiry_date'] ?? null
            ];

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error adding inventory item: " . $e->getMessage());
            throw new Exception("Failed to add inventory item");
        }
    }

    public function updateStock($itemId, $quantity, $type = 'add') {
        try {
            $query = "UPDATE {$this->table} SET quantity = CASE WHEN :type = 'add' THEN quantity + :quantity ELSE quantity - :quantity END, updated_at = NOW() WHERE id = :id";

            $params = [
                ':id' => $itemId,
                ':quantity' => $quantity,
                ':type' => $type
            ];

            $result = $this->db->executeQuery($query, $params);

            // Log stock movement
            $query = "INSERT INTO stock_movements (item_id, quantity, movement_type, movement_date) VALUES (:item_id, :quantity, :movement_type, NOW())";

            $params = [
                ':item_id' => $itemId,
                ':quantity' => $quantity,
                ':movement_type' => $type
            ];

            $this->db->executeQuery($query, $params);

            return $result;
        } catch (Exception $e) {
            error_log("Error updating stock: " . $e->getMessage());
            throw new Exception("Failed to update stock");
        }
    }

    public function getItem($itemId) {
        try {
            $query = "SELECT i.*, s.name as supplier_name FROM {$this->table} i LEFT JOIN suppliers s ON i.supplier_id = s.id WHERE i.id = :id";

            return $this->db->fetchOne($query, [':id' => $itemId]);
        } catch (Exception $e) {
            error_log("Error fetching inventory item: " . $e->getMessage());
            throw new Exception("Failed to fetch inventory item");
        }
    }

    public function getLowStockItems() {
        try {
            $query = "SELECT i.*, s.name as supplier_name FROM {$this->table} i LEFT JOIN suppliers s ON i.supplier_id = s.id WHERE i.quantity <= i.reorder_level";

            return $this->db->fetchAll($query);
        } catch (Exception $e) {
            error_log("Error fetching low stock items: " . $e->getMessage());
            throw new Exception("Failed to fetch low stock items"); 
        }
    }

    public function getExpiringItems($daysThreshold = 30) {
        try {
            $query = "SELECT i.*, s.name as supplier_name FROM {$this->table} i LEFT JOIN suppliers s ON i.supplier_id = s.id WHERE i.expiry_date IS NOT NULL AND i.expiry_date <= DATE_ADD(CURDATE(), INTERVAL :days DAY) AND i.quantity > 0 ORDER BY i.expiry_date ASC";

            return $this->db->fetchAll($query, [':days' => $daysThreshold]);
        } catch (Exception $e) {
            error_log("Error fetching expiring items: " . $e->getMessage());
            throw new Exception("Failed to fetch expiring items");
        }
    }

    public function createOrder($supplierId, $items) {
        try {
            $this->db->beginTransaction();

            // Create purchase order
            $query = "INSERT INTO purchase_orders (supplier_id, status, created_at) VALUES (:supplier_id, 'pending', NOW())";

            $orderId = $this->db->executeQuery($query, [':supplier_id' => $supplierId]);

            // Add order items
            foreach ($items as $item) {
                $query = "INSERT INTO purchase_order_items (order_id, item_id, quantity, unit_price) VALUES (:order_id, :item_id, :quantity, :unit_price)";

                $params = [
                    ':order_id' => $orderId,
                    ':item_id' => $item['id'],
                    ':quantity' => $item['quantity'],
                    ':unit_price' => $item['unit_price']
                ];

                $this->db->executeQuery($query, $params);
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error creating purchase order: " . $e->getMessage());
            throw new Exception("Failed to create purchase order");
        }
    }
}