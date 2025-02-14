<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-card">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Bills</h3>
                            <div class="card-actions">
                                <a href="create.php" class="btn btn-primary">
                                    Create New Bill
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Bill ID</th>
                                            <th>Patient</th>
                                            <th>Date</th>
                                            <th>Total Amount</th>
                                            <th>Paid Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once '../../classes/Billing.php';
                                        require_once '../../classes/Patient.php';

                                        $db = new DatabaseConnection();
                                        $billing = new Billing($db);
                                        $bills = $billing->getAllBills();

                                        foreach ($bills as $bill) {
                                            echo "<tr>";
                                            echo "<td>{$bill['id']}</td>";
                                            echo "<td>{$bill['patient_name']}</td>";
                                            echo "<td>{$bill['bill_date']}</td>";
                                            echo "<td>" . number_format($bill['total_amount'], 2) . "</td>";
                                            echo "<td>" . number_format($bill['paid_amount'], 2) . "</td>";
                                            echo "<td><span class='badge bg-" . getBadgeColor($bill['status']) . "'>{$bill['status']}</span></td>";
                                            echo "<td>
                                        <a href='view.php?id={$bill['id']}' class='btn btn-sm btn-primary'>View</a>
                                        <a href='process_payment.php?id={$bill['id']}' class='btn btn-sm btn-success'>Process Payment</a>
                                      </td>";
                                            echo "</tr>";
                                        }

                                        function getBadgeColor($status)
                                        {
                                            switch ($status) {
                                                case 'paid':
                                                    return 'success';
                                                case 'pending':
                                                    return 'warning';
                                                case 'cancelled':
                                                    return 'danger';
                                                default:
                                                    return 'secondary';
                                            }
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
</div>