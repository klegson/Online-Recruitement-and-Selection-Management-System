<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    header("Location: ../login.php");
    exit();
}

// Fetch document types
$stmt = $pdo->query("SELECT * FROM document_types ORDER BY name");
$documentTypes = $stmt->fetchAll();

// Fetch all possible positions from ENUM column
$stmt = $pdo->query("SHOW COLUMNS FROM jobs WHERE Field = 'position'");
$column = $stmt->fetch();
$enumString = $column['Type'];

// Extract ENUM values
preg_match("/^enum\((.*)\)$/", $enumString, $matches);
$positions = str_getcsv($matches[1], ',', "'", "\\");

// Fetch all possible departments from ENUM column
$stmt = $pdo->query("SHOW COLUMNS FROM jobs WHERE Field = 'department'");
$column = $stmt->fetch();
$enumString = $column['Type'];

// Extract ENUM values
preg_match("/^enum\((.*)\)$/", $enumString, $matches);
$departments = str_getcsv($matches[1], ',', "'", "\\");

include('../includes/header.php');
?>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-8 my-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Create New Job Posting</h2>
            <p class="text-gray-600 text-sm mt-1">Fill in the job details and select required documents</p>
        </div>

        <form action="../actions/create_job_action.php" method="POST" class="space-y-6">
            <!-- Basic Job Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-briefcase mr-1"></i> Position
                    </label>
                    <select name="position" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Position</option>
                        <?php foreach ($positions as $position): ?>
                        <option value="<?= htmlspecialchars($position) ?>"><?= htmlspecialchars($position) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-1"></i> Department
                    </label>
                    <select name="department" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $department): ?>
                        <option value="<?= htmlspecialchars($department) ?>"><?= htmlspecialchars($department) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-badge mr-1"></i> Plantilla Item No.
                    </label>
                    <input type="text" name="plantillaItemNo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-chart-line mr-1"></i> Salary Grade
                    </label>
                    <input type="text" name="salaryGrade" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-peso-sign mr-1"></i> Monthly Salary
                    </label>
                    <input type="number" step="0.01" name="monthlySalary" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-1"></i> Application Deadline
                </label>
                <input type="date" name="deadline" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-file-alt mr-1"></i> Job Description
                </label>
                <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <!-- Document Requirements Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-pdf mr-2"></i>Required Documents
                </h3>                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <?php foreach ($documentTypes as $docType): ?>
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                        <input type="checkbox" name="document_requirements[]" value="<?= $docType['id'] ?>" class="mr-3 text-blue-600 focus:ring-blue-500">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                            <span class="text-sm text-gray-700"><?= htmlspecialchars($docType['name']) ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="hr_dashboard.php" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="primary-bg text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition">
                    <i class="fas fa-check mr-2"></i>Publish Job
                </button>
            </div>
        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>