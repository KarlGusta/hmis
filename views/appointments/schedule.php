<?php
$pageTitle = "Appointment Schedule";

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

require_once '../../classes/Patient.php';
require_once '../../classes/Doctor.php';
require_once '../../classes/Department.php';
require_once '../../config/database.php';

$db = new DatabaseConnection();
$patient = new Patient($db);
$doctor = new Doctor($db);
$department = new Department($db);

$patients = $patient->getAllPatients();
$doctors = $doctor->getAllDoctors();
$departments = $department->getAllDepartments();
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Schedule New Appointment</h3>
                        </div>
                        <div class="card-body">
                            <form action="../../handlers/create_appointment.php" method="POST">
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label required">Patient</label>
                                        <select class="form-select" name="patient_id" required>
                                            <option value="">Select Patient</option>
                                            <?php foreach($patients as $p): ?>
                                            <option value="<?= htmlspecialchars($p['id']) ?>">
                                                <?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label required">Doctor</label>
                                        <select class="form-select" name="doctor_id" required>
                                            <option value="">Select Doctor</option>
                                            <?php foreach($doctors as $d): ?>
                                            <option value="<?= htmlspecialchars($d['id']) ?>">
                                                <?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?>
                                                (<?= htmlspecialchars($d['specialization']) ?>)
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label required">Department</label>
                                        <select class="form-select" name="department_id" required>
                                            <option value="">Select Department</option>
                                            <?php foreach($departments as $dept): ?>
                                            <option value="<?= htmlspecialchars($dept['id']) ?>">
                                                <?= htmlspecialchars($dept['name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label required">Appointment Date & Time</label>
                                        <input type="datetime-local" class="form-control" name="appointment_datetime"
                                            required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-12">
                                        <label class="form-label required">Reason for Visit</label>
                                        <input type="text" class="form-control" name="reason" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Additional Notes</label>
                                    <textarea class="form-control" name="notes" rows="3"></textarea>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="btn button-custom">Schedule Appointment</button>
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