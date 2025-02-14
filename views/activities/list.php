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
                            <h3 class="card-title">User Activity Log</h3>
                            <div class="card-actions">
                                <div class="btn-group">
                                    <button type="button" class="btn button-custom dropdown-toggle" data-bs-toggle="dropdown">
                                        Filter by Type
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="?type=all">All Activities</a>
                                        <a class="dropdown-item" href="?type=login">Login Activities</a>
                                        <a class="dropdown-item" href="?type=doctor">Doctor Activities</a>
                                        <a class="dropdown-item" href="?type=patient">Patient Activities</a>
                                        <a class="dropdown-item" href="?type=appointment">Appointment Activities</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>User</th>
                                            <th>Activity Type</th>
                                            <th>Description</th>
                                            <th>IP Address</th>
                                            <th>Status</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once '../../config/database.php';
                                        require_once '../../classes/ActivityLogger.php';

                                        $db = new DatabaseConnection();
                                        $logger = new ActivityLogger($db);

                                        // Get filter type from URL
                                        $activityType = $_GET['type'] ?? 'all';

                                        // Prepare the query based on filter
                                        $query = "SELECT ua.*, u.username 
                                                 FROM user_activities ua 
                                                 LEFT JOIN users u ON ua.user_id = u.id";
                                                 
                                        if ($activityType !== 'all') {
                                            $query .= " WHERE ua.activity_type LIKE :type";
                                            $stmt = $db->conn->prepare($query);
                                            $typeFilter = strtoupper($activityType) . "%";
                                            $stmt->bindParam(':type', $typeFilter);
                                        } else {
                                            $query .= " ORDER BY ua.created_at DESC LIMIT 100";
                                            $stmt = $db->conn->prepare($query);
                                        }

                                        if (!$stmt) {
                                            die("Query preparation failed: " . $db->conn->error);
                                        }

                                        $stmt->execute();
                                        $activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                                        foreach ($activities as $activity): ?> 
                                        <tr>
                                            <td><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($activity['created_at']))) ?></td>
                                            <td><?= htmlspecialchars($activity['username'] ?? 'Unknown') ?></td>
                                            <td>
                                                <span class="badge button-custom-sm">
                                                    <?= htmlspecialchars($activity['activity_type']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($activity['activity_description']) ?></td>
                                            <td><?= htmlspecialchars($activity['ip_address']) ?></td>
                                            <td>
                                                <span class="badge <?= $activity['status'] === 'success' ? 'button-custom-black-sm' : 'button-custom-sm' ?>">
                                                    <?= htmlspecialchars($activity['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button"
                                                        class="btn btn-sm button-custom-white-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#activityModal<?= $activity['id'] ?>">
                                                    View Details
                                                    </button>

                                                    <!-- Modal for activity details -->
                                                     <div class="modal fade" id="activityModal<?= $activity['id'] ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Activity Details</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <dl class="row">
                                                                        <dt class="col-5">Timestamp:</dt>
                                                                        <dd class="col-7"><?= htmlspecialchars($activity['created_at']) ?></dd>

                                                                        <dt class="col-5">User Agent:</dt>
                                                                        <dd class="col-7"><?= htmlspecialchars($activity['user_agent']) ?></dd>

                                                                        <dt class="col-5">Entity Type:</dt>
                                                                        <dd class="col-7"><?= htmlspecialchars($activity['entity_type']) ?></dd>

                                                                        <dt class="col-5">Entity ID:</dt>
                                                                        <dd class="col-7"><?= htmlspecialchars($activity['entity_id']) ?></dd>

                                                                        <dt class="col-5">Description:</dt>
                                                                        <dd class="col-7"><?= htmlspecialchars($activity['activity_description']) ?></dd>
                                                                    </dl>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn button-custom" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                     </div>
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