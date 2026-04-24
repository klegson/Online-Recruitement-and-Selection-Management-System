<?php
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DepEd Region V</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .primary-color { color: #06508c; }
        .primary-bg { background-color: #06508c; }
        .primary-hover:hover { background-color: #054073; }
        .sidebar-item {
            transition: all 0.3s ease;
        }
        .sidebar-item:hover {
            background-color: #054073;
            transform: translateX(4px);
        }
        .sidebar-item.active {
            background-color: #054073;
            border-left: 4px solid #ffffff;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-900 text-white">
        <!-- Logo Section -->
        <div class="p-6 border-b border-blue-800">
            <div class="flex items-center">
                <img src="../images/navlogo.jpg" alt="DepEd Logo" class="h-10 w-auto mr-3">
                <div>
                    <div class="text-lg font-bold">DepEd Region V</div>
                    <div class="text-xs text-blue-300">Admin Portal</div>
                </div>
            </div>
        </div>
        
        <!-- User Info -->
        <div class="p-4 border-b border-blue-800">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-700 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div class="font-semibold"><?php echo htmlspecialchars($_SESSION['firstName'] . ' ' . $_SESSION['lastName']); ?></div>
                    <div class="text-xs text-blue-300">Administrator</div>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="admin_dashboard.php" class="sidebar-item flex items-center p-3 rounded-lg text-white hover:text-white <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="manage_users.php" class="sidebar-item flex items-center p-3 rounded-lg text-white hover:text-white <?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : ''; ?>">
                        <i class="fas fa-users w-5 mr-3"></i>
                        Users
                    </a>
                </li>
                <li>
                    <a href="job_management.php" class="sidebar-item flex items-center p-3 rounded-lg text-white hover:text-white <?php echo basename($_SERVER['PHP_SELF']) == 'job_management.php' ? 'active' : ''; ?>">
                        <i class="fas fa-briefcase w-5 mr-3"></i>
                        Job Management
                    </a>
                </li>
                <li>
                    <a href="audit_logs.php" class="sidebar-item flex items-center p-3 rounded-lg text-white hover:text-white <?php echo basename($_SERVER['PHP_SELF']) == 'audit_logs.php' ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-list w-5 mr-3"></i>
                        Audit Logs
                    </a>
                </li>
                <li>
                    <a href="settings.php" class="sidebar-item flex items-center p-3 rounded-lg text-white hover:text-white <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                        <i class="fas fa-cog w-5 mr-3"></i>
                        Settings
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Bottom Section -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-blue-800">
            <a href="../actions/logout.php" class="flex items-center p-3 rounded-lg text-white hover:bg-red-600 transition-colors">
                <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                Logout
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="ml-64">
        <!-- Top Bar -->
        <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">
                        <?php echo date('F d, Y h:i A'); ?>
                    </span>
                    <button onclick="toggleMobileSidebar()" class="md:hidden text-gray-600 hover:text-gray-800">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Page Content -->
        <div class="p-6">
