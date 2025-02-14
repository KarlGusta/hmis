<?php
$pageTitle = "Add Medication - HMIS";
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
                            <h3 class="card-title">Add New Medication</h3>
                        </div>
                        <div class="card-body">
                            <form action="../../handlers/add_medication.php" method="POST">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">Medication Name</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Generic Name</label>
                                        <input type="text" class="form-control" name="generic_name">
                                    </div>
                                </div>

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

                                <div class="form-footer">
                                    <button type="submit" class="btn button-custom">Add Medication</button>
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