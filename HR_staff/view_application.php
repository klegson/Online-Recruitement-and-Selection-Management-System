<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: applications.php");
    exit();
}

$applicationId = $_GET['id'];

// Fetch application details with job and user info
$stmt = $pdo->prepare("
    SELECT a.*, j.position, j.department, j.description, j.salaryGrade, j.monthlySalary, j.deadline,
           u.firstName, u.lastName, u.email, u.dateJoined,
           updater.firstName as updaterFirstName, updater.lastName as updaterLastName
    FROM applications a
    JOIN jobs j ON a.jobId = j.jobId
    JOIN users u ON a.userId = u.userId
    LEFT JOIN users updater ON a.updatedBy = updater.userId
    WHERE a.applicationId = ?
");
$stmt->execute([$applicationId]);
$application = $stmt->fetch();

if (!$application) {
    header("Location: applications.php");
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

<!-- Success Message -->
<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <i class="fas fa-check-circle mr-2"></i>Application status updated successfully!
    </div>
<?php endif; ?>

<!-- Error Message -->
<?php if (isset($_GET['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i>Failed to update application status. Please try again.
    </div>
<?php endif; ?>

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
                    <a href="applications.php" class="text-gray-700 hover:text-gray-900">
                        Applications
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500">Application Details</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Application Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Applicant Information -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-user mr-2"></i>Applicant Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Full Name</p>
                    <p class="font-medium"><?= htmlspecialchars($application['firstName'] . ' ' . $application['lastName']) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email Address</p>
                    <p class="font-medium"><?= htmlspecialchars($application['email']) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Date Joined</p>
                    <p class="font-medium"><?= date('M d, Y', strtotime($application['dateJoined'])) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Applied Date</p>
                    <p class="font-medium"><?= date('M d, Y', strtotime($application['appliedAt'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-user-circle mr-2"></i>Additional Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Application Code</p>
                    <p class="font-medium text-blue-600"><?= htmlspecialchars($application['applicationCode'] ?? 'N/A') ?></p>
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

        <!-- Job Information -->
        <div class="bg-white rounded-xl shadow-sm p-6">
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
            </div>
            <?php if ($application['description']): ?>
            <div class="mt-4">
                <p class="text-sm text-gray-500 mb-2">Job Description</p>
                <p class="text-gray-700"><?= nl2br(htmlspecialchars($application['description'])) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Submitted Documents -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-file-pdf mr-2"></i>Submitted Documents
            </h3>
            <?php if (!empty($files)): ?>
                <div class="space-y-3">
                    <?php foreach ($files as $file): ?>
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
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
                                    Requirement: <?= htmlspecialchars($file['documentName']) ?> for this position
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
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
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
                    <p class="text-xs text-gray-400 mt-1">Applicant has not uploaded any required documents.</p>
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
            
            <form action="../actions/update_application_status.php" method="POST" class="space-y-4">
                <input type="hidden" name="applicationId" value="<?= $applicationId ?>">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Status</label>
                    <?php
                    $statusColors = [
                        'Pending' => 'bg-yellow-100 text-yellow-800',
                        'Shortlisted' => 'bg-green-100 text-green-800',
                        'Rejected' => 'bg-red-100 text-red-800',
                        'Hired' => 'bg-blue-100 text-blue-800'
                    ];
                    $statusClass = $statusColors[$application['status']] ?? 'bg-gray-100 text-gray-800';
                    ?>
                    <span class="<?= $statusClass ?> text-xs px-2 py-1 rounded-full">
                        <?= htmlspecialchars($application['status']) ?>
                    </span>
                </div>

                <?php if ($application['updatedBy'] && $application['updaterFirstName']): ?>
                <div class="text-sm text-gray-600">
                    <i class="fas fa-user-edit mr-1"></i>
                    Last updated by: <?= htmlspecialchars($application['updaterFirstName'] . ' ' . $application['updaterLastName']) ?>
                    <?php if (isset($application['updatedAt']) && $application['updatedAt'] && $application['appliedAt'] != $application['updatedAt']): ?>
                        <span class="text-xs text-gray-500">(Original applicant: <?= htmlspecialchars($application['firstName'] . ' ' . $application['lastName']) ?>)</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Update Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Pending" <?= $application['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Shortlisted" <?= $application['status'] === 'Shortlisted' ? 'selected' : '' ?>>Shortlisted</option>
                        <option value="Rejected" <?= $application['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        <option value="Hired" <?= $application['status'] === 'Hired' ? 'selected' : '' ?>>Hired</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">HR Notes</label>
                    <textarea name="hrNotes" rows="4" placeholder="Add notes about this application..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($application['hrNotes'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="w-full primary-bg text-white py-2 rounded-lg hover:bg-blue-800 transition">
                    <i class="fas fa-save mr-2"></i>Update Status
                </button>
            </form>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-bolt mr-2"></i>Quick Actions
            </h3>
            <div class="space-y-3">
                <a href="mailto:<?= htmlspecialchars($application['email']) ?>" class="w-full block text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-envelope mr-2"></i>Contact Applicant
                </a>
                <a href="applications.php" class="w-full block text-center border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Applications
                </a>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
