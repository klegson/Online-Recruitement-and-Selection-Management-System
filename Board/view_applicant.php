<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Board') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: board_dashboard.php");
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
    WHERE a.applicationId = ? AND a.status = 'Qualified'
");
$stmt->execute([$applicationId]);
$application = $stmt->fetch();

if (!$application) {
    header("Location: board_dashboard.php");
    exit();
}

// Fetch application files
$files = [];
try {
    $stmt = $pdo->prepare("
        SELECT af.*, dt.name as documentName
        FROM application_files af
        JOIN document_types dt ON af.documentTypeId = dt.id
        WHERE af.applicationId = ?
    ");
    $stmt->execute([$applicationId]);
    $files = $stmt->fetchAll();
} catch (Exception $e) {
    $files = [];
}

include('../includes/header.php');
?>

<!-- Success Message -->
<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?= htmlspecialchars($_GET['success']) ?>
    </div>
<?php endif; ?>

<!-- Error Message -->
<?php if (isset($_GET['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Applicant Details</h2>
        <a href="board_dashboard.php" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Applicant Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="text-lg font-semibold mb-4 text-gray-700">Personal Information</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-gray-600">Name:</span>
                    <span class="font-medium ml-2"><?= htmlspecialchars($application['firstName'] . ' ' . $application['lastName']) ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Email:</span>
                    <span class="font-medium ml-2"><?= htmlspecialchars($application['email']) ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Applied Date:</span>
                    <span class="font-medium ml-2"><?= date('M d, Y', strtotime($application['appliedAt'])) ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Status:</span>
                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full ml-2">Qualified</span>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold mb-4 text-gray-700">Job Information</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-gray-600">Position:</span>
                    <span class="font-medium ml-2"><?= htmlspecialchars($application['position']) ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Department:</span>
                    <span class="font-medium ml-2"><?= htmlspecialchars($application['department']) ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Salary Grade:</span>
                    <span class="font-medium ml-2"><?= htmlspecialchars($application['salaryGrade']) ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Monthly Salary:</span>
                    <span class="font-medium ml-2"><?= htmlspecialchars($application['monthlySalary']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- HR Review Information -->
    <?php if ($application['updaterFirstName']): ?>
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded">
            <h3 class="text-lg font-semibold mb-2 text-blue-800">HR Review</h3>
            <div class="space-y-2">
                <div>
                    <span class="text-blue-600">Reviewed by:</span>
                    <span class="font-medium ml-2"><?= htmlspecialchars($application['updaterFirstName'] . ' ' . $application['updaterLastName']) ?></span>
                </div>
                <?php if ($application['hrNotes']): ?>
                    <div>
                        <span class="text-blue-600">HR Notes:</span>
                        <p class="mt-2 text-blue-700"><?= htmlspecialchars($application['hrNotes']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Job Description -->
    <?php if ($application['description']): ?>
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3 text-gray-700">Job Description</h3>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-gray-700"><?= nl2br(htmlspecialchars($application['description'])) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Submitted Documents -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3 text-gray-700">Submitted Documents</h3>
        <?php if (!empty($files)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($files as $file): ?>
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-500 mr-3"></i>
                            <span class="font-medium"><?= htmlspecialchars($file['documentName']) ?></span>
                        </div>
                        <a href="../<?= htmlspecialchars($file['filePath']) ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-download mr-1"></i>Download
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 italic">No documents uploaded</p>
        <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
        <a href="mailto:<?= htmlspecialchars($application['email']) ?>" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            <i class="fas fa-envelope mr-2"></i>Contact Applicant
        </a>
        <form action="../actions/recommend_candidate.php" method="POST" class="inline">
            <input type="hidden" name="applicationId" value="<?= $application['applicationId'] ?>">
            <button type="submit" onclick="return confirm('Are you sure you want to recommend this applicant for hiring?')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                <i class="fas fa-thumbs-up mr-2"></i>Recommend for Hiring
            </button>
        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
