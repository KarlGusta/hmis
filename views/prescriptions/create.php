<?php
require_once '../../config/database.php';
require_once '../../classes/Prescription.php';
require_once '../../classes/Medication.php';

// Create database connection instance
$db = new DatabaseConnection();

// Check if consultation ID is provided
$consultationId = $_GET['consultation_id'] ?? null;
if (!$consultationId) {
    $_SESSION['error'] = "No consultation selected";
    header("Location: consultation_list.php");
    exit();
}

$prescription = new Prescription($db);
$medication = new Medication($db);

// Get consultation details to fetch medical_record_id
$query = "SELECT medical_record_id FROM consultations WHERE id = ?";
$consultation = $db->fetchOne($query, [$consultationId]);

if (!$consultation) {
    $_SESSION['error'] = "Invalid consultation";
    header("Location: consultation_list.php");
    exit();
}

// Get medications for dropdown
$medications = $medication->getAllMedications();

// Now include the layout files after all potential redirects
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Create Prescription</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo path('handlers'); ?>prescription_handler.php" method="POST">
                                <input type="hidden" name="consultation_id" value="<?= htmlspecialchars($consultationId) ?>">
                                <input type="hidden" name="action" value="create">

                                <div id="prescription-items">
                                    <div class="prescription-item mb-3">
                                        <div class="row mb-3">
                                            <div class="col-sm-6">
                                                <label class="form-label required">Medication</label>
                                                <select name="medications[]" class="form-select" required>
                                                    <option value="">Select Medication</option>
                                                    <?php foreach ($medications as $med): ?>
                                                        <option value="<?= $med['id'] ?>">
                                                            <?= htmlspecialchars($med['name']) ?>
                                                            (<?= htmlspecialchars($med['generic_name']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>    
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label required">Frequency</label>
                                                <select name="frequencies[]" class="form-select" required>
                                                    <option value="OD">Once daily (OD)</option>
                                                    <option value="BD">Twice daily (BD)</option>
                                                    <option value="TDS">Three times daily (TDS)</option>
                                                    <option value="QDS">Four times daily (QDS)</option>
                                                    <option value="Q4H">Every 4 hours (Q4H)</option>
                                                    <option value="Q6H">Every 6 hours (Q6H)</option>
                                                    <option value="Q8H">Every 8 hours (Q8H)</option>
                                                    <option value="Q12H">Every 12 hours (Q12H)</option>
                                                    <option value="STAT">Immediately (STAT)</option>
                                                    <option value="PRN">As needed (PRN)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <label class="form-label required">Dosage</label>
                                                <input type="text" name="dosages[]" class="form-control" required placeholder="e.g. 1">
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="form-label required">Duration (Days)</label>
                                                <input type="number" name="durations[]" class="form-control" required placeholder="Days">
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="form-label required">Quantity</label>
                                                <input type="number" name="quantities[]" class="form-control" required placeholder="Total units">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-sm-12">
                                                <label class="form-label">Special Instructions</label>
                                                <input type="text" name="special_instructions[]" class="form-control" placeholder="Optional">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="button" class="btn btn-secondary" id="add-medication">+ Add Medication</button>
                                </div>

                                <input type="hidden" name="medical_record_id" value="<?= htmlspecialchars($consultation['medical_record_id']) ?>">
                                <input type="hidden" name="route" value="oral">

                                <div class="form-footer">
                                    <button type="submit" class="btn button-custom">Save Prescription</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../../includes/main_footer.php'; ?>
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