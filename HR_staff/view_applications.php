<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: hr_dashboard.php");
    exit();
}

$jobId = $_GET['id'];

// Fetch job details
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE jobId = ?");
$stmt->execute([$jobId]);
$job = $stmt->fetch();

if (!$job) {
    header("Location: hr_dashboard.php");
    exit();
}

// Fetch applications for this job
$stmt = $pdo->prepare("
    SELECT a.*, u.firstName, u.lastName, u.email 
    FROM applications a 
    JOIN users u ON a.userId = u.userId 
    WHERE a.jobId = ? 
    ORDER BY a.appliedAt DESC
");
$stmt->execute([$jobId]);
$applications = $stmt->fetchAll();

include('../includes/header.php');
?>

<!-- Breadcrumb -->
<div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="hr_dashboard.php" class="text-gray-700 hover:text-gray-900">
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500">Applications</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Job Info Header -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($job['position']) ?></h1>
            <p class="text-gray-600 mt-1"><?= htmlspecialchars($job['department']) ?></p>
        </div>
        <div class="text-right">
            <span class="<?= $job['jobStatus'] === 'Open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?> text-xs px-2 py-1 rounded-full">
                <?= htmlspecialchars($job['jobStatus']) ?>
            </span>
            <p class="text-sm text-gray-500 mt-2">Total Applications: <?= count($applications) ?></p>
        </div>
    </div>
</div>

<!-- Applications List -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-800">Applicants</h2>
    </div>
    
    <?php if (!empty($applications)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($applications as $app): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-blue-600 font-semibold text-sm">
                                        <?= strtoupper(substr($app['firstName'], 0, 1)) ?>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($app['firstName'] . ' ' . $app['lastName']) ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600"><?= htmlspecialchars($app['email']) ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600">
                                <?= date('M d, Y', strtotime($app['appliedAt'])) ?>
                            </span>
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
                            <div class="flex items-center space-x-2">
                                <a href="view_application.php?id=<?= $app['applicationId'] ?>" class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="mailto:<?= htmlspecialchars($app['email']) ?>" class="text-green-600 hover:text-green-900" title="Email Applicant">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500 text-lg">No applications yet</p>
            <p class="text-gray-400 text-sm mt-2">Applications will appear here when candidates apply for this position</p>
        </div>
    <?php endif; ?>
</div>

<div class="mt-6">
    <a href="hr_dashboard.php" class="inline-flex items-center text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Dashboard
    </a>
</div>

<?php include('../includes/footer.php'); ?>
