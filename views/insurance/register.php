<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Register Insurance Provider</h3>
                        </div>
                        <div class="card-body">
                            <form action="../../handlers/register_insurance.php" method="POST">
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label required">Provider Name</label>
                                        <input type="text" class="form-control" name="provider_name" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label required">Contact Number</label>
                                        <input type="tel" class="form-control" name="contact_number" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label required">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label required">Website</label>
                                        <input type="url" class="form-control" name="website">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label required">Address</label>
                                        <textarea class="form-control" name="address" row="2" required></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label">Coverage Types</label>
                                        <textarea class="form-control" name="coverage_types" rows="2"></textarea>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Policy Details</label>
                                        <textarea class="form-control" name="policy_details" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="form-footer mt-4">
                                    <button type="submit" class="btn btn-primary button-custom">Register Provider</button>
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

<?php include '../../includes/footer_scripts.php'; ?>