<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

require_once '../../classes/Appointment.php';
require_once '../../config/database.php';

$db = new DatabaseConnection();
$appointment = new Appointment($db);

function getVitalSignColor($status) {
    return $status === 'high' ? 'text-danger' : 'text-success';
}

// Get appointment ID from URL
$appointmentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$appointmentId) {
    $_SESSION['error'] = "Invalid appointment ID";
    header('Location: list.php');
    exit;
}

// Get appointment details
$appointmentDetails = $appointment->getAppointment($appointmentId);

if (!$appointmentDetails) {
    $_SESSION['error'] = "Appointment not found";
    header('Location: list.php');
    exit;
}
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Patient Check-In</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h4>Appointment Details</h4>
                                    <p><strong>Patient:</strong> <?= htmlspecialchars($appointmentDetails['patient_name']) ?></p>
                                    <p><strong>Doctor:</strong> <?= htmlspecialchars($appointmentDetails['doctor_name']) ?></p>
                                    <p><strong>Date & Time:</strong> <?= htmlspecialchars(date('M d, Y h:i A', strtotime($appointmentDetails['appointment_datetime']))) ?></p>
                                    <p><strong>Reason:</strong> <?= htmlspecialchars($appointmentDetails['reason']) ?></p>
                                </div>
                            </div>

                            <form action="../../handlers/process_check_in.php" method="POST">
                                <input type="hidden" name="appointment_id" value="<?= $appointmentId ?>">
                                <input type="hidden" name="patient_id" value="<?= htmlspecialchars($appointmentDetails['patient_id']) ?>">
                                <input type="hidden" name="department_id" value="<?= htmlspecialchars($appointmentDetails['department_id']) ?>">
                                
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h4>Triage Assessment</h4>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Blood Pressure
                                            <span id="blood_pressure_status" class="ms-2 vital-status"></span>
                                        </label>
                                        <input type="text" class="form-control" name="vital_signs[blood_pressure]"
                                               placeholder="e.g., 120/80" required
                                               oninput="updateVitalSignStatus('blood_pressure', this.value)">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Temperature (°C)
                                            <span id="temperature_status" class="ms-2 vital-status"></span>
                                        </label>
                                        <input type="number" step="0.1" class="form-control"
                                               name="vital_signs[temperature]" required
                                               oninput="updateVitalSignStatus('temperature', this.value)">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Heart Rate (bpm)
                                            <span id="heart_rate_status" class="ms-2 vital-status"></span>
                                        </label>
                                        <input type="number" class="form-control"
                                               name="vital_signs[heart_rate]" required
                                               oninput="updateVitalSignStatus('heart_rate', this.value)">
                                    </div>
                                </div>

                                <!-- Triage Priority -->
                                 <div class="mb-3">
                                    <label class="form-label">Priority Level</label>
                                    <select class="form-select" name="priority" required>
                                        <option value="normal">Normal</option>
                                        <option value="urgent">Urgent</option>
                                        <option value="emergency">Emergency</option>
                                    </select>
                                 </div>

                                <div class="mb-3">
                                    <label class="form-label">Current Symptoms</label>
                                    <textarea class="form-control" name="symptoms" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Additional Notes</label>
                                    <textarea class="form-control" name="notes" rows="3"></textarea>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="btn button-custom">Complete Check-In & Add to Queue</button>
                                    <a href="list.php" class="btn button-custom-white ms-2">Cancel</a>
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

<?php include '../../includes/footer_scripts.php'; ?> 