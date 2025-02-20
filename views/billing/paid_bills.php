<?php
$pageTitle = "Paid Bills";
require_once '../../config/database.php';
require_once '../../classes/Billing.php';

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

// Process filter parameters
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$patientId = $_GET['patient_id'] ?? null;

$db = new DatabaseConnection();
$billing = new Billing($db);
$paidBills = $billing->getPaidBills($startDate, $endDate, $patientId);
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <?php include '../../includes/alerts.php'; ?>

            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Paid Bills</h3>
                            <div class="card-actions">
                                <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                                    <i class="ti ti-filter me-1"></i> Filter    
                                </button>
                            </div>
                        </div>
                        <div class="collapse" id="filterCollapse">
                            <div class="card-body border-bottom py-3">
                                <form method="GET" action="" class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">End Date</label>
                                        <input type="date" class="form-control" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Patient ID (Optional)</label>
                                        <input type="text" class="form-control" name="patient_id" value=<?= htmlspecialchars($patientId ?? '') ?>" placeholder="Enter patient ID">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary w-100">
                                            Apply Filter
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Bill ID</th>
                                            <th>Patient</th>
                                            <th>Medication</th>
                                            <th>Amount</th>
                                            <th>Payment Method</th>
                                            <th>Payment Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($paidBills)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No paid bills found for the selected criteria</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($paidBills as $bill): ?>
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
                                                    <td><?= htmlspecialchars(number_format($bill['total_amount'], 2)) ?></td>
                                                    <td><?= htmlspecialchars(ucfirst($bill['payment_method'])) ?></td>
                                                    <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($bill['payment_date']))) ?></td>
                                                    <td>
                                                        <a href="../../handlers/billing_handler.php?action=view_receipt&id=<?= htmlspecialchars($bill['id']) ?>"
                                                           class="btn btn-sm button-custom" target="_blank">
                                                            <i class="ti ti-receipt me-1"></i> Receipt
                                                        </a>
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

<?php include '../../includes/footer_scripts.php'; ?>