<?php

// Function to calculate time ago
function timeAgo($datetime, $secondsSincePosted = null) {
    // Use the pre-calculated seconds difference if available
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

session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Applicant') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Handle sorting
$sortOrder = $_GET['sort'] ?? 'newest';
$orderClause = $sortOrder === 'oldest' ? 'ASC' : 'DESC';

// Fetch user statistics
$totalApplications = $pdo->prepare("SELECT COUNT(*) as count FROM applications WHERE userId = ?");
$totalApplications->execute([$userId]);
$totalApplications = $totalApplications->fetch()['count'];

$pendingApplications = $pdo->prepare("SELECT COUNT(*) as count FROM applications WHERE userId = ? AND status = 'Pending'");
$pendingApplications->execute([$userId]);
$pendingApplications = $pendingApplications->fetch()['count'];

$shortlistedApplications = $pdo->prepare("SELECT COUNT(*) as count FROM applications WHERE userId = ? AND status = 'Shortlisted'");
$shortlistedApplications->execute([$userId]);
$shortlistedApplications = $shortlistedApplications->fetch()['count'];

$hiredApplications = $pdo->prepare("SELECT COUNT(*) as count FROM applications WHERE userId = ? AND status = 'Hired'");
$hiredApplications->execute([$userId]);
$hiredApplications = $hiredApplications->fetch()['count'];

// Fetch recent applications
$stmt = $pdo->prepare("
    SELECT a.*, j.position, j.department, j.salaryGrade, j.deadline 
    FROM applications a 
    JOIN jobs j ON a.jobId = j.jobId 
    WHERE a.userId = ? 
    ORDER BY a.appliedAt DESC 
    LIMIT 10
");
$stmt->execute([$userId]);
$recentApplications = $stmt->fetchAll();

// Fetch recommended jobs (open jobs with deadline not passed)
$stmt = $pdo->prepare("
    SELECT j.*, COUNT(a.applicationId) as applicantCount, TIMESTAMPDIFF(SECOND, j.postedAt, NOW()) as secondsSincePosted 
    FROM jobs j 
    LEFT JOIN applications a ON j.jobId = a.jobId 
    WHERE j.jobStatus = 'Open' AND j.deadline >= CURDATE() 
    GROUP BY j.jobId 
    ORDER BY j.postedAt $orderClause 
    LIMIT 8
");
$stmt->execute();
$recommendedJobs = $stmt->fetchAll();

// Fetch newest jobs (latest 3 for horizontal section with pagination)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * 3;

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

// Get total newest jobs count for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM jobs WHERE jobStatus = 'Open' AND deadline >= CURDATE()");
$stmt->execute();
$totalNewestJobs = $stmt->fetch()['total'];
$totalPages = ceil($totalNewestJobs / 3);

// Get unique departments for filter
$departments = $pdo->query("SELECT DISTINCT department FROM jobs WHERE jobStatus = 'Open' ORDER BY department")->fetchAll();

include('../includes/header.php');
?>

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

<!-- Main Dashboard Container -->
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="grid grid-cols-12 gap-6">
            
            <!-- Left Sidebar - Filters -->
            <div class="col-span-3">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                    <h3 class="font-semibold text-gray-800 mb-6">Filters</h3>
                    
                    <!-- Search Bar -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" placeholder="Search jobs..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <!-- Salary Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Salary Range</label>
                        <div class="space-y-2">
                            <input type="range" min="0" max="100000" value="50000" 
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>₱0</span>
                                <span>₱100,000</span>
                            </div>
                        </div>
                    </div>
                    
                    
                    <!-- Department Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Department</label>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            <?php foreach ($departments as $dept): ?>
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-2 text-blue-600 rounded focus:ring-blue-500">
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($dept['department']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Sort Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select id="sortFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="newest" <?= $sortOrder === 'newest' ? 'selected' : '' ?>>Newest First</option>
                            <option value="oldest" <?= $sortOrder === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                        </select>
                    </div>
                    
                    <button class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Apply Filters
                    </button>
                </div>
            </div>
            
            <!-- Center Content - Job Listings -->
            <div class="col-span-6">
                <!-- Newest Jobs Section -->
                <div class="mb-8">
                    <div class="relative">
                        <h3 class="font-semibold text-gray-800 mb-4">Newest Jobs</h3>
                        
                        <!-- Jobs Container with Navigation -->
                        <div class="relative flex items-center">
                            <!-- Navigation Arrows -->
                            <?php if ($totalPages > 1): ?>
                                <button onclick="loadNewestJobs('prev')" 
                                        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 bg-white rounded-full p-2 shadow-md hover:shadow-lg hover:bg-gray-50 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                                        <?= $page <= 1 ? 'disabled' : '' ?>>
                                    <i class="fas fa-chevron-left text-gray-700 text-sm"></i>
                                </button>
                                <button onclick="loadNewestJobs('next')" 
                                        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 bg-white rounded-full p-2 shadow-md hover:shadow-lg hover:bg-gray-50 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                                        <?= $page >= $totalPages ? 'disabled' : '' ?>>
                                    <i class="fas fa-chevron-right text-gray-700 text-sm"></i>
                                </button>
                            <?php endif; ?>
                            
                            <!-- Jobs Container -->
                            <div id="newestJobsContainer" class="overflow-hidden w-full">
                                <div id="newestJobsSlider" class="flex transition-all duration-500 ease-out">
                                <?php if (!empty($newestJobs)): ?>
                                    <div class="grid grid-cols-3 gap-4 w-full">
                                        <?php foreach ($newestJobs as $job): ?>
                                            <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer" onclick="window.location.href='apply_job.php?id=<?= $job['jobId'] ?>'">
                                                <div class="flex items-center mb-2">
                                                    <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center mr-2">
                                                        <i class="fas fa-building text-gray-600 text-xs"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="font-medium text-gray-800 text-xs truncate"><?= htmlspecialchars($job['position']) ?></h4>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <i class="fas fa-money-bill-wave mr-1 text-gray-400"></i>
                                                        <?= htmlspecialchars($job['salaryGrade']) ?>
                                                    </div>
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <i class="fas fa-users mr-1 text-gray-400"></i>
                                                        <?= $job['applicantCount'] ?> applied
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        <?= timeAgo($job['postedAt'], $job['secondsSincePosted']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="bg-white rounded-xl shadow-sm p-6 text-center w-full">
                                        <i class="fas fa-briefcase text-gray-300 text-3xl mb-3"></i>
                                        <p class="text-gray-500">No new jobs available</p>
                                    </div>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Other Jobs Section -->
                <div>
                    <h3 class="font-semibold text-gray-800 mb-4">Other Jobs</h3>
                    <?php if (!empty($recommendedJobs)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recommendedJobs as $job): ?>
                                <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                                    <div class="flex items-start">
                                        <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                            <i class="fas fa-building text-gray-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-gray-800 text-lg"><?= htmlspecialchars($job['position']) ?></h4>
                                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($job['department']) ?></p>
                                                </div>
                                                <div class="flex flex-col items-end space-y-1 ml-4">
                                                    <?php 
                                                    $daysSincePosted = floor($job['secondsSincePosted'] / 86400);
                                                    if ($daysSincePosted <= 7): 
                                                    ?>
                                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">
                                                            NEW
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-4 mb-3">
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <i class="fas fa-money-bill-wave mr-2 text-gray-400"></i>
                                                    <div>
                                                        <div class="font-medium text-gray-800"><?= htmlspecialchars($job['salaryGrade']) ?></div>
                                                        <div class="text-xs text-gray-500">Salary Grade</div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <i class="fas fa-users mr-2 text-gray-400"></i>
                                                    <div>
                                                        <div class="font-medium text-gray-800"><?= $job['applicantCount'] ?></div>
                                                        <div class="text-xs text-gray-500">Applicants</div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <p class="text-sm text-gray-700 mb-3 line-clamp-2">
                                                <?= nl2br(htmlspecialchars(substr($job['description'] ?? '', 0, 150))) ?>...
                                            </p>
                                            
                                            <div class="flex flex-wrap gap-2 mb-4">
                                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">PHP</span>
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <div class="text-xs text-gray-500">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <?= timeAgo($job['postedAt'], $job['secondsSincePosted']) ?>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="text-xs text-gray-600">
                                                        <i class="fas fa-calendar-alt mr-1"></i>
                                                        Deadline: <?= date('M j, Y', strtotime($job['deadline'])) ?>
                                                    </div>
                                                    <a href="apply_job.php?id=<?= $job['jobId'] ?>" 
                                                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                                        Apply Now
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                            <i class="fas fa-briefcase text-gray-300 text-5xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No job openings</h3>
                            <p class="text-gray-500 mb-6">Check back later for new opportunities</p>
                            <a href="browse_jobs.php" class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-search mr-2"></i>Browse All Jobs
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right Sidebar -->
            <div class="col-span-3">
                <!-- User Profile Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($_SESSION['firstName'] . ' ' . $_SESSION['lastName']) ?></h3>
                        <p class="text-sm text-gray-600 mb-4">Applicant</p>
                        <button class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            Edit Profile
                        </button>
                    </div>
                </div>
                
                <!-- Quick Stats Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Quick Stats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Applications</span>
                            <span class="font-semibold text-gray-800"><?= $totalApplications ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Pending</span>
                            <span class="font-semibold text-yellow-600"><?= $pendingApplications ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Shortlisted</span>
                            <span class="font-semibold text-green-600"><?= $shortlistedApplications ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Hired</span>
                            <span class="font-semibold text-purple-600"><?= $hiredApplications ?></span>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('sortFilter').addEventListener('change', function() {
    const sortValue = this.value;
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('sort', sortValue);
    window.location.href = currentUrl.toString();
});

let currentPage = <?= $page ?>;
const totalPages = <?= $totalPages ?>;

async function loadNewestJobs(direction) {
    if (direction === 'prev' && currentPage <= 1) return;
    if (direction === 'next' && currentPage >= totalPages) return;
    
    const newPage = direction === 'prev' ? currentPage - 1 : currentPage + 1;
    
    // Disable buttons during loading
    const prevBtn = document.querySelector('button[onclick*="prev"]');
    const nextBtn = document.querySelector('button[onclick*="next"]');
    if (prevBtn) prevBtn.disabled = true;
    if (nextBtn) nextBtn.disabled = true;
    
    try {
        const response = await fetch(`../actions/get_newest_jobs.php?page=${newPage}`);
        const data = await response.json();
        
        if (data.error) {
            console.error(data.error);
            return;
        }
        
        // Update slider with smooth sliding animation
        const slider = document.getElementById('newestJobsSlider');
        
        // Add opacity transition for smooth fade
        slider.style.transition = 'transform 0.4s ease-out, opacity 0.2s ease-out';
        
        // Fade out and slide out
        slider.style.opacity = '0.7';
        if (direction === 'next') {
            slider.style.transform = 'translateX(-20px)';
        } else {
            slider.style.transform = 'translateX(20px)';
        }
        
        setTimeout(() => {
            // Update content instantly
            slider.innerHTML = `<div class="grid grid-cols-3 gap-4 w-full">${data.html}</div>`;
            
            // Reset position for slide in
            if (direction === 'next') {
                slider.style.transform = 'translateX(20px)';
            } else {
                slider.style.transform = 'translateX(-20px)';
            }
            
            // Slide in and fade in
            setTimeout(() => {
                slider.style.opacity = '1';
                slider.style.transform = 'translateX(0)';
            }, 50);
            
            // Update page and button states
            currentPage = data.page;
            updateButtonStates();
        }, 200);
        
    } catch (error) {
        console.error('Error loading jobs:', error);
        // Reset slider on error
        const slider = document.getElementById('newestJobsSlider');
        slider.style.opacity = '1';
        slider.style.transform = 'translateX(0)';
    } finally {
        // Re-enable buttons
        if (prevBtn) prevBtn.disabled = false;
        if (nextBtn) nextBtn.disabled = false;
    }
}

function updateButtonStates() {
    const prevBtn = document.querySelector('button[onclick*="prev"]');
    const nextBtn = document.querySelector('button[onclick*="next"]');
    
    if (prevBtn) {
        prevBtn.disabled = currentPage <= 1;
    }
    if (nextBtn) {
        nextBtn.disabled = currentPage >= totalPages;
    }
}
</script>

<?php include('../includes/footer.php'); ?>
