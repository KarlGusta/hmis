<?php
require_once '../../classes/Prescription.php';
require_once '../../config/database.php';

$db = new DatabaseConnection();
$prescription = new Prescription($db);

// Get filter parameters
$medicalRecordId = $_GET['medical_record_id'] ?? null;

try {
    // Get prescriptions based on medical record
    if ($medicalRecordId) {
        $prescriptions = $prescription->getPrescriptionsByMedicalRecord($medicalRecordId);        
    } else {
        // If no medical record specified, show an error or redirect
        $_SESSION['error'] = "No medical record specified";
        header('Location: ../medical-records/list.php');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error loading prescriptions: " . $e->getMessage();
    $prescriptions = [];
}

$pageTitle = "Prescriptions List";

// Move includes after all potential redirects
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <!-- Prescriptions List -->
                    <div class="card card-custom">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Prescriptions</h3>
                            <a href="create.php?medical_record_id=<?= htmlspecialchars($medicalRecordId) ?>"
                               class="btn button-custom">
                               <i class="fas fa-plus"></i> Add Prescription
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Medication</th>
                                            <th>Dosage</th>
                                            <th>Frequency</th>
                                            <th>Duration</th>
                                            <th>Category</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($prescriptions)) : ?>
                                            <?php foreach ($prescriptions as $rx) : ?>
                                                <tr>
                                                    <td>
                                                        <?= htmlspecialchars($rx['medication_name']) ?>
                                                        <?= $rx['generic_name'] ? '(' . htmlspecialchars($rx['generic_name']) . ')' : ''?>
                                                    </td>
                                                    <td><?= htmlspecialchars($rx['dosage']) ?></td>
                                                    <td><?= htmlspecialchars($rx['frequency']) ?></td>
                                                    <td><?= htmlspecialchars($rx['duration']) ?></td>
                                                    <td><?= htmlspecialchars($rx['medication_category'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="detail.php?id=<?= $rx['id'] ?>"
                                                               class="btn btn-sm button-custom-white-sm">View</a>
                                                            <a href="edit.php?id=<?= $rx['id'] ?>"
                                                               class="btn btn-sm button-custom-white-sm">Edit</a>
                                                            <button class="btn btn-sm btn-danger delete-prescription"
                                                                    data-id="<?= $rx['id'] ?>">Delete</button>      
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No prescriptions found</td>
                                            </tr>        
                                        <?php endif; ?>    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <?php include '../../includes/main_footer.php'; ?>
</div>

<?php include '../../includes/footer_scripts.php'; ?>