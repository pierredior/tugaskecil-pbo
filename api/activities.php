<?php
// api/activities.php
require_once '../config/database.php';

// Only allow authenticated users
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get parameters
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    
    try {
        $activities = getRecentActivities($limit);
        
        // Format the date to be more JavaScript-friendly
        foreach ($activities as &$activity) {
            $activity['created_at'] = date('Y-m-d H:i:s', strtotime($activity['created_at']));
        }
        
        echo json_encode([
            'success' => true,
            'activities' => $activities
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Database error occurred'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}