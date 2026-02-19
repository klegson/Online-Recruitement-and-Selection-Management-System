<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Recruitment System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .primary-color { color: #06508c; }
        .primary-bg { background-color: #06508c; }
        .primary-hover:hover { background-color: #054073; }
        .light-blue { background-color: #e8f2ff; }
        .text-light-blue { color: #5a9fd4; }
        .border-primary { border-color: #06508c; }
        .deped-blue { color: #003366; }
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #06508c;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        .nav-link:hover {
            color: #06508c;
            transform: translateY(-1px);
        }
        .nav-link.active {
            color: #06508c;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="../images/navlogo.jpg" alt="DepEd Logo" class="h-16 w-auto mr-3">
                    <div class="deped-blue">
                        <div class="text-xs font-bold leading-none">REPUBLIC OF THE PHILIPPINES</div>
                        <div class="text-lg font-black leading-none">DEPARTMENT OF EDUCATION</div>
                        <div class="text-sm font-bold leading-none">REGION V</div>
                        <div class="text-xs font-medium leading-none mt-1">DepEd Bicol CARES, SHARES and SERVES with a SMILE</div>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8 flex-1">
                    <div class="flex items-center space-x-8 ml-48">
                        <?php if ($_SESSION['user_role'] === 'HR_Staff'): ?>
                            <a href="../HR_staff/hr_dashboard.php" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">Dashboard</a>
                            <a href="../HR_staff/create_job.php" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">Post Job</a>
                            <a href="../HR_staff/applications.php" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">Applications</a>
                        <?php elseif ($_SESSION['user_role'] === 'Admin'): ?>
                            <a href="../Admin/admin_dashboard.php" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">Dashboard</a>
                        <?php elseif ($_SESSION['user_role'] === 'Applicant'): ?>
                            <a href="../Applicants/applicants_dashboard.php" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">Dashboard</a>
                            <a href="../Applicants/browse_jobs.php" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">Browse Jobs</a>
                            <a href="../Applicants/my_applications.php" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">My Applications</a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- User Info and Logout Dropdown -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="relative">
                        <button onclick="toggleUserDropdown()" class="flex items-center space-x-2 border border-gray-300 bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                            <i class="fas fa-user"></i>
                            <span><?= htmlspecialchars((isset($_SESSION['firstName']) ? $_SESSION['firstName'] : '') . ' ' . (isset($_SESSION['lastName']) ? $_SESSION['lastName'] : '')) ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                            <div class="py-1">
                                <div class="px-4 py-2 text-sm text-gray-500 border-b border-gray-100">
                                    <?= htmlspecialchars($_SESSION['user_role']) ?>
                                </div>
                                <a href="../actions/logout.php" onclick="return confirm('Are you sure you want to logout?')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Mobile Menu Button -->
                <button class="md:hidden primary-color" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <?php if ($_SESSION['user_role'] === 'HR_Staff'): ?>
                    <a href="../HR_staff/hr_dashboard.php" class="block py-2 nav-link primary-color hover:text-light-blue">Dashboard</a>
                    <a href="../HR_staff/create_job.php" class="block py-2 nav-link primary-color hover:text-light-blue">Post Job</a>
                    <a href="../HR_staff/applications.php" class="block py-2 nav-link primary-color hover:text-light-blue">Applications</a>
                <?php elseif ($_SESSION['user_role'] === 'Admin'): ?>
                    <a href="../Admin/admin_dashboard.php" class="block py-2 nav-link primary-color hover:text-light-blue">Dashboard</a>
                <?php elseif ($_SESSION['user_role'] === 'Applicant'): ?>
                    <a href="../Applicants/applicants_dashboard.php" class="block py-2 nav-link primary-color hover:text-light-blue">Dashboard</a>
                    <a href="../Applicants/browse_jobs.php" class="block py-2 nav-link primary-color hover:text-light-blue">Browse Jobs</a>
                    <a href="../Applicants/my_applications.php" class="block py-2 nav-link primary-color hover:text-light-blue">My Applications</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        }
        
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('button[onclick="toggleUserDropdown()"]');
            
            if (!button && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>

    <div class="max-w-7xl mx-auto px-4 py-6">