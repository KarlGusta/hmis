<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Insurance Providers</h3>
                            <div class="card-actions">
                                <a href="register.php" class="btn button-custom">Add New Provider</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Provider ID</th>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once '../../classes/InsuranceProvider.php';
                                        require_once '../../config/database.php';

                                        $db = new DatabaseConnection();
                                        $provider = new InsuranceProvider($db);
                                        $providers = $provider->getAllProviders();

                                        foreach ($providers as $p): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p['provider_id']) ?></td>
                                            <td><?= htmlspecialchars($p['provider_name']) ?></td>
                                            <td><?= htmlspecialchars($p['contact_number']) ?></td>
                                            <td><?= htmlspecialchars($p['email']) ?></td>
                                            <td>
                                                <span class="badge <?= $p['status'] === 'active' ? 'button-custom-black-sm' : 'button-custom-sm' ?>">
                                                    <?= htmlspecialchars($p['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="view.php?id=<?= $p['provider_id'] ?>" class="btn btn-sm button-custom-white-sm">View</a>
                                                <a href="edit.php?id=<?= $p['provider_id'] ?>" class="btn btn-sm button-custom-white-sm">Edit</a>
                                                <button onclick="toggleStatus('<?= $p['provider_id'] ?>')" class="btn btn-sm <?= $p['status'] === 'active' ? 'button-custom-black-sm' : 'button-custom-sm' ?>">
                                                    <?= $p['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../../includes/main_footer.php'; ?>
</div>

<?php include '../../includes/footer_scripts.php'; ?>