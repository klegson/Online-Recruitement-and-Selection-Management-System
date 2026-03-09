<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Applicant') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * 3;

// Fetch newest jobs for the requested page
$stmt = $pdo->prepare("
    SELECT j.*, COUNT(a.applicationId) as applicantCount, TIMESTAMPDIFF(SECOND, j.postedAt, NOW()) as secondsSincePosted 
    FROM jobs j 
    LEFT JOIN applications a ON j.jobId = a.jobId 
    WHERE j.jobStatus = 'Open' AND j.deadline >= CURDATE() 
    GROUP BY j.jobId 
    ORDER BY j.postedAt DESC 
    LIMIT 3 OFFSET $offset
");
$stmt->execute();
$newestJobs = $stmt->fetchAll();

// Function to calculate time ago
function timeAgo($datetime, $secondsSincePosted = null) {
    if ($secondsSincePosted !== null) {
        $diff = (int)$secondsSincePosted;
    } else {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
    }
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } else {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    }
}

// Generate HTML for the jobs
$html = '';
if (!empty($newestJobs)) {
    foreach ($newestJobs as $job) {
        $html .= '
            <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer" onclick="window.location.href=\'apply_job.php?id=' . $job['jobId'] . '\'">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-building text-gray-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-800 text-xs truncate">' . htmlspecialchars($job['position']) . '</h4>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center text-xs text-gray-600">
                        <i class="fas fa-money-bill-wave mr-1 text-gray-400"></i>
                        ' . htmlspecialchars($job['salaryGrade']) . '
                    </div>
                    <div class="flex items-center text-xs text-gray-600">
                        <i class="fas fa-users mr-1 text-gray-400"></i>
                        ' . $job['applicantCount'] . ' applied
                    </div>
                    <div class="text-xs text-gray-500">
                        ' . timeAgo($job['postedAt'], $job['secondsSincePosted']) . '
                    </div>
                </div>
            </div>';
    }
} else {
    $html = '
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <i class="fas fa-briefcase text-gray-300 text-3xl mb-3"></i>
            <p class="text-gray-500">No new jobs available</p>
        </div>';
}

echo json_encode([
    'html' => $html,
    'page' => $page
]);
?>
