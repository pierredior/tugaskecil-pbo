<?php
// barang/edit.php
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

// Get barang ID from URL
$barangId = intval($_GET['id'] ?? 0);

if ($barangId <= 0) {
    redirect('index.php');
}

$pdo = getDBConnection();

// Get the barang to edit
$stmt = $pdo->prepare("SELECT * FROM barang WHERE id = ?");
$stmt->execute([$barangId]);
$barang = $stmt->fetch();

if (!$barang) {
    redirect('index.php');
}

// Get all categories, suppliers, and tokos
$categories = getAllCategories();
$suppliers = getAllSuppliers();
$tokos = getAllToko();

$title = 'Edit Barang - Modern Web Store';
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
        $nama_barang = sanitizeInput($_POST['nama_barang'] ?? '');
        $deskripsi = sanitizeInput($_POST['deskripsi'] ?? '');
        $harga = floatval($_POST['harga'] ?? 0);
        $stok = intval($_POST['stok'] ?? 0);
        $kategori_id = intval($_POST['kategori_id'] ?? 0);
        $supplier_id = intval($_POST['supplier_id'] ?? 0);
        $toko_id = intval($_POST['toko_id'] ?? 1);
        $gambar = sanitizeInput($_POST['gambar'] ?? '');

        // Validation
        if (empty($nama_barang)) {
            $error = 'Nama barang is required.';
        } elseif ($harga < 0) {
            $error = 'Harga must be a positive number.';
        } elseif ($stok < 0) {
            $error = 'Stok must be a positive number.';
        } else {
            try {
                // Get the original name for the log
                $originalStmt = $pdo->prepare("SELECT nama_barang FROM barang WHERE id = ?");
                $originalStmt->execute([$barangId]);
                $originalBarang = $originalStmt->fetch();
                $originalName = $originalBarang['nama_barang'];

                $stmt = $pdo->prepare("UPDATE barang SET nama_barang = ?, deskripsi = ?, harga = ?, stok = ?, kategori_id = ?, supplier_id = ?, toko_id = ?, gambar = ?, updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([$nama_barang, $deskripsi, $harga, $stok, $kategori_id, $supplier_id, $toko_id, $gambar, $barangId]);

                if ($result) {
                    // Log the activity
                    logActivity('update_barang', "Updated product: $originalName to $nama_barang");

                    $success = 'Barang updated successfully!';

                    // Redirect to prevent resubmission
                    if (isset($_POST['submit_and_continue'])) {
                        // Reload the page with the updated data
                        $stmt = $pdo->prepare("SELECT * FROM barang WHERE id = ?");
                        $stmt->execute([$barangId]);
                        $barang = $stmt->fetch();
                    } else {
                        redirect('index.php');
                    }
                } else {
                    $error = 'Failed to update barang. Please try again.';
                }
            } catch (PDOException $e) {
                $error = 'An error occurred while updating the barang. Please try again.';
            }
        }
    }
}
?>

<div class="content p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Edit Barang</h1>
        <p class="text-base-content/70">Update existing inventory item</p>
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
            
            <form method="POST" action="edit.php?id=<?php echo e($barangId); ?>" data-validate>
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control mb-4">
                        <label class="label" for="nama_barang">
                            <span class="label-text">Nama Barang <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="text"
                            name="nama_barang"
                            id="nama_barang"
                            placeholder="Enter barang name"
                            class="input input-bordered w-full"
                            value="<?php echo e($barang['nama_barang']); ?>"
                            data-validate="required|min:1"
                        />
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="kategori_id">
                            <span class="label-text">Kategori</span>
                        </label>
                        <select
                            name="kategori_id"
                            id="kategori_id"
                            class="select select-bordered w-full"
                        >
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo e($category['id']); ?>" <?php echo ($barang['kategori_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo e($category['nama_kategori']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="harga">
                            <span class="label-text">Harga <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="number"
                            name="harga"
                            id="harga"
                            placeholder="Enter price"
                            class="input input-bordered w-full"
                            value="<?php echo e($barang['harga']); ?>"
                            min="0"
                            step="0.01"
                            data-validate="required|numeric|min:0"
                        />
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="stok">
                            <span class="label-text">Stok <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="number"
                            name="stok"
                            id="stok"
                            placeholder="Enter stock quantity"
                            class="input input-bordered w-full"
                            value="<?php echo e($barang['stok']); ?>"
                            min="0"
                            data-validate="required|numeric|min:0"
                        />
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="supplier_id">
                            <span class="label-text">Supplier</span>
                        </label>
                        <select
                            name="supplier_id"
                            id="supplier_id"
                            class="select select-bordered w-full"
                        >
                            <option value="">Select a supplier</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?php echo e($supplier['id']); ?>" <?php echo ($barang['supplier_id'] == $supplier['id']) ? 'selected' : ''; ?>>
                                    <?php echo e($supplier['nama_supplier']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="toko_id">
                            <span class="label-text">Toko</span>
                        </label>
                        <select
                            name="toko_id"
                            id="toko_id"
                            class="select select-bordered w-full"
                        >
                            <?php foreach ($tokos as $toko): ?>
                                <option value="<?php echo e($toko['id']); ?>" <?php echo ($barang['toko_id'] == $toko['id']) ? 'selected' : ''; ?>>
                                    <?php echo e($toko['nama_toko']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-control mb-4 md:col-span-2">
                        <label class="label" for="gambar">
                            <span class="label-text">Gambar URL</span>
                        </label>
                        <input
                            type="text"
                            name="gambar"
                            id="gambar"
                            placeholder="Enter image URL"
                            class="input input-bordered w-full"
                            value="<?php echo e($barang['gambar']); ?>"
                        />
                    </div>

                    <div class="form-control mb-4 md:col-span-2">
                        <label class="label" for="deskripsi">
                            <span class="label-text">Deskripsi</span>
                        </label>
                        <textarea
                            name="deskripsi"
                            id="deskripsi"
                            placeholder="Enter barang description"
                            class="textarea textarea-bordered w-full h-32"
                        ><?php echo e($barang['deskripsi']); ?></textarea>
                    </div>
                </div>
                
                <div class="card-actions justify-end mt-6">
                    <a href="index.php" class="btn btn-outline">Cancel</a>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Save Changes
                    </button>
                    <button type="submit" name="submit_and_continue" class="btn btn-secondary">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Update & Continue
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