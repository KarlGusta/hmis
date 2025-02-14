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
    
    // Validate input
    $item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
    $new_price = filter_input(INPUT_POST, 'new_price', FILTER_VALIDATE_FLOAT);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);

    if (!$item_id || !$new_price) {
        throw new Exception('Invalid input parameters');
    }

    // Update the price
    $item->updatePrice($item_id, $new_price, $_SESSION['user_id'], $notes);

    // Log the activity
    $activityLogger->logActivity([
        'user_id' => $_SESSION['user_id'],
        'activity_type' => 'PRICE_UPDATE',
        'activity_description' => "Updated price for item ID: $item_id to $new_price",
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'entity_type' => 'item',
        'entity_id' => $item_id,
        'status' => 'success'
    ]);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Price updated successfully'
    ]);

} catch (Exception $e) {
    // Log error
    if (isset($activityLogger)) {
        $activityLogger->logActivity([
            'user_id' => $_SESSION['user_id'] ?? null,
            'activity_type' => 'PRICE_UPDATE',
            'activity_description' => "Failed to update price: " . $e->getMessage(),
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