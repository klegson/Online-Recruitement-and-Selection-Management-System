<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$userId = $_GET['id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE userId = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: manage_users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $userRole = $_POST['userRole'];
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($firstName) || empty($lastName) || empty($email) || empty($userRole)) {
        $_SESSION['error'] = "All fields except password are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
    } elseif (!empty($password) && strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters!";
    } else {
        try {
            // Check if email already exists (excluding current user)
            $stmt = $pdo->prepare("SELECT userId FROM users WHERE email = ? AND userId != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = "Email already exists!";
            } else {
                // Update user
                if (!empty($password)) {
                    // Update with new password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("
                        UPDATE users SET firstName = ?, lastName = ?, email = ?, password = ?, userRole = ? 
                        WHERE userId = ?
                    ");
                    $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $userRole, $userId]);
                } else {
                    // Update without changing password
                    $stmt = $pdo->prepare("
                        UPDATE users SET firstName = ?, lastName = ?, email = ?, userRole = ? 
                        WHERE userId = ?
                    ");
                    $stmt->execute([$firstName, $lastName, $email, $userRole, $userId]);
                }
                
                $_SESSION['success'] = "User updated successfully!";
                header("Location: manage_users.php");
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error updating user: " . $e->getMessage();
        }
    }
}

include('../includes/header.php');
?>

<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
            <a href="manage_users.php" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Users
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input type="text" name="firstName" value="<?= htmlspecialchars($user['firstName']) ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" name="lastName" value="<?= htmlspecialchars($user['lastName']) ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" name="password" minlength="6"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password (minimum 6 characters if changed)</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">User Role</label>
                <select name="userRole" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Role</option>
                    <option value="Admin" <?= $user['userRole'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="HR_Staff" <?= $user['userRole'] === 'HR_Staff' ? 'selected' : '' ?>>HR Staff</option>
                    <option value="Applicant" <?= $user['userRole'] === 'Applicant' ? 'selected' : '' ?>>Applicant</option>
                </select>
            </div>

            <!-- User Info -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">User Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">User ID:</span>
                        <span class="font-medium"><?= $user['userId'] ?></span>
                    </div>
                    <div>
                        <span class="text-gray-500">Joined:</span>
                        <span class="font-medium"><?= date('M d, Y', strtotime($user['dateJoined'])) ?></span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="manage_users.php" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="primary-bg text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Update User
                </button>
            </div>
        </form>
    </div>
</div>

</div>
