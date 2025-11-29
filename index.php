<?php
// index.php - Landing page
require_once 'config/database.php';

// If user is already logged in, redirect to appropriate dashboard
if (isLoggedIn()) {
    $user = getCurrentUser();
    if ($user['role'] === 'admin') {
        redirect('admin/index.php');
    } else {
        redirect('user/index.php');
    }
}

$title = 'MerchShipe - Home';
include 'views/header.php';
?>

<div class="min-h-screen bg-base-200">
    <!-- Hero Section -->
    <div class="hero bg-base-100">
        <div class="hero-content text-center">
            <div class="max-w-md">
                <div class="flex justify-center mb-4">
                    <div class="bg-primary p-4 rounded-full">
                        <i data-lucide="shopping-bag" class="w-12 h-12 text-primary-content"></i>
                    </div>
                </div>
                <h1 class="text-5xl font-bold">MerchShipe</h1>
                <p class="py-6">A sleek, responsive inventory management system with role-based access control and a beautiful user interface.</p>
                <div class="flex justify-center gap-4">
                    <a href="login.php" class="btn btn-primary">
                        <i data-lucide="log-in" class="w-4 h-4 mr-2"></i> Login
                    </a>
                    <a href="register.php" class="btn btn-outline">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Register
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container mx-auto px-4 py-16">
        <h2 class="text-3xl font-bold text-center mb-12">Why Choose MerchShipe?</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body items-center text-center">
                    <div class="bg-primary p-4 rounded-full">
                        <i data-lucide="shield-check" class="w-12 h-12 text-primary-content"></i>
                    </div>
                    <h3 class="card-title mt-4">Secure Management</h3>
                    <p>Role-based access control with secure login and registration system.</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow-xl">
                <div class="card-body items-center text-center">
                    <div class="bg-secondary p-4 rounded-full">
                        <i data-lucide="package" class="w-12 h-12 text-secondary-content"></i>
                    </div>
                    <h3 class="card-title mt-4">Comprehensive Inventory</h3>
                    <p>Track products, categories, suppliers, and store locations in one place.</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow-xl">
                <div class="card-body items-center text-center">
                    <div class="bg-accent p-4 rounded-full">
                        <i data-lucide="smartphone" class="w-12 h-12 text-accent-content"></i>
                    </div>
                    <h3 class="card-title mt-4">Fully Responsive</h3>
                    <p>Fully responsive layout that works on all devices and screen sizes.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="hero bg-primary text-primary-content">
        <div class="hero-content text-center">
            <div class="max-w-md">
                <h1 class="text-3xl font-bold">Start Managing Your Inventory Today</h1>
                <p class="py-6">Join MerchShipe to manage your products efficiently with our modern tools.</p>
                <a href="register.php" class="btn btn-primary bg-white text-primary hover:bg-gray-100">
                    <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Create Account
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>