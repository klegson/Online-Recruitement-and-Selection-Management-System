<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Board') {
    header("Location: ../login.php");
    exit();
}

// Fetch qualified applicants with their files and HR reviews
$stmt = $pdo->query("
    SELECT a.*, j.position, j.department, j.salaryGrade, j.monthlySalary,
           u.firstName, u.lastName, u.email, u.dateJoined,
           updater.firstName as hrFirstName, updater.lastName as hrLastName,
           GROUP_CONCAT(DISTINCT CONCAT(dt.name, '|', af.filePath) SEPARATOR '||') as files
    FROM applications a
    JOIN jobs j ON a.jobId = j.jobId
    JOIN users u ON a.userId = u.userId
    LEFT JOIN users updater ON a.updatedBy = updater.userId
    LEFT JOIN application_files af ON a.applicationId = af.applicationId
    LEFT JOIN document_types dt ON af.documentTypeId = dt.id
    WHERE a.status = 'Qualified' AND a.updatedBy IS NOT NULL
    GROUP BY a.applicationId
    ORDER BY a.appliedAt DESC
");
$qualifiedApplicants = $stmt->fetchAll();

// Get statistics
$totalQualified = count($qualifiedApplicants);
$positionsCount = $pdo->query("SELECT COUNT(DISTINCT jobId) as count FROM applications WHERE status = 'Qualified' AND updatedBy IS NOT NULL")->fetch()['count'];

include('../includes/header.php');
?>

<!-- Welcome Section -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-800 text-white py-12 mb-8">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Board Review Dashboard</h1>
                <p class="text-blue-100">Review qualified applicants recommended by HR staff</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-users text-6xl text-blue-200"></i>
            </div>
        </div>
    </div>
</div>

<!-- Fixed Statistics Bar -->
<div class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm mb-8">
    <div class="container mx-auto px-4 py-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Qualified Applicants</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $totalQualified ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-user-check text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Open Positions</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $positionsCount ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-briefcase text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">HR Reviewed</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $totalQualified ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-clipboard-check text-purple-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Last Updated</p>
                        <p class="text-lg font-bold text-gray-800"><?= date('M d, Y') ?></p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<!-- Qualified Candidates Section -->
<div class="container mx-auto px-4">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Qualified Applicants</h2>
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <i class="fas fa-info-circle"></i>
                    <span>Applicants reviewed and qualified by HR staff</span>
                </div>
            </div>
        </div>
        
        <?php if (!empty($qualifiedApplicants)): ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 p-6">
                <?php foreach ($qualifiedApplicants as $applicant): ?>
                    <div class="border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 text-white rounded-t-xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-lg">
                                            <?= strtoupper(substr($applicant['firstName'], 0, 1)) ?>
                                        </span>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-lg">
                                            <?= htmlspecialchars($applicant['firstName'] . ' ' . $applicant['lastName']) ?>
                                        </h3>
                                        <p class="text-blue-100 text-sm">
                                            <?= htmlspecialchars($applicant['position']) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                        Qualified
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4">
                            <!-- Quick Info -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 uppercase">Department</p>
                                    <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($applicant['department']) ?></p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 uppercase">Salary Grade</p>
                                    <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($applicant['salaryGrade']) ?></p>
                                </div>
                            </div>

                            <!-- Contact Info -->
                            <div class="mb-4 space-y-2">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-envelope mr-2 text-gray-400 w-4"></i>
                                    <span class="truncate"><?= htmlspecialchars($applicant['email']) ?></span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-calendar mr-2 text-gray-400 w-4"></i>
                                    Applied: <?= date('M d, Y', strtotime($applicant['appliedAt'])) ?>
                                </div>
                            </div>

                            <!-- HR Review -->
                            <?php if ($applicant['hrFirstName']): ?>
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mb-4 rounded">
                                    <p class="text-xs font-semibold text-blue-800 mb-1">HR Reviewer</p>
                                    <p class="text-sm text-blue-700">
                                        <?= htmlspecialchars($applicant['hrFirstName'] . ' ' . $applicant['hrLastName']) ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <!-- HR Notes -->
                            <?php if (!empty($applicant['hrNotes'])): ?>
                                <div class="bg-amber-50 border-l-4 border-amber-400 p-3 mb-4 rounded">
                                    <p class="text-xs font-semibold text-amber-800 mb-1">HR Notes</p>
                                    <p class="text-sm text-amber-700">
                                        <?= htmlspecialchars(substr($applicant['hrNotes'], 0, 80)) ?>...
                                    </p>
                                </div>
                            <?php endif; ?>

                            <!-- Documents -->
                            <div class="mb-4">
                                <p class="text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-file-pdf mr-2"></i>Documents (<?= !empty($applicant['files']) ? count(explode('||', $applicant['files'])) : 0 ?>)
                                </p>
                                <?php if (!empty($applicant['files'])): ?>
                                    <div class="flex flex-wrap gap-2">
                                        <?php 
                                        $filesArray = explode('||', $applicant['files']);
                                        $fileCount = 0;
                                        foreach ($filesArray as $fileData): 
                                            if (!empty($fileData) && $fileCount < 2) {
                                                list($docName, $filePath) = explode('|', $fileData);
                                        ?>
                                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">
                                                <i class="fas fa-file-pdf mr-1"></i>
                                                <?= htmlspecialchars(substr($docName, 0, 12)) ?>...
                                            </span>
                                        <?php 
                                                $fileCount++;
                                            }
                                        endforeach; 
                                        if (count($filesArray) > 2): ?>
                                            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                                                +<?= count($filesArray) - 2 ?> more
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-sm text-gray-500 italic">No documents uploaded</p>
                                <?php endif; ?>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2 pt-3 border-t border-gray-200">
                                <a href="view_applicant.php?id=<?= $applicant['applicationId'] ?>" 
                                        class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium text-center block">
                                    <i class="fas fa-eye mr-1"></i>View Details
                                </a>
                                <button onclick="recommendForHiring(<?= $applicant['applicationId'] ?>)" 
                                        class="flex-1 bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                                    <i class="fas fa-thumbs-up mr-1"></i>Recommend
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-16">
                <i class="fas fa-user-check text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Qualified Applicants Yet</h3>
                <p class="text-gray-500 mb-4">HR staff hasn't qualified any applicants for review</p>
                <p class="text-sm text-gray-400">Qualified applicants will appear here once HR reviews and approves them</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>

<script>
function recommendForHiring(applicationId) {
    if (confirm('Are you sure you want to recommend this applicant for hiring?')) {
        fetch('actions/recommend_candidate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'applicationId=' + applicationId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Applicant recommended for hiring successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to recommend applicant. Please try again.');
        });
    }
}
</script>
