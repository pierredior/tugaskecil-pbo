<?php
// logout.php
require_once 'config/database.php';

// Log the logout activity if user was logged in
if (isLoggedIn()) {
    logActivity('logout', 'User logged out successfully');
}

// Destroy all session data
session_destroy();

// Redirect to login page
header('Location: login.php');
exit();