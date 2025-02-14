<?php
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';
require_once '../../classes/Prescription.php';
require_once '../../classes/Medication.php';

$prescriptionId = $_GET['id'] ?? null;
if (!$prescriptionId) {
    $_SESSION['error'] = "No prescription selected";
    header("Location: prescriptions_list.php");
    exit();
}

$prescription = new Prescription($db);
$medication = new Medication($db);

// Fetch prescription details
$prescriptionDetails = $prescription->getPrescriptionDetails($prescriptionId);
$medications = $medication->getAllMedications();
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Prescription</h3>
                </div>
                <div class="card-body">
                    <form action="prescription_handler.php" method="POST">
                        <input type="hidden" name="prescription_id" value="<?= htmlspecialchars($prescriptionId) ?>">
                        <input type="hidden" name="action" value="edit">

                        <div id="prescription-items">
                            <?php foreach ($prescriptionDetails as $item): ?>
                            <div class="prescription-item mb-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Medication</label>
                                        <select name="medications[]" class="form-select" required>
                                            <?php foreach ($medications as $med): ?>
                                                <option value="<?= $med['id'] ?>"
                                                    <?= $med['id'] == $item['medication_id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($med['name']) ?>
                                                    (<?= htmlspecialchars($med['generic_name']) ?>)
                                                </option>
                                            <?php endforeach; ?>    
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Dosage</label>
                                        <input type="text" name="dosages[]" class="form-control"
                                               value="<?= htmlspecialchars($item['dosage']) ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Frequency</label>
                                        <input type="text" name="frequencies[]" class="form-control"
                                               value="<?= htmlspecialchars($item['frequency']) ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Duration</label>
                                        <input type="text" name="durations[]" class="form-control"
                                               value="<?= htmlspecialchars($item['duration']) ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Notes</label>
                                        <input type="text" name="notes[]" class="form-control"
                                               value="<?= htmlspecialchars($item['notes'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary" id="add-medication">+ Add Medication</button>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn button-custom">Update Prescription</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('add-medication').addEventListener('click', function() {
        const container = document.getElementById('prescription-items');
        const template = container.querySelector('.prescription-item');
        const newItem = template.cloneNode(true);

        // Clear input values in the new item
        newItem.querySelectorAll('input, select').forEach(input => {
            input.value = '';
        });

        container.appendChild(newItem);
    });
</script>

<?php include '../../includes/footer_scripts.php'; ?>