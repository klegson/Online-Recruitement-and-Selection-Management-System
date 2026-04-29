<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Board') {
    header("Location: ../login.php");
    exit();
}

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

$totalQualified = count($qualifiedApplicants);
$positionsCount = $pdo->query("SELECT COUNT(DISTINCT jobId) as count FROM applications WHERE status = 'Qualified' AND updatedBy IS NOT NULL")->fetch()['count'];

include('../includes/header.php');
?>

<div class="bg-[#003366] text-white py-8 mb-6">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold">Board Review Dashboard</h1>
        <p class="text-blue-300 mt-1">Review qualified applicants recommended by HR staff</p>
    </div>
</div>

<div class="container mx-auto px-4 mb-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 border">
            <p class="text-sm text-gray-600">Total Qualified</p>
            <p class="text-2xl font-bold text-gray-800"><?= $totalQualified ?></p>
        </div>
        <div class="bg-white p-4 border">
            <p class="text-sm text-gray-600">Open Positions</p>
            <p class="text-2xl font-bold text-gray-800"><?= $positionsCount ?></p>
        </div>
        <div class="bg-white p-4 border">
            <p class="text-sm text-gray-600">HR Reviewed</p>
            <p class="text-2xl font-bold text-gray-800"><?= $totalQualified ?></p>
        </div>
        <div class="bg-white p-4 border">
            <p class="text-sm text-gray-600">Date</p>
            <p class="text-xl font-bold text-gray-800"><?= date('M d, Y') ?></p>
        </div>
    </div>
</div>

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

<div class="container mx-auto px-4">
    <div class="bg-white border">
        <div class="px-4 py-3 bg-gray-50 border-b">
            <h2 class="font-semibold text-gray-800">Qualified Applicants</h2>
        </div>
        
        <?php if (!empty($qualifiedApplicants)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Applicant</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Position</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Department</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Salary Grade</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Applied</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($qualifiedApplicants as $applicant): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($applicant['firstName'] . ' ' . $applicant['lastName']) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($applicant['email']) ?></p>
                                    <?php if ($applicant['hrFirstName']): ?>
                                        <p class="text-xs text-gray-400 mt-1">HR: <?= htmlspecialchars($applicant['hrFirstName'] . ' ' . $applicant['hrLastName']) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($applicant['position']) ?></td>
                                <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($applicant['department']) ?></td>
                                <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($applicant['salaryGrade']) ?></td>
                                <td class="px-4 py-3 text-gray-600"><?= date('M d, Y', strtotime($applicant['appliedAt'])) ?></td>
                                <td class="px-4 py-3">
                                    <a href="view_applicant.php?id=<?= $applicant['applicationId'] ?>" class="text-[#003366] hover:underline font-medium mr-3">View</a>
                                    <button onclick="recommendForHiring(<?= $applicant['applicationId'] ?>)" class="text-green-600 hover:underline font-medium">Recommend</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="px-4 py-12 text-center text-gray-500">
                <p class="text-lg">No qualified applicants yet.</p>
                <p class="text-sm mt-1">HR staff hasn't qualified any applicants for review.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>

<script>
function recommendForHiring(applicationId) {
    if (confirm('Are you sure you want to recommend this applicant for hiring?')) {
        fetch('../actions/recommend_candidate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application-x-www-form-urlencoded',
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