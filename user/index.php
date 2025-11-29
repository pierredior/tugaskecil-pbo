<?php
// user/index.php
require_once '../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

// Check if user has admin role, redirect to admin dashboard if so
$user = getCurrentUser();
if ($user['role'] === 'admin') {
    redirect('../admin/index.php');
}

$title = 'User Dashboard - Modern Web Store';
include '../views/header.php';
include '../views/topnav.php';
include '../views/sidebar.php';
?>

<div class="content p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">User Dashboard</h1>
        <p class="text-base-content/70">Welcome back, <?php echo e($user['username']); ?>!</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <?php
        $pdo = getDBConnection();
        
        // Total barang
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM barang");
        $totalBarang = $stmt->fetch()['count'];
        
        // Total barang with low stock (less than 5)
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM barang WHERE stok < 5");
        $lowStockCount = $stmt->fetch()['count'];
        
        // Total categories
        $stmt = $pdo->query("SELECT COUNT(DISTINCT kategori) as count FROM barang");
        $totalCategories = $stmt->fetch()['count'];
        ?>
        
        <div class="card bg-primary text-primary-content">
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
        
        <div class="card bg-warning text-warning-content">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold"><?php echo $lowStockCount; ?></h2>
                        <p class="text-sm opacity-80">Low Stock Items</p>
                    </div>
                    <i data-lucide="alert-triangle" class="w-10 h-10 opacity-80"></i>
                </div>
            </div>
        </div>
        
        <div class="card bg-info text-info-content">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold"><?php echo $totalCategories; ?></h2>
                        <p class="text-sm opacity-80">Categories</p>
                    </div>
                    <i data-lucide="tag" class="w-10 h-10 opacity-80"></i>
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="../barang/index.php" class="btn btn-outline">
                    <i data-lucide="package" class="w-4 h-4 mr-2"></i> View Barang
                </a>
                <a href="#" class="btn btn-outline">
                    <i data-lucide="shopping-cart" class="w-4 h-4 mr-2"></i> My Orders
                </a>
                <a href="#" class="btn btn-outline">
                    <i data-lucide="settings" class="w-4 h-4 mr-2"></i> Profile Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Barang -->
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <h2 class="card-title flex items-center gap-2">
                <i data-lucide="package" class="w-5 h-5"></i> Recent Barang
            </h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM barang ORDER BY created_at DESC LIMIT 5");
                        while ($barang = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?php echo e($barang['nama_barang']); ?></td>
                            <td><span class="badge badge-ghost"><?php echo e($barang['kategori']); ?></span></td>
                            <td>Rp <?php echo number_format($barang['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($barang['stok'] < 5): ?>
                                    <span class="badge badge-warning"><?php echo $barang['stok']; ?> left</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?php echo $barang['stok']; ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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
    // Function to update activities in real-time (for user dashboard)
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