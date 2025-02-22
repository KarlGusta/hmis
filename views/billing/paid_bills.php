<?php
$pageTitle = "Paid Bills";
require_once '../../config/database.php';
require_once '../../classes/Billing.php';

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

$db = new DatabaseConnection();
$billing = new Billing($db);

// Get filter parameters
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$patientId = $_GET['patient_id'] ?? null;

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
                        </div>
                        <div class="card-body">
                            <!-- Filter Form -->
                            <form class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" 
                                           value="<?= htmlspecialchars($startDate) ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="end_date" 
                                           value="<?= htmlspecialchars($endDate) ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Patient ID (Optional)</label>
                                    <input type="text" class="form-control" name="patient_id" 
                                           value="<?= htmlspecialchars($patientId ?? '') ?>" 
                                           placeholder="Enter patient ID">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn button-custom d-block">Filter</button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <form id="receiptForm" method="get" action="receipt.php">
                                    <div class="mb-3">
                                        <button type="submit" class="btn button-custom btn-sm" id="generateCombinedReceipt" disabled>
                                            Generate Combined Receipt
                                        </button>
                                    </div>
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                                <th>Bill ID</th>
                                                <th>Patient</th>
                                                <th>Item</th>
                                                <th>Amount</th>
                                                <th>Payment Method</th>
                                                <th>Payment Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($paidBills)): ?>
                                                <tr>
                                                    <td colspan="8" class="text-center">No paid bills found for the selected period</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($paidBills as $bill): ?>
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" name="selected_bills[]" 
                                                                   value="<?= htmlspecialchars($bill['id']) ?>" 
                                                                   class="form-check-input bill-checkbox"
                                                                   data-patient="<?= htmlspecialchars($bill['patient_number']) ?>">
                                                        </td>
                                                        <td><?= htmlspecialchars($bill['id']) ?></td>
                                                        <td>
                                                            <?= htmlspecialchars($bill['first_name'] . ' ' . $bill['last_name']) ?>
                                                            <div class="text-muted"><?= htmlspecialchars($bill['patient_number']) ?></div>
                                                        </td>
                                                        <td>
                                                            <?= htmlspecialchars($bill['medication_name']) ?>
                                                            <?php if ($bill['form'] && $bill['strength']): ?>
                                                                <div class="text-muted">
                                                                    <?= htmlspecialchars($bill['form']) ?> - <?= htmlspecialchars($bill['strength']) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= htmlspecialchars(number_format($bill['total_amount'], 2)) ?></td>
                                                        <td>
                                                            <?= htmlspecialchars(ucfirst($bill['payment_method'])) ?>
                                                            <?php if ($bill['payment_reference']): ?>
                                                                <div class="text-muted">
                                                                    Ref: <?= htmlspecialchars($bill['payment_reference']) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($bill['payment_date']))) ?></td>
                                                        <td>
                                                            <a href="receipt.php?id=<?= htmlspecialchars($bill['id']) ?>" 
                                                               class="btn btn-sm button-custom-white-sm">
                                                                View Receipt
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const billCheckboxes = document.querySelectorAll('.bill-checkbox');
    const generateButton = document.getElementById('generateCombinedReceipt');
    const receiptForm = document.getElementById('receiptForm');

    // Handle "Select All" checkbox
    selectAllCheckbox.addEventListener('change', function() {
        const firstPatientId = document.querySelector('.bill-checkbox')?.dataset.patient;
        billCheckboxes.forEach(checkbox => {
            if (checkbox.dataset.patient === firstPatientId) {
                checkbox.checked = this.checked;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });

    // Handle individual checkboxes
    billCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const selectedCheckboxes = document.querySelectorAll('.bill-checkbox:checked');
            const selectedPatients = new Set(
                Array.from(selectedCheckboxes).map(cb => cb.dataset.patient)
            );

            // Enable button only if at least one bill is selected and all selected bills belong to same patient
            generateButton.disabled = selectedCheckboxes.length === 0 || selectedPatients.size > 1;

            // Update select all checkbox state
            const firstPatientCheckboxes = Array.from(billCheckboxes)
                .filter(cb => cb.dataset.patient === this.dataset.patient);
            const firstPatientSelected = firstPatientCheckboxes
                .every(cb => cb.checked);
            selectAllCheckbox.checked = firstPatientSelected;
        });
    });

    // Handle form submission
    receiptForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const selectedBills = Array.from(document.querySelectorAll('.bill-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        if (selectedBills.length > 0) {
            window.location.href = 'receipt.php?id=' + selectedBills.join(',');
        }
    });
});
</script>

<?php include '../../includes/footer_scripts.php'; ?> 