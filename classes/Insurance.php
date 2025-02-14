<?php
require_once(__DIR__ . '/../config/database.php');

class Insurance {
    private $db;
    private $table = 'insurance_policies';

    public function __construct() {
        $this->db = new DatabaseConnection();
    }
    
    public function addPolicy($patientId, $policyData) {
        try {
            $query = "INSERT INTO {$this->table} (patient_id, provider_id, policy_number, coverage_type, start_date, end_date, coverage_limit, status, created_at) VALUES (:patient_id, :provider_id, :policy_number, :coverage_type, :start_date, :end_date, :coverage_limit, :status, NOW())";

            $params = [
                ':patient_id' => $patientId,
                ':provider_id' => $policyData['provider_id'],
                ':policy_number' => $policyData['policy_number'],
                ':coverage_type' => $policyData['coverage_type'],
                ':start_date' => $policyData['start_date'],
                ':end_date' => $policyData['end_date'],
                ':coverage_limit' => $policyData['coverage_limit'],
                ':status' => 'active'
            ];

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error adding insurance policy: " . $e->getMessage());
            throw new Exception("Failed to add insurance policy");
        }
    }

    public function updatePolicy($policyId, $data) {
        try {
            $updateFields = [];
            $params = [':id' => $policyId];

            foreach ($data as $key => $value) {
                if (in_array($key, ['coverage_type', 'end_date', 'coverage_limit', 'status'])) {
                    $updateFields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if (empty($updateFields)) {
                return false;
            }

            $query = "UPDATE {$this->table} SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = :id";

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error updating insurance policy: " . $e->getMessage());
            throw new Exception("Failed to update insurance policy"); 
        }
    }

    public function getPolicy($policyId) {
        try {
            $query = "SELECT p.*, pr.name as provider_name, pt.name as patient_name FROM {$this->table} p JOIN insurance_providers pr ON p.provider_id = pr.id JOIN patients pt ON p.patient_id = pt.id WHERE p.id = :id";

            return $this->db->fetchOne($query, [':id' => $policyId]);
        } catch (Exception $e) {
            error_log("Error fetching policy details: " . $e->getMessage());
            throw new Exception("Failed to fetch policy details"); 
        }
    }

    public function getPatientPolicies($patientId) {
        try {
            $query = "SELECT p.*, pr.name as provider_name FROM {$this->table} p JOIN insurance_providers pr ON p.provider_id = pr.id WHERE p.patient_id = :patient_id ORDER BY p.start_date DESC";

            return $this->db->fetchAll($query, [':patient_id' => $patientId]);
        } catch (Exception $e) {
            error_log("Error fetching patient policies: " . $e->getMessage());
            throw new Exception("Failed to fetch patient insurance policies");
        }
    }

    public function submitClaim($policyId, $billId, $claimData) {
        try {
            $this->db->beginTransaction();

            $query = "INSERT INTO insurance_claims (policy_id, bill_id, claim_amount, diagnosis_code, procedure_code, status, submitted_at) VALUES (:policy_id, :bill_id, :claim_amount, :diagnosis_code, :procedure_code, 'submitted', NOW())";

            $params = [
                ':policy_id' => $policyId,
                ':bill_id' => $billId,
                ':claim_amount' => $claimData['claim_amount'],
                ':diagnosis_code' => $claimData['diagnosis_code'],
                ':procedure_code' => $claimData['procedure_code']
            ];

            $claimId = $this->db->executeQuery($query, $params);

            // Add documents
            if (!empty($claimData['documents'])) {
                $docQuery = "INSERT INTO claim_documents (claim_id, document_type, file_path) VALUES (:claim_id, :document_type, :file_path)";

                foreach ($claimData['documents'] as $doc) {
                    $docParams = [
                        ':claim_id' => $claimId,
                        ':document_type' => $doc['type'],
                        ':file_path' => $doc['path']
                    ];
                    $this->db->executeQuery($docQuery, $docParams);
                }
            }

            $this->db->commit();
            return $claimId;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error submitting insurance claim: " . $e->getMessage());
            throw new Exception("Failed to submit insurance claim"); 
        }
    }

    public function updateClaimStatus($claimId, $status, $response = null) {
        try {
            $query = "UPDATE insurance_claims SET status =:status, response = :response, updated_at = NOW() WHERE id = :id";

            $params = [
                ':id' => $claimId,
                ':status' => $status,
                ':response' => $response
            ];

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error updating claim status: " . $e->getMessage());
            throw new Exception("Failed to update claim status");
        }
    }
    
    public function getClaimStatus($claimId) {
        try {
            $query = "SELECT c.*, p.policy_number, pr.name as provider_name FROM insurance_claims c JOIN insurance_policies p ON c.policy_id = p.id JOIN insurance_providers pr ON p.provider_id = pr.id WHERE c.id = :id";

            return $this->db->fetchOne($query, [':id' => $claimId]);
        } catch (Exception $e) {
            error_log("Error fetching claim status: " . $e->getMessage());
            throw new Exception("Failed to fetch claim status"); 
        }
    }
}