<?php
// supplier/edit.php
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

// Get supplier ID from URL
$supplierId = intval($_GET['id'] ?? 0);

if ($supplierId <= 0) {
    redirect('index.php');
}

$pdo = getDBConnection();

// Get the supplier to edit
$stmt = $pdo->prepare("SELECT * FROM supplier WHERE id = ?");
$stmt->execute([$supplierId]);
$supplier = $stmt->fetch();

if (!$supplier) {
    redirect('index.php');
}

$title = 'Edit Supplier - Modern Web Store';
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
        $nama_supplier = sanitizeInput($_POST['nama_supplier'] ?? '');
        $alamat = sanitizeInput($_POST['alamat'] ?? '');
        $telepon = sanitizeInput($_POST['telepon'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        
        // Validation
        if (empty($nama_supplier)) {
            $error = 'Nama supplier is required.';
        } elseif (!empty($email) && !validateEmail($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE supplier SET nama_supplier = ?, alamat = ?, telepon = ?, email = ?, updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([$nama_supplier, $alamat, $telepon, $email, $supplierId]);
                
                if ($result) {
                    $success = 'Supplier updated successfully!';
                    
                    // Reload the supplier data
                    $stmt = $pdo->prepare("SELECT * FROM supplier WHERE id = ?");
                    $stmt->execute([$supplierId]);
                    $supplier = $stmt->fetch();
                } else {
                    $error = 'Failed to update supplier. Please try again.';
                }
            } catch (PDOException $e) {
                $error = 'An error occurred while updating the supplier. Please try again.';
            }
        }
    }
}
?>

<div class="content p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Edit Supplier</h1>
        <p class="text-base-content/70">Update existing product supplier</p>
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
            
            <form method="POST" action="edit.php?id=<?php echo e($supplierId); ?>" data-validate>
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-control mb-4">
                    <label class="label" for="nama_supplier">
                        <span class="label-text">Nama Supplier <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="text" 
                        name="nama_supplier" 
                        id="nama_supplier"
                        placeholder="Enter supplier name"
                        class="input input-bordered w-full"
                        value="<?php echo e($supplier['nama_supplier']); ?>"
                        data-validate="required|min:1"
                    />
                </div>
                
                <div class="form-control mb-4">
                    <label class="label" for="alamat">
                        <span class="label-text">Alamat</span>
                    </label>
                    <textarea 
                        name="alamat" 
                        id="alamat"
                        placeholder="Enter supplier address"
                        class="textarea textarea-bordered w-full h-24"
                    ><?php echo e($supplier['alamat']); ?></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control mb-4">
                        <label class="label" for="telepon">
                            <span class="label-text">Telepon</span>
                        </label>
                        <input 
                            type="text" 
                            name="telepon" 
                            id="telepon"
                            placeholder="Enter phone number"
                            class="input input-bordered w-full"
                            value="<?php echo e($supplier['telepon']); ?>"
                        />
                    </div>
                    
                    <div class="form-control mb-4">
                        <label class="label" for="email">
                            <span class="label-text">Email</span>
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            placeholder="Enter email address"
                            class="input input-bordered w-full"
                            value="<?php echo e($supplier['email']); ?>"
                        />
                    </div>
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