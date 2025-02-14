<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

$bill_id = $_GET['id'] ?? null;
if (!bill_id) {
    header('Location: list.php');
    exit;
}

require_once '../../classes/Billing.php';
$billing = new Billing($db);
$bill = $billing->getBill($bill_id);
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bill Details #<?php echo $bill_id; ?></h3>
                    <div class="card-actions">
                        <button onclick="window.print()" class="btn btn-primary">Print Bill</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h4>Patient Information</h4>
                            <p>Name: <?php echo $bill['patient_name']; ?></p>
                            <p>Insurance: <?php echo $bill['insurance_provider'] ?? 'None'; ?></p>
                        </div>
                        <div class="col-sm-6">
                            <h4>Bill Information</h4>
                            <p>Date: <?php echo $bill['bill_date']; ?></p>
                            <p>Status: <span class="badge bg-<?php echo getBadgeColor($bill['status']); ?>"><?php echo $bill['status']; ?></span></p>
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bill['items'] as $item): ?>
                                    <tr>
                                        <td><?php echo $item['item_name']; ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo number_format($item['unit_price'], 2); ?></td>
                                        <td><?php echo number_format($item['total_price'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                    <td><strong><?php echo number_format($bill['total_amount'], 2); ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Paid Amount:</td>
                                    <td><?php echo number_format($bill['paid_amount'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Balance:</td>
                                    <td><?php echo number_format($bill['total_amount'] - $bill['paid_amount'], 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <?php if($bill['status'] !== 'paid'): ?>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Process Payment</h4>
                            </div>
                            <div class="card-body">
                                <form action="../../handlers/process_payment.php" method="POST">
                                    <input type="hidden" name="bill_id" value="<?php echo $bill_id; ?>">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label class="form-label">Payment Amount</label>
                                            <input type="number" class="form-control" name="amount" step="0.01" max="<?php echo $bill['total_amount'] - $bill['paid_amount']; ?>" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label">Payment Method</label>
                                            <select class="form-select" name="payment_method" required>
                                                <option value="cash">Cash</option>
                                                <option value="card">Card</option>
                                                <option value="mobile_money">Mobile Money</option>
                                                <option value="insurance">Insurance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-footer mt-3">
                                        <button type="submit" class="btn btn-primary">Process Payment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>