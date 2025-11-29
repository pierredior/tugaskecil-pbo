<?php
// barang/add.php
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

$title = 'Add Barang - Modern Web Store';
include '../views/header.php';
include '../views/topnav.php';
include '../views/sidebar.php';

$error = '';
$success = '';

// Get all categories, suppliers, and tokos
$categories = getAllCategories();
$suppliers = getAllSuppliers();
$tokos = getAllToko();

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
        $toko_id = intval($_POST['toko_id'] ?? 1); // Default to first toko
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
                $pdo = getDBConnection();

                $stmt = $pdo->prepare("INSERT INTO barang (nama_barang, deskripsi, harga, stok, kategori_id, supplier_id, toko_id, gambar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nama_barang, $deskripsi, $harga, $stok, $kategori_id, $supplier_id, $toko_id, $gambar]);

                // Log the activity
                logActivity('create_barang', "Created new product: $nama_barang");

                $success = 'Barang added successfully!';

                // Redirect to prevent resubmission
                if (isset($_POST['submit_and_add'])) {
                    // Stay on the page to add another
                } else {
                    redirect('index.php');
                }
            } catch (PDOException $e) {
                $error = 'An error occurred while adding the barang. Please try again.';
            }
        }
    }
}
?>

<div class="content p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Add New Barang</h1>
        <p class="text-base-content/70">Create a new inventory item</p>
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
                            value="<?php echo isset($_POST['nama_barang']) ? e($_POST['nama_barang']) : ''; ?>"
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
                                <option value="<?php echo e($category['id']); ?>" <?php echo (isset($_POST['kategori_id']) && $_POST['kategori_id'] == $category['id']) ? 'selected' : ''; ?>>
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
                            value="<?php echo isset($_POST['harga']) ? e($_POST['harga']) : ''; ?>"
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
                            value="<?php echo isset($_POST['stok']) ? e($_POST['stok']) : ''; ?>"
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
                                <option value="<?php echo e($supplier['id']); ?>" <?php echo (isset($_POST['supplier_id']) && $_POST['supplier_id'] == $supplier['id']) ? 'selected' : ''; ?>>
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
                            <option value="1" <?php echo (isset($_POST['toko_id']) && $_POST['toko_id'] == 1) ? 'selected' : ''; ?>>
                                <?php
                                $firstToko = $tokos[0] ?? null;
                                echo $firstToko ? e($firstToko['nama_toko']) : 'Default Toko';
                                ?>
                            </option>
                            <?php foreach ($tokos as $toko): ?>
                                <?php if ($toko['id'] != 1): // Skip the first one since it's already selected as default ?>
                                <option value="<?php echo e($toko['id']); ?>" <?php echo (isset($_POST['toko_id']) && $_POST['toko_id'] == $toko['id']) ? 'selected' : ''; ?>>
                                    <?php echo e($toko['nama_toko']); ?>
                                </option>
                                <?php endif; ?>
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
                            value="<?php echo isset($_POST['gambar']) ? e($_POST['gambar']) : ''; ?>"
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
                        ><?php echo isset($_POST['deskripsi']) ? e($_POST['deskripsi']) : ''; ?></textarea>
                    </div>
                </div>
                
                <div class="card-actions justify-end mt-6">
                    <a href="index.php" class="btn btn-outline">Cancel</a>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Save Barang
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