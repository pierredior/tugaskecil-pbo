<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is not logged in, redirect to login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$userRole = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .error-code {
            font-size: 4rem;
            color: #e74c3c;
            margin: 0;
        }
        .error-message {
            font-size: 1.5rem;
            color: #333;
            margin: 1rem 0;
        }
        .user-role {
            font-size: 1rem;
            color: #666;
            margin-bottom: 1.5rem;
        }
        .btn {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">403</h1>
        <h2 class="error-message">Access Denied</h2>
        <p class="user-role">You are logged in as: <strong><?php echo htmlspecialchars(ucfirst($userRole)); ?></strong></p>
        <p>You don't have permission to access this resource.</p>
        <a href="<?php
            // Redirect based on user role to their specific dashboard
            switch($userRole) {
                case 'admin': echo 'admin/index.php'; break;
                case 'user': echo 'user/index.php'; break;
                default: echo 'login.php';
            }
        ?>" class="btn">Go to Dashboard</a>
    </div>
</body>
</html>