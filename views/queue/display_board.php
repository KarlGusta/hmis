<?php
require_once '../../classes/PatientQueue.php';
require_once '../../config/database.php';

$db = new DatabaseConnection();
$queue = new PatientQueue($db);
$currentQueue = $queue->getCurrentQueue();

// Organize queue by department
$queueByDepartment = [];
foreach ($currentQueue as $patient) {
    $departmentName = $patient['department_name'];
    if (!isset($queueByDepartment[$departmentName])) {
        $queueByDepartment[$departmentName] = [];
    }
    $queueByDepartment[$departmentName][] = $patient;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Display Board</title>
    <link href="../../assets/css/tabler.min.css" rel="stylesheet" />
    <style>
    body {
        background: #f5f7fb;
    }

    .queue-board {
        font-size: 1.2em;
        margin: 20px;
    }

    .priority-emergency {
        background-color: #fce9e9 !important;
    }

    .priority-urgent {
        background-color: #fff3cd !important;
    }

    .queue-number {
        font-size: 1.8em;
        font-weight: bold;
    }

    .department-title {
        background: #206bc4;
        color: white;
        padding: 10px;
        margin-bottom: 15px;
    }

    .patient-card {
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .patient-status {
        font-size: 1.1em;
        color: #666;
    }

    .patient-room {
        font-size: 1.2em;
        font-weight: 500;
    }
    </style>
</head>

<body>
    <div class="container-fluid queue-board">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h1>Patient Queue Status</h1>
                <div class="text-muted">Last Updated: <?= date('h:i A') ?></div>
            </div>
        </div>

        <div class="row">
            <?php foreach ($queueByDepartment as $department => $patients): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="department-title">
                        <h3 class="m-0"><?= htmlspecialchars($department) ?></h3>
                    </div>
                    <div class="card-body">
                        <?php foreach ($patients as $patient): ?>
                        <div class="patient-card priority-<?= htmlspecialchars($patient['priority']) ?>">
                            <div class="row align-items-center">
                                <div class="col-4">
                                    <div class="queue-number"><?= htmlspecialchars($patient['queue_number']) ?></div>
                                </div>
                                <div class="col-4">
                                    <div class="patient-status">
                                        <?= $patient['room_number'] ? 'Called' : 'Waiting' ?>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="patient-room">
                                        <?= $patient['room_number'] ? 'Room ' . htmlspecialchars($patient['room_number']) : '-' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    // Refresh the page every 30 seconds
    setTimeout(function() {
        location.reload();
    }, 30000);
    </script>
</body>

</html>