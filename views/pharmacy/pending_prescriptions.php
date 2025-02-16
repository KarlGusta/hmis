<?php
$pageTitle = "Pending Prescriptions";
require_once '../../config/database.php';
require_once '../../classes/Pharmacy.php';

$db = new DatabaseConnection();
$pharmacy = new Pharmacy($db);

$pendingPrescriptions = []; // Initialize as empty array
try {
    $pendingPrescriptions = $pharmacy->getPendingPrescriptions();
} catch (Exception $e) {
    $_SESSION['error'] = "Error loading prescriptions: " . $e->getMessage();
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <?php include '../../includes/alerts.php'; ?>
            
            <div class="card card-custom">
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
                            <tbody>
                                <?php if (empty($pendingPrescriptions)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No pending prescriptions available.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pendingPrescriptions as $prescription): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($prescription['first_name'] . ' ' . $prescription['last_name']) ?></td>
                                            <td><?= htmlspecialchars($prescription['medication_name']) ?> <?= htmlspecialchars($prescription['strength']) ?></td>
                                            <td><?= htmlspecialchars($prescription['dosage']) ?></td>
                                            <td><?= htmlspecialchars($prescription['prescribed_by_name']) ?></td>
                                            <td><?= date('M d, Y', strtotime($prescription['created_at'])) ?></td>
                                            <td>
                                                <button class="btn btn-primary btn-sm"
                                                        onclick="showDispensingModal(<?= $prescription['id'] ?>)">
                                                    Dispense    
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dispensing Modal -->
<div class="modal fade" id="dispensingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../handlers/pharmacy_handler.php" method="POST">
                <input type="hidden" name="action" value="dispense">
                <input type="hidden" name="prescription_id" id="prescription_id">

                <div class="modal-header">
                    <h5 class="modal-title">Dispense Medication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Quantity to Dispense</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Dispense</button>
                </div>
            </form>
        </div>
    </div>
</div> 

<script>
function showDispensingModal(prescriptionId) {
    document.getElementById('prescription_id').value = prescriptionId;
    const modal = new bootstrap.Modal(document.getElementById('dispensingModal'));
    modal.show();
}    
</script>
<?php include '../../includes/footer_scripts.php'; ?>