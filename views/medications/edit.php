<?php
$pageTitle = "Edit Medication - HMIS";
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['error'] = "Invalid medication ID";
    header('Location: list.php');
    exit;
}

$db = new DatabaseConnection();
$medication = new Medication($db);
$med = $medication->getMedication($id);

if (!$med) {
    $_SESSION['error'] = "Medication not found";
    header('Location: list.php');
    exit;
}
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Medication</h3>
                        </div>
                        <div class="card-body">
                            <form action="../../handlers/update_medication.php" method="POST">
                                <input type="hidden" name="id" value="<?= $med['id'] ?>">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">Medication Name</label>
                                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($med['name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Generic Name</label>
                                        <input type="text" class="form-control" name="generic_name" value="<?= htmlspecialchars($med['generic_name']) ?>">
                                    </div>
                                </div>

                                <!-- Similar fields as add.php but with pre-filled  -->
                                 <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label required">Category</label>
                                        <select class="form-select" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php
                                            require_once '../../config/database.php';
                                            $db = new DatabaseConnection();

                                            $stmt = $db->conn->query("SELECT * FROM medication_categories WHERE status = 'active'");
                                            while ($category = $stmt->fetch_assoc()) {
                                                echo "<option value='{$category['id']}'>{$category['name']}</option>";
                                            } 
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label required">Form</label>
                                        <select class="form-select" name="form" required>
                                            <option value="tablet">Tablet</option>
                                            <option value="capsule">Capsule</option>
                                            <option value="syrup">Syrup</option>
                                            <option value="injection">Injection</option>
                                            <option value="cream">Cream</option>
                                            <option value="ointment">Ointment</option>
                                            <option value="drops">Drops</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label required">Strength</label>
                                        <input type="text" class="form-control" name="strength" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label required">Unit</label>
                                        <input type="text" class="form-control" name="unit" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label required">Unit Price</label>
                                        <input type="number" class="form-control" name="unit_price" step="0.01" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label required">Initial Stock</label>
                                        <input type="number" class="form-control" name="stock_quantity" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label required">Reorder Level</label>
                                        <input type="number" class="form-control" name="reorder_level" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Manufacturer</label>
                                        <input type="text" class="form-control" name="manufacturer">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Barcode</label>
                                        <input type="text" class="form-control" name="barcode">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Storage Conditions</label>
                                        <textarea class="form-control" name="storage_conditions" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Status</label>
                                            <select class="form-select" name="status">
                                                <option value="active" <?= $med['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= $med['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                            </select>                                        
                                    </div>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="btn button-custom">Update Medication</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>