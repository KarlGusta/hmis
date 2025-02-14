<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

// Initialize database and doctor class
require_once '../../config/database.php';
require_once '../../classes/Doctor.php';

$db = new DatabaseConnection();
$doctor = new Doctor($db);

// Get all doctors
$doctors = $doctor->getAllDoctors();
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Doctors List</h3>
                            <div class="card-actions">
                                <a href="register.php" class="btn button-custom">Add New Doctor</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible">
                                <?php
                                    echo $_SESSION['success'];
                                    unset($_SESSION['success']);
                                    ?>
                                <a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                            </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <?php
                                    echo $_SESSION['error'];
                                    unset($_SESSION['error']);
                                    ?>
                                <a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                            </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Photo</th>
                                            <th>Name</th>
                                            <th>Specialization</th>
                                            <th>Contact</th>
                                            <th>Experience</th>
                                            <th>Status</th>
                                            <th class="w-1">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($doctors && count($doctors) > 0): ?>
                                        <?php foreach ($doctors as $doc): ?>
                                        <tr>
                                            <td>
                                                <?php if ($doc['photo']): ?>
                                                <img src="../../uploads/doctors/<?php echo htmlspecialchars($doc['photo']); ?>"
                                                    class="avatar" alt="Doctor photo">
                                                <?php else: ?>
                                                <span class="avatar">
                                                    <?php echo strtoupper(substr($doc['first_name'], 0, 1) . substr($doc['last_name'], 0, 1)); ?>
                                                </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="font-weight-medium">
                                                    <?php echo htmlspecialchars($doc['first_name'] . ' ' . $doc['last_name']); ?>
                                                </div>
                                                <div class="text-muted">
                                                    License: <?php echo htmlspecialchars($doc['license_number']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div><?php echo htmlspecialchars($doc['specialization']); ?></div>
                                                <div class="text-muted"><?php echo htmlspecialchars($doc['qualification']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div><?php echo htmlspecialchars($doc['email']); ?></div>
                                                <div class="text-muted"><?php echo htmlspecialchars($doc['phone']); ?></div>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($doc['experience_years']); ?> years
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = $doc['status'] === 'active' ? 'button-custom-black-sm' : 'button-custom-sm';
                                                ?>
                                                <span class="badge <?php echo $statusClass; ?>">
                                                    <?php echo ucfirst(htmlspecialchars($doc['status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="view.php?id=<?php echo $doc['id']; ?>" class="btn btn-sm button-custom-white-sm">View</a>
                                                <a href="edit.php?id=<?php echo $doc['id']; ?>" class="btn btn-sm button-custom-white-sm">Edit</a>
                                                <button type="button" class="btn btn-sm button-custom-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal<?php echo $doc['id']; ?>">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Delete Confirmation Modal -->
                                         <div class="modal modal-blur fade" id="deleteModal<?php echo $doc['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <div class="modal-title">Are you sure?</div>
                                                        <div>This action cannot be undone.</div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="../../handlers/delete_doctor.php" method="POST">
                                                            <input type="hidden" name="doctor_id" value="<?php echo $doc['id']; ?>">
                                                            <button type="submit" class="btn btn-danger">Yes, delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                         </div>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No doctors found</td>
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