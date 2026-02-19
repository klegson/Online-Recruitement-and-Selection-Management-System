<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $userRole = $_POST['userRole'];
    
    // Validate input
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($userRole)) {
        $_SESSION['error'] = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
    } elseif (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters!";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT userId FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = "Email already exists!";
            } else {
                // Create user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO users (firstName, lastName, email, password, userRole, dateJoined) 
                    VALUES (?, ?, ?, ?, ?, CURDATE())
                ");
                $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $userRole]);
                
                $_SESSION['success'] = "User created successfully!";
                header("Location: manage_users.php");
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error creating user: " . $e->getMessage();
        }
    }
}

include('../includes/header.php');
?>

<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Add New User</h1>
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
                    <input type="text" name="firstName" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" name="lastName" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required minlength="6"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">User Role</label>
                <select name="userRole" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="HR_Staff">HR Staff</option>
                    <option value="Applicant">Applicant</option>
                </select>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="manage_users.php" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="primary-bg text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Create User
                </button>
            </div>
        </form>
    </div>
</div>

</div>
