<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Create New Bill</h3>
                        </div>
                        <div class="card-body">
                            <form action="../../handlers/create_bill.php" method="POST">
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label required">Patient</label>
                                        <select class="form-select" name="patient_id" required>
                                            <option value="">Select Patient</option>
                                            <?php
                                            require_once '../../classes/Patient.php';
                                            require_once '../../config/database.php';

                                            $db = new DatabaseConnection();
                                            $patient = new Patient($db);
                                            $patients = $patient->getAllPatients();
                                            foreach($patients as $p) {
                                                echo "<option value='{$p['id']}'>{$p['first_name']} {$p['last_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Appointment</label>
                                        <select class="form-select" name="appointment_id">
                                            <option value="">Select Appointment</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="bill_items">
                                    <div class="row mb-3 bill-item">
                                        <div class="col-sm-3">
                                            <label class="form-label required">Item Type</label>
                                            <select class="form-select item-type" name="items[0][type]" required>
                                                <option value="">Select Type</option>
                                                <option value="consultation">Consultation</option>
                                                <option value="laboratory">Laboratory Test</option>
                                                <option value="medication">Medication</option>
                                                <option value="procedure">Procedure</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3">
                                            <label class="form-label required">Item</label>
                                            <select class="form-select item-select" name="items[0][item_id]" required>
                                                <option value="">Select Item</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="form-label required">Quantity</label>
                                            <input type="number" class="form-control item-quantity" name="items[0][quantity]" required min="1" value="1">
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="form-label">Unit Price</label>
                                            <input type="number" class="form-control item-price" name="items[0][unit_price]" readonly>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="form-label">Subtotal</label>
                                            <input type="number" class="form-control item-subtotal" readonly>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-outline-primary mb-3" onclick="addBillItem()">
                                    Add Item
                                </button>

                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4>Bill Summary</h4>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Total Amount:</span>
                                                    <span id="total_amount">0.00</span>
                                                </div>
                                                <input type="hidden" name="total_amount" id="total_amount_input">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="btn btn-primary">Create Bill</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../../includes/main_footer.php'; ?>
</div>