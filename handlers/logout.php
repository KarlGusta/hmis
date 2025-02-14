<?php
session_start();
require_once '../config/paths.php';

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: ' . path('views', 'login.php'));
exit;