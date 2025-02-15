<?php
$pageTitle = "Patient Consultation";

require_once '../../config/database.php';
require_once '../../classes/Consultation.php';
require_once '../../classes/Prescription.php';

$db = new DatabaseConnection();
$consultation = new Consultation($db);
$prescription = new Prescription($db);

$consultationId = $_GET['id'] ?? null;

try {
    $consultationData = $consultation->getConsultation($consultationId);
    $medicalRecordId = $consultationData['medical_record_id'];
} catch (Exception $e) {
    $_SESSION['error'] = "Error loading consultation: " . $e->getMessage();
    header('Location: ../queue/waiting_room.php');
    exit;
}

// Only include header files after all potential redirects
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <!-- Add Error/Success Messages -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div> 
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>    
            <div class="row row-cards">
                <div class="col-md-12">
                    <form action="../../handlers/update_consultation.php" method="POST" id="consultationForm">
                        <input type="hidden" name="consultation_id" value="<?= htmlspecialchars($consultationId) ?>">

                        <!-- Patient Information Card -->
                        <div class="card card-custom mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Patient Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Patient:</strong> <?= htmlspecialchars($consultationData['patient_name']) ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Age:</strong> <?= calculateAge($consultationData['date_of_birth']) ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Gender:</strong> <?= htmlspecialchars($consultationData['gender']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-custom mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Room Information</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                // Get room information for this consultation
                                $query = "SELECT r.*
                                         FROM rooms r
                                         JOIN room_history rh ON r.id = rh.room_id
                                         WHERE rh.consultation_id = ?
                                         AND rh.end_time IS NULL";
                                $roomInfo = $db->fetchOne($query, [$consultationId]);         
                                ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Room Number: </strong><?= htmlspecialchars($roomInfo['room_number'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Room Type: </strong><?= ucfirst(htmlspecialchars($roomInfo['room_type'] ?? 'N/A')) ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Features: </strong><?= htmlspecialchars($roomInfo['features'] ?? 'None') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Consultation Details Card -->
                        <div class="card card-custom mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Consultation Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Chief Complaint</label>
                                    <textarea class="form-control" name="chief_complaint" rows="2" readonly><?= htmlspecialchars($consultationData['chief_complaint']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">History of Present Illness</label>
                                    <textarea class="form-control" name="history_of_illness" rows="3"><?= htmlspecialchars($consultationData['history_of_illness'] ?? '') ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Diagnosis</label>
                                    <textarea class="form-control" name="diagnosis" rows="2" required><?= htmlspecialchars($consultationData['diagnosis'] ?? '') ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Treatment Plan</label>
                                    <textarea class="form-control" name="treatment_plan" rows="3" required><?= htmlspecialchars($consultationData['treatment_plan'] ?? '') ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Prescription</label>
                                    <textarea class="form-control" name="prescription" rows="3"><?= htmlspecialchars($consultationData['prescription'] ?? '') ?></textarea>
                                </div>
                                <div class="card card-custom mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Prescriptions</h3>
                                        <div class="card-actions">
                                            <button type="button" class="btn button-custom" data-bs-toggle="modal" data-bs-target="#addPrescriptionModal">
                                                Add Prescription
                                            </button>
                                        </div>
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
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $prescriptions = [];
                                                    if ($medicalRecordId) {
                                                        $prescriptions = $prescription->getMedicalRecordPrescriptions($medicalRecordId);
                                                    }
                                                    foreach ($prescriptions as $rx): ?>
                                                       <tr>
                                                           <td><?= htmlspecialchars($rx['medication_name']) ?> <?= htmlspecialchars($rx['strength']) ?> <?= htmlspecialchars($rx['form']) ?></td>
                                                           <td><?= htmlspecialchars($rx['dosage']) ?></td>
                                                           <td><?= htmlspecialchars($rx['frequency']) ?></td>
                                                           <td><?= htmlspecialchars($rx['duration']) ?></td>
                                                           <td><?= htmlspecialchars($rx['instructions']) ?></td>
                                                           <td>
                                                               <button type="button" 
                                                                       class="btn btn-sm button-custom-white-sm"
                                                                       onclick="editPrescription(<?= htmlspecialchars(json_encode($rx)) ?>)">
                                                                   Edit     
                                                               </button>
                                                               <button type="button" 
                                                                       class="btn btn-sm button-custom-white-sm"
                                                                       onclick="deletePrescription(<?= $rx['id'] ?>)">
                                                                   Delete 
                                                               </button>
                                                           </td>
                                                       </tr>
                                                    <?php endforeach; ?>   
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Lab Requests</label>
                                    <textarea class="form-control" name="lab_requests" rows="2"><?= htmlspecialchars($consultationData['lab_requests'] ?? '') ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Follow-up Date</label>
                                            <input type="date" class="form-control" name="follow_up_date" value="<?= htmlspecialchars($consultationData['follow_up_date'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Additional Notes</label>
                                    <textarea class="form-control" name="consultation_notes" rows="3"><?= htmlspecialchars($consultationData['consultation_notes'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div> 

                        <!-- Action Buttons -->
                        <div class="form-footer mt-4">
                            <button type="submit" name="action" value="save" class="btn button-custom me-2">
                                Save Changes
                            </button>
                            <button type="submit" name="action" value="complete" class="btn button-custom-success"
                                    onclick="return confirm('Are you sure you want to complete this consultation? This will also release the room.')">
                                Complete Consultation
                            </button>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include '../../includes/main_footer.php'; ?>
</div>

<!-- Move Prescription Modal outside the consultation form -->
<div class="modal fade" id="addPrescriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../handlers/prescription_handler.php" method="POST">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="medical_record_id" value="<?= $medicalRecordId ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Add Prescription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Medication</label>
                        <select name="medication_id" class="form-select" required>
                            <option value="">Select Medication</option>
                            <?php foreach ($prescription->getAllMedications() as $med): ?>
                                <option value="<?= $med['id'] ?>">
                                    <?= htmlspecialchars($med['name']) ?> <?= htmlspecialchars($med['strength']) ?> <?= htmlspecialchars($med['form']) ?>
                                </option>
                            <?php endforeach; ?>    
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosage</label>
                        <input type="text" name="dosage" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frequency</label>
                        <input type="text" name="frequency" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instructions</label>
                        <textarea name="instructions" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Prescription</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer_scripts.php'; ?>

<?php
function calculateAge($dob) {
    return date_diff(date_create($dob), date_create('today'))->y;
}
?>

<script>
function editPrescription(prescription) {
    // Populate and show the edit modal
    const modal = document.querySelector('#addPrescriptionModal');
    const form = modal.querySelector('form');

    form.action.value = 'update';
    form.medication_id.value = prescription.medication_id;
    form.dosage.value = prescription.dosage;
    form.frequency.value = prescription.frequency;
    form.duration.value = prescription.duration;
    form.instructions.value = prescription.instructions;

    // Add prescription_id field
    let prescriptionIdInput = form.querySelector('input[name="prescription_id"]');
    if (!prescriptionIdInput) {
        prescriptionIdInput = document.createElement('input');
        prescriptionIdInput.type = 'hidden';
        prescriptionIdInput.name = 'prescription_id';
        form.appendChild(prescriptionIdInput);
    }
    prescriptionIdInput.value = prescription.id;

    // Show the modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}    

function deletePrescription(prescriptionId) {
    if (confirm('Are you sure you want to delete this prescription?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../../handlers/prescription_handler.php';

        const fields = {
            'action': 'delete',
            'prescription_id': prescriptionId,
            'medical_record_id': <?= $medicalRecordId ?>
        };

        for (const [name, value] of Object.entries(fields)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const prescriptionForm = document.querySelector('#addPrescriptionModal form');

    prescriptionForm.addEventListener('submit', function(e) {
        const medication = prescriptionForm.querySelector('[name="medication_id"]').value;
        const dosage = prescriptionForm.querySelector('[name="dosage"]').value;
        const frequency = prescriptionForm.querySelector('[name="frequency"]').value;
        const duration = prescriptionForm.querySelector('[name="duration"]').value;

        if (!medication || !dosage || !frequency || !duration) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }
    });

    // Reset form when modal is closed
    const prescriptionModal = document.getElementById('addPrescriptionModal');
    prescriptionModal.addEventListener('hidden.bs.modal', function() {
        prescriptionForm.reset();
        prescriptionForm.action.value = 'create';
        const prescriptionIdInput = prescriptionForm.querySelector('input[name="prescription_id"]');
        if (prescriptionIdInput) {
            prescriptionIdInput.remove();
        } 
    });
});
</script>