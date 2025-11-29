<?php
// barang/index.php
require_once '../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

// Check if user has admin role to determine functionality
$user = getCurrentUser();
if ($user['role'] !== 'admin') {
    // Users can view but cannot perform admin actions
    // Do nothing - allow access to view
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';

$title = 'Barang Management - Modern Web Store';
include '../views/header.php';
include '../views/topnav.php';
include '../views/sidebar.php';

// Handle search
$search = $_GET['search'] ?? '';
$pdo = getDBConnection();

// Build query with joins to get related data
$query = "SELECT b.*,
          k.nama_kategori,
          s.nama_supplier,
          t.nama_toko
          FROM barang b
          LEFT JOIN kategori k ON b.kategori_id = k.id
          LEFT JOIN supplier s ON b.supplier_id = s.id
          LEFT JOIN toko t ON b.toko_id = t.id
          WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (b.nama_barang LIKE ? OR b.deskripsi LIKE ? OR k.nama_kategori LIKE ? OR s.nama_supplier LIKE ? OR t.nama_toko LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%"];
}
$query .= " ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$barangList = $stmt->fetchAll();
?>

<div class="content p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Barang Management</h1>
        <p class="text-base-content/70">Manage your inventory items</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error mt-4">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span><?php echo e($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success mt-4">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span><?php echo e($success); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Controls -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <?php if (hasRole('admin')): ?>
        <a href="add.php" class="btn btn-primary">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add New Barang
        </a>
        <?php else: ?>
        <div>
            <h2 class="text-lg font-semibold">Barang List</h2>
            <p class="text-sm text-base-content/70">View-only mode</p>
        </div>
        <?php endif; ?>

        <form method="GET" class="flex-1 max-w-md">
            <div class="flex gap-2">
                <input
                    type="text"
                    name="search"
                    placeholder="Search barang..."
                    class="input input-bordered flex-1"
                    value="<?php echo e($search); ?>"
                />
                <button type="submit" class="btn btn-outline">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Barang Table -->
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <?php if (empty($barangList)): ?>
                <div class="text-center py-10">
                    <i data-lucide="package" class="w-16 h-16 mx-auto text-base-content/30"></i>
                    <h3 class="text-xl font-semibold mt-4">No barang found</h3>
                    <p class="text-base-content/70 mt-2">Create your first barang item to get started</p>
                    <a href="add.php" class="btn btn-primary mt-4">Add Barang</a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Supplier</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Toko</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Get related data for display
                            $categoryMap = [];
                            $supplierMap = [];
                            $tokoMap = [];

                            if (!empty($barangList)) {
                                // Get category names
                                $categoryIds = array_column($barangList, 'kategori_id');
                                $categoryIds = array_filter($categoryIds, function($id) { return !empty($id); });
                                if (!empty($categoryIds)) {
                                    $catPlaceholders = str_repeat('?,', count($categoryIds) - 1) . '?';
                                    $catStmt = $pdo->prepare("SELECT id, nama_kategori FROM kategori WHERE id IN ($catPlaceholders)");
                                    $catStmt->execute($categoryIds);
                                    $categoryMap = $catStmt->fetchAll(PDO::FETCH_KEY_PAIR);
                                }

                                // Get supplier names
                                $supplierIds = array_column($barangList, 'supplier_id');
                                $supplierIds = array_filter($supplierIds, function($id) { return !empty($id); });
                                if (!empty($supplierIds)) {
                                    $supPlaceholders = str_repeat('?,', count($supplierIds) - 1) . '?';
                                    $supStmt = $pdo->prepare("SELECT id, nama_supplier FROM supplier WHERE id IN ($supPlaceholders)");
                                    $supStmt->execute($supplierIds);
                                    $supplierMap = $supStmt->fetchAll(PDO::FETCH_KEY_PAIR);
                                }

                                // Get toko names
                                $tokoIds = array_column($barangList, 'toko_id');
                                $tokoIds = array_filter($tokoIds, function($id) { return !empty($id); });
                                if (!empty($tokoIds)) {
                                    $tokPlaceholders = str_repeat('?,', count($tokoIds) - 1) . '?';
                                    $tokStmt = $pdo->prepare("SELECT id, nama_toko FROM toko WHERE id IN ($tokPlaceholders)");
                                    $tokStmt->execute($tokoIds);
                                    $tokoMap = $tokStmt->fetchAll(PDO::FETCH_KEY_PAIR);
                                }
                            }
                            ?>
                            <?php foreach ($barangList as $barang): ?>
                            <tr>
                                <td><?php echo e($barang['id']); ?></td>
                                <td>
                                    <div class="font-semibold"><?php echo e($barang['nama_barang']); ?></div>
                                    <div class="text-sm opacity-70 max-w-xs"><?php echo strlen($barang['deskripsi']) > 30 ? substr(e($barang['deskripsi']), 0, 30) . '...' : e($barang['deskripsi']); ?></div>
                                </td>
                                <td>
                                    <?php if (!empty($barang['kategori_id']) && isset($categoryMap[$barang['kategori_id']])): ?>
                                        <span class="badge badge-ghost"><?php echo e($categoryMap[$barang['kategori_id']]); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-ghost">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($barang['supplier_id']) && isset($supplierMap[$barang['supplier_id']])): ?>
                                        <span class="badge badge-ghost"><?php echo e($supplierMap[$barang['supplier_id']]); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-ghost">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>Rp <?php echo number_format($barang['harga'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php if ($barang['stok'] < 5): ?>
                                        <span class="badge badge-warning"><?php echo e($barang['stok']); ?> left</span>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?php echo e($barang['stok']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($barang['toko_id']) && isset($tokoMap[$barang['toko_id']])): ?>
                                        <span class="badge badge-ghost"><?php echo e($tokoMap[$barang['toko_id']]); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-ghost">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (hasRole('admin')): ?>
                                    <div class="flex gap-2">
                                        <a
                                            href="edit.php?id=<?php echo e($barang['id']); ?>"
                                            class="btn btn-xs btn-outline"
                                            title="Edit"
                                        >
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <button
                                            type="button"
                                            class="btn btn-xs btn-outline btn-error delete-btn"
                                            title="Delete"
                                            data-id="<?php echo e($barang['id']); ?>"
                                            data-name="<?php echo e(addslashes($barang['nama_barang'])); ?>"
                                        >
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                    <?php else: ?>
                                    <span class="text-sm text-base-content/70">Read-only</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide icons after DOM content loads
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // Add event listeners to all delete buttons
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete "${name}". This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create form and submit for deletion
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'delete.php';
                        form.style.display = 'none';

                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'id';
                        idInput.value = id;
                        form.appendChild(idInput);

                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = 'csrf_token';
                        csrfInput.value = '<?php echo generateCSRFToken(); ?>';
                        form.appendChild(csrfInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    });
</script>

<?php include '../views/footer.php'; ?>