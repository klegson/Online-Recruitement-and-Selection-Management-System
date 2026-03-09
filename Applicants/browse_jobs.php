<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Applicant') {
    header("Location: ../login.php");
    exit();
}

// Handle search and filters
$search = $_GET['search'] ?? '';
$department = $_GET['department'] ?? '';
$position = $_GET['position'] ?? '';

// Build query
$whereConditions = ["j.jobStatus = 'Open'", "j.deadline >= CURDATE()"];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(j.position LIKE ? OR j.department LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($department)) {
    $whereConditions[] = "j.department = ?";
    $params[] = $department;
}

if (!empty($position)) {
    $whereConditions[] = "j.position = ?";
    $params[] = $position;
}

$whereClause = "WHERE " . implode(" AND ", $whereConditions);

// Fetch jobs
$stmt = $pdo->prepare("
    SELECT j.*, 
           (SELECT COUNT(*) FROM applications a WHERE a.jobId = j.jobId) as applicationCount,
           DATEDIFF(CURDATE(), j.postedAt) as daysSincePosted
    FROM jobs j 
    $whereClause
    ORDER BY j.postedAt DESC
");
$stmt->execute($params);
$jobs = $stmt->fetchAll();

// Get unique departments and positions for filters
$departments = $pdo->query("SELECT DISTINCT department FROM jobs WHERE jobStatus = 'Open' ORDER BY department")->fetchAll();
$positions = $pdo->query("SELECT DISTINCT position FROM jobs WHERE jobStatus = 'Open' ORDER BY position")->fetchAll();

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
                <h1 class="text-2xl font-bold text-gray-800">Browse Job Opportunities</h1>
                <p class="text-gray-600 text-sm mt-1">Discover available positions at DepEd Region V</p>
            </div>
            <a href="applicants_dashboard.php" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Jobs</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Search by position or department..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= htmlspecialchars($dept['department']) ?>" <?= $department === $dept['department'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dept['department']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                    <select name="position" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Positions</option>
                        <?php foreach ($positions as $pos): ?>
                            <option value="<?= htmlspecialchars($pos['position']) ?>" <?= $position === $pos['position'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pos['position']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="primary-bg text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Search Jobs
                </button>
            </div>
        </form>
    </div>

    <!-- Jobs List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <?php if (!empty($jobs)): ?>
            <div class="divide-y divide-gray-200">
                <?php foreach ($jobs as $job): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                    <?= htmlspecialchars($job['position']) ?>
                                </h3>
                                <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                                    <span>
                                        <i class="fas fa-building mr-1"></i>
                                        <?= htmlspecialchars($job['department']) ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-money-bill-wave mr-1"></i>
                                        <?= htmlspecialchars($job['salaryGrade']) ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-users mr-1"></i>
                                        <?= $job['applicationCount'] ?> applicants
                                    </span>
                                </div>
                                <p class="text-gray-700 mb-4">
                                    <?= nl2br(htmlspecialchars(substr($job['description'], 0, 200))) ?>...
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4 text-sm">
                                        <span class="text-green-600 font-medium">
                                            <i class="fas fa-calendar-check mr-1"></i>
                                            Deadline: <?= date('M d, Y', strtotime($job['deadline'])) ?>
                                        </span>
                                        <span class="text-gray-600">
                                            <i class="fas fa-clock mr-1"></i>
                                            Posted: <?= date('M d, Y', strtotime($job['postedAt'])) ?>
                                        </span>
                                        <?php if ($job['daysSincePosted'] <= 7): ?>
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                                <i class="fas fa-sparkles mr-1"></i>NEW
                                            </span>
                                        <?php endif; ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                            <?= htmlspecialchars($job['jobStatus']) ?>
                                        </span>
                                    </div>
                                    <a href="apply_job.php?id=<?= $job['jobId'] ?>" 
                                       class="primary-bg text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-paper-plane mr-2"></i>Apply Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="p-12 text-center">
                <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No jobs found</h3>
                <p class="text-gray-500 mb-6">Try adjusting your search criteria or check back later for new opportunities.</p>
                <a href="browse_jobs.php" class="text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-redo mr-2"></i>Clear Filters
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

</div>
