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

// Handle filtering parameters
$search = $_GET['search'] ?? '';
$minSalary = $_GET['minSalary'] ?? 0;
$maxSalary = $_GET['maxSalary'] ?? 200000;
$departments = $_GET['departments'] ?? '';

// Build WHERE conditions for filtering
$whereConditions = ["j.jobStatus = 'Open'", "j.deadline >= CURDATE()"];
$params = [];

// Add search condition
if (!empty($search)) {
    $whereConditions[] = "(j.position LIKE ? OR j.department LIKE ? OR j.description LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

// Add salary range condition
if ($minSalary > 0 || $maxSalary < 200000) {
    $whereConditions[] = "j.minSalary >= ? AND j.maxSalary <= ?";
    $params[] = $minSalary;
    $params[] = $maxSalary;
}

// Add department condition
if (!empty($departments)) {
    $deptArray = explode(',', $departments);
    $deptPlaceholders = str_repeat('?,', count($deptArray) - 1) . '?';
    $whereConditions[] = "j.department IN ($deptPlaceholders)";
    $params = array_merge($params, $deptArray);
}

$whereClause = implode(' AND ', $whereConditions);

// Fetch newest jobs (latest 3 for horizontal section with pagination)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * 3;

$stmt = $pdo->prepare("
    SELECT j.*, COUNT(a.applicationId) as applicantCount, TIMESTAMPDIFF(SECOND, j.postedAt, NOW()) as secondsSincePosted 
    FROM jobs j 
    LEFT JOIN applications a ON j.jobId = a.jobId 
    WHERE $whereClause
    GROUP BY j.jobId 
    ORDER BY j.postedAt DESC 
    LIMIT 3 OFFSET $offset
");
$stmt->execute($params);
$newestJobs = $stmt->fetchAll();

// Get total newest jobs count for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM jobs j WHERE $whereClause");
$stmt->execute($params);
$totalNewestJobs = $stmt->fetch()['total'];
$totalPages = ceil($totalNewestJobs / 3);

// Get unique departments for filter
$allDepartments = $pdo->query("SELECT DISTINCT department FROM jobs WHERE jobStatus = 'Open' ORDER BY department")->fetchAll();

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
    <div class="max-w-[1400px] mx-auto px-6 py-8">
        <div class="grid grid-cols-10 gap-6">
            
            <!-- Left Sidebar - Filters -->
            <div class="col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6 h-fit">
                    <h3 class="font-semibold text-gray-800 mb-6">Filters</h3>
                    
                    <!-- Search Bar -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" id="searchInput" placeholder="Search jobs..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <!-- Salary Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Salary Range</label>
                        <div class="space-y-4">
                            <!-- Min Salary -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs text-gray-600">Minimum</span>
                                    <span class="text-xs font-medium text-blue-600">₱<span id="minSalaryValue">0</span></span>
                                </div>
                                <input type="range" id="minSalary" min="0" max="200000" value="0" step="5000"
                                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                            </div>
                            
                            <!-- Max Salary -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs text-gray-600">Maximum</span>
                                    <span class="text-xs font-medium text-blue-600">₱<span id="maxSalaryValue">200000</span></span>
                                </div>
                                <input type="range" id="maxSalary" min="0" max="200000" value="200000" step="5000"
                                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                            </div>
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
                    
                    <button onclick="applyFilters()" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Apply Filters
                    </button>
                </div>
            </div>
            
            <!-- Center Content - Job Listings -->
            <div class="col-span-6 space-y-6">
                <!-- Newest Jobs Section -->
                <div class="bg-white rounded-xl shadow-sm p-6 h-fit">
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
                                                        <?= $job['applicantCount'] ?> applicant<?= $job['applicantCount'] != 1 ? 's' : '' ?>
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
                <div id="otherJobsSection">
                    <h3 class="font-semibold text-gray-800 mb-4">Other Jobs</h3>
                    <?php if (!empty($recommendedJobs)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recommendedJobs as $job): ?>
                                <div class="job-card bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow h-fit">
                                    <div class="flex items-start">
                                        <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                            <i class="fas fa-building text-gray-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between mb-2">
                                                <div>
                                                    <h4 class="job-position font-semibold text-gray-800 text-lg"><?= htmlspecialchars($job['position']) ?></h4>
                                                    <p class="job-department text-sm text-gray-600"><?= htmlspecialchars($job['department']) ?></p>
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
                                                        <div class="salary-grade font-medium text-gray-800"><?= htmlspecialchars($job['salaryGrade']) ?></div>
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
            <div class="col-span-2 space-y-6">
                <!-- User Profile Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 h-fit">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800"><?= htmlspecialchars(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')) ?></h3>
                        <p class="text-sm text-gray-600 mb-4">Applicant</p>
                        <button class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            Edit Profile
                        </button>
                    </div>
                </div>
                
                <!-- Recent Applications -->
                <div class="bg-white rounded-xl shadow-sm p-6 h-fit">
                    <h3 class="font-semibold text-gray-800 mb-4">Recent Applications</h3>
                    <?php if (!empty($recentApplications)): ?>
                        <div class="space-y-3">
                            <?php foreach (array_slice($recentApplications, 0, 5) as $app): ?>
                                <div class="border-l-4 border-blue-500 pl-3 py-2">
                                    <h5 class="font-medium text-gray-800 text-sm"><?= htmlspecialchars($app['position']) ?></h5>
                                    <p class="text-xs text-gray-600"><?= htmlspecialchars($app['department']) ?></p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-xs text-gray-500"><?= timeAgo($app['appliedAt']) ?></span>
                                        <span class="text-xs px-2 py-1 rounded-full bg-<?= $app['status'] === 'Pending' ? 'yellow' : ($app['status'] === 'Qualified' ? 'green' : 'red') ?>-100 text-<?= $app['status'] === 'Pending' ? 'yellow' : ($app['status'] === 'Qualified' ? 'green' : 'red') ?>-800">
                                            <?= htmlspecialchars($app['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($recentApplications) > 5): ?>
                            <a href="my_applications.php" class="block text-center text-sm text-blue-600 hover:text-blue-800 mt-4">
                                View All Applications →
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-500 text-center">No applications yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('sortFilter').addEventListener('change', function() {
    const sortValue = this.value;
    const currentUrl = new window.URL(window.location);
    currentUrl.searchParams.set('sort', sortValue);
    window.location.href = currentUrl.toString();
});

// Salary Range Functions
const minSalarySlider = document.getElementById('minSalary');
const maxSalarySlider = document.getElementById('maxSalary');
const minSalaryValue = document.getElementById('minSalaryValue');
const maxSalaryValue = document.getElementById('maxSalaryValue');

// Update salary display values
minSalarySlider.addEventListener('input', function() {
    const minValue = parseInt(this.value);
    const maxValue = parseInt(maxSalarySlider.value);
    
    // Ensure min doesn't exceed max
    if (minValue > maxValue) {
        this.value = maxValue;
        minSalaryValue.textContent = maxValue.toLocaleString();
    } else {
        minSalaryValue.textContent = minValue.toLocaleString();
    }
});

maxSalarySlider.addEventListener('input', function() {
    const minValue = parseInt(minSalarySlider.value);
    const maxValue = parseInt(this.value);
    
    // Ensure max doesn't go below min
    if (maxValue < minValue) {
        this.value = minValue;
        maxSalaryValue.textContent = minValue.toLocaleString();
    } else {
        maxSalaryValue.textContent = maxValue.toLocaleString();
    }
});

// Filter functionality
function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const minSalary = document.getElementById('minSalary').value;
    const maxSalary = document.getElementById('maxSalary').value;
    const sort = document.getElementById('sortFilter').value;
    
    let url = '?';
    const params = [];
    
    if (search) params.push('search=' + encodeURIComponent(search));
    if (minSalary !== '0') params.push('minSalary=' + minSalary);
    if (maxSalary !== '200000') params.push('maxSalary=' + maxSalary);
    if (sort !== 'newest') params.push('sort=' + sort);
    
    url += params.join('&');
    
    window.location.href = url || window.location.pathname;
}

// Real-time search
document.getElementById('searchInput').addEventListener('input', applyFilters);

// Salary filter change
minSalarySlider.addEventListener('change', applyFilters);
maxSalarySlider.addEventListener('change', applyFilters);

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
