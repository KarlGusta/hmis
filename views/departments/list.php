<?php
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';
require_once '../../config/database.php';
require_once '../../classes/Department.php';

$db = new DatabaseConnection();
$department = new Department($db);
$departments = $department->getAllDepartments();
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Departments</h3>
                            <div class="ms-auto">
                                <a href="register.php" class="btn button-custom">
                                    Add New Department
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th class="w-1">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($departments as $dept): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($dept['code']) ?></td>
                                        <td><?= htmlspecialchars($dept['name']) ?></td>
                                        <td><?= htmlspecialchars($dept['description']) ?></td>
                                        <td>
                                            <span class="badge <?= $dept['status'] === 'active' ? 'button-custom-black-sm' : 'button-custom-sm' ?>">
                                                <?= ucfirst(htmlspecialchars($dept['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form action="../../handlers/toggle_department_status.php" method="POST" class="d-inline">
                                                <input type="hidden" name="department_id" value="<?= $dept['id'] ?>">
                                                <button type="submit" class="btn btn-sm <?= $dept['status'] === 'active' ? 'button-custom-white-sm' : 'button-custom-black-sm' ?>">
                                                    <?= $dept['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                                </button>
                                            </form>
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

<?php require_once '../../includes/footer_scripts.php'; ?>