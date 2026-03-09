<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Applicant') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$jobId = $_GET['id'];
$userId = $_SESSION['user_id'];

// Fetch job details
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE jobId = ? AND jobStatus = 'Open' AND deadline >= CURDATE()");
$stmt->execute([$jobId]);
$job = $stmt->fetch();

if (!$job) {
    $_SESSION['error'] = "Job not found or no longer available";
    header("Location: ../index.php");
    exit();
}

// Check if already applied
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM applications WHERE userId = ? AND jobId = ?");
$stmt->execute([$userId, $jobId]);
$alreadyApplied = $stmt->fetch()['count'] > 0;

if ($alreadyApplied) {
    $_SESSION['error'] = "You have already applied for this position";
    header("Location: ../index.php");
    exit();
}

// Fetch user details
$stmt = $pdo->prepare("SELECT firstName, lastName, email FROM users WHERE userId = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Fetch document requirements for this job
$stmt = $pdo->prepare("
    SELECT dt.* FROM document_types dt 
    JOIN job_requirements jr ON dt.id = jr.documentTypeId 
    WHERE jr.jobId = ?
");
$stmt->execute([$jobId]);
$requiredDocuments = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Generate application code
        $applicationCode = 'APP' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Insert application
        $stmt = $pdo->prepare("
            INSERT INTO applications (
                applicationCode, userId, jobId, fullName, contactNumber, 
                religion, ethnicity, isPersonWithDisability, isSoloParent, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
        ");
        $stmt->execute([
            $applicationCode,
            $userId,
            $jobId,
            $_POST['fullName'],
            $_POST['contactNumber'],
            $_POST['religion'],
            $_POST['ethnicity'],
            isset($_POST['isPersonWithDisability']) ? 1 : 0,
            isset($_POST['isSoloParent']) ? 1 : 0
        ]);
        
        $applicationId = $pdo->lastInsertId();
        
        // Handle file uploads
        if (isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
            foreach ($_FILES['documents']['name'] as $index => $name) {
                if ($_FILES['documents']['error'][$index] === UPLOAD_ERR_OK) {
                    $documentId = $_POST['document_ids'][$index];
                    $fileName = time() . '_' . basename($name);
                    $uploadPath = '../uploads/' . $fileName;
                    
                    if (move_uploaded_file($_FILES['documents']['tmp_name'][$index], $uploadPath)) {
                        $stmt = $pdo->prepare("
                            INSERT INTO application_files (applicationId, documentTypeId, filePath) 
                            VALUES (?, ?, ?)
                        ");
                        $stmt->execute([$applicationId, $documentId, $uploadPath]);
                    }
                }
            }
        }
        
        $pdo->commit();
        $_SESSION['success'] = "Application submitted successfully! Your application code is: " . $applicationCode;
        header("Location: applicants_dashboard.php");
        exit();
        
    } catch (Exception $e) {
        $pdo->rollback();
        $error = "Error submitting application: " . $e->getMessage();
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
        <!-- Comprehensive Job Details -->
        <div class="mb-8 pb-6 border-b">

            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($job['position']) ?></h1>
                    <p class="text-lg text-gray-600 mb-3"><?= htmlspecialchars($job['department']) ?></p>
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-medium">
                            <?= htmlspecialchars($job['employmentType'] ?? 'Full-time') ?>
                        </span>
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-medium">
                            <?= htmlspecialchars($job['jobStatus']) ?>
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-blue-600">₱<?= number_format($job['monthlySalary'], 2) ?></div>
                    <div class="text-sm text-gray-500"><?= htmlspecialchars($job['salaryGrade']) ?></div>
                    <div class="text-xs text-gray-400 mt-1">per month</div>
                </div>
            </div>

            <!-- Job Overview Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-briefcase text-gray-400 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Job Type</span>
                    </div>
                    <div class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($job['employmentType'] ?? 'Full-time') ?></div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-clock text-gray-400 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Work Hours</span>
                    </div>
                    <div class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($job['workHours'] ?? 'Full-time') ?></div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Deadline</span>
                    </div>
                    <div class="text-lg font-semibold text-red-600"><?= date('M d, Y', strtotime($job['deadline'])) ?></div>
                    <div class="text-xs text-gray-500"><?= date('F j, Y', strtotime($job['postedAt'])) ?> - Posted</div>
                </div>
            </div>
            <!-- Job Description -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-file-alt text-gray-400 mr-2"></i>Job Description
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="prose max-w-none text-gray-700">
                        <?= nl2br(htmlspecialchars($job['description'])) ?>
                    </div>
                </div>
            </div>

            <!-- Requirements -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-clipboard-check text-gray-400 mr-2"></i>Requirements & Qualifications
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="prose max-w-none text-gray-700">
                        <?= nl2br(htmlspecialchars($job['requirements'] ?? 'Bachelor\'s degree relevant to the position')) ?>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <?php if (!empty($job['responsibilities']) || !empty($job['skillsRequired'])): ?>
            <div class="grid md:grid-cols-2 gap-6">
                <?php if (!empty($job['responsibilities'])): ?>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-tasks text-gray-400 mr-2"></i>Key Responsibilities
                    </h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="prose max-w-none text-gray-700">
                            <?= nl2br(htmlspecialchars($job['responsibilities'])) ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($job['skillsRequired'])): ?>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-cogs text-gray-400 mr-2"></i>Skills Required
                    </h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="prose max-w-none text-gray-700">
                            <?= nl2br(htmlspecialchars($job['skillsRequired'])) ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Application Form -->
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Application Information</h2>
            
            <!-- Personal Information -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="fullName" value="<?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                        <input type="tel" name="contactNumber" placeholder="09XXXXXXXXX" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Religion</label>
                        <input type="text" name="religion" placeholder="e.g., Roman Catholic" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ethnicity</label>
                        <input type="text" name="ethnicity" placeholder="e.g., Filipino" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <!-- Disability and Solo Parent Status -->
                <div class="grid md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Person with Disability</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="isPersonWithDisability" value="1" class="mr-2">
                                <span>Yes</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="isPersonWithDisability" value="0" checked class="mr-2">
                                <span>No</span>
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Solo Parent</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="isSoloParent" value="1" class="mr-2">
                                <span>Yes</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="isSoloParent" value="0" checked class="mr-2">
                                <span>No</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Required Documents -->
            <?php if (!empty($requiredDocuments)): ?>
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Required Documents</h3>
                <p class="text-sm text-gray-600 mb-4">Please upload the following documents (PDF format only)</p>
                
                <div class="space-y-4">
                    <?php foreach ($requiredDocuments as $index => $doc): ?>
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-500 mr-3"></i>
                            <span class="font-medium"><?= htmlspecialchars($doc['name']) ?></span>
                        </div>
                        <input type="file" name="documents[]" accept=".pdf" required
                               class="text-sm">
                        <input type="hidden" name="document_ids[]" value="<?= $doc['id'] ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Job Description -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Job Description</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($job['description'] ?? 'No description available.')) ?></p>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="../index.php" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="primary-bg text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-check mr-2"></i>Submit Application
                </button>
            </div>
        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
