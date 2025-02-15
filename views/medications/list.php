<?php
$pageTitle = "Medications List - HMIS";
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
                            <h3 class="card-title">Medications List</h3>
                            <div class="card-actions">
                                <a href="add.php" class="btn button-custom">
                                    Add New Medication
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Generic Name</th>
                                            <th>Category</th>
                                            <th>Unit</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once '../../config/database.php';
                                        require_once '../../classes/Medication.php';

                                        $db = new DatabaseConnection();
                                        $medication = new Medication($db);
                                        $medications = $medication->getAllMedications();
                                        
                                        foreach ($medications as $med) {
                                            $stockClass = $med['current_stock'] <= $med['minimum_stock']
                                                ? 'text-danger'
                                                : 'text-success';
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($med['name']) ?></td>
                                                    <td><?= htmlspecialchars($med['generic_name']) ?></td>
                                                    <td><?= htmlspecialchars($med['category']) ?></td>
                                                    <td><?= htmlspecialchars($med['unit']) ?></td>
                                                    <td><?= number_format($med['unit_price'], 2) ?></td>
                                                    <td class="<?= $stockClass ?>">
                                                        <?= $med['current_stock'] ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?= $med['status'] === 'active' ? 'button-custom-black-sm' : 'button-custom-sm' ?>">
                                                            <?= ucfirst($med['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="edit.php?id=<?= $med['id'] ?>" class="btn btn-sm button-custom-white-sm">Edit</a>
                                                        <button type="button"
                                                                class="btn btn-sm button-custom-white-sm"
                                                                onclick="updateStock(<?= $med['id'] ?>)">
                                                            Update Stock
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

<!-- Stock Update Modal -->
 <div class="modal fade" id="stockUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="stockUpdateForm" method="POST" action="../../handlers/update_stock.php">
                <div class="modal-header">
                    <h5 class="modal-title">Update Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="medication_id" id="medication_id">
                    <div class="mb-3">
                        <label class="form-label">Operation</label>
                        <select name="operation" class="form-select" required>
                            <option value="add">Add Stock</option>
                            <option value="subtract">Remove Stock</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
 </div>

 <?php include '../../includes/footer_scripts.php'; ?>