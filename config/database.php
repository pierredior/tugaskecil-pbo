<?php
// config/database.php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'web_store_app');
define('DB_USER', 'root');  // Change this to your MySQL username
define('DB_PASS', '');      // Change this to your MySQL password

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// CSRF Token functions
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Redirect function
function redirect($location) {
    if (!headers_sent()) {
        header("Location: $location");
        exit();
    } else {
        // If headers are already sent, use JavaScript as fallback
        echo '<script type="text/javascript">';
        echo 'window.location.href="' . addslashes($location) . '";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($location) . '">';
        echo '</noscript>';
        exit();
    }
}

// Escape function for output
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get current user info
function getCurrentUser() {
    if (isLoggedIn()) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

// Check user role
function hasRole($role) {
    $user = getCurrentUser();
    return $user && $user['role'] === $role;
}

// Check if user has any of the specified roles
function hasAnyRole($roles) {
    $user = getCurrentUser();
    if (!$user) return false;
    return in_array($user['role'], (array)$roles);
}

// Sanitize input
function sanitizeInput($input) {
    return trim(filter_var($input, FILTER_SANITIZE_STRING));
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Get all categories
function getAllCategories() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
    return $stmt->fetchAll();
}

// Get all suppliers
function getAllSuppliers() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM supplier ORDER BY nama_supplier ASC");
    return $stmt->fetchAll();
}

// Get all tokos
function getAllToko() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM toko ORDER BY nama_toko ASC");
    return $stmt->fetchAll();
}

// Log user activity
function logActivity($action, $description = '') {
    // Only try to log if session has started and user is logged in
    if (session_status() !== PHP_SESSION_ACTIVE || !isLoggedIn()) return;

    try {
        $pdo = getDBConnection();
        $user = getCurrentUser();

        if (!$user) return; // Exit if we can't get user info

        $stmt = $pdo->prepare("INSERT INTO activities (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $user['id'],
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch (Exception $e) {
        // Silently fail to avoid header issues - activity logging is non-critical
        error_log("Activity logging failed: " . $e->getMessage());
    }
}

// Get recent activities for dashboard
function getRecentActivities($limit = 5) {
    $pdo = getDBConnection();

    // If user is admin, show all activities; otherwise, show only user's activities
    $user = getCurrentUser();
    if ($user && $user['role'] === 'admin') {
        $stmt = $pdo->prepare("
            SELECT a.*, u.username
            FROM activities a
            JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
    } else {
        $stmt = $pdo->prepare("
            SELECT a.*, u.username
            FROM activities a
            JOIN users u ON a.user_id = u.id
            WHERE a.user_id = ?
            ORDER BY a.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$user['id'], $limit]);
    }

    return $stmt->fetchAll();
}