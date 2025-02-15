<?php
$pageTitle = "Prescriptions List - HMIS";
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Prescriptions List</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Medication</th>
                                            <th>Dosage</th>
                                            <th>Frequency</th>
                                            <th>Duration</th>
                                            <th>Instructions</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once '../../config/database.php';
                                        require_once '../../classes/Prescription.php';

                                        $db = new DatabaseConnection();
                                        $prescription = new Prescription($db);
                                        
                                        // Get medical record ID from URL parameter
                                        $medicalRecordId = isset($_GET['medical_record_id']) ? $_GET['medical_record_id'] : null;
                                        
                                        if ($medicalRecordId) {
                                            $prescriptions = $prescription->getMedicalRecordPrescriptions($medicalRecordId);
                                            
                                            foreach ($prescriptions as $rx) {
                                                $statusClass = $rx['status'] === 'active' 
                                                    ? 'button-custom-black-sm' 
                                                    : 'button-custom-sm';
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?= htmlspecialchars($rx['medication_name']) ?>
                                                        <div class="text-muted">
                                                            <?= htmlspecialchars($rx['form']) ?> - <?= htmlspecialchars($rx['strength']) ?>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($rx['dosage']) ?></td>
                                                    <td><?= htmlspecialchars($rx['frequency']) ?></td>
                                                    <td><?= htmlspecialchars($rx['duration']) ?></td>
                                                    <td><?= htmlspecialchars($rx['instructions']) ?></td>
                                                    <td>
                                                        <span class="badge <?= $statusClass ?>">
                                                            <?= ucfirst($rx['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="edit.php?id=<?= $rx['id'] ?>" 
                                                           class="btn btn-sm button-custom-white-sm">
                                                            Edit
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-sm button-custom-white-sm"
                                                                onclick="deletePrescription(<?= $rx['id'] ?>)">
                                                            Cancel
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="7" class="text-center">No medical record ID provided</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deletePrescription(id) {
    if (confirm('Are you sure you want to cancel this prescription?')) {
        fetch('../../handlers/prescription_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error cancelling prescription: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while cancelling the prescription');
        });
    }
}
</script>

<?php include '../../includes/footer_scripts.php'; ?>
