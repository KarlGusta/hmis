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
            <?php include '../../includes/alerts.php'; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pending Prescriptions</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Medication</th>
                                    <th>Dosage</th>
                                    <th>Prescribed By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>