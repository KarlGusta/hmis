<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
include '../../config/database.php';

$db = new DatabaseConnection();
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Register New Doctor</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo path('handlers'); ?>register_doctor.php" method="POST" enctype="multipart/form-data">
                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    </div>
                                </div>

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
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">License Number</label>
                                        <input type="text" class="form-control" id="license_number" name="license_number" required>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label">National ID</label>
                                        <input type="text" class="form-control" id="national_id" name="national_id">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Phone</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Specialization</label>
                                        <select class="form-select" name="specialization" required>
                                            <option value="">Select Specialization</option>
                                            <option value="Cardiology">Cardiology</option>
                                            <option value="Dermatology">Dermatology</option>
                                            <option value="Neurology">Neurology</option>
                                            <option value="Pediatrics">Pediatrics</option>
                                            <option value="Orthopedics">Orthopedics</option>
                                            <option value="General Medicine">General Medicine</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Department</label>
                                        <select class="form-select" name="department_id" required>
                                            <option value="">Select Department</option>
                                            <?php
                                            $sql = "SELECT id, name FROM departments WHERE status = 'active' ORDER BY name";
                                            $result = $db->conn->query($sql);
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-sm-6">
                                        <label class="form-label required">Qualification</label>
                                        <input type="text" class="form-control" id="qualification" name="qualification" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Experience (Years)</label>
                                        <input type="number" class="form-control" id="experience_years" name="experience_years" required>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Consultation Fee</label>
                                        <input type="number" class="form-control" id="consultation_fee" name="consultation_fee" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Available Days</label>
                                        <div class="form-selectgroup">
                                            <label class="form-selectgroup-item">
                                                <input type="checkbox" name="available_days[]" value="Monday" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label">Monday</span>
                                            </label>
                                            <label class="form-selectgroup-item">
                                                <input type="checkbox" name="available_days[]" value="Tuesday" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label">Tuesday</span>
                                            </label>
                                            <label class="form-selectgroup-item">
                                                <input type="checkbox" name="available_days[]" value="Wednesday" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label">Wednesday</span>
                                            </label>
                                            <label class="form-selectgroup-item">
                                                <input type="checkbox" name="available_days[]" value="Thursday" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label">Thursday</span>
                                            </label>
                                            <label class="form-selectgroup-item">
                                                <input type="checkbox" name="available_days[]" value="Friday" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label">Friday</span>
                                            </label>
                                            <label class="form-selectgroup-item">
                                                <input type="checkbox" name="available_days[]" value="Saturday" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label">Saturday</span>
                                            </label>
                                            <label class="form-selectgroup-item">
                                                <input type="checkbox" name="available_days[]" value="Sunday" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label">Sunday</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label">Photo</label>
                                        <input type="file" class="form-control" name="photo" id="photo">
                                    </div>
                                </div>

                                <div class="form-footer mt-4">
                                    <button type="submit" class="btn btn-primary button-custom">Register Doctor</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../../includes/main_footer.php'; ?>
</div>

<?php include '../../includes/footer_scripts.php'; ?>