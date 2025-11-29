<?php
// kategori/index.php
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

$title = 'Kategori Management - Modern Web Store';
include '../views/header.php';
include '../views/topnav.php';
include '../views/sidebar.php';

// Handle search
$search = $_GET['search'] ?? '';
$pdo = getDBConnection();

// Build query
$query = "SELECT * FROM kategori WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (nama_kategori LIKE ? OR deskripsi LIKE ?)";
    $params = ["%$search%", "%$search%"];
}
$query .= " ORDER BY nama_kategori ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$kategoris = $stmt->fetchAll();
?>

<div class="content p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Kategori Management</h1>
        <p class="text-base-content/70">Manage product categories</p>
        
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
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add New Kategori
        </a>
        <?php else: ?>
        <div>
            <h2 class="text-lg font-semibold">Kategori List</h2>
            <p class="text-sm text-base-content/70">View-only mode</p>
        </div>
        <?php endif; ?>

        <form method="GET" class="flex-1 max-w-md">
            <div class="flex gap-2">
                <input
                    type="text"
                    name="search"
                    placeholder="Search kategori..."
                    class="input input-bordered flex-1"
                    value="<?php echo e($search); ?>"
                />
                <button type="submit" class="btn btn-outline">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Kategori Table -->
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <?php if (empty($kategoris)): ?>
                <div class="text-center py-10">
                    <i data-lucide="tag" class="w-16 h-16 mx-auto text-base-content/30"></i>
                    <h3 class="text-xl font-semibold mt-4">No categories found</h3>
                    <p class="text-base-content/70 mt-2">Create your first category to get started</p>
                    <a href="add.php" class="btn btn-primary mt-4">Add Category</a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kategoris as $kategori): ?>
                            <tr>
                                <td><?php echo e($kategori['id']); ?></td>
                                <td class="font-semibold"><?php echo e($kategori['nama_kategori']); ?></td>
                                <td class="max-w-xs"><?php echo strlen($kategori['deskripsi']) > 50 ? substr(e($kategori['deskripsi']), 0, 50) . '...' : e($kategori['deskripsi']); ?></td>
                                <td><?php echo date('d M Y', strtotime($kategori['created_at'])); ?></td>
                                <td>
                                    <?php if (hasRole('admin')): ?>
                                    <div class="flex gap-2">
                                        <a
                                            href="edit.php?id=<?php echo e($kategori['id']); ?>"
                                            class="btn btn-xs btn-outline"
                                            title="Edit"
                                        >
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <button
                                            type="button"
                                            class="btn btn-xs btn-outline btn-error delete-btn"
                                            title="Delete"
                                            data-id="<?php echo e($kategori['id']); ?>"
                                            data-name="<?php echo e(addslashes($kategori['nama_kategori'])); ?>"
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