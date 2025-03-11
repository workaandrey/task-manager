<?php
// Get the current user from the global scope if available
$current_user = $current_user ?? null;
$permissions = $permissions ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <!-- Include Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#4da3ff',
                            DEFAULT: '#2563eb',
                            dark: '#1e40af',
                        },
                        secondary: {
                            light: '#f3f4f6',
                            DEFAULT: '#e5e7eb',
                            dark: '#9ca3af',
                        },
                        danger: {
                            light: '#fca5a5',
                            DEFAULT: '#ef4444',
                            dark: '#b91c1c',
                        },
                        success: {
                            light: '#86efac',
                            DEFAULT: '#22c55e',
                            dark: '#15803d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom status badge styles */
        .status-pending { @apply bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium; }
        .status-in_progress { @apply bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium; }
        .status-completed { @apply bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="bg-primary shadow-md">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-white">Task Manager</h1>
                <nav class="flex space-x-4 text-white">
                    <?php if(isset($auth) && $auth->isLoggedIn()): ?>
                        <a href="/" class="hover:text-primary-light">Home</a>
                        <a href="/tasks" class="hover:text-primary-light">Tasks</a>
                        <?php if(isset($permissions) && $permissions->isAdmin()): ?>
                            <a href="/users" class="hover:text-primary-light">Users</a>
                        <?php endif; ?>
                        <span class="ml-6 flex items-center">
                            <span class="mr-2">
                                Welcome, <?php echo htmlspecialchars($current_user['username']); ?> 
                                <?php if(isset($current_user['role'])): ?>
                                    <span class="text-xs bg-white/20 px-1 py-0.5 rounded"><?php echo htmlspecialchars($current_user['role']); ?></span>
                                <?php endif; ?>
                            </span>
                            <a href="logout.php" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded">Logout</a>
                        </span>
                    <?php else: ?>
                        <a href="login.php" class="hover:text-primary-light">Login</a>
                        <a href="register.php" class="hover:text-primary-light">Register</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    <div class="container mx-auto px-4 py-6 flex-grow">
        <div class="bg-white shadow-md rounded-lg p-6">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
        