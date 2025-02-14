<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Authentication.php';

// Initialize database connection
$db = new DatabaseConnection();
$auth = new Authentication($db);

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: auth/login.php');
    exit();
}

// Check session timeout
if (!$auth->checkSessionTimeout()) {
    header('Location: login.php?timeout=1');
    exit();
}

// Get current user details
$currentUser = $auth->getCurrentUser();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo APP_NAME; ?> - Dashboard</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
        <?php include 'includes/header.php'; ?>

        <div class="container">
            <div class="dashboard">
                <h1>Welcome, <?php echo htmlspecialchars($currentUser['username']); ?></h1>
                
                <div class="quick-stats">
                    <!-- Quick statistics Section -->
                     <?php if ($auth->hasPermission('view_statistics')): ?>
                        <div class="stats-container">
                            <!-- Add your statistics widgets here -->
                        </div>
                        <?php endif; ?>
                </div>

                <div class="main-menu">
                    <!-- Dynamic Menu Based on User Role -->
                     <?php if ($auth->hasPermission('patient_view')): ?>
                        <div class="menu-item">
                            <a href="patient/list.php">Patient Management</a>
                        </div>
                        <?php endif; ?>

                        <?php if ($auth->hasPermission('appointment_view')): ?>
                            <div class="menu-item">
                                <a href="appointment/list.php">Appointments</a>
                            </div>
                            <?php endif; ?>

                            <?php if ($auth->hasPermission('billing_view')): ?>
                                <div class="menu-item">
                                    <a href="billing/list.php">Billing Management</a>
                                </div>
                                <?php endif; ?>

                                <?php if ($auth->hasPermission('staff_view')): ?>
                                    <div class="menu-item">
                                        <a href="staff/list.php">Staff Management</a>
                                    </div>
                                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
        <script src="assets/js/main.js"></script>
    </body>
</html>