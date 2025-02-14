<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
                            <h3 class="card-title">Patient List</h3>
                            <div class="card-actions">
                                <a href="register.php" class="btn button-custom">
                                    <i class="fas fa-plus"></i> New Patient 
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Age</th>
                                            <th>Last Visit</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            require_once '../../config/database.php';
                                            require_once '../../classes/Patient.php';

                                            $db = new DatabaseConnection();
                                            $patient = new Patient($db);
                                            $patients = $patient->getAllPatients();

                                            foreach ($patients as $p) {
                                                $age = date_diff(date_create($p['date_of_birth']), date_create('today'))->y;
                                                $statusClass = '';
                                                switch($p['status']) {
                                                    case 'active':
                                                        $statusClass = 'badge bg-success';
                                                        break;
                                                    case 'inactive':
                                                        $statusClass = 'badge bg-secondary';
                                                        break;
                                                    case 'admitted':
                                                        $statusClass = 'badge bg-warning';
                                                        break;        
                                                }
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($p['patient_id']) ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if ($p['photo']): ?>
                                                                <span class="avatar avatar-sm me-2" style="background-image:url(../../uploads/patients/<?= htmlspecialchars($p['photo']) ?>)"></span>
                                                            <?php else: ?>
                                                                <span class="avatar avatar-sm me-2"><?= strtoupper(substr($p['first_name'], 0, 1) . substr($p['last_name'], 0, 1)) ?></span>
                                                            <?php endif; ?>
                                                            <div>
                                                                <?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($p['phone']) ?></td>
                                                    <td><?= htmlspecialchars($p['email']) ?></td>
                                                    <td><?= $age ?></td>
                                                    <td><?= $p['last_visit'] ? date('Y-m-d', strtotime($p['last_visit'])) : 'Never' ?></td>
                                                    <td><span class="badge button-custom-sm"><?= ucfirst($p['status']) ?></span></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="view.php?id=<?= $p['id'] ?>" class="btn btn-sm button-custom-white-sm">View</a>
                                                            <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-sm button-custom-sm">Edit</a>
                                                            <button type="button" class="btn btn-sm button-custom-black-sm" onclick="deletePatient(<?= $p['id'] ?>)">Delete</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } catch (Exception $e) {
                                            echo "<tr><td colspan='8' class='text-center text-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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
    <?php include '../../includes/main_footer.php'; ?>
</div>

<?php include '../../includes/footer_scripts.php'; ?>
