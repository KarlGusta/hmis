<?php
$pageTitle = "Register Patient";

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
                            <h3 class="card-title">Register New Patient</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo path('handlers'); ?>register_patient.php" method="POST" enctype="multipart/form-data">
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
                                        <label class="form-label required">Phone</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label">Emergency Contact Name</label>
                                        <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name">
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label">Emergency Contact Phone</label>
                                        <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Date of Birth</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label required">Gender</label>
                                        <select class="form-select" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Allergies</label>
                                        <textarea class="form-control" id="allergies" name="allergies" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Current Medications</label>
                                        <textarea class="form-control" id="current_medications" name="current_medications" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label">Insurance Provider</label>
                                        <select class="form-select" name="insurance_provider" id="insurance_provider">
                                            <option value="">Select Provider</option>
                                            <?php
                                            try {
                                                require_once '../../classes/InsuranceProvider.php';
                                                require_once '../../config/database.php';
                                                
                                                $db = new DatabaseConnection();
                                                $provider = new InsuranceProvider($db);
                                                $activeProviders = $provider->getActiveProviders();

                                                foreach($activeProviders as $p) {
                                                    echo "<option value='{$p['id']}'>{$p['provider_name']}</option>";
                                                }
                                            } catch (Exception $e) {
                                                echo "<!-- Error: " . htmlspecialchars($e->getMessage()) . " -->";
                                                echo "<option value=''>Error loading providers</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label">Insurance ID</label>
                                        <input type="text" class="form-control" id="insurance_id" name="insurance_id">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 col-md-6">
                                        <label class="form-label">Photo</label>
                                        <input type="file" class="form-control" id="photo" name="photo">
                                    </div>
                                </div>

                                <div class="card card-custom mt-4">
                                    <div class="card-header">
                                        <h3 class="card-title">Medical History</h3>
                                    </div>
                                    <div class="card-body">
                                        <div id="medical_history_container">
                                            <div class="medical-history-entry card mb-3">
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-sm-6">
                                                            <label class="form-label">Condition Name</label>
                                                            <input type="text" class="form-control" name="medical_history[0][name]">
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label">Diagnosis Date</label>
                                                            <input type="date" class="form-control" name="medical_history[0][diagnosis_date]">
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label">Notes</label>
                                                            <input type="text" class="form-control" name="medical_history[0][notes]">
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label">Chronic?</label>
                                                            <select class="form-select" name="medical_history[0][is_chronic]">
                                                                <option value="0">No</option>
                                                                <option value="1">Yes</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn button-outline-custom" onclick="addMedicalHistoryField()">
                                            Add More Medical History
                                        </button>
                                    </div>
                                </div>

                                <div class="form-footer mt-4">
                                    <button type="submit" class="btn btn-primary button-custom">Register Patient</button>
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