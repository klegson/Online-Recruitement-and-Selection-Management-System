</div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="mobileSidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden" onclick="toggleMobileSidebar()"></div>
    
    <!-- Mobile Sidebar -->
    <div id="mobileSidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-900 text-white transform -translate-x-full transition-transform duration-300 md:hidden">
        <!-- Mobile Logo Section -->
        <div class="p-6 border-b border-blue-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <img src="../images/navlogo.jpg" alt="DepEd Logo" class="h-10 w-auto mr-3">
                    <div>
                        <div class="text-lg font-bold">DepEd Region V</div>
                        <div class="text-xs text-blue-300">Admin Portal</div>
                    </div>
                </div>
                <button onclick="toggleMobileSidebar()" class="text-white hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile User Info -->
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
        
        <!-- Mobile Navigation -->
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="admin_dashboard.php" class="sidebar-item flex items-center p-3 rounded-lg text-white hover:text-white">
                        <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="manage_users.php" class="sidebar-item flex items-center p-3 rounded-lg text-white hover:text-white">
                        <i class="fas fa-users w-5 mr-3"></i>
                        Users
                    </a>
                </li>
                <li>
                    <a href="job_management.php" class="sidebar-item flex items-center p-3 rounded-lg text-white hover:text-white">
                        <i class="fas fa-briefcase w-5 mr-3"></i>
                        Job Management
                    </a>
                </li>
                <li>
                    <a href="audit_logs.php" class="sidebar-item flex items-center p-3 rounded-lg text-white hover:text-white">
                        <i class="fas fa-clipboard-list w-5 mr-3"></i>
                        Audit Logs
                    </a>
                </li>
                <li>
                    <a href="settings.php" class="sidebar-item flex items-center p-3 rounded-lg text-white hover:text-white">
                        <i class="fas fa-cog w-5 mr-3"></i>
                        Settings
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Mobile Bottom Section -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-blue-800">
            <a href="../actions/logout.php" class="flex items-center p-3 rounded-lg text-white hover:bg-red-600 transition-colors">
                <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                Logout
            </a>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('mobileSidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        
        // Auto-hide mobile sidebar on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                document.getElementById('mobileSidebar').classList.add('-translate-x-full');
                document.getElementById('mobileSidebarOverlay').classList.add('hidden');
            }
        });
        
        // Close sidebar when clicking on navigation items (mobile)
        document.querySelectorAll('#mobileSidebar .sidebar-item').forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    toggleMobileSidebar();
                }
            });
        });
    </script>
</body>
</html>
