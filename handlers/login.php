<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../config/paths.php';

try {
    $db = new DatabaseConnection();
    $auth = new Auth($db);

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        throw new Exception("Username and password are required");
    }

    $user = $auth->login($username, $password);

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['logged_in'] = true;

    header('Location: ' . path('views', 'queue') . 'waiting_room.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ' . path('auth', 'login'));
    exit;
}