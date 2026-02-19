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
$error = '';
$success = '';

// Fetch job details
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE jobId = ?");
$stmt->execute([$jobId]);
$job = $stmt->fetch();

if (!$job) {
    header("Location: hr_dashboard.php");
    exit();
}

// Fetch document types
$stmt = $pdo->query("SELECT * FROM document_types ORDER BY name");
$documentTypes = $stmt->fetchAll();

// Fetch current job's document requirements
$stmt = $pdo->prepare("SELECT documentTypeId FROM job_requirements WHERE jobId = ?");
$stmt->execute([$jobId]);
$currentDocRequirements = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $position = $_POST['position'];
    $department = $_POST['department'];
    $salaryGrade = $_POST['salaryGrade'];
    $monthlySalary = $_POST['monthlySalary'];
    $deadline = $_POST['deadline'];
    $description = $_POST['description'];
    $documentRequirements = $_POST['document_requirements'] ?? [];
    $status = $_POST['status'];
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Update job
        $stmt = $pdo->prepare("
            UPDATE jobs 
            SET position = ?, department = ?, salaryGrade = ?, monthlySalary = ?, 
                deadline = ?, description = ?, jobStatus = ?, updatedAt = NOW()
            WHERE jobId = ?
        ");
        $stmt->execute([$position, $department, $salaryGrade, $monthlySalary, $deadline, $description, $status, $jobId]);
        
        // Update document requirements
        // Delete existing requirements
        $stmt = $pdo->prepare("DELETE FROM job_requirements WHERE jobId = ?");
        $stmt->execute([$jobId]);
        
        // Insert new requirements
        foreach ($documentRequirements as $docId) {
            $stmt = $pdo->prepare("INSERT INTO job_requirements (jobId, documentTypeId) VALUES (?, ?)");
            $stmt->execute([$jobId, $docId]);
        }
        
        $pdo->commit();
        $success = "Job updated successfully!";
        
        // Refresh job data
        $stmt = $pdo->prepare("SELECT * FROM jobs WHERE jobId = ?");
        $stmt->execute([$jobId]);
        $job = $stmt->fetch();
        
        // Refresh document requirements
        $stmt = $pdo->prepare("SELECT documentTypeId FROM job_requirements WHERE jobId = ?");
        $stmt->execute([$jobId]);
        $currentDocRequirements = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
    } catch (PDOException $e) {
        $pdo->rollback();
        $error = "Error updating job: " . $e->getMessage();
    }
}

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
                    <span class="text-gray-500">Edit Job</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Job Posting</h1>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-briefcase mr-1"></i> Position
                    </label>
                    <select name="position" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Position</option>
                        <option value="Accountant I" <?= $job['position'] === 'Accountant I' ? 'selected' : '' ?>>Accountant I</option>
                        <option value="Accountant II" <?= $job['position'] === 'Accountant II' ? 'selected' : '' ?>>Accountant II</option>
                        <option value="Accountant III" <?= $job['position'] === 'Accountant III' ? 'selected' : '' ?>>Accountant III</option>
                        <option value="Accountant IV" <?= $job['position'] === 'Accountant IV' ? 'selected' : '' ?>>Accountant IV</option>
                        <option value="Accounting Analyst" <?= $job['position'] === 'Accounting Analyst' ? 'selected' : '' ?>>Accounting Analyst</option>
                        <option value="Accounting Clerk II" <?= $job['position'] === 'Accounting Clerk II' ? 'selected' : '' ?>>Accounting Clerk II</option>
                        <option value="Administrative Assistant VI" <?= $job['position'] === 'Administrative Assistant VI' ? 'selected' : '' ?>>Administrative Assistant VI</option>
                        <option value="Administrative Aide I" <?= $job['position'] === 'Administrative Aide I' ? 'selected' : '' ?>>Administrative Aide I</option>
                        <option value="Administrative Aide II" <?= $job['position'] === 'Administrative Aide II' ? 'selected' : '' ?>>Administrative Aide II</option>
                        <option value="Administrative Aide III" <?= $job['position'] === 'Administrative Aide III' ? 'selected' : '' ?>>Administrative Aide III</option>
                        <option value="Administrative Aide IV" <?= $job['position'] === 'Administrative Aide IV' ? 'selected' : '' ?>>Administrative Aide IV</option>
                        <option value="Administrative Aide V" <?= $job['position'] === 'Administrative Aide V' ? 'selected' : '' ?>>Administrative Aide V</option>
                        <option value="Administrative Aide VI" <?= $job['position'] === 'Administrative Aide VI' ? 'selected' : '' ?>>Administrative Aide VI</option>
                        <option value="Administrative Assistant I" <?= $job['position'] === 'Administrative Assistant I' ? 'selected' : '' ?>>Administrative Assistant I</option>
                        <option value="Administrative Assistant II" <?= $job['position'] === 'Administrative Assistant II' ? 'selected' : '' ?>>Administrative Assistant II</option>
                        <option value="Administrative Assistant III" <?= $job['position'] === 'Administrative Assistant III' ? 'selected' : '' ?>>Administrative Assistant III</option>
                        <option value="Administrative Assistant V" <?= $job['position'] === 'Administrative Assistant V' ? 'selected' : '' ?>>Administrative Assistant V</option>
                        <option value="Administrative Officer I" <?= $job['position'] === 'Administrative Officer I' ? 'selected' : '' ?>>Administrative Officer I</option>
                        <option value="Administrative Officer II" <?= $job['position'] === 'Administrative Officer II' ? 'selected' : '' ?>>Administrative Officer II</option>
                        <option value="Administrative Officer III" <?= $job['position'] === 'Administrative Officer III' ? 'selected' : '' ?>>Administrative Officer III</option>
                        <option value="Administrative Officer IV" <?= $job['position'] === 'Administrative Officer IV' ? 'selected' : '' ?>>Administrative Officer IV</option>
                        <option value="Administrative Officer V" <?= $job['position'] === 'Administrative Officer V' ? 'selected' : '' ?>>Administrative Officer V</option>
                        <option value="Agriculturist I" <?= $job['position'] === 'Agriculturist I' ? 'selected' : '' ?>>Agriculturist I</option>
                        <option value="Agriculturist II" <?= $job['position'] === 'Agriculturist II' ? 'selected' : '' ?>>Agriculturist II</option>
                        <option value="Aquacultural Technician II" <?= $job['position'] === 'Aquacultural Technician II' ? 'selected' : '' ?>>Aquacultural Technician II</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-1"></i> Department
                    </label>
                    <select name="department" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Administrative Division" <?= $job['department'] === 'Administrative Division' ? 'selected' : '' ?>>Administrative Division</option>
                        <option value="Curriculum and Learning Management Division" <?= $job['department'] === 'Curriculum and Learning Management Division' ? 'selected' : '' ?>>Curriculum and Learning Management Division</option>
                        <option value="Finance Division" <?= $job['department'] === 'Finance Division' ? 'selected' : '' ?>>Finance Division</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-layer-group mr-1"></i> Salary Grade
                    </label>
                    <input type="text" name="salaryGrade" value="<?= htmlspecialchars($job['salaryGrade']) ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-peso-sign mr-1"></i> Monthly Salary
                    </label>
                    <input type="number" step="0.01" name="monthlySalary" value="<?= htmlspecialchars($job['monthlySalary']) ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-1"></i> Application Deadline
                    </label>
                    <input type="date" name="deadline" value="<?= htmlspecialchars($job['deadline']) ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-toggle-on mr-1"></i> Status
                    </label>
                    <select name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Open" <?= $job['jobStatus'] === 'Open' ? 'selected' : '' ?>>Open</option>
                        <option value="Closed" <?= $job['jobStatus'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-file-alt mr-1"></i> Job Description
                </label>
                <textarea name="description" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($job['description'] ?? '') ?></textarea>
            </div>
            
            <!-- Document Requirements Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-pdf mr-2"></i>Required Documents
                </h3>
                <p class="text-sm text-gray-600 mb-4">Select the documents that applicants must submit for this position</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <?php foreach ($documentTypes as $docType): ?>
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                        <input type="checkbox" name="document_requirements[]" value="<?= $docType['id'] ?>" 
                               class="mr-3 text-blue-600 focus:ring-blue-500"
                               <?= in_array($docType['id'], $currentDocRequirements) ? 'checked' : '' ?>>
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                            <span class="text-sm text-gray-700"><?= htmlspecialchars($docType['name']) ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4">
                <a href="hr_dashboard.php" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update Job
                </button>
            </div>
        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
