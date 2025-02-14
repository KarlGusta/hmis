<?php
require_once '../config/database.php';
require_once '../classes/Item.php';
require_once '../classes/ActivityLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $db = new DatabaseConnection();
    $item = new Item($db);
    $activityLogger = new ActivityLogger($db);

    // Sanitize and validate input data
    $data = [
        'item_code' => filter_input(INPUT_POST, 'item_code', FILTER_SANITIZE_STRING),
        'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
        'description' => filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING),
        'category_id' => filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT),
        'unit' => filter_input(INPUT_POST, 'unit', FILTER_SANITIZE_STRING),
        'current_price' => filter_input(INPUT_POST, 'current_price', FILTER_VALIDATE_FLOAT),
        'reorder_level' => filter_input(INPUT_POST, 'reorder_level', FILTER_VALIDATE_INT),
        'status' => filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING),
        'user_id' => $_SESSION['user_id']
    ]; 

    // Validate required fields
    $required_fields = ['item_code', 'name', 'category_id', 'unit', 'current_price'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            throw new Exception("$field is required");
        }
    }

    $item_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $action_type = $item_id ? 'UPDATE' : 'CREATE';

    if ($item_id) {
        // Update existing item
        $data['id'] = $item_id;
        $item->updateItem($data);
        $message = "Item updated successfully";
    } else {
        // Create new item
        $item_id = $item->addItem($data);
        $message = "Item added successfully";
    }

    // Log the activity
    $activityLogger->logActivity([
        'user_id' => $_SESSION['user_id'],
        'activity_type' => 'ITEM_' . $action_type,
        'activity_description' => "$message (Item ID: $item_id)",
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'entity_type' => 'item',
        'entity_id' => $item_id,
        'status' => 'success'
    ]);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => $message,
        'item_id' => $item_id
    ]);

} catch (Exception $e) {
    // Log error
    if(isset($activityLogger)) {
        $activityLogger->logActivity([
            'user_id' => $_SESSION['user_id'] ?? null,
            'activity_type' => 'ITEM_' . ($item_id ? 'UPDATE' : 'CREATE'),
            'activity_description' => "Failed to save item: " . $e->getMessage(),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'entity_type' => 'item',
            'entity_id' => $item_id ?? null,
            'status' => 'error'
        ]);
    }

    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
?>