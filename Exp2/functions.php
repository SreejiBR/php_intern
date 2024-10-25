<?php
require 'config.php';
// Redirect if not authenticated
function isAuthenticated() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
    }
}
// Sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}
// Generate CSRF token
function generateToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
// Verify CSRF token
function verifyToken($token) {
    return $token === $_SESSION['csrf_token'];
}
?>
