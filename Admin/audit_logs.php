<?php
session_start();
require_once '../config/db.php';
require_once '../includes/admin_sidebar.php';
require_once '../includes/audit_functions.php';

// Pagination and filtering
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get filters
$filters = [];
if (!empty($_GET['userId'])) {
    $filters['userId'] = $_GET['userId'];
}
if (!empty($_GET['action'])) {
    $filters['action'] = $_GET['action'];
}
if (!empty($_GET['date_from'])) {
    $filters['date_from'] = $_GET['date_from'];
}
if (!empty($_GET['date_to'])) {
    $filters['date_to'] = $_GET['date_to'];
}

// Get data
$auditLogs = getAuditLogs($filters, $perPage, $offset);
$totalLogs = countAuditLogs($filters);
$totalPages = ceil($totalLogs / $perPage);

// Get users for filter dropdown
$users = $pdo->query("SELECT userId, firstName, lastName FROM users ORDER BY firstName, lastName")->fetchAll();
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

<!-- Tabs Navigation -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
            <button onclick="showTab('activity')" class="tab-btn active px-6 py-3 border-b-2 border-blue-500 font-medium text-blue-600 focus:outline-none" data-tab="activity">
                <i class="fas fa-history mr-2"></i>Activity History
            </button>
            <button onclick="showTab('login')" class="tab-btn px-6 py-3 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none" data-tab="login">
                <i class="fas fa-sign-in-alt mr-2"></i>Login History
            </button>
        </nav>
    </div>
</div>

<!-- Activity History Tab -->
<div id="activity-tab" class="tab-content">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">Activity History</h3>
            <button onclick="showFilters()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-filter mr-2"></i>Filters
            </button>
        </div>
        
        <!-- Filters Panel (Hidden by default) -->
        <div id="filtersPanel" class="hidden mb-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-semibold text-gray-800 mb-4">Filter Activity Logs</h4>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                    <select name="userId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Users</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['userId'] ?>" <?= isset($_GET['userId']) && $_GET['userId'] == $user['userId'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                    <input type="text" name="action" value="<?= htmlspecialchars($_GET['action'] ?? '') ?>" 
                           placeholder="Search actions..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2" placeholder="From">
                    <input type="date" name="date_to" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="To">
                </div>
                
                <div class="lg:col-span-4 flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Apply Filters
                    </button>
                    <a href="audit_logs.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Activity Logs Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Date & Time</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">User</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Action</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Entity</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Details</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($auditLogs as $log): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4 text-sm text-gray-600">
                                <?= date('M d, Y H:i:s', strtotime($log['timestamp'] ?? '')) ?>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">
                                    <?= htmlspecialchars($log['firstName'] . ' ' . $log['lastName']) ?>
                                </div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($log['userRole']) ?></div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    <?= htmlspecialchars($log['action']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                <?php 
                                // Extract entity info from details
                                $entityInfo = '';
                                if (preg_match('/\[Entity: (\w+) #(\d+)\]/', $log['details'], $matches)) {
                                    $entityInfo = '<span class="font-medium">' . htmlspecialchars($matches[1]) . '</span> #' . $matches[2];
                                }
                                echo $entityInfo ?: '-';
                                ?>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600 max-w-xs">
                                <div class="truncate" title="<?= htmlspecialchars($log['details']) ?>">
                                    <?= htmlspecialchars($log['details']) ?>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                <?= htmlspecialchars($log['ipAddress'] ?? 'Unknown') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-6 flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Showing <?= ($offset + 1) ?> to <?= min($offset + $perPage, $totalLogs) ?> of <?= $totalLogs ?> entries
                </div>
                <div class="flex space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?><?= isset($_SERVER['QUERY_STRING']) ? '&' . http_build_query($_GET) : '' ?>" 
                           class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Previous
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?= $i ?><?= isset($_SERVER['QUERY_STRING']) ? '&' . http_build_query($_GET) : '' ?>" 
                           class="px-3 py-2 <?= $i == $page ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50' ?> rounded-lg">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?><?= isset($_SERVER['QUERY_STRING']) ? '&' . http_build_query($_GET) : '' ?>" 
                           class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Login History Tab -->
<div id="login-tab" class="tab-content hidden">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">Login History</h3>
        </div>
        
        <?php
        // Get login history
        $loginHistory = getLoginHistory([], $perPage, $offset);
        $totalLogins = countLoginHistory([]);
        $totalLoginPages = ceil($totalLogins / $perPage);
        ?>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Date & Time</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">User</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Email</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Failure Reason</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loginHistory as $login): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4 text-sm text-gray-600">
                                <?= date('M d, Y H:i:s', strtotime($login['created_at'])) ?>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">
                                    <?= htmlspecialchars($login['firstName'] . ' ' . $login['lastName']) ?>
                                </div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($login['userRole']) ?></div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                <?= htmlspecialchars($login['email']) ?>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-1 text-xs rounded-full 
                                    <?= $login['status'] === 'Success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= htmlspecialchars($login['status']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                <?= htmlspecialchars($login['failure_reason'] ?? '-') ?>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                <?= htmlspecialchars($login['ip_address']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_sidebar_footer.php'; ?>

<script>
// Tab functionality
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    activeBtn.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
}

// Filter panel toggle
function showFilters() {
    const panel = document.getElementById('filtersPanel');
    panel.classList.toggle('hidden');
}
</script>
