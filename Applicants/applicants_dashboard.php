<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Applicant') {
    header("Location: ../login.php");
    exit();
}
?>

<?php
$userId = $_SESSION['user_id'];

// Fetch user statistics
$totalApplications = $pdo->prepare("SELECT COUNT(*) as count FROM applications WHERE userId = ?");
$totalApplications->execute([$userId]);
$totalApplications = $totalApplications->fetch()['count'];

$pendingApplications = $pdo->prepare("SELECT COUNT(*) as count FROM applications WHERE userId = ? AND status = 'Pending'");
$pendingApplications->execute([$userId]);
$pendingApplications = $pendingApplications->fetch()['count'];

$shortlistedApplications = $pdo->prepare("SELECT COUNT(*) as count FROM applications WHERE userId = ? AND status = 'Shortlisted'");
$shortlistedApplications->execute([$userId]);
$shortlistedApplications = $shortlistedApplications->fetch()['count'];

$hiredApplications = $pdo->prepare("SELECT COUNT(*) as count FROM applications WHERE userId = ? AND status = 'Hired'");
$hiredApplications->execute([$userId]);
$hiredApplications = $hiredApplications->fetch()['count'];

// Fetch recent applications
$stmt = $pdo->prepare("
    SELECT a.*, j.position, j.department, j.salaryGrade, j.deadline 
    FROM applications a 
    JOIN jobs j ON a.jobId = j.jobId 
    WHERE a.userId = ? 
    ORDER BY a.appliedAt DESC 
    LIMIT 10
");
$stmt->execute([$userId]);
$recentApplications = $stmt->fetchAll();

// Fetch recommended jobs (open jobs with deadline not passed)
$stmt = $pdo->prepare("
    SELECT * FROM jobs 
    WHERE jobStatus = 'Open' AND deadline >= CURDATE() 
    ORDER BY createdAt DESC 
    LIMIT 8
");
$stmt->execute();
$recommendedJobs = $stmt->fetchAll();

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

<!-- Welcome Section -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12 mb-8">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Welcome back, <?= htmlspecialchars($_SESSION['firstName'] ?? 'Applicant') ?>!</h1>
                <p class="text-blue-100">Track your applications and discover new opportunities</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-user-graduate text-6xl text-blue-200"></i>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Applications</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalApplications ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-alt text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Pending</p>
                <p class="text-2xl font-bold text-gray-800"><?= $pendingApplications ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Shortlisted</p>
                <p class="text-2xl font-bold text-gray-800"><?= $shortlistedApplications ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-star text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Hired</p>
                <p class="text-2xl font-bold text-gray-800"><?= $hiredApplications ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-trophy text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Applications -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Recent Applications</h2>
                <a href="my_applications.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
        </div>
        
        <?php if (!empty($recentApplications)): ?>
            <div class="p-6 space-y-4">
                <?php foreach ($recentApplications as $app): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($app['position']) ?></h3>
                            <p class="text-sm text-gray-600"><?= htmlspecialchars($app['department']) ?></p>
                            <p class="text-xs text-gray-500 mt-1">Applied: <?= date('M d, Y', strtotime($app['appliedAt'])) ?></p>
                        </div>
                        <div class="text-right">
                            <?php
                            $statusColors = [
                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                'Shortlisted' => 'bg-green-100 text-green-800',
                                'Rejected' => 'bg-red-100 text-red-800',
                                'Hired' => 'bg-purple-100 text-purple-800'
                            ];
                            $statusClass = $statusColors[$app['status']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="<?= $statusClass ?> text-xs px-2 py-1 rounded-full">
                                <?= htmlspecialchars($app['status']) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="p-12 text-center">
                <i class="fas fa-file-alt text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">No applications yet</p>
                <p class="text-gray-400 text-sm mt-2">Start applying for jobs to see them here</p>
                <a href="browse_jobs.php" class="inline-block mt-4 primary-bg text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Browse Jobs
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Recommended Jobs -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Recommended Jobs</h2>
                <a href="browse_jobs.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Browse All</a>
            </div>
        </div>
        
        <?php if (!empty($recommendedJobs)): ?>
            <div class="p-6 space-y-4">
                <?php foreach ($recommendedJobs as $job): ?>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($job['position']) ?></h3>
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                <?= htmlspecialchars($job['jobStatus']) ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($job['department']) ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-money-bill-wave mr-1"></i>
                                <?= htmlspecialchars($job['salaryGrade']) ?>
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Deadline: <?= date('M d', strtotime($job['deadline'])) ?>
                            </span>
                        </div>
                        <a href="apply_job.php?id=<?= $job['jobId'] ?>" class="mt-3 inline-block w-full text-center primary-bg text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                            Apply Now
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="p-12 text-center">
                <i class="fas fa-briefcase text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">No job openings</p>
                <p class="text-gray-400 text-sm mt-2">Check back later for new opportunities</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-8 bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="browse_jobs.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-10 h-10 primary-bg rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-search text-white"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Browse Jobs</h3>
                <p class="text-sm text-gray-600">Find new opportunities</p>
            </div>
        </a>
        
        <a href="my_applications.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-list text-white"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">My Applications</h3>
                <p class="text-sm text-gray-600">Track your progress</p>
            </div>
        </a>
        
        <a href="#" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-user-edit text-white"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Update Profile</h3>
                <p class="text-sm text-gray-600">Keep your info current</p>
            </div>
        </a>
    </div>
</div>

<?php include('../includes/footer.php'); ?>