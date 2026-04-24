<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Applicant') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch all applications for this user
$stmt = $pdo->prepare("
    SELECT a.*, j.position, j.department, j.salaryGrade, j.monthlySalary, j.deadline, j.jobStatus
    FROM applications a
    JOIN jobs j ON a.jobId = j.jobId
    WHERE a.userId = ?
    ORDER BY a.appliedAt DESC
");
$stmt->execute([$userId]);
$applications = $stmt->fetchAll();

// Handle delete application
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $applicationId = $_GET['delete'];
    
    // Verify this application belongs to the current user
    $stmt = $pdo->prepare("SELECT userId FROM applications WHERE applicationId = ?");
    $stmt->execute([$applicationId]);
    $app = $stmt->fetch();
    
    if ($app && $app['userId'] == $userId) {
        try {
            $pdo->beginTransaction();
            
            // Delete application files and physical files
            $stmt = $pdo->prepare("SELECT filePath FROM application_files WHERE applicationId = ?");
            $stmt->execute([$applicationId]);
            $files = $stmt->fetchAll();
            
            foreach ($files as $file) {
                if (file_exists($file['filePath'])) {
                    unlink($file['filePath']);
                }
            }
            
            // Delete application files
            $stmt = $pdo->prepare("DELETE FROM application_files WHERE applicationId = ?");
            $stmt->execute([$applicationId]);
            
            // Delete application
            $stmt = $pdo->prepare("DELETE FROM applications WHERE applicationId = ?");
            $stmt->execute([$applicationId]);
            
            $pdo->commit();
            $_SESSION['success'] = "Application deleted successfully!";
            
        } catch (Exception $e) {
            $pdo->rollback();
            $_SESSION['error'] = "Error deleting application: " . $e->getMessage();
        }
    }
    
    header("Location: my_applications.php");
    exit();
}

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

<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-8 my-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">My Applications</h1>
            <p class="text-gray-600 text-sm mt-1">Track and manage your job applications</p>
        </div>

        <?php if (!empty($applications)): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Application Code</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Position</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Department</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Salary Grade</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Applied Date</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-4 px-4">
                                    <span class="font-mono text-sm text-blue-600 font-medium">
                                        <?= htmlspecialchars($app['applicationCode'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div>
                                        <p class="font-medium text-gray-900"><?= htmlspecialchars($app['position']) ?></p>
                                        <p class="text-sm text-gray-500"><?= number_format($app['monthlySalary'], 2) ?>/month</p>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="text-gray-700"><?= htmlspecialchars($app['department']) ?></span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="text-gray-700"><?= htmlspecialchars($app['salaryGrade']) ?></span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="text-sm text-gray-600">
                                        <?= date('M d, Y', strtotime($app['appliedAt'])) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <?php
                                    $statusColors = [
                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                        'Qualified' => 'bg-green-100 text-green-800',
                                        'Disqualified' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusClass = $statusColors[$app['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="<?= $statusClass ?> text-xs px-2 py-1 rounded-full">
                                        <?= htmlspecialchars($app['status']) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex space-x-2">
                                        <a href="view_my_application.php?id=<?= $app['applicationId'] ?>" 
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        <?php if ($app['status'] === 'Pending'): ?>
                                            <a href="edit_application.php?id=<?= $app['applicationId'] ?>" 
                                               class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                            <button onclick="confirmDelete(<?= $app['applicationId'] ?>)" 
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
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
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fas fa-file-alt text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No applications yet</h3>
                <p class="text-gray-500 mb-6">Start applying for jobs to see them here</p>
                <a href="applicants_dashboard.php" class="primary-bg text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Browse Jobs
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmDelete(applicationId) {
    if (confirm('Are you sure you want to delete this application? This action cannot be undone.')) {
        window.location.href = 'my_applications.php?delete=' + applicationId;
    }
}
</script>

