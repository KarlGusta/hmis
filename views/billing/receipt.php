<?php
require_once '../../config/database.php';
require_once '../../classes/Billing.php';

// Simple receipt view without header/sidebar/navbar
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid billing ID");
} 

$db = new DatabaseConnection();
$billing = new Billing($db);
$billingId = $_GET['id'];
$billDetails = $billing->getBillingDetails($billId);
$paymentHistory = $billing->getPaymentHistory($billId);

if (!$billDetails || $billDetails['status'] !== 'paid') {
    die("Bill not found or not paid");
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Receipt - <?= htmlspecialchars($billDetails['id']) ?></title>
        <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
        <style>
            body {
                font-family: Aria, sans-serif;
                line-height: 1.6;
                padding: 20px;
            } 
            .receipt-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ddd;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .receipt-header {
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 20px;
                border-bottom: 1px solid #ddd;
            }
            .receipt-details {
                margin-bottom: 30px;
            }
            .receipt-footer {
                margin-top: 30px;
                text-align: center;
                font-size: 0.9em;
                color: #667;
            }
            @media print {
                .no-print {
                    display: none !important;
                }
                body {
                    padding: 0;
                }
                .receipt-container {
                    border: none;
                    box-shadow: none;
                }
            }
        </style>
    </head>
    <body>
        <div class="receipt-container">
            <div class="receipt-header">
                <h2>PAYMENT RECEIPT</h2>
                <p>Receipt No: R-<?= htmlspecialchars($billDetails['id']) ?></p>
                <p>Date: <?= htmlspecialchars(date('Y-m-d H:i', strtotime($billDetails['payment_date']))) ?></p>
            </div>

            <div class="receipt-details">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Patient Information</h5>
                        <p>
                            <strong>Name</strong> <?= htmlspecialchars($billDetails['first_name'] . ' ' . $billDetails['last_name']) ?><br> 
                            <strong>ID:</strong> <?= htmlspecialchars($billDetails['patient_number']) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5>Payment Information</h5>
                        <p>
                            <strong>Payment Method:</strong> <?= htmlspecialchars(ucfirst($billDetails['payment_method'])) ?><br>
                            <?php if (!empty($billDetails['payment_reference'])): ?>
                            <strong>Reference:</strong> <?= htmlspecialchars($billDetails['payment_reference']) ?><br>
                            <?php endif; ?>
                            <strong>Amount Paid:</strong> <?= htmlspecialchars(number_format($billDetails['total_amount'], 2)) ?>    
                        </p>
                    </div>
                </div>

                <h5 class="mt-4">Medication Details</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Medication</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?= htmlspecialchars($billDetails['medication_name']) ?><br>
                                <small><?= htmlspecialchars($billDetails['form']) ?> - <?= htmlspecialchars($billDetails['strength']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($billDetails['quantity']) ?></td>
                            <td><?= htmlspecialchars(number_format($billDetails['unit_price'], 2)) ?></td>
                            <td><?= htmlspecialchars(number_format($billDetails['total_amount'], 2)) ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total Amount:</th>
                            <th><?= htmlspecialchars(number_format($billDetails['total_amount'], 2)) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="receipt-footer">
                <p>Thank you for your payment!</p>
                <p>This is an electronically generated receipt and does not require a signature.</p>
            </div>

            <div class="mt-4 no-print">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="ti ti-printer"></i> Print Receipt
                </button>
                <a href="../billing/paid_bills.php" class="btn btn-secondary">
                    Back to Paid Bills
                </a>
            </div>
        </div>

        <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    </body>
</html>