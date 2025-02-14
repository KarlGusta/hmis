<?php
function set_message($type, $message) {
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['message'] = [
        'type' => $type,
        'text' => $message
    ];
}

function get_message() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return null;
} 