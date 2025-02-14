<?php
$pageTitle = "Waiting Room";

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

require_once '../../classes/PatientQueue.php';
require_once '../../config/database.php';
require_once '../../classes/Room.php';

$db = new DatabaseConnection();
$queue = new PatientQueue($db);

// Get current queue with error handling
try {
    $currentQueue = $queue->getCurrentQueue();
    
    // Group queue by department
    $queueByDepartment = [];
    if (!empty($currentQueue)) {
        foreach ($currentQueue as $item) {
            $queueByDepartment[$item['department_name']][] = [
                'queue_number' => $item['queue_number'],
                'patient_name' => $item['patient_name'],
                'priority' => $item['priority'],
                'doctor_name' => $item['doctor_name'],
                'wait_time' => $item['wait_time'],
                'room_number' => $item['room_number'] ?? null,
                'patient_id' => $item['patient_id'] ?? null,
                'queue_id' => $item['id'] ?? null
            ];
        }
    }
    
    // Log successful queue retrieval
    error_log("Queue retrieved successfully. Count: " . count($currentQueue));
} catch (Exception $e) {
    error_log("Error retrieving queue: " . $e->getMessage());
    $queueByDepartment = [];
}

// Debug output with proper error handling
if (empty($currentQueue)) {
    error_log("Queue is empty at " . date('Y-m-d H:i:s'));
    if (isset($db->mysqli)) {
        error_log("Database connection status: " . ($db->mysqli->ping() ? 'Connected' : 'Disconnected'));
    }
}

// Additional debug output to see what data we're getting
echo "<!-- Debug: Current Queue Data: " . print_r($currentQueue, true) . " -->";
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Waiting Room Queue</h3>
                            <div class="card-actions">
                                <a href="../appointments/check_in.php" class="btn button-custom">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    Check In New Patient
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                            <?php endif; ?>

                            <?php if (empty($queueByDepartment)): ?>
                            <div class="empty">
                                <div class="empty-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="12" cy="12" r="9" />
                                        <line x1="9" y1="10" x2="9.01" y2="10" />
                                        <line x1="15" y1="10" x2="15.01" y2="10" />
                                        <line x1="9" y1="15" x2="15" y2="15" />
                                    </svg>
                                </div>
                                <p class="empty-title">No patients in queue</p>
                                <p class="empty-subtitle text-muted">
                                    There are currently no patients waiting to be seen.
                                </p>
                            </div>
                            <?php else: ?>
                            <?php foreach ($queueByDepartment as $department => $patients): ?>
                            <div class="mb-4">
                                <h4><?= htmlspecialchars($department) ?></h4>
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th>Queue Number</th>
                                                <th>Patient Name</th>
                                                <th>Priority</th>
                                                <th>Doctor</th>
                                                <th>Wait Time</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($patients as $patient): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($patient['queue_number']) ?></td>
                                                <td><?= htmlspecialchars($patient['patient_name']) ?></td>
                                                <td>
                                                    <span class="badge button-custom-sm">
                                                        <?= ucfirst(htmlspecialchars($patient['priority'])) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($patient['doctor_name']) ?></td>
                                                <td><?= $patient['wait_time'] ?> mins</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <span
                                                            class="badge <?= $patient['room_number'] ? 'button-custom-black-sm' : 'button-custom-sm' ?>">
                                                            <?php if ($patient['room_number']): ?>
                                                            Room <?= htmlspecialchars($patient['room_number']) ?>
                                                            <?php else: ?>
                                                            Waiting
                                                            <?php endif; ?>
                                                        </span>
                                                        <?php if (!$patient['room_number']): ?>
                                                        <button type="button" class="btn btn-sm button-custom"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#startConsultationModal"
                                                            onclick="prepareConsultation('<?= htmlspecialchars($patient['patient_id']) ?>', '<?= htmlspecialchars($patient['queue_id']) ?>')">
                                                            Start Consultation
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../../includes/main_footer.php'; ?>
</div>

<!-- Modal -->
<div class="modal" id="startConsultationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../handlers/start_consultation.php" method="POST">
                <input type="hidden" name="patient_id" id="modalPatientId">
                <input type="hidden" name="queue_id" id="modalQueueId">

                <div class="modal-header">
                    <h5 class="modal-title">Start Consultation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Select Room</label>
                        <select class="form-select" name="room_id" required>
                            <option value="">Choose a room...</option>
                            <?php
                            $room = new Room($db);
                            try {
                                // Get department_id from the queue item or user session
                                $department_id = isset($_SESSION['department_id']) ? $_SESSION['department_id'] : 
                                    (isset($currentQueue[0]['department_id']) ? $currentQueue[0]['department_id'] : null);
                                
                                if ($department_id) {
                                    $availableRooms = $room->getAvailableRooms($department_id);
                                    foreach ($availableRooms as $availableRoom):
                            ?>
                            <option value="<?= htmlspecialchars($availableRoom['id']) ?>">
                                Room <?= htmlspecialchars($availableRoom['room_number']) ?>
                                (<?= ucfirst(htmlspecialchars($availableRoom['room_type'])) ?>)
                            </option>
                            <?php
                                    endforeach;
                                } else {
                                    error_log("No department_id available for room selection");
                                }
                            } catch (Exception $e) {
                                error_log("Error loading available rooms: " . $e->getMessage());
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Initial Notes</label>
                        <textarea class="form-control" name="initial_notes" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn button-custom">Start Consultation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer_scripts.php'; ?>

<script>
// Refresh the page every 30 seconds to update the queue
setTimeout(function() {
    location.reload();
}, 30000);
</script>