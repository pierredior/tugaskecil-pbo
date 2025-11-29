<?php
// login.php
require_once 'config/database.php';

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    $user = getCurrentUser();
    if ($user['role'] === 'admin') {
        redirect('admin/index.php');
    } else {
        redirect('user/index.php');
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Username and password are required.';
        } else {
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    // Log the login activity
                    logActivity('login', 'User logged in successfully');

                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        redirect('admin/index.php');
                    } else {
                        redirect('user/index.php');
                    }
                } else {
                    $error = 'Invalid username or password.';
                }
            } catch (PDOException $e) {
                $error = 'An error occurred. Please try again later.';
            }
        }
    }
}
?>

<?php $title = 'Login - MerchShipe'; include 'views/header.php'; ?>

<div class="min-h-screen flex flex-col items-center justify-center bg-base-200">
    <div class="card w-full max-w-md bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="text-center mb-6">
                <div class="bg-primary p-3 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="shopping-bag" class="w-8 h-8 text-primary-content"></i>
                </div>
                <h2 class="text-2xl font-bold">Welcome to MerchShipe</h2>
                <p class="text-base-content/70 mt-2">Sign in to your account</p>
            </div>

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

            <form method="POST" action="login.php" data-validate>
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <div class="form-control mb-4">
                    <label class="label" for="username">
                        <span class="label-text">Username or Email</span>
                    </label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        placeholder="Enter username or email"
                        class="input input-bordered w-full"
                        value="<?php echo isset($_POST['username']) ? e($_POST['username']) : ''; ?>"
                        data-validate="required|min:3"
                    />
                </div>

                <div class="form-control mb-6">
                    <label class="label" for="password">
                        <span class="label-text">Password</span>
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter password"
                        class="input input-bordered w-full"
                        data-validate="required|min:3"
                    />
                </div>

                <div class="form-control">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="log-in" class="w-4 h-4 mr-2"></i> Sign In
                    </button>
                </div>
            </form>

            <div class="divider my-4">OR</div>

            <div class="text-center">
                <p>Don't have an account? <a href="register.php" class="link link-primary">Sign up</a></p>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>