<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
include '../../config/database.php';

$db = new DatabaseConnection();

// Get departments for dropdown
$stmt = $db->conn->prepare("SELECT id, name FROM departments WHERE status = 'active' ORDER BY name");
$stmt->execute();
$departments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
        <?php include '../../includes/alerts.php'; ?>
        
            <div class="row row-cards">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Register New User</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo path('handlers'); ?>register_user.php" method="POST">
                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">First Name</label>
                                        <input type="text" class="form-control" name="first_name" required>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Username</label>
                                        <input type="text" class="form-control" name="username" required>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Password</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Confirm Password</label>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Roles</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="admin" id="role_admin">
                                            <label class="form-check-label" for="role_admin">Administrator</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="doctor" id="role_doctor">
                                            <label class="form-check-label" for="role_doctor">Doctor</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="nurse" id="role_nurse">
                                            <label class="form-check-label" for="role_nurse">Nurse</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="receptionist" id="role_receptionist">
                                            <label class="form-check-label" for="role_receptionist">Receptionist</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="pharmacist" id="role_pharmacist">
                                            <label class="form-check-label" for="role_pharmacist">Pharmacist</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="lab_technician" id="role_lab">
                                            <label class="form-check-label" for="role_lab">Lab Technician</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="cashier" id="role_cashier">
                                            <label class="form-check-label" for="role_cashier">Cashier</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label">Department</label>
                                        <select class="form-select" name="department_id">
                                            <option value="">Select Department</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= htmlspecialchars($dept['id']) ?>">
                                                    <?= htmlspecialchars($dept['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Doctor-specific fields (initially hidden) -->
                                <div id="doctor-fields" style="display: none;">
                                    <div class="row mb-3">
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label required">Gender</label>
                                            <select class="form-select" name="gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label required">Date of Birth</label>
                                            <input type="date" class="form-control" name="date_of_birth">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label required">National ID</label>
                                            <input type="text" class="form-control" name="national_id">
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label required">License Number</label>
                                            <input type="text" class="form-control" name="license_number">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label required">Specialization</label>
                                            <input type="text" class="form-control" name="specialization">
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label required">Qualification</label>
                                            <input type="text" class="form-control" name="qualification">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label required">Experience (Years)</label>
                                            <input type="number" class="form-control" name="experience_years">
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label required">Consultation Fee</label>
                                            <input type="number" step="0.01" class="form-control" name="consultation_fee">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label">Bio</label>
                                            <textarea class="form-control" name="bio" rows="3"></textarea>
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label">Office Number</label>
                                            <input type="text" class="form-control" name="office_number">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label">Available Days</label>
                                            <input type="text" class="form-control" name="available_days" 
                                                   placeholder="e.g., Monday,Tuesday,Wednesday">
                                            <small class="form-text text-muted">Separate days with commas</small>
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label">Available Times</label>
                                            <input type="text" class="form-control" name="available_times" 
                                                   placeholder="e.g., 09:00-17:00">
                                            <small class="form-text text-muted">Use 24-hour format (HH:MM-HH:MM)</small>
                                        </div>
                                    </div>

                                    <!-- Contact Information -->
                                    <h4 class="mt-4 mb-3">Contact Information</h4>
                                    <div class="row mb-3">
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label required">Phone</label>
                                            <input type="text" class="form-control" name="phone">
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label">Address</label>
                                            <input type="text" class="form-control" name="address">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="city">
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label">Country</label>
                                            <input type="text" class="form-control" name="country">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label">Emergency Contact Name</label>
                                            <input type="text" class="form-control" name="emergency_contact_name">
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <label class="form-label">Emergency Contact Phone</label>
                                            <input type="text" class="form-control" name="emergency_contact_phone">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="btn button-custom">Register User</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer_scripts.php'; ?>

</body>
</html> 