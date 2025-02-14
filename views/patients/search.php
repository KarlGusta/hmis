<?php 
$pageTitle = "Search Patient";

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>
<!-- Page body -->
<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Patient Search</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form action="<?php echo path('handlers'); ?>search_patient.php" method="GET" class="d-flex">
                                <input type="text" class="form-control me-2" name="search" placeholder="Search by name, ID, or contact number" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                                <button type="submit" class="btn button-custom">Search</button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Patient ID</th>
                                    <th>Name</th>
                                    <th>Date of Birth</th>
                                    <th>Contact Number</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($patients) && !empty($patients)): ?>
                                    <?php foreach ($patients as $patient): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($patient['patient_id']); ?></td>
                                            <td><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($patient['date_of_birth']); ?></td>
                                            <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                            <td><?php echo htmlspecialchars($patient['email']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No patients found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once __DIR__ . '/../../includes/main_footer.php'; ?>
</div>
<!-- End of page body -->

<?php require_once __DIR__ .  '/../../includes/footer_scripts.php'; ?>