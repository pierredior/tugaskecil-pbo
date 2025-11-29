<!DOCTYPE html>
<html lang="en" data-theme="light"> <!-- Set default theme to light -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? e($title) : 'MerchShipe'; ?></title>

    <!-- Tailwind CSS & DaisyUI CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Custom Styles -->
    <style>
        .sidebar {
            width: 16rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            z-index: 40;
            height: calc(100vh - 4rem);
        }
        .sidebar.collapsed {
            width: 6rem;
        }
        .content {
            margin-left: 16rem;
            transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .collapsed + .content {
            margin-left: 6rem;
        }
        .menu-item-text {
            opacity: 1;
            max-width: 100%;
            transition: opacity 0.3s ease-in-out, max-width 0.3s ease-in-out, visibility 0.3s ease-in-out;
            display: inline-block;
            visibility: visible;
        }
        .sidebar.collapsed .menu-item-text {
            opacity: 0;
            max-width: 0;
            visibility: hidden;
            margin: 0;
            padding: 0;
        }
        /* Smooth animation for sidebar toggle */
        .sidebar-toggle-animation {
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar.collapsed .sidebar-toggle-animation {
            transform: rotate(180deg);
        }
        /* Add smooth scrolling for content */
        .content {
            overflow-x: hidden;
        }
    </style>
</head>
<body class="bg-base-100">