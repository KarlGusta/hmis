<?php
$pageTitle = "Pending Prescriptions";
require_once '../../config/database.php';
require_once '../../classes/Pharmacy.php';

$db = new DatabaseConnection();
$pharmacy = new Pharmacy($db);

try {
    $pendingPrescriptions = $pharmacy->getPendingPrescriptions();
} catch (Exception $e) {
    $_SESSION['error'] = "Error loading prescriptions: " . $e->getMessage();
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            
        </div>
    </div>
</div>