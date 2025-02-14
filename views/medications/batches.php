<?php
$pageTitle = "Medication Batches - HMIS";
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

$medication_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Manage Batches</h3>
                            <div class="card-actions">
                                <button type="button" class="btn button-custom" data-bs-toggle="modal" data-bs-target="#addBatchModal">
                                    Add New Batch
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Batch Number</th>
                                            <th>Quantity</th>
                                            <th>Manufacturing Date</th>
                                            <th>Expiry Date</th>
                                            <th>Supplier</th>
                                            <th>Purchase Price</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once '../../config/database.php';

                                        $db = new DatabaseConnection();
                                        $stmt = $db->conn->prepare(
                                            "SELECT * FROM medication_batches
                                            WHERE medication_id = ?
                                            ORDER BY expiring_date ASC"
                                        );
                                        $stmt->bind_param("i", $medication_id);
                                        $stmt->execute();
                                        $batches = $stmt->get_result();

                                        while ($batch = $batches->fetch_assoc()) {
                                            $expiryClass = strtotime($batch['expiring_date']) < time() ? 'text-danger' : '';
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($batch['batch_number']) ?></td>
                                                <td><?= $batch['quantity'] ?></td>
                                                <td><?= $batch['manufacturing_date'] ?></td>
                                                <td class="<?= $expiryClass ?>"><?= $batch['expiring_date'] ?></td>
                                                <td><?= htmlspecialchars($batch['supplier']) ?></td>
                                                <td><?= number_format($batch['purchase_price'], 2) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $batch['status'] === 'active' ? 'success' : 'danger' ?>">
                                                        <?= ucfirst($batch['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-warning" onclick="editBatch(<?= $batch['id'] ?>)">
                                                        Edit
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Batch Modal -->
 <div class="modal fade" id="addBatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../handlers/add-batch.php" method="POST">
                <input type="hidden" name="medication_id" value="<?= $medication_id ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Batch form fields -->
                     <div class="mb-3">
                        <label class="form-label required">Batch Number</label>
                        <input type="text" class="form-control" name="batch_number" required>
                     </div>
                     <div class="mb-3">
                        <label class="form-label required">Quantity</label>
                        <input type="number" class="form-control" name="quantity" required>
                     </div>
                     <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Manufacturing Date</label>
                            <input type="date" class="form-control" name="manufacturing_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Expiry Date</label>
                            <input type="date" class="form-control" name="expiry_date" required>
                        </div>
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <select class="form-select" name="supplier_id">
                            <option value="">Select Supplier</option>
                            <?php
                            $suppliers = $db->conn->query("SELECT * FROM suppliers WHERE status = 'active'");
                            while ($supplier = $suppliers->fetch_assoc()) {
                                echo "<option value='{$supplier['id']}'>{$supplier['name']}</option>";
                            }
                            ?>
                        </select>
                     </div>
                     <div class="mb-3">
                        <label class="form-label required">Purchase Price</label>
                        <input type="number" step="0.01" class="form-control" name="purchase_price" required>
                     </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn button-custom">Add Batch</button>
                </div>
            </form>
        </div>
    </div>
 </div>

 <?php include '../../includes/footer_scripts.php'; ?>