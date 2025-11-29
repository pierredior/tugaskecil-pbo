<?php
// toko/add.php
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

$title = 'Add Toko - Modern Web Store';
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
        $nama_toko = sanitizeInput($_POST['nama_toko'] ?? '');
        $alamat = sanitizeInput($_POST['alamat'] ?? '');
        $telepon = sanitizeInput($_POST['telepon'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        
        // Validation
        if (empty($nama_toko)) {
            $error = 'Nama toko is required.';
        } elseif (!empty($email) && !validateEmail($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            try {
                $pdo = getDBConnection();
                
                $stmt = $pdo->prepare("INSERT INTO toko (nama_toko, alamat, telepon, email) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nama_toko, $alamat, $telepon, $email]);
                
                $success = 'Toko added successfully!';
                
                // Redirect to prevent resubmission
                if (isset($_POST['submit_and_add'])) {
                    // Stay on the page to add another
                } else {
                    redirect('index.php');
                }
            } catch (PDOException $e) {
                $error = 'An error occurred while adding the toko. Please try again.';
            }
        }
    }
}
?>

<div class="content p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Add New Toko</h1>
        <p class="text-base-content/70">Create a new store location</p>
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
            
            <form method="POST" action="add.php" data-validate>
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-control mb-4">
                    <label class="label" for="nama_toko">
                        <span class="label-text">Nama Toko <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="text" 
                        name="nama_toko" 
                        id="nama_toko"
                        placeholder="Enter toko name"
                        class="input input-bordered w-full"
                        value="<?php echo isset($_POST['nama_toko']) ? e($_POST['nama_toko']) : ''; ?>"
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
                        placeholder="Enter toko address"
                        class="textarea textarea-bordered w-full h-24"
                    ><?php echo isset($_POST['alamat']) ? e($_POST['alamat']) : ''; ?></textarea>
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
                            value="<?php echo isset($_POST['telepon']) ? e($_POST['telepon']) : ''; ?>"
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
                            value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>"
                        />
                    </div>
                </div>
                
                <div class="card-actions justify-end mt-6">
                    <a href="index.php" class="btn btn-outline">Cancel</a>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Save Toko
                    </button>
                    <button type="submit" name="submit_and_add" class="btn btn-secondary">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Save & Add Another
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