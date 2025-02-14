<?php
require_once '../config/database.php';
require_once '../classes/Patient.php';

try {
    $db = new DatabaseConnection();
    $patient = new Patient($db);

    // Get search term
    $searchTerm = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);

    // Perform search
    $patients = $patient->searchPatient($searchTerm);

    // Include the view file with the results
    include '../views/patients/search.php';
} catch (Exception $e) {
    $_SESSION['error'] = "Search failed: " . $e->getMessage();
    header('Location: ' . path('views', 'patients') . 'search.php');
    exit;
}
?>