<?php
require_once __DIR__ . '/../config/database.php';

class ItemCategory {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    } 

    // Get all active item categories
    public function getAllCategories() {
        try {
            $stmt = $this->db->conn->prepare(
                "SELECT id, name, description, status
                FROM item_categories
                WHERE status = 'active'
                ORDER BY name ASC"
            );

            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching categories: " . $e->getMessage());
        }
    }

    // Get a specific category by ID
    public function getCategory($id) {
        try {
            $stmt = $this->db->conn->prepare(
                "SELECT id, name, description, status
                FROM item_categories
                WHERE id = ?"
            );

            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (Exception $e) {
            throw new Exception("Error fetching category: " . $e->getMessage());
        }
    }

    // Add a new category
    public function addCategory($data) {
        try {
            $stmt = $this->db->conn->prepare(
                "INSERT INTO item_categories (name, description)
                VALUES (?, ?)"
            );

            $stmt->bind_param("ss", $data['name'], $data['description']);
            $stmt->execute();
            return $this->db->conn->insert_id; 
        } catch (Exception $e) {
            throw new Exception("Error adding category: " . $e->getMessage());
        }
    }

    // Update an existing category
    public function updateCategory($id, $data) {
        try {
            $stmt = $this->db->conn->prepare(
                "UPDATE item_categories
                SET name = ?, description = ?, status = ?
                WHERE id = ?"
            );

            $stmt->bind_param("sssi",
                $data['name'],
                $data['description'],
                $data['status'],
                $id
        );
          
            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Error updating category: " . $e->getMessage());
        }
    }

    // Delete a category (soft delete by setting status to inactive)
    public function deleteCategory($id) {
        try {
            $stmt = $this->db->conn->prepare(
                "UPDATE item_categories
                SET status = 'inactive'
                WHERE id = ?"
            );

            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Error deleting category: " . $e->getMessage()); 
        }
    }
}