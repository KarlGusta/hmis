<?php
$pageTitle = "Pending Bills";
require_once '../../config/database.php';
require_once '../../classes/Billing.php';

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

$db = new DatabaseConnection();
$billing = new Billing($db);
$pendingBills = $billing->getPendingBills();
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <?php include '../../includes/alerts.php'; ?>

            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Pending Bills</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Bill ID</th>
                                            <th>Patient</th>
                                            <th>Medication</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total Amount</th>
                                            <th>Date Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($pendingBills)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No pending bills available</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($pendingBills as $bill): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($bill['id']) ?></td>
                                                    <td>
                                                        <?= htmlspecialchars($bill['first_name'] . ' ' . $bill['last_name']) ?>
                                                        <div class="text-muted"><?= htmlspecialchars($bill['patient_number']) ?></div>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($bill['medication_name']) ?>
                                                        <div class="text-muted">
                                                            <?= htmlspecialchars($bill['form']) ?> - <?= htmlspecialchars($bill['strength']) ?>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($bill['quantity']) ?></td>
                                                    <td><?= htmlspecialchars(number_format($bill['unit_price'], 2)) ?></td>
                                                    <td><?= htmlspecialchars(number_format($bill['total_amount'], 2)) ?></td>
                                                    <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($bill['created_at']))) ?></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="payment.php?id=<?= htmlspecialchars($bill['id']) ?>"
                                                               class="btn btn-sm button-custom">
                                                                Record Payment
                                                            </a>
                                                            <button type="button"
                                                                    class="btn btn-sm btn-danger"
                                                                    onclick="showCancelModal(<?= $bill['id'] ?>)">
                                                                Cancel    
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>    
                                            <?php endforeach; ?>
                                        <?php endif; ?>        
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

<!-- Cancel Bill Modal -->
<div class="modal fade" id="cancelBillModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../handlers/billing_handler.php" method="POST">
                <input type="hidden" name="action" value="cancel_bill">
                <input type="hidden" name="billing_id" id="billing_id_to_cancel">

                <div class="modal-header">
                    <h5 class="modal-title">Cancel Bill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this bill? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Bill</button>
                </div>
            </form>
        </div>
    </div>
</div> 

<script>
    function showCancelModal(billingId) {
        const modal = document.getElementById('cancelBillModal');
        modal.querySelector('#billing_id_to_cancel').value = billingId;

        // Show the modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show(); 
    }
</script>

<?php include '../../includes/footer_scripts.php'; ?>