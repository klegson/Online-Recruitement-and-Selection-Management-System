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
    WHERE a.applicationId = ? AND a.userId = ? AND a.status = 'Pending'
");
$stmt->execute([$applicationId, $userId]);
$application = $stmt->fetch();

if (!$application) {
    $_SESSION['error'] = "Application not found or cannot be edited";
    header("Location: my_applications.php");
    exit();
}

// Fetch current application files
$stmt = $pdo->prepare("
    SELECT af.*, dt.name as documentName
    FROM application_files af
    JOIN document_types dt ON af.documentTypeId = dt.id
    WHERE af.applicationId = ?
");
$stmt->execute([$applicationId]);
$currentFiles = $stmt->fetchAll();

// Fetch document requirements for this job
$stmt = $pdo->prepare("
    SELECT dt.* FROM document_types dt 
    JOIN job_requirements jr ON dt.id = jr.documentTypeId 
    WHERE jr.jobId = ?
");
$stmt->execute([$application['jobId']]);
$requiredDocuments = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Update application
        $stmt = $pdo->prepare("
            UPDATE applications SET 
                fullName = ?, contactNumber = ?, religion = ?, ethnicity = ?, 
                isPersonWithDisability = ?, isSoloParent = ?
            WHERE applicationId = ?
        ");
        $stmt->execute([
            $_POST['fullName'],
            $_POST['contactNumber'],
            $_POST['religion'],
            $_POST['ethnicity'],
            isset($_POST['isPersonWithDisability']) ? 1 : 0,
            isset($_POST['isSoloParent']) ? 1 : 0,
            $applicationId
        ]);
        
        // Handle file uploads (replace existing files)
        if (isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
            foreach ($_FILES['documents']['name'] as $index => $name) {
                if ($_FILES['documents']['error'][$index] === UPLOAD_ERR_OK) {
                    $documentId = $_POST['document_ids'][$index];
                    $fileName = time() . '_' . basename($name);
                    $uploadPath = '../uploads/' . $fileName;
                    
                    // Delete old file if exists
                    $stmt = $pdo->prepare("SELECT filePath FROM application_files WHERE applicationId = ? AND documentTypeId = ?");
                    $stmt->execute([$applicationId, $documentId]);
                    $oldFile = $stmt->fetch();
                    if ($oldFile && file_exists($oldFile['filePath'])) {
                        unlink($oldFile['filePath']);
                    }
                    
                    if (move_uploaded_file($_FILES['documents']['tmp_name'][$index], $uploadPath)) {
                        $stmt = $pdo->prepare("
                            UPDATE application_files SET filePath = ? 
                            WHERE applicationId = ? AND documentTypeId = ?
                        ");
                        $stmt->execute([$uploadPath, $applicationId, $documentId]);
                    }
                }
            }
        }
        
        $pdo->commit();
        $_SESSION['success'] = "Application updated successfully!";
        header("Location: view_my_application.php?id=" . $applicationId);
        exit();
        
    } catch (Exception $e) {
        $pdo->rollback();
        $error = "Error updating application: " . $e->getMessage();
    }
}

include('../includes/header.php');
?>

<!-- Success/Error Messages -->
<?php if (isset($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-8 my-6">
        <!-- Header -->
        <div class="mb-8 pb-6 border-b">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Application</h1>
                    <p class="text-gray-600">Application Code: <span class="font-mono text-blue-600"><?= htmlspecialchars($application['applicationCode'] ?? 'N/A') ?></span></p>
                </div>
                <a href="view_my_application.php?id=<?= $applicationId ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </div>

        <!-- Job Details Display -->
        <div class="mb-8 p-6 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Job Position</h3>
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
            </div>
        </div>

        <!-- Edit Form -->
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Update Personal Information</h2>
            
            <!-- Personal Information -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="fullName" value="<?= htmlspecialchars($application['fullName'] ?? '') ?>" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                        <input type="tel" name="contactNumber" value="<?= htmlspecialchars($application['contactNumber'] ?? '') ?>" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Religion</label>
                        <input type="text" name="religion" value="<?= htmlspecialchars($application['religion'] ?? '') ?>" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ethnicity</label>
                        <input type="text" name="ethnicity" value="<?= htmlspecialchars($application['ethnicity'] ?? '') ?>" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <!-- Disability and Solo Parent Status -->
                <div class="grid md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Person with Disability</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="isPersonWithDisability" value="1" <?= ($application['isPersonWithDisability'] ?? 0) ? 'checked' : '' ?> class="mr-2">
                                <span>Yes</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="isPersonWithDisability" value="0" <?= !($application['isPersonWithDisability'] ?? 0) ? 'checked' : '' ?> class="mr-2">
                                <span>No</span>
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Solo Parent</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="isSoloParent" value="1" <?= ($application['isSoloParent'] ?? 0) ? 'checked' : '' ?> class="mr-2">
                                <span>Yes</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="isSoloParent" value="0" <?= !($application['isSoloParent'] ?? 0) ? 'checked' : '' ?> class="mr-2">
                                <span>No</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Document Updates -->
            <?php if (!empty($requiredDocuments)): ?>
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Documents</h3>
                <p class="text-sm text-gray-600 mb-4">Upload new documents to replace existing ones (PDF format only)</p>
                
                <div class="space-y-4">
                    <?php foreach ($requiredDocuments as $doc): ?>
                    <?php 
                    $currentFile = null;
                    foreach ($currentFiles as $file) {
                        if ($file['documentTypeId'] == $doc['id']) {
                            $currentFile = $file;
                            break;
                        }
                    }
                    ?>
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-500 mr-3"></i>
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($doc['name']) ?></p>
                                <?php if ($currentFile): ?>
                                    <p class="text-xs text-gray-500">Current: Uploaded on <?= date('M d, Y', strtotime($currentFile['uploadedAt'])) ?></p>
                                <?php else: ?>
                                    <p class="text-xs text-red-500">Not uploaded yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <input type="file" name="documents[]" accept=".pdf"
                               class="text-sm">
                        <input type="hidden" name="document_ids[]" value="<?= $doc['id'] ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="view_my_application.php?id=<?= $applicationId ?>" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="primary-bg text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
