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
                            <h3 class="card-title">System Users</h3>
                            <div class="card-actions">
                                <a href="register.php" class="btn button-custom">Add New User</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once '../../config/database.php';

                                        $db = new DatabaseConnection();
                                        $stmt = $db->conn->prepare(
                                            "SELECT u.*, d.name as department_name,
                                                    GROUP_CONCAT(r.name) as role
                                             FROM users u 
                                             LEFT JOIN departments d ON u.department_id = d.id 
                                             LEFT JOIN user_roles ur ON u.id = ur.user_id
                                             LEFT JOIN roles r ON ur.role_id = r.id
                                             GROUP BY u.id, u.username, u.email, u.first_name, u.last_name, u.status, u.created_at, d.name
                                             ORDER BY u.created_at DESC"
                                        );
                                        $stmt->execute();
                                        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                                        foreach ($users as $user): 
                                            // Convert role string to array and format for display
                                            $roles = explode(',', $user['role']);
                                            $formatted_roles = array_map('ucfirst', $roles);
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                            <td><?= htmlspecialchars($user['username']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <?php foreach ($formatted_roles as $role): ?>
                                                    <span class="badge button-custom-black-sm me-1">
                                                        <?= htmlspecialchars($role) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </td>
                                            <td><?= htmlspecialchars($user['department_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge <?= $user['status'] === 'active' ? 'button-custom-black-sm' : 'button-custom-sm' ?>">
                                                    <?= htmlspecialchars($user['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="view.php?id=<?= $user['id'] ?>" class="btn btn-sm button-custom-white-sm">View</a>
                                                <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-sm button-custom-white-sm">Edit</a>
                                                <button onclick="toggleUserStatus('<?= $user['id'] ?>')" class="btn btn-sm <?= $user['status'] === 'active' ? 'button-custom-black-sm' : 'button-custom-sm' ?>">
                                                    <?= $user['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
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

<script>
function toggleUserStatus(userId) {
    if (confirm('Are you sure you want to change this user\'s status?')) {
        fetch('../../handlers/toggle_user_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'user_id=' + userId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request.');
        });
    }
}
</script>

<?php include '../../includes/footer_scripts.php'; ?> 