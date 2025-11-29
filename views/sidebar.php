<?php
// views/sidebar.php
require_once '../config/database.php';

// Get current user
$user = getCurrentUser();
?>

<!-- Backdrop for mobile -->
<div id="sidebar-backdrop" class="fixed inset-0 z-30 bg-black bg-opacity-50 hidden"></div>

<!-- Sidebar -->
<div class="sidebar fixed top-16 left-0 bg-base-200 flex flex-col z-40">
    <!-- Sidebar Header -->
    <div class="p-4 border-b border-base-300 flex items-center justify-between">
        <h2 class="text-lg font-semibold flex items-center gap-2">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span class="menu-item-text">Menu</span>
        </h2>
        <button id="sidebar-toggle" class="btn btn-ghost btn-sm sidebar-toggle-animation">
            <i data-lucide="chevrons-left" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- Sidebar Content -->
    <ul class="menu p-2 flex-1">
        <?php if (isLoggedIn()): ?>
            <li>
                <a href="../admin/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/index.php') !== false ? 'active' : ''; ?>">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    <span class="menu-item-text">Dashboard</span>
                </a>
            </li>
            
            <?php if (hasRole('admin') || hasRole('user')): ?>
                <li class="menu-title">
                    <span class="menu-item-text">Inventory</span>
                </li>
                <li>
                    <a href="../barang/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], '/barang/index.php') !== false ? 'active' : ''; ?>">
                        <i data-lucide="package" class="w-5 h-5"></i>
                        <span class="menu-item-text">Barang</span>
                    </a>
                </li>
                <li>
                    <a href="../kategori/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], '/kategori/index.php') !== false ? 'active' : ''; ?>">
                        <i data-lucide="tag" class="w-5 h-5"></i>
                        <span class="menu-item-text">Kategori</span>
                    </a>
                </li>
                <li>
                    <a href="../supplier/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], '/supplier/index.php') !== false ? 'active' : ''; ?>">
                        <i data-lucide="truck" class="w-5 h-5"></i>
                        <span class="menu-item-text">Supplier</span>
                    </a>
                </li>
                <li>
                    <a href="../toko/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], '/toko/index.php') !== false ? 'active' : ''; ?>">
                        <i data-lucide="store" class="w-5 h-5"></i>
                        <span class="menu-item-text">Toko</span>
                    </a>
                </li>
            <?php endif; ?>
            
            <li class="menu-title mt-auto">
                <span class="menu-item-text">User</span>
            </li>
            <li>
                <a href="../logout.php">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span class="menu-item-text">Logout</span>
                </a>
            </li>
        <?php else: ?>
            <li>
                <a href="../login.php" class="<?php echo strpos($_SERVER['PHP_SELF'], '/login.php') !== false ? 'active' : ''; ?>">
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    <span class="menu-item-text">Login</span>
                </a>
            </li>
            <li>
                <a href="../register.php" class="<?php echo strpos($_SERVER['PHP_SELF'], '/register.php') !== false ? 'active' : ''; ?>">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                    <span class="menu-item-text">Register</span>
                </a>
            </li>
            <li>
                <a href="../" class="<?php echo strpos($_SERVER['PHP_SELF'], '/index.php') !== false || strpos($_SERVER['PHP_SELF'], '/index.php') === false ? 'active' : ''; ?>">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    <span class="menu-item-text">Home</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>

<script>
    // Sidebar toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.getElementById('sidebar-toggle');
        const backdrop = document.getElementById('sidebar-backdrop');
        const toggleIcon = toggleBtn.querySelector('i');
        const isMobile = window.innerWidth < 768;

        // Check if sidebar state is saved in localStorage
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
        }

        // Toggle sidebar and backdrop visibility for mobile
        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            const isNowCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isNowCollapsed);

            // Update icon based on state
            if (isNowCollapsed) {
                toggleIcon.setAttribute('data-lucide', 'chevrons-right');
                backdrop.classList.add('hidden');
            } else {
                toggleIcon.setAttribute('data-lucide', 'chevrons-left');
                // Only show backdrop on mobile when sidebar is expanded
                if (isMobile) {
                    backdrop.classList.remove('hidden');
                } else {
                    backdrop.classList.add('hidden');
                }
            }

            lucide.createIcons();
        }

        // Handle click on toggle button
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });

        // Close sidebar when clicking on backdrop (mobile)
        backdrop.addEventListener('click', function() {
            if (!sidebar.classList.contains('collapsed')) {
                sidebar.classList.add('collapsed');
                backdrop.classList.add('hidden');
                localStorage.setItem('sidebarCollapsed', 'true');

                // Update icon
                toggleIcon.setAttribute('data-lucide', 'chevrons-right');
                lucide.createIcons();
            }
        });

        // Handle window resize to update mobile state
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                // On desktop, always hide backdrop
                backdrop.classList.add('hidden');
            } else if (!sidebar.classList.contains('collapsed')) {
                // On mobile, show backdrop if sidebar is expanded
                backdrop.classList.remove('hidden');
            }
        });
    });
</script>