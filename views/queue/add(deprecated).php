<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Add Patient to Queue</h3>
                        </div>
                        <div class="card-body">
                            <form action="../../handlers/queue_handler.php" method="POST">
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label required">Patient ID</label>
                                        <input type="text" class="form-control" name="patient_id" required>
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
                                        <select class="form-control" name="priority">
                                            <option value="normal">Normal</option>
                                            <option value="urgent">Urgent</option>
                                            <option value="emergency">Emergency</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Symptoms</label>
                                        <textarea class="form-control" name="symptoms" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Additional Notes</label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="form-footer mt-4">
                                    <button type="submit" class="btn btn-primary button-custom">Add to Queue</button>
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