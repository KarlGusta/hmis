<?php
$pageTitle = "Consultations List";

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

require_once '../../classes/Consultation.php';
require_once '../../config/database.php';

$db = new DatabaseConnection();
$consultation = new Consultation($db);

// Get filter parameters
$status = $_GET['status'] ?? 'all';
$dateFrom = $_GET['date_from'] ?? date('Y-m-d');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');
$doctorId = $_GET['doctor_id'] ?? null;

try {
    // Get consultations based on filters
    $consultations = $consultation->getDoctorConsultations(
        $_SESSION['user_id'],
        $dateFrom,
        $dateTo,
        $status
    ); 
} catch (Exception $e) {
    $_SESSION['error'] = "Error loading consultations: " . $e->getMessage();
}
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <!-- Filters Section -->
                    <div class="card mb-3 card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Filters</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All</option>
                                        <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>In
                                            Progress
                                        </option>
                                        <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">From Date</label>
                                    <input type="date" class="form-control" name="date_from"
                                        value="<?= htmlspecialchars($dateFrom) ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">To Date</label>
                                    <input type="date" class="form-control" name="date_to"
                                        value="<?= htmlspecialchars($dateTo) ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn button-custom d-block">Apply Filters</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Consultations List -->
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Consultations</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Patient</th>
                                            <th>Chief Complaint</th>
                                            <th>Status</th>
                                            <th>Room</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($consultations)) : ?>
                                        <?php foreach ($consultations as $consultation) : ?>
                                        <tr>
                                            <td><?= date('M d, Y H:i', strtotime($consultation['created_at'])) ?></td>
                                            <td><?= htmlspecialchars($consultation['patient_name']) ?></td>
                                            <td><?= htmlspecialchars($consultation['chief_complaint']) ?></td>
                                            <td>
                                                <span class="badge <?= $consultation['status'] === 'completed' ? 'button-custom-black-sm' : 'button-custom-sm' ?>">
                                                    <?= ucfirst($consultation['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($consultation['room_number'] ?? 'N/A') ?></td>
                                            <td>
                                                <a href="consultation.php?id=<?= $consultation['id'] ?>" 
                                                   class="btn btn-sm button-custom-white-sm">View</a>
                                                <?php if ($consultation['status'] !== 'completed') : ?>
                                                <a href="consultation.php?id=<?= $consultation['id'] ?>" 
                                                   class="btn btn-sm button-custom-white-sm">Continue</a>
                                                <?php endif; ?>
                                                <a href="../prescriptions/create.php?consultation_id=<?= $consultation['id'] ?>" 
                                                   class="btn btn-sm button-custom-white-sm">Prescriptions</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else : ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No consultations found</td>
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