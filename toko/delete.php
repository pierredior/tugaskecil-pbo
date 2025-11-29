<?php
// toko/delete.php
require_once '../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

// Check if user has admin role
$user = getCurrentUser();
if ($user['role'] !== 'admin') {
    redirect('../403.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
        header('Location: index.php?error=' . urlencode($error));
        exit();
    } else {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $error = 'Invalid toko ID.';
        } else {
            try {
                $pdo = getDBConnection();
                
                // Check if toko exists
                $stmt = $pdo->prepare("SELECT id FROM toko WHERE id = ?");
                $stmt->execute([$id]);
                
                if (!$stmt->fetch()) {
                    $error = 'Toko not found.';
                } else {
                    // Delete the toko
                    $stmt = $pdo->prepare("DELETE FROM toko WHERE id = ?");
                    $result = $stmt->execute([$id]);
                    
                    if ($result) {
                        $success = 'Toko deleted successfully.';
                    } else {
                        $error = 'Failed to delete toko. Please try again.';
                    }
                }
            } catch (PDOException $e) {
                $error = 'An error occurred while deleting the toko. Please try again.';
            }
        }
    }
    
    // Redirect back to index with message
    if (!empty($error)) {
        header('Location: index.php?error=' . urlencode($error));
    } else {
        header('Location: index.php?success=' . urlencode($success));
    }
    exit();
} else {
    // If accessed directly without POST, redirect back
    redirect('index.php');
}