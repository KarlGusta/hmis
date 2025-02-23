<?php
require_once '../../config/database.php';
require_once '../../classes/Billing.php';

// Simple receipt view without header/sidebar/navbar
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid billing ID");
} 

$db = new DatabaseConnection();
$billing = new Billing($db);

// Handle multiple billing IDs (comma-separated)
$billingIds = array_map('trim', explode(',', $_GET['id']));
$totalAmount = 0;
$allBillDetails = [];
$allPaymentHistory = [];

foreach ($billingIds as $billingId) {
    $billDetails = $billing->getBillingDetails($billingId);
    $paymentHistory = $billing->getPaymentHistory($billingId);
    
    if (!$billDetails || $billDetails['status'] !== 'paid') {
        die("Bill not found or not paid");
    }
    
    $allBillDetails[] = $billDetails;
    $allPaymentHistory = array_merge($allPaymentHistory, $paymentHistory);
    $totalAmount += $billDetails['total_amount'];
}

// Use the first bill's details for patient information
$firstBill = $allBillDetails[0];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Receipt - <?= htmlspecialchars($firstBill['id']) ?></title>
        <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
            }
            .card-lg {
                margin: 2rem auto;
                max-width: 800px;
                padding: 2rem;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                border: 1px solid #ddd;
            }
            .table-transparent {
                margin: 2rem 0;
            }
            .table-transparent td, .table-transparent th {
                padding: 0.75rem;
                vertical-align: top;
                border-top: 1px solid #dee2e6;
            }
            .strong {
                font-weight: bold;
            }
            @media print {
                .no-print {
                    display: none !important;
                }
                body {
                    padding: 0;
                    margin: 0;
                }
                .card-lg {
                    margin: 0;
                    padding: 1rem;
                    border: none;
                    box-shadow: none;
                }
                .container-xl {
                    padding: 0;
                    margin: 0;
                }
                /* Adjust font sizes for print */
                body {
                    font-size: 12px;
                    line-height: 1.4;
                }
                .h3 {
                    font-size: 16px;
                    margin-bottom: 0.5rem;
                }
                h1 {
                    font-size: 20px;
                    margin: 1rem 0;
                }
                .table td, .table th {
                    padding: 0.25rem 0.5rem;
                }
                .my-5 {
                    margin-top: 1rem !important;
                    margin-bottom: 1rem !important;
                }
                .mt-4 {
                    margin-top: 0.75rem !important;
                }
                .mt-5 {
                    margin-top: 1rem !important;
                }
            }
        </style>
    </head>
    <body>
        <div class="container-xl">
            <!-- Page title -->
            <div class="page-header d-print-none">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">Receipt</h2>
                    </div>
                    <div class="col-12 col-md-auto ms-auto d-print-none">
                        <button type="button" class="btn btn-primary d-print-none" onclick="window.print();">
                            <i class="ti ti-printer"></i> Print Receipt
                        </button>
                    </div>
                </div>
            </div>

            <div class="card card-lg">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p class="h3">Hospital Name</p>
                            <address>
                                Hospital Address<br>
                                Contact Information<br>
                                Email
                            </address>
                        </div>
                        <div class="col-6 text-end">
                            <p class="h3">Patient Information</p>
                            <address>
                                <?= htmlspecialchars($firstBill['first_name'] . ' ' . $firstBill['last_name']) ?><br>
                                ID: <?= htmlspecialchars($firstBill['patient_number']) ?><br>
                            </address>
                        </div>
                        <div class="col-12 my-5">
                            <h1>Receipt #R-<?= htmlspecialchars($firstBill['id']) ?></h1>
                        </div>
                    </div>

                    <table class="table table-transparent table-responsive">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 1%">#</th>
                                <th>Medication</th>
                                <th class="text-center" style="width: 1%">Qty</th>
                                <th class="text-end" style="width: 1%">Unit Price</th>
                                <th class="text-end" style="width: 1%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 1; foreach ($allBillDetails as $bill): ?>
                            <tr>
                                <td class="text-center"><?= $counter++ ?></td>
                                <td>
                                    <p class="strong mb-1"><?= htmlspecialchars($bill['medication_name']) ?></p>
                                    <?php if ($bill['form'] && $bill['strength']): ?>
                                    <div class="text-muted"><?= htmlspecialchars($bill['form']) ?> - <?= htmlspecialchars($bill['strength']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= htmlspecialchars($bill['quantity']) ?></td>
                                <td class="text-end"><?= htmlspecialchars(number_format($bill['unit_price'], 2)) ?></td>
                                <td class="text-end"><?= htmlspecialchars(number_format($bill['total_amount'], 2)) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tr>
                            <td colspan="4" class="strong text-end">Total Amount:</td>
                            <td class="text-end strong"><?= htmlspecialchars(number_format($totalAmount, 2)) ?></td>
                        </tr>
                    </table>

                    <h5 class="mt-4">Payment Details</h5>
                    <table class="table table-transparent table-responsive">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allPaymentHistory as $payment): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($payment['transaction_date']))) ?></td>
                                <td><?= htmlspecialchars(ucfirst($payment['payment_method'])) ?></td>
                                <td><?= htmlspecialchars($payment['payment_reference'] ?? '-') ?></td>
                                <td class="text-end"><?= htmlspecialchars(number_format($payment['amount'], 2)) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <p class="text-muted text-center mt-5">
                        Thank you for choosing our services!<br>
                        This is an electronically generated receipt and does not require a signature.
                    </p>
                </div>
            </div>
        </div>

        <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    </body>
</html>