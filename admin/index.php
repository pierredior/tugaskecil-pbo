<?php
// admin/index.php
require_once '../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

// Check if user has admin role
$user = getCurrentUser();
if ($user['role'] !== 'admin') {
    redirect('../user/index.php');
}

$title = 'Admin Dashboard - Modern Web Store';
include '../views/header.php';
include '../views/topnav.php';
include '../views/sidebar.php';
?>

<div class="content p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Admin Dashboard</h1>
        <p class="text-base-content/70">Welcome back, <?php echo e($user['username']); ?>!</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <?php
        $pdo = getDBConnection();
        
        // Total users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $totalUsers = $stmt->fetch()['count'];
        
        // Total barang
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM barang");
        $totalBarang = $stmt->fetch()['count'];
        
        // Total admin users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $totalAdmins = $stmt->fetch()['count'];
        
        // Total regular users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
        $totalRegularUsers = $stmt->fetch()['count'];
        ?>
        
        <div class="card bg-primary text-primary-content">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold"><?php echo $totalUsers; ?></h2>
                        <p class="text-sm opacity-80">Total Users</p>
                    </div>
                    <i data-lucide="users" class="w-10 h-10 opacity-80"></i>
                </div>
            </div>
        </div>
        
        <div class="card bg-secondary text-secondary-content">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold"><?php echo $totalBarang; ?></h2>
                        <p class="text-sm opacity-80">Total Barang</p>
                    </div>
                    <i data-lucide="package" class="w-10 h-10 opacity-80"></i>
                </div>
            </div>
        </div>
        
        <div class="card bg-accent text-accent-content">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold"><?php echo $totalAdmins; ?></h2>
                        <p class="text-sm opacity-80">Admin Users</p>
                    </div>
                    <i data-lucide="shield-check" class="w-10 h-10 opacity-80"></i>
                </div>
            </div>
        </div>
        
        <div class="card bg-info text-info-content">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold"><?php echo $totalRegularUsers; ?></h2>
                        <p class="text-sm opacity-80">Regular Users</p>
                    </div>
                    <i data-lucide="user" class="w-10 h-10 opacity-80"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <h2 class="card-title flex items-center gap-2">
                <i data-lucide="flash" class="w-5 h-5"></i> Quick Actions
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="../barang/index.php" class="btn btn-outline">
                    <i data-lucide="package" class="w-4 h-4 mr-2"></i> Manage Barang
                </a>
                <a href="../user/index.php" class="btn btn-outline">
                    <i data-lucide="users" class="w-4 h-4 mr-2"></i> Manage Users
                </a>
                <a href="../barang/add.php" class="btn btn-primary">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Barang
                </a>
                <a href="#" class="btn btn-outline">
                    <i data-lucide="settings" class="w-4 h-4 mr-2"></i> Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title flex items-center gap-2">
                <i data-lucide="activity" class="w-5 h-5"></i> Recent Activities
            </h2>
            <div class="overflow-x-auto" id="recent-activities-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody id="activities-table-body">
                        <?php
                        $activities = getRecentActivities(5);
                        foreach ($activities as $activity):
                        ?>
                        <tr>
                            <td><?php echo e($activity['username']); ?></td>
                            <td><?php echo ucfirst(e($activity['action'])); ?></td>
                            <td><?php echo e($activity['description']); ?></td>
                            <td><?php echo date('d M Y H:i', strtotime($activity['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to update activities in real-time
    function updateRecentActivities() {
        fetch('../api/activities.php?limit=5')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('activities-table-body');
            if (data.success && data.activities) {
                tbody.innerHTML = '';
                data.activities.forEach(activity => {
                    const formattedDate = new Date(activity.created_at).toLocaleString();
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${activity.username}</td>
                        <td>${activity.action.charAt(0).toUpperCase() + activity.action.slice(1)}</td>
                        <td>${activity.description}</td>
                        <td>${formattedDate}</td>
                    `;
                    tbody.appendChild(row);
                });
            }
        })
        .catch(error => console.error('Error fetching activities:', error));
    }

    // Update activities every 30 seconds
    setInterval(updateRecentActivities, 30000);

    // Also update when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateRecentActivities();
        lucide.createIcons();
    });
</script>

<?php include '../views/footer.php'; ?>