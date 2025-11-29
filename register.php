<?php
// register.php
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
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            $error = 'All fields are required.';
        } elseif (strlen($username) < 3) {
            $error = 'Username must be at least 3 characters long.';
        } elseif (!validateEmail($email)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 3) {
            $error = 'Password must be at least 3 characters long.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            try {
                $pdo = getDBConnection();

                // Check if username or email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);

                if ($stmt->fetch()) {
                    $error = 'Username or email already exists.';
                } else {
                    // Insert new user
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
                    $stmt->execute([$username, $email, $hashedPassword]);

                    // Log the registration activity
                    logActivity('registration', 'New user registered successfully');

                    $success = 'Registration successful! You can now login.';
                }
            } catch (PDOException $e) {
                $error = 'An error occurred. Please try again later.';
            }
        }
    }
}
?>

<?php $title = 'Register - MerchShipe'; include 'views/header.php'; ?>

<div class="min-h-screen flex flex-col items-center justify-center bg-base-200">
    <div class="card w-full max-w-md bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="text-center mb-6">
                <div class="bg-primary p-3 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="shopping-bag" class="w-8 h-8 text-primary-content"></i>
                </div>
                <h2 class="text-2xl font-bold">Create Your MerchShipe Account</h2>
                <p class="text-base-content/70 mt-2">Join our inventory management platform</p>
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

            <form method="POST" action="register.php" data-validate>
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <div class="form-control mb-4">
                    <label class="label" for="username">
                        <span class="label-text">Username</span>
                    </label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        placeholder="Choose a username"
                        class="input input-bordered w-full"
                        value="<?php echo isset($_POST['username']) ? e($_POST['username']) : ''; ?>"
                        data-validate="required|min:3"
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
                        placeholder="Enter your email"
                        class="input input-bordered w-full"
                        value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>"
                        data-validate="required|email"
                    />
                </div>

                <div class="form-control mb-4">
                    <label class="label" for="password">
                        <span class="label-text">Password</span>
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Create a password"
                        class="input input-bordered w-full"
                        data-validate="required|min:3"
                        data-strength
                    />
                    <div class="mt-1 text-xs text-gray-500">
                        Password should be at least 3 characters.
                    </div>
                </div>

                <div class="form-control mb-6">
                    <label class="label" for="confirm_password">
                        <span class="label-text">Confirm Password</span>
                    </label>
                    <input
                        type="password"
                        name="confirm_password"
                        id="confirm_password"
                        placeholder="Confirm your password"
                        class="input input-bordered w-full"
                        data-validate="required|min:6"
                    />
                </div>

                <div class="form-control">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Create Account
                    </button>
                </div>
            </form>

            <div class="divider my-4">OR</div>

            <div class="text-center">
                <p>Already have an account? <a href="login.php" class="link link-primary">Sign in</a></p>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>