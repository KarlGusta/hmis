<?php
$pageTitle = "Billing Reports";
require_once '../../config/database.php';
require_once '../../classes/Billing.php';

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

// Process date parameters
$startDate = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$endDate = $_GET['end_date'] ?? date('Y-m-d');

$db = new DatabaseConnection();
$billing = new Billing($db);
$reportData = $billing->getBillingReport($startDate, $endDate);
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <?php include '../../includes/alerts.php'; ?>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Billing Reports</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="" class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Generate Report
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cards">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Paid Amount</div>
                            </div>
                            <div class="h1 mb-3"><?= number_format($reportData['total_paid'] ?? 0, 2) ?></div>
                            <div class="d-flex mb-2">
                                <div>Number of Paid Bills</div>
                                <div class="ms-auto">
                                    <span class="text-green d-inline-flex align-items-center lh-1">
                                        <?= $reportData['paid_count'] ?? 0 ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Pending Amount</div>
                            </div>
                            <div class="h1 mb-3"><?= number_format($reportData['total_pending'] ?? 0, 2) ?></div>
                            <div class="d-flex mb-2">
                                <div>Number of Pending Bills</div>
                                <div class="ms-auto">
                                    <span class="text-yellow d-inline-flex align-items-center lh-1">
                                        <?= $reportData['pending_count'] ?? 0 ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Revenue</div>
                            </div>
                            <div class="h1 mb-3"><?= number_format(($reportData['total_paid'] ?? 0), 2) ?></div>
                            <div class="d-flex mb-2">
                                <div>Period</div>
                                <div class="ms-auto">
                                    <?= htmlspecialchars($startDate) ?> to <?= htmlspecialchars($endDate) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Collection Rate</div>
                            </div>
                            <?php
                            $totalBilled = ($reportData['total_paid'] ?? 0) + ($reportData['total_pending'] ?? 0);
                            $collectionRate = $totalBilled > 0 ? (($reportData['total_paid'] ?? 0) / $totalBilled) * 100 : 0;
                            ?>
                            <div class="h1 mb-3"><?= number_format($collectionRate, 2) ?>%</div>
                            <div class="d-flex mb-2">
                                <div>Total Bills</div>
                                <div class="ms-auto">
                                    <?= ($reportData['paid_count'] ?? 0) + ($reportData['pending_count'] ?? 0) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Billing Summary</h3>
                            <div class="card-actions">
                                <a href="paid_bills.php?start_date=<?= htmlspecialchars($startDate) ?>$end_date=<?= htmlspecialchars($endDate) ?>"
                                   class="btn btn-primary">
                                    View Detailed Report
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <p>Period: <strong><?= htmlspecialchars($startDate) ?></strong> to <strong><?= htmlspecialchars($endDate) ?></strong></p>

                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Billing Statistics</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Total Billed Amount:</th>
                                            <td><?= number_format($totalBilled, 2) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Total Collected Amount:</th>
                                            <td><?= number_format($reportData['total_paid']) ?? 0, 2 ?></td>
                                        </tr>
                                        <tr>
                                            <th>Outstanding Amount:</th>
                                            <td><?= number_format($reportData['total_pending'] ?? 0, 2) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Collection Rate:</th>
                                            <td><?= number_format($collectionRate, 2) ?>%</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Bill Counts</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Total Bills:</th>
                                            <td><?= ($reportData['paid_count'] ?? 0) + ($reportData['pending_count'] ?? 0) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Paid Bills:</th>
                                            <td><?= $reportData['paid_count'] ?? 0 ?></td>
                                        </tr>
                                        <tr>
                                            <th>Pending Bills:</th>
                                            <td><?= $reportData['pending_count'] ?? 0 ?></td>
                                        </tr>
                                        <tr>
                                            <th>Payment Completion:</th>
                                            <td>
                                                <?php
                                                $totalBills = ($reportData['paid_count'] ?? 0) + ($reportData['pending_count'] ?? 0);
                                                $completionRate = $totalBills > 0 ? (($reportData['paid_count'] ?? 0) / $totalBills) * 100 : 0;
                                                echo number_format($completionRate, 2) . '%';
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
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