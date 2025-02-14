<?php
$pageTitle = "Room List";

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

require_once '../../classes/Room.php';
require_once '../../classes/Department.php';
require_once '../../config/database.php';

$db = new DatabaseConnection();
$room = new Room($db);
$department = new Department($db);

// Get filters from query parameters
$departmentFilter = $_GET['department_id'] ?? 'all';
$statusFilter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

try {
    // Get all departments for filter dropdown
    $departments = $department->getAllDepartments();

    // Get rooms with filters
    $rooms = $room->getAllRooms($departmentFilter, $statusFilter, $search);
} catch (Exception $e) {
    $_SESSION['error'] = "Error loading rooms: " . $e->getMessage();
    $rooms = [];
    $departments = [];
}
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <!-- Filter Card -->
                    <div class="card mb-3 card-custom">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" class="form-select">
                                        <option value="all">All Departments</option>
                                        <?php foreach ($departments as $dept): ?>
                                        <option value="<?= htmlspecialchars($dept['id']) ?>"
                                            <?= $departmentFilter === $dept['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dept['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="all">All Status</option>
                                        <option value="available" <?= $statusFilter === 'available' ? 'selected' : '' ?>>
                                            Available</option>
                                        <option value="occupied" <?= $statusFilter === 'occupied' ? 'selected' : '' ?>>Occupied
                                        </option>
                                        <option value="maintenance" <?= $statusFilter === 'maintenance' ? 'selected' : '' ?>>
                                            Maintenance</option>
                                        <option value="reserved" <?= $statusFilter === 'reserved' ? 'selected' : '' ?>>Reserved
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        value="<?= htmlspecialchars($search) ?>" placeholder="Room number...">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn button-custom w-100">
                                        Apply Filters
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Rooms List Card -->
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Rooms</h3>
                            <div class="card-actions">
                                <a href="add.php" class="btn button-custom">Add New Room</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Room Number</th>
                                            <th>Department</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Current Patient</th>
                                            <th>Features</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($rooms)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                No rooms found matching the criteria
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($rooms as $roomItem): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($roomItem['room_number']) ?></td>
                                            <td><?= htmlspecialchars($roomItem['department_name']) ?></td>
                                            <td><?= ucfirst(htmlspecialchars($roomItem['room_type'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= getStatusColor($roomItem['status']) ?>">
                                                    <?= ucfirst(htmlspecialchars($roomItem['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($roomItem['current_patient_id']): ?>
                                                <?= htmlspecialchars($roomItem['patient_name']) ?>
                                                <?php else: ?>
                                                -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($roomItem['features'] ?: '-') ?>
                                            </td>
                                            <td>
                                                <div class="btn-list">
                                                    <a href="edit_room.php?id=<?= $roomItem['id'] ?>" 
                                                        class="btn btn-sm button-custom-white-sm">Edit</a>
                                                    <a href="room_history.php?id=<?= $roomItem['id'] ?>" 
                                                        class="btn btn-sm button-custom-white-sm">History</a>
                                                    <?php if ($roomItem['status'] === 'occupied'): ?>
                                                    <form action="../../handlers/release_room.php" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to release this room?')">
                                                        <input type="hidden" name="room_id" value="<?= $roomItem['id'] ?>">
                                                        <button type="submit" class="btn btn-sm button-custom-sm">
                                                            Release
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                </div>
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
    <?php include '../../includes/main_footer.php'; ?>
</div>

<?php include '../../includes/footer_scripts.php'; ?>

<?php
function getStatusColor($status) {
    $colors = [
        'available' => 'button-custom-white',
        'occupied' => 'button-custom-black',
        'maintenance' => 'button-custom-white',
        'reserved' => 'button-custom-white'
    ];
    return $colors[$status] ?? 'secondary';
}
?>