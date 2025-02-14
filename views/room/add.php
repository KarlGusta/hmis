<?php
$pageTitle = "Add New Room";

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

require_once '../../classes/Department.php';
require_once '../../config/database.php';

$db = new DatabaseConnection();
$department = new Department($db);

try {
    $departments = $department->getAllDepartments();
} catch (Exception $e) {
    $_SESSION['error'] = "Error loading departments: " . $e->getMessage();
    $departments = [];
}
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Add New Room</h3>
                        </div>
                        <div class="card-body">
                            <form action="../../handlers/add_room.php" method="POST" class="needs-validation" novalidate>
                                <!-- Room Basic Information -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">Room Number</label>
                                        <input type="text"
                                               class="form-control"
                                               name="room_number"
                                               required
                                               pattern="^[A-Za-z0-9-]{1,20}$">
                                        <div class="invalid-feedback">
                                            Please enter a valid room number (letters, numbers, and hyphens only)
                                        </div>       
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required">Department</label>
                                        <select class="form-select" name="department_id" required>
                                            <option value="">Select Department</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= htmlspecialchars($dept['id']) ?>">
                                                    <?= htmlspecialchars($dept['name']) ?>
                                                </option>
                                            <?php endforeach; ?>    
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a department
                                        </div>
                                    </div>
                                </div> 
                                
                                <!-- Room Details -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">Room Type</label>
                                        <select class="form-select" name="room_type" required>
                                            <option value="">Select Room Type</option>
                                            <option value="consultation">Consultation Room</option>
                                            <option value="emergency">Emergency Room</option>
                                            <option value="procedure">Procedure Room</option>
                                            <option value="ward">Ward Room</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a room type
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Capacity</label>
                                        <input type="number"
                                               class="form-control"
                                               name="capacity"
                                               min="1"
                                               max="20"
                                               value="1">
                                        <div class="invalid-feedback">
                                            Please enter a valid capacity (1-20)
                                        </div>       
                                    </div>
                                </div> 

                                <!--Room Features and Notes -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Features</label>
                                        <textarea class="form-control"
                                                  name="features"
                                                  rows="3"
                                                  placeholder="List special features or equipment"><?= htmlspecialchars($_POST['features'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control"
                                                  name="notes"
                                                  rows="3"
                                                  placeholder="Additional notes about the room"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="form-footer">
                                    <div class="d-flex justify-content-between">
                                        <a href="room_management.php" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn button-custom">Add Room</button>
                                    </div>
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

