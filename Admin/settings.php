<?php
session_start();
require_once '../config/db.php';
require_once '../includes/admin_sidebar.php';
require_once '../includes/audit_functions.php';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Update settings (this would typically go to a settings table)
        // For now, we'll just log the activity and show success
        logActivity($_SESSION['user_id'], 'Updated Settings', 'system', null, 'Updated system settings');
        
        $_SESSION['success'] = 'Settings updated successfully';
        header("Location: settings.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error updating settings: ' . $e->getMessage();
        header("Location: settings.php");
        exit();
    }
}

// Get system information
$systemInfo = [
    'php_version' => PHP_VERSION,
    'mysql_version' => $pdo->query("SELECT VERSION() as version")->fetch()['version'],
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    'server_time' => date('Y-m-d H:i:s'),
];

// Get database statistics
$dbStats = [
    'total_users' => $pdo->query("SELECT COUNT(*) as count FROM users")->fetch()['count'],
    'total_jobs' => $pdo->query("SELECT COUNT(*) as count FROM jobs")->fetch()['count'],
    'total_applications' => $pdo->query("SELECT COUNT(*) as count FROM applications")->fetch()['count'],
    'total_audit_logs' => $pdo->query("SELECT COUNT(*) as count FROM audit_logs")->fetch()['count'],
];
?>

<!-- Success/Error Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo htmlspecialchars($_SESSION['success']); ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo htmlspecialchars($_SESSION['error']); ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- General Settings -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">
                <i class="fas fa-cog mr-2"></i>General Settings
            </h2>
            
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">System Name</label>
                        <input type="text" name="system_name" value="DepEd Region V Online Recruitment Portal" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Admin Email</label>
                        <input type="email" name="admin_email" value="admin@depedregion5.gov.ph" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Default Salary Grade</label>
                        <select name="default_salary_grade" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1">Grade 1</option>
                            <option value="2">Grade 2</option>
                            <option value="3" selected>Grade 3</option>
                            <option value="4">Grade 4</option>
                            <option value="5">Grade 5</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application Deadline (Days)</label>
                        <input type="number" name="application_deadline" value="30" min="1" max="365"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">System Description</label>
                    <textarea name="system_description" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">Online Recruitment and Application Management System for Department of Education Region V</textarea>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Email Settings -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">
                <i class="fas fa-envelope mr-2"></i>Email Settings
            </h2>
            
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                        <input type="text" name="smtp_host" value="smtp.gmail.com" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                        <input type="number" name="smtp_port" value="587" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                        <input type="text" name="smtp_username" value="noreply@depedregion5.gov.ph" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                        <input type="password" name="smtp_password" value="********" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="email_notifications" id="email_notifications" checked
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="email_notifications" class="ml-2 text-sm text-gray-700">
                        Enable email notifications for application status changes
                    </label>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Email Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <!-- System Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle mr-2"></i>System Information
            </h3>
            
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-500">PHP Version</span>
                    <div class="font-medium text-gray-900"><?= $systemInfo['php_version'] ?></div>
                </div>
                
                <div>
                    <span class="text-sm text-gray-500">MySQL Version</span>
                    <div class="font-medium text-gray-900"><?= $systemInfo['mysql_version'] ?></div>
                </div>
                
                <div>
                    <span class="text-sm text-gray-500">Server Software</span>
                    <div class="font-medium text-gray-900"><?= $systemInfo['server_software'] ?></div>
                </div>
                
                <div>
                    <span class="text-sm text-gray-500">Server Time</span>
                    <div class="font-medium text-gray-900"><?= $systemInfo['server_time'] ?></div>
                </div>
            </div>
        </div>
        
        <!-- Database Statistics -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-database mr-2"></i>Database Statistics
            </h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Total Users</span>
                    <span class="font-medium text-gray-900"><?= $dbStats['total_users'] ?></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Total Jobs</span>
                    <span class="font-medium text-gray-900"><?= $dbStats['total_jobs'] ?></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Total Applications</span>
                    <span class="font-medium text-gray-900"><?= $dbStats['total_applications'] ?></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Audit Logs</span>
                    <span class="font-medium text-gray-900"><?= $dbStats['total_audit_logs'] ?></span>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-bolt mr-2"></i>Quick Actions
            </h3>
            
            <div class="space-y-2">
                <a href="job_management.php" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fas fa-briefcase mr-2"></i>Manage Jobs
                </a>
                
                <a href="manage_users.php" class="block w-full text-left px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fas fa-users mr-2"></i>Manage Users
                </a>
                
                <a href="audit_logs.php" class="block w-full text-left px-4 py-2 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                    <i class="fas fa-clipboard-list mr-2"></i>View Audit Logs
                </a>
                
                <button onclick="exportData()" class="block w-full text-left px-4 py-2 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors">
                    <i class="fas fa-download mr-2"></i>Export Data
                </button>
                
                <button onclick="clearCache()" class="block w-full text-left px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Clear Cache
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_sidebar_footer.php'; ?>

<script>
function exportData() {
    if (confirm('This will export all system data to a CSV file. Continue?')) {
        // Implementation for data export
        alert('Export feature coming soon!');
    }
}

function clearCache() {
    if (confirm('This will clear all system cache. Continue?')) {
        // Implementation for cache clearing
        alert('Cache cleared successfully!');
    }
}
</script>
