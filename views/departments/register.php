<?php
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Register Department</h3>
                        </div>
                        <div class="card-body">
                            <form action="../../handlers/register_department.php" method="POST">
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label">Department Code</label>
                                        <input type="text" class="form-control" name="code" placeholder="Leave blank for auto-generation">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label required">Department Name</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="btn button-custom">Register Department</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer_scripts.php'; ?>