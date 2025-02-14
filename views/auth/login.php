<?php
$auth_not_required = true;
$pageTitle = "Login - HMIS";
session_start();
include '../../includes/header.php';

// Add debugging to check if we reach this point
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="card card-md card-custom">
            <div class="card-body">
                <h2 class="text-center mb-4">Login to your account</h2>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>
                <form action="<?php echo path('handlers'); ?>login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username or Email</label>
                        <input type="text" name="username" class="form-control" placeholder="Enter username or email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn button-custom w-100">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer_scripts.php'; ?>