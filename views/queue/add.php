<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Add Patient to Queue</h3>
                        </div>
                        <div class="card-body">
                            <form action="../../handlers/add_to_queue.php" method="POST">
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label required">Patient ID</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="patient_id" name="patient_id" required>
                                            <button class="btn btn-outline-secondary" type="button" id="search_patient">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                    <path d="M21 21l-6 -6" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div id="patient_details" class="mt-2"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label required">Department</label>
                                        <select class="form-control" name="department_id" required>
                                            <option value="">Select Department</option>
                                            <?php
                                            require_once '../../classes/Department.php';
                                            require_once '../../config/database.php';

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
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label required">Priority</label>
                                        <select class="form-select" name="priority" required>
                                            <option value="normal">Normal</option>
                                            <option value="urgent">Urgent</option>
                                            <option value="emergency">Emergency</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Room Number</label>
                                        <input type="text" class="form-control" name="room_number">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Symptoms</label>
                                        <textarea class="form-control" name="symptoms" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="btn button-custom">Add to Queue</button>
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