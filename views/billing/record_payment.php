<?php
$pageTitle = "Record Payment";
require_once '../../config/database.php';
require_once '../../classes/Billing.php';

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid billing ID";
    header('Location: pending_bills.php');
    exit;
}

$db = new DatabaseConnection();
$billing = new Billing($db);
$billId = $_GET['id'];
$billDetails = $billing->getBillingDetails($billId);

if (!$billDetails || $billDetails['status'] !== 'pending') {
    $_SESSION['error'] = "Bill not found or already paid";
    header('Location: pending_bills.php');
    exit;
}
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <?php include '../../includes/alerts.php'; ?>

            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Record Payment</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h4>Bill Details</h4>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 150px;">Bill ID:</th>
                                            <td><?= htmlspecialchars($billDetails['id']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Patient:</th>
                                            <td>
                                                <?= htmlspecialchars($billDetails['first_name'] . ' ' . $billDetails['last_name']) ?>
                                                <div class="text-muted"><?= htmlspecialchars($billDetails['patient_number']) ?></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Medication:</th>
                                            <td>
                                                <?= htmlspecialchars($billDetails['medication_name']) ?>
                                                <div class="text-muted">
                                                    <?= htmlspecialchars($billDetails['form']) ?> - <?= htmlspecialchars($billDetails['strength']) ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Quantity:</th>
                                            <td><?= htmlspecialchars($billDetails['quantity']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Unit Price:</th>
                                            <td><?= htmlspecialchars(number_format($billDetails['unit_price'], 2)) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Total Amount:</th>
                                            <td><strong><?= htmlspecialchars(number_format($billDetails['total_amount'], 2)) ?></strong></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h4>Payment Information</h4>
                                    <form action="../../handlers/billing_handler.php" method="POST">
                                        <input type="hidden" name="action" value="record_payment">
                                        <input type="hidden" name="billing_id" value="<?= htmlspecialchars($billDetails['id']) ?>">
                                        <input type="hidden" name="amount" value="<?= htmlspecialchars($billDetails['total_amount']) ?>">

                                        <div class="mb-3">
                                            <label class="form-label required">Payment Method</label>
                                            <select name="payment_method" class="form-select" required>
                                                <option value="">-- Select Payment Method --</option>
                                                <option value="cash">Cash</option>
                                                <option value="card">Credit/Debit Card</option>
                                                <option value="insurance">Insurance</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="mobile_money">Mobile Money</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Payment Reference</label>
                                            <input type="text" name="payment_reference" class="form-control" placeholder="Transaction ID, receipt number, etc.">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Notes</label>
                                            <textarea name="notes" class="form-control" rows="3" placeholder="Any additional information..."></textarea>
                                        </div>

                                        <div class="form-footer">
                                            <a href="pending_bills.php" class="btn btn-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary ms-auto">Record Payment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer_scripts.php'; ?>