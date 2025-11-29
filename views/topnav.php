<?php
// views/topnav.php
require_once '../config/database.php';

// Get current user
$user = getCurrentUser();
?>

<!-- Top Navigation Bar -->
<div class="navbar bg-base-100 shadow-md z-10">
    <div class="flex-1">
        <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </div>
            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                <?php if (isLoggedIn()): ?>
                    <li><a href="../admin/index.php">Dashboard</a></li>
                    <li><a href="../barang/index.php">Barang</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="../login.php">Login</a></li>
                    <li><a href="../register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <a href="../" class="btn btn-ghost text-xl flex items-center gap-2">
            <i data-lucide="shopping-bag" class="w-6 h-6"></i>
            <span>MerchShipe</span>
        </a>
    </div>
    
    <div class="flex-none gap-2">
        <?php if (isLoggedIn()): ?>
            <!-- Theme Toggle -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                    <i data-lucide="sun" class="w-6 h-6 theme-icon"></i>
                </div>
                <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                    <li><a id="theme-light" class="active" onclick="setTheme('light')">Light</a></li>
                    <li><a id="theme-dark" onclick="setTheme('dark')">Dark</a></li>
                    <li><a id="theme-forest" onclick="setTheme('forest')">Forest</a></li>
                    <li><a id="theme-cyberpunk" onclick="setTheme('cyberpunk')">Cyberpunk</a></li>
                </ul>
            </div>
            
            <!-- User Menu -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full bg-primary text-white flex items-center justify-center">
                        <span class="font-bold"><?php echo substr(e($user['username']), 0, 1); ?></span>
                    </div>
                </div>
                <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                    <li class="menu-title">
                        <span class="text-xs opacity-50"><?php echo e($user['username']); ?></span>
                    </li>
                    <li><a href="../admin/index.php">
                        <i data-lucide="home" class="w-4 h-4"></i> Dashboard
                    </a></li>
                    <li><a href="../logout.php">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                    </a></li>
                </ul>
            </div>
        <?php else: ?>
            <div class="flex gap-2">
                <a href="../login.php" class="btn btn-outline">Login</a>
                <a href="../register.php" class="btn btn-primary">Register</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Theme management
    function setTheme(themeName) {
        document.documentElement.setAttribute('data-theme', themeName);
        localStorage.setItem('theme', themeName);
        
        // Update active state
        document.querySelectorAll('#theme-light, #theme-dark, #theme-forest, #theme-cyberpunk').forEach(el => {
            el.classList.remove('active');
        });
        event.target.classList.add('active');
        
        // Update icon
        const themeIcon = document.querySelector('.theme-icon');
        lucide.replace();
    }
    
    // Initialize theme
    (function() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);
        
        // Update the active class for the saved theme
        setTimeout(() => {
            const activeThemeBtn = document.getElementById(`theme-${savedTheme}`);
            if (activeThemeBtn) {
                activeThemeBtn.classList.add('active');
            }
        }, 10);
    })();
</script>