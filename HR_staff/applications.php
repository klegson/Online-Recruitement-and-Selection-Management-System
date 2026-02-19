<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    header("Location: ../login.php");
    exit();
}

// Fetch all applications with job and user details
$stmt = $pdo->query("
    SELECT a.*, j.position, j.department, j.deadline,
           u.firstName, u.lastName, u.email
    FROM applications a
    JOIN jobs j ON a.jobId = j.jobId
    JOIN users u ON a.userId = u.userId
    ORDER BY a.appliedAt DESC
");
$applications = $stmt->fetchAll();

// Get statistics
$totalApplications = count($applications);
$pendingCount = count(array_filter($applications, fn($app) => $app['status'] === 'Pending'));
$shortlistedCount = count(array_filter($applications, fn($app) => $app['status'] === 'Shortlisted'));
$rejectedCount = count(array_filter($applications, fn($app) => $app['status'] === 'Rejected'));

include('../includes/header.php');
?>

<!-- Success Message -->
<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <i class="fas fa-check-circle mr-2"></i>
        <?php 
        if ($_GET['success'] === 'updated') echo 'Application status updated successfully!';
        elseif ($_GET['success'] === 'deleted') echo 'Application deleted successfully!';
        ?>
    </div>
<?php endif; ?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Applications Management</h1>
    <p class="text-gray-600 text-sm mt-1">Review and manage job applications</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Applications</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalApplications ?></p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-file-alt text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Pending Review</p>
                <p class="text-2xl font-bold text-gray-800"><?= $pendingCount ?></p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Shortlisted</p>
                <p class="text-2xl font-bold text-gray-800"><?= $shortlistedCount ?></p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-user-check text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Rejected</p>
                <p class="text-2xl font-bold text-gray-800"><?= $rejectedCount ?></p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-user-times text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Applications Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-800">All Applications</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($applications as $app): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                <?= htmlspecialchars($app['firstName'] . ' ' . $app['lastName']) ?>
                            </p>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($app['email']) ?></p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900"><?= htmlspecialchars($app['position']) ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600"><?= htmlspecialchars($app['department']) ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600"><?= date('M d, Y', strtotime($app['appliedAt'])) ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $statusColors = [
                            'Pending' => 'bg-yellow-100 text-yellow-800',
                            'Shortlisted' => 'bg-green-100 text-green-800',
                            'Rejected' => 'bg-red-100 text-red-800',
                            'Hired' => 'bg-blue-100 text-blue-800'
                        ];
                        $statusClass = $statusColors[$app['status']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="<?= $statusClass ?> text-xs px-2 py-1 rounded-full">
                            <?= htmlspecialchars($app['status']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-3">
                            <a href="view_application.php?id=<?= $app['applicationId'] ?>" class="text-blue-600 hover:text-blue-900" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit_application.php?id=<?= $app['applicationId'] ?>" class="text-yellow-600 hover:text-yellow-900" title="Edit Status">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="../actions/delete_application_action.php?id=<?= $app['applicationId'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this application?')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($applications)): ?>
        <div class="text-center py-12">
            <i class="fas fa-file-alt text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500 text-lg">No applications yet</p>
            <p class="text-gray-400 text-sm mt-2">Applications will appear here when candidates apply for jobs</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
