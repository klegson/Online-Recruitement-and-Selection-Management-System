<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    header("Location: ../login.php");
    exit();
}

// Fetch statistics
$totalJobs = $pdo->query("SELECT COUNT(*) as count FROM jobs")->fetch()['count'];
$openJobs = $pdo->query("SELECT COUNT(*) as count FROM jobs WHERE jobStatus = 'Open'")->fetch()['count'];
$totalApplications = $pdo->query("SELECT COUNT(*) as count FROM applications")->fetch()['count'];
$pendingApplications = $pdo->query("SELECT COUNT(*) as count FROM applications WHERE status = 'Pending'")->fetch()['count'];

// Fetch all jobs with application counts
$stmt = $pdo->query("
    SELECT j.*, 
           COUNT(a.applicationId) as application_count,
           COUNT(CASE WHEN a.status = 'Pending' THEN 1 END) as pending_count
    FROM jobs j 
    LEFT JOIN applications a ON j.jobId = a.jobId 
    GROUP BY j.jobId 
    ORDER BY j.createdAt DESC
");
$jobs = $stmt->fetchAll();

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

<!-- Success Message -->
<?php if (isset($_GET['success']) && $_GET['success'] === 'job_created'): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <i class="fas fa-check-circle mr-2"></i>Job posting created successfully!
    </div>
<?php endif; ?>

<!-- Welcome Section -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12 mb-8">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Welcome back, <?= htmlspecialchars($_SESSION['firstName'] ?? 'HR Staff') ?>!</h1>
                <p class="text-blue-100">Manage job postings and review applications</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-user-tie text-6xl text-blue-200"></i>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Jobs</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalJobs ?></p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-briefcase text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Open Jobs</p>
                <p class="text-2xl font-bold text-gray-800"><?= $openJobs ?></p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-door-open text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Applications</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalApplications ?></p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <i class="fas fa-users text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Pending Review</p>
                <p class="text-2xl font-bold text-gray-800"><?= $pendingApplications ?></p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-clock text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Job Management Section -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Job Postings</h2>
            <a href="create_job.php" class="primary-bg text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition flex items-center">
                <i class="fas fa-plus mr-2"></i>Post New Job
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applications</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($jobs as $job): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($job['position']) ?></p>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($job['plantillaItemNo']) ?></p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600"><?= htmlspecialchars($job['department']) ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <p class="text-sm text-gray-900"><?= htmlspecialchars($job['salaryGrade']) ?></p>
                            <p class="text-xs text-gray-500">₱<?= number_format($job['monthlySalary'], 2) ?></p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600"><?= date('M d, Y', strtotime($job['deadline'])) ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900"><?= $job['application_count'] ?></span>
                            <?php if ($job['pending_count'] > 0): ?>
                                <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">
                                    <?= $job['pending_count'] ?> pending
                                </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($job['jobStatus'] === 'Open'): ?>
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Open</span>
                        <?php else: ?>
                            <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">Closed</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-3">
                            <a href="applications.php?job=<?= $job['jobId'] ?>" class="text-blue-600 hover:text-blue-900" title="View Applications">
                                <i class="fas fa-users"></i>
                            </a>
                            <a href="edit_job.php?id=<?= $job['jobId'] ?>" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="../actions/toggle_job_status.php?id=<?= $job['jobId'] ?>" class="text-gray-600 hover:text-gray-900" title="Toggle Status">
                                <i class="fas fa-power-off"></i>
                            </a>
                            <a href="../actions/delete_job_action.php?id=<?= $job['jobId'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this job posting?')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($jobs)): ?>
        <div class="text-center py-12">
            <i class="fas fa-briefcase text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500 text-lg">No job postings yet</p>
            <p class="text-gray-400 text-sm mt-2">Create your first job posting to get started</p>
        </div>
        <?php endif; ?>
    </div>
</div>
