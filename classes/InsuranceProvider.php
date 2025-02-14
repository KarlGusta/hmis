<?php
class InsuranceProvider {
    private $db;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    public function register($data) {
        try {
            $this->db->conn->begin_transaction();

            // Generate provider ID
            $provider_id = $this->generateProviderId();

            $stmt = $this->db->conn->prepare(
                "INSERT INTO insurance_providers (provider_id, provider_name, contact_number, email, website, address, coverage_types, policy_details, status) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $status = 'active';
            $stmt->bind_param(
                "sssssssss",
                $provider_id,
                $data['provider_name'],
                $data['contact_number'],
                $data['email'],
                $data['website'],
                $data['address'],
                $data['coverage_types'],
                $data['policy_details'],
                $status                
            );

            $stmt->execute();
            $this->db->conn->commit();

            return $provider_id;
        } catch (Exception $e) {
            $this->db->conn->rollback();
            throw $e;
        }
    }

    private function generateProviderId() {
        $prefix = 'INS';
        $timestamp = date('YmdHis');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . $timestamp . $random;
    }

    public function getAllProviders() {
        $stmt = $this->db->conn->prepare(
            "SELECT provider_id, provider_name, contact_number, email, status FROM insurance_providers ORDER BY created_at DESC"
        );

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function toggleStatus($provider_id) {
        try {
            $this->db->conn->begin_transaction();

            $stmt = $this->db->conn->prepare("UPDATE insurance_providers SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE provider_id = ?");
            $stmt->bind_param("s", $provider_id);
            $stmt->execute();

            $this->db->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->db->conn->rollback();
            throw $e;
        }
    }

    public function getActiveProviders() {
        $stmt = $this->db->conn->prepare("SELECT id, provider_name FROM insurance_providers WHERE status = 'active' ORDER BY provider_name ASC");

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}