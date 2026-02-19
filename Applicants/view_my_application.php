<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Applicant') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: my_applications.php");
    exit();
}

$applicationId = $_GET['id'];
$userId = $_SESSION['user_id'];

// Fetch application details
$stmt = $pdo->prepare("
    SELECT a.*, j.position, j.department, j.description, j.salaryGrade, j.monthlySalary, j.deadline, j.jobStatus
    FROM applications a
    JOIN jobs j ON a.jobId = j.jobId
    WHERE a.applicationId = ? AND a.userId = ?
");
$stmt->execute([$applicationId, $userId]);
$application = $stmt->fetch();

if (!$application) {
    header("Location: my_applications.php");
    exit();
}

// Fetch application files
$stmt = $pdo->prepare("
    SELECT af.*, dt.name as documentName
    FROM application_files af
    JOIN document_types dt ON af.documentTypeId = dt.id
    WHERE af.applicationId = ?
");
$stmt->execute([$applicationId]);
$files = $stmt->fetchAll();

include('../includes/header.php');
?>

<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-8 my-6">
        <!-- Header -->
        <div class="mb-6 pb-6 border-b">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Application Details</h1>
                    <p class="text-gray-600">Application Code: <span class="font-mono text-blue-600"><?= htmlspecialchars($application['applicationCode'] ?? 'N/A') ?></span></p>
                </div>
                <div class="flex space-x-3">
                    <a href="my_applications.php" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Applications
                    </a>
                    <?php if ($application['status'] === 'Pending'): ?>
                        <a href="edit_application.php?id=<?= $applicationId ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-edit mr-2"></i>Edit Application
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user mr-2"></i>Personal Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Full Name</p>
                            <p class="font-medium"><?= htmlspecialchars($application['fullName'] ?? 'N/A') ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Contact Number</p>
                            <p class="font-medium"><?= htmlspecialchars($application['contactNumber'] ?? 'N/A') ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Religion</p>
                            <p class="font-medium"><?= htmlspecialchars($application['religion'] ?? 'N/A') ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Ethnicity</p>
                            <p class="font-medium"><?= htmlspecialchars($application['ethnicity'] ?? 'N/A') ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Person with Disability</p>
                            <p class="font-medium">
                                <?php if (isset($application['isPersonWithDisability'])): ?>
                                    <span class="<?= $application['isPersonWithDisability'] ? 'text-green-600' : 'text-gray-600' ?>">
                                        <?= $application['isPersonWithDisability'] ? 'Yes' : 'No' ?>
                                    </span>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Solo Parent</p>
                            <p class="font-medium">
                                <?php if (isset($application['isSoloParent'])): ?>
                                    <span class="<?= $application['isSoloParent'] ? 'text-green-600' : 'text-gray-600' ?>">
                                        <?= $application['isSoloParent'] ? 'Yes' : 'No' ?>
                                    </span>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Job Details -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-briefcase mr-2"></i>Job Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Position</p>
                            <p class="font-medium"><?= htmlspecialchars($application['position']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Department</p>
                            <p class="font-medium"><?= htmlspecialchars($application['department']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Salary Grade</p>
                            <p class="font-medium"><?= htmlspecialchars($application['salaryGrade']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Monthly Salary</p>
                            <p class="font-medium">₱<?= number_format($application['monthlySalary'], 2) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Application Deadline</p>
                            <p class="font-medium"><?= date('M d, Y', strtotime($application['deadline'])) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Applied Date</p>
                            <p class="font-medium"><?= date('M d, Y', strtotime($application['appliedAt'])) ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($application['description'])): ?>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500 mb-2">Job Description</p>
                        <div class="bg-white p-3 rounded-lg">
                            <p class="text-gray-700"><?= nl2br(htmlspecialchars($application['description'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Submitted Documents -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-file-pdf mr-2"></i>Submitted Documents
                    </h3>
                    <?php if (!empty($files)): ?>
                        <div class="space-y-3">
                            <?php foreach ($files as $file): ?>
                            <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-file-pdf text-red-500 mr-3"></i>
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full mr-2">
                                                <?= htmlspecialchars($file['documentName']) ?>
                                            </span>
                                            Application Document
                                        </p>
                                        <p class="text-xs text-gray-500">Uploaded: <?= date('M d, Y h:i A', strtotime($file['uploadedAt'])) ?></p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Required document for <?= htmlspecialchars($application['position']) ?> position
                                        </p>
                                    </div>
                                </div>
                                <a href="<?= htmlspecialchars($file['filePath']) ?>" target="_blank" class="text-blue-600 hover:text-blue-800" title="Download document">
                                    <i class="fas fa-download mr-2"></i>Download
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Summary Section -->
                        <div class="mt-6 p-4 bg-white rounded-lg border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-clipboard-check mr-1"></i>Document Summary
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                                <?php 
                                $submittedTypes = array_map(function($f) { return $f['documentName']; }, $files);
                                foreach ($submittedTypes as $type): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-gray-700"><?= htmlspecialchars($type) ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                Total documents submitted: <?= count($files) ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-6">
                            <i class="fas fa-file-upload text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No documents submitted yet.</p>
                            <p class="text-xs text-gray-400 mt-1">You haven't uploaded any required documents for this application.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Application Status -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-flag mr-2"></i>Application Status
                    </h3>
                    <div class="text-center">
                        <?php
                        $statusColors = [
                            'Pending' => 'bg-yellow-100 text-yellow-800',
                            'Shortlisted' => 'bg-green-100 text-green-800',
                            'Rejected' => 'bg-red-100 text-red-800',
                            'Hired' => 'bg-purple-100 text-purple-800'
                        ];
                        $statusClass = $statusColors[$application['status']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="<?= $statusClass ?> text-lg px-4 py-2 rounded-full">
                            <?= htmlspecialchars($application['status']) ?>
                        </span>
                        <p class="text-sm text-gray-500 mt-2">
                            Applied on <?= date('M d, Y', strtotime($application['appliedAt'])) ?>
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <?php if ($application['status'] === 'Pending'): ?>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-cog mr-2"></i>Actions
                    </h3>
                    <div class="space-y-3">
                        <a href="edit_application.php?id=<?= $applicationId ?>" class="w-full block text-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-edit mr-2"></i>Edit Application
                        </a>
                        <button onclick="confirmDelete()" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash mr-2"></i>Withdraw Application
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to withdraw this application? This action cannot be undone.')) {
        window.location.href = 'my_applications.php?delete=<?= $applicationId ?>';
    }
}
</script>

<?php include('../includes/footer.php'); ?>
