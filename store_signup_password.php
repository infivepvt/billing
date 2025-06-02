<?php
session_start();

// Ensure the request is POST and contains the password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup_password'])) {
    $password = trim($_POST['signup_password']);
    if (!empty($password)) {
        // Store the password in the session
        $_SESSION['signup_password'] = $password;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Password cannot be empty']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
exit;
?>