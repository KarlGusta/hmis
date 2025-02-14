<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    if (!isset($_GET['term'])) {
        throw new Exception('Search term is required');
    }

    $db = new DatabaseConnection();
    $term = $db->conn->real_escape_string($_GET['term']);
    
    // Debug: Log the search term
    error_log("Search term: " . $term);
    
    $query = "SELECT 
                id,
                patient_id,
                first_name,
                last_name,
                phone
              FROM patients 
              WHERE CONCAT(first_name, ' ', last_name) LIKE '%$term%' 
              OR patient_id LIKE '%$term%' 
              OR phone LIKE '%$term%' 
              LIMIT 1";
    
    // Debug: Log the query
    error_log("Query: " . $query);
    
    $result = $db->conn->query($query);
    
    if (!$result) {
        // Debug: Log MySQL error if query fails
        error_log("MySQL Error: " . $db->conn->error);
        throw new Exception("Database query failed: " . $db->conn->error);
    }
    
    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        // Debug: Log found patient data
        error_log("Found patient: " . json_encode($patient));
        
        $response = [
            'success' => true,
            'patient' => [
                'id' => $patient['id'],
                'first_name' => $patient['first_name'],
                'last_name' => $patient['last_name'],
                'patient_id' => $patient['patient_id'],
                'phone' => $patient['phone']
            ]
        ];
        
        // Debug: Log response
        error_log("Sending response: " . json_encode($response));
        echo json_encode($response);
    } else {
        // Debug: Log no results found
        error_log("No patient found for term: " . $term);
        echo json_encode([
            'success' => false,
            'message' => 'Patient not found'
        ]);
    }

} catch (Exception $e) {
    error_log("Error in outpatient search: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}