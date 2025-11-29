<?php
// kategori/edit.php
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

// Get kategori ID from URL
$kategoriId = intval($_GET['id'] ?? 0);

if ($kategoriId <= 0) {
    redirect('index.php');
}

$pdo = getDBConnection();

// Get the kategori to edit
$stmt = $pdo->prepare("SELECT * FROM kategori WHERE id = ?");
$stmt->execute([$kategoriId]);
$kategori = $stmt->fetch();

if (!$kategori) {
    redirect('index.php');
}

$title = 'Edit Kategori - Modern Web Store';
include '../views/header.php';
include '../views/topnav.php';
include '../views/sidebar.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $nama_kategori = sanitizeInput($_POST['nama_kategori'] ?? '');
        $deskripsi = sanitizeInput($_POST['deskripsi'] ?? '');
        
        // Validation
        if (empty($nama_kategori)) {
            $error = 'Nama kategori is required.';
        } else {
            try {
                // Get the original name for the log
                $originalStmt = $pdo->prepare("SELECT nama_kategori FROM kategori WHERE id = ?");
                $originalStmt->execute([$kategoriId]);
                $originalKategori = $originalStmt->fetch();
                $originalName = $originalKategori['nama_kategori'];

                $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = ?, deskripsi = ?, updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([$nama_kategori, $deskripsi, $kategoriId]);

                if ($result) {
                    // Log the activity
                    logActivity('update_kategori', "Updated category: $originalName to $nama_kategori");

                    $success = 'Kategori updated successfully!';

                    // Reload the kategori data
                    $stmt = $pdo->prepare("SELECT * FROM kategori WHERE id = ?");
                    $stmt->execute([$kategoriId]);
                    $kategori = $stmt->fetch();
                } else {
                    $error = 'Failed to update kategori. Please try again.';
                }
            } catch (PDOException $e) {
                $error = 'An error occurred while updating the kategori. Please try again.';
            }
        }
    }
}
?>

<div class="content p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Edit Kategori</h1>
        <p class="text-base-content/70">Update existing product category</p>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error mb-4">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    <span><?php echo e($error); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success mb-4">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <span><?php echo e($success); ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="edit.php?id=<?php echo e($kategoriId); ?>" data-validate>
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-control mb-4">
                    <label class="label" for="nama_kategori">
                        <span class="label-text">Nama Kategori <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="text" 
                        name="nama_kategori" 
                        id="nama_kategori"
                        placeholder="Enter category name"
                        class="input input-bordered w-full"
                        value="<?php echo e($kategori['nama_kategori']); ?>"
                        data-validate="required|min:1"
                    />
                </div>
                
                <div class="form-control mb-4">
                    <label class="label" for="deskripsi">
                        <span class="label-text">Deskripsi</span>
                    </label>
                    <textarea 
                        name="deskripsi" 
                        id="deskripsi"
                        placeholder="Enter category description"
                        class="textarea textarea-bordered w-full h-32"
                    ><?php echo e($kategori['deskripsi']); ?></textarea>
                </div>
                
                <div class="card-actions justify-end mt-6">
                    <a href="index.php" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide icons after DOM content loads
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>

<?php include '../views/footer.php'; ?>