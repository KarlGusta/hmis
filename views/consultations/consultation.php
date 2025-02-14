<?php
$pageTitle = "Patient Consultation";

require_once '../../config/database.php';
require_once '../../classes/Consultation.php';

$db = new DatabaseConnection();
$consultation = new Consultation($db);

$consultationId = $_GET['id'] ?? null;

try {
    $consultationData = $consultation->getConsultation($consultationId);
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

<?php include '../../includes/footer_scripts.php'; ?>

<?php
function calculateAge($dob) {
    return date_diff(date_create($dob), date_create('today'))->y;
}
?>