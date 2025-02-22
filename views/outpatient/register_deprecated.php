<?php
$pageTitle = "Register Outpatient - HMIS";

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Register Outpatient Visit</h3>
                        </div>
                        <div class="card-body">
                            <form action="../../handlers/register_outpatient.php" method="POST">
                                <!-- Patient Search Section -->
                                 <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">Search Patient</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="patient_search" placeholder="Search by name, ID or phone">
                                            <button type="button" class="btn button-custom" onclick="searchPatient()">
                                                Search
                                            </button>
                                        </div>
                                    </div>
                                 </div>

                                 <!-- Patient Details Section (Initially Hidden) -->
                                  <div id="patient_details" class="row mb-3" style="display: none;">
                                    <input type="hidden" name="patient_id" id="patient_id">
                                    <div class="col-md-4">
                                        <label class="form-label">Patient Name</label>
                                        <input type="text" class="form-control" id="patient_name" name="patient_name" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Patient ID</label>
                                        <input type="text" class="form-control" id="patient_number" name="patient_number" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="patient_phone" name="patient_phone" readonly>
                                    </div>
                                  </div>

                                  <!-- Visit Details -->
                                   <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">Department</label>
                                        <select class="form-select" name="department_id" required>
                                            <option value="">Select Department</option>
                                            <?php
                                            try {
                                                require_once '../../config/database.php';

                                                $db = new DatabaseConnection();
                                                $stmt = $db->conn->query("SELECT * FROM departments WHERE status = 'active'");
                                                while ($dept = $stmt->fetch_assoc()) {
                                                    echo "<option value='{$dept['id']}'>{$dept['name']}</option>";
                                                }
                                            } catch (Exception $e) {
                                                echo "<!-- Error: " . htmlspecialchars($e->getMessage()) . " -->";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required">Priority</label>
                                        <select class="form-select" name="priority" required>
                                            <option value="normal">Normal</option>
                                            <option value="urgent">Urgent</option>
                                            <option value="emergency">Emergency</option>
                                        </select>
                                    </div>
                                   </div>

                                   <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label required">Symptoms</label>
                                        <textarea class="form-control" name="symptoms" rows="3" required></textarea>
                                    </div>
                                   </div>

                                   <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Additional Notes</label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea>
                                    </div>
                                   </div>

                                   <div class="form-footer">
                                    <button type="submit" class="btn button-custom">Register Visit</button>
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