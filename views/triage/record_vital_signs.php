<?php
$pageTitle = "Appointments List";

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

require_once '../../classes/Appointment.php';
require_once '../../config/database.php';

$db = new DatabaseConnection();
$appointment = new Appointment($db);

// Get appointments based on user role
$appointments = [];
if ($_SESSION['role'] === 'doctor') {
    $appointments = $appointment->getAppointmentsByDoctor($_SESSION['doctor_id']);
} elseif ($_SESSION['role'] === 'patient') {
    $appointments = $appointment->getAppointmentsByPatient($_SESSION['patient_id']);
} else {
    $appointments = $appointment->getAllAppointments();
}
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Record Vital Signs</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($appointments as $apt): ?>
                                        <tr>
                                            <td><?= htmlspecialchars(date('M d, Y h:i A', strtotime($apt['appointment_datetime']))) ?>
                                            </td>
                                            <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                            <td><?= htmlspecialchars($apt['doctor_name']) ?>
                                                <div class="text-muted"><?= htmlspecialchars($apt['specialization']) ?>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($apt['reason']) ?></td>
                                            <td>
                                                <span class="badge <?= getStatusColor($apt['status']) ?>">
                                                    <?= ucfirst(htmlspecialchars($apt['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <?php if ($apt['status'] === 'scheduled'): ?>
                                                        <a href="check_in.php?id=<?= $apt['id'] ?>" class="btn btn-sm button-custom-white-sm mr-2">
                                                            Record Vital Signs
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
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

<?php
function getStatusColor($status) {
    if ($status === 'completed' || $status === 'confirmed') {
        return 'button-custom-black-sm';
    }
    return 'button-custom-sm';
}

function canModifyAppointment($appointment) {
    // Only allow modifications if appointment is scheduled/confirmed and user has appropriate role
    $allowedStatuses = ['scheduled', 'confirmed'];
    $allowedRoles = ['admin', 'receptionist'];

    return in_array($appointment['status'], $allowedStatuses) &&
           in_array($_SESSION['role'], $allowedRoles);
}
?>

<?php include '../../includes/footer_scripts.php'; ?>