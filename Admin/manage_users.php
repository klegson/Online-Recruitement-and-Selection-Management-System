<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Handle user actions
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $userId = $_GET['id'];
        if ($userId != $_SESSION['user_id']) { // Prevent admin from deleting themselves
            $stmt = $pdo->prepare("DELETE FROM users WHERE userId = ?");
            $stmt->execute([$userId]);
            $_SESSION['success'] = "User deleted successfully!";
        } else {
            $_SESSION['error'] = "You cannot delete your own account!";
        }
        header("Location: manage_users.php");
        exit();
    }
}

// Fetch all users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$totalUsers = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];
$totalPages = ceil($totalUsers / $perPage);

$stmt = $pdo->prepare("
    SELECT userId, firstName, lastName, email, userRole, dateJoined 
    FROM users 
    ORDER BY dateJoined DESC 
    LIMIT ? OFFSET ?
");
$stmt->execute([$perPage, $offset]);
$users = $stmt->fetchAll();

include('../includes/header.php');
?>

<!-- Success/Error Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
                <p class="text-gray-600 text-sm mt-1">Manage system users and their roles</p>
            </div>
            <a href="add_user.php" class="primary-bg text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Add User
            </a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-4 px-6 font-semibold text-gray-700">User</th>
                        <th class="text-left py-4 px-6 font-semibold text-gray-700">Email</th>
                        <th class="text-left py-4 px-6 font-semibold text-gray-700">Role</th>
                        <th class="text-left py-4 px-6 font-semibold text-gray-700">Joined</th>
                        <th class="text-left py-4 px-6 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>
                                        </p>
                                        <?php if ($user['userId'] == $_SESSION['user_id']): ?>
                                            <span class="text-xs text-blue-600 font-medium">You</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-gray-600"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="py-4 px-6">
                                <span class="inline-block px-3 py-1 text-xs rounded-full font-medium
                                    <?= $user['userRole'] === 'Admin' ? 'bg-purple-100 text-purple-800' : 
                                       ($user['userRole'] === 'HR_Staff' ? 'bg-green-100 text-green-800' : 
                                       'bg-blue-100 text-blue-800') ?>">
                                    <?= htmlspecialchars($user['userRole']) ?>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                <?= date('M d, Y', strtotime($user['dateJoined'])) ?>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex space-x-2">
                                    <a href="edit_user.php?id=<?= $user['userId'] ?>" 
                                       class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <?php if ($user['userId'] != $_SESSION['user_id']): ?>
                                        <button onclick="confirmDelete(<?= $user['userId'] ?>)" 
                                                class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-600">
                        Showing <?= ($offset + 1) ?> to <?= min($offset + $perPage, $totalUsers) ?> of <?= $totalUsers ?> users
                    </p>
                    <div class="flex space-x-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm"><?= $i ?></span>
                            <?php else: ?>
                                <a href="?page=<?= $i ?>" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                                    <?= $i ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmDelete(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        window.location.href = 'manage_users.php?action=delete&id=' + userId;
    }
}
</script>

</div>
