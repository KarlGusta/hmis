<?php
$pageTitle = "Register- HMIS";
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="card card-md card-custom">
            <div class="card-body">
                <h2 class="text-center mb-4">Create new account</h2>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>
                <form action="<?php echo path('handlers'); ?>register.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-control">
                            <option value="">Select Department (Optional)</option>
                            <?php
                            require_once '../../config/database.php';
                            require_once '../../classes/Department.php';
                            
                            $db = new DatabaseConnection();
                            $departmentObj = new Department($db);
                            $departments = $departmentObj->getActiveDepartments();
                            
                            foreach ($departments as $department) {
                                echo "<option value='" . htmlspecialchars($department['id']) . "'>" 
                                    . htmlspecialchars($department['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn button-custom w-100">Create account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include '../../includes/main_footer.php'; ?>
</div>

<?php include '../../includes/footer_scripts.php'; ?>