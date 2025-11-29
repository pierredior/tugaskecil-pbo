<?php
// supplier/index.php
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

$title = 'Supplier Management - Modern Web Store';
include '../views/header.php';
include '../views/topnav.php';
include '../views/sidebar.php';

// Handle search
$search = $_GET['search'] ?? '';
$pdo = getDBConnection();

// Build query
$query = "SELECT * FROM supplier WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (nama_supplier LIKE ? OR alamat LIKE ? OR telepon LIKE ? OR email LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
}
$query .= " ORDER BY nama_supplier ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$suppliers = $stmt->fetchAll();
?>

<div class="content p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Supplier Management</h1>
        <p class="text-base-content/70">Manage product suppliers</p>
        
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
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add New Supplier
        </a>
        <?php else: ?>
        <div>
            <h2 class="text-lg font-semibold">Supplier List</h2>
            <p class="text-sm text-base-content/70">View-only mode</p>
        </div>
        <?php endif; ?>

        <form method="GET" class="flex-1 max-w-md">
            <div class="flex gap-2">
                <input
                    type="text"
                    name="search"
                    placeholder="Search suppliers..."
                    class="input input-bordered flex-1"
                    value="<?php echo e($search); ?>"
                />
                <button type="submit" class="btn btn-outline">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Supplier Table -->
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <?php if (empty($suppliers)): ?>
                <div class="text-center py-10">
                    <i data-lucide="truck" class="w-16 h-16 mx-auto text-base-content/30"></i>
                    <h3 class="text-xl font-semibold mt-4">No suppliers found</h3>
                    <p class="text-base-content/70 mt-2">Create your first supplier to get started</p>
                    <a href="add.php" class="btn btn-primary mt-4">Add Supplier</a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Supplier</th>
                                <th>Alamat</th>
                                <th>Telepon</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td><?php echo e($supplier['id']); ?></td>
                                <td class="font-semibold"><?php echo e($supplier['nama_supplier']); ?></td>
                                <td class="max-w-xs"><?php echo strlen($supplier['alamat']) > 30 ? substr(e($supplier['alamat']), 0, 30) . '...' : e($supplier['alamat']); ?></td>
                                <td><?php echo e($supplier['telepon']); ?></td>
                                <td><?php echo e($supplier['email']); ?></td>
                                <td>
                                    <?php if (hasRole('admin')): ?>
                                    <div class="flex gap-2">
                                        <a
                                            href="edit.php?id=<?php echo e($supplier['id']); ?>"
                                            class="btn btn-xs btn-outline"
                                            title="Edit"
                                        >
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <button
                                            type="button"
                                            class="btn btn-xs btn-outline btn-error delete-btn"
                                            title="Delete"
                                            data-id="<?php echo e($supplier['id']); ?>"
                                            data-name="<?php echo e(addslashes($supplier['nama_supplier'])); ?>"
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