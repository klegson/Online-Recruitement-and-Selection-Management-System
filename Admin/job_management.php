 <?php
session_start();
require_once '../config/db.php';
require_once '../includes/admin_sidebar.php';
require_once '../includes/audit_functions.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    try {
        if ($_POST['action'] === 'add_department') {
            $stmt = $pdo->prepare("INSERT INTO departments (dept_name) VALUES (?)");
            $stmt->execute([$_POST['dept_name']]);
            $id = $pdo->lastInsertId();
            
            // Log the activity
            logActivity($_SESSION['user_id'], 'Created Department', 'department', $id, 
                "Created new department: " . $_POST['dept_name']);
            
            echo json_encode(['success' => true, 'message' => 'Department added successfully', 'id' => $id]);
        } elseif ($_POST['action'] === 'edit_department') {
            $stmt = $pdo->prepare("UPDATE departments SET dept_name = ? WHERE id = ?");
            $stmt->execute([$_POST['dept_name'], $_POST['id']]);
            
            // Log the activity
            logActivity($_SESSION['user_id'], 'Updated Department', 'department', $_POST['id'], 
                "Updated department to: " . $_POST['dept_name']);
            
            echo json_encode(['success' => true, 'message' => 'Department updated successfully']);
        } elseif ($_POST['action'] === 'delete_department') {
            // Check if department is being used by any jobs
            $checkStmt = $pdo->prepare("SELECT COUNT(*) as count FROM jobs WHERE dept_id = ?");
            $checkStmt->execute([$_POST['id']]);
            $count = $checkStmt->fetch()['count'];
            
            if ($count > 0) {
                echo json_encode(['success' => false, 'message' => "Cannot delete department - $count jobs are using it"]);
            } else {
                $stmt = $pdo->prepare("UPDATE departments SET is_active = FALSE WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                
                // Log the activity
                logActivity($_SESSION['user_id'], 'Deleted Department', 'department', $_POST['id'], 
                    "Deleted department ID: " . $_POST['id']);
                
                echo json_encode(['success' => true, 'message' => 'Department deleted successfully']);
            }
        } elseif ($_POST['action'] === 'add_position') {
            $stmt = $pdo->prepare("INSERT INTO positions (position_name) VALUES (?)");
            $stmt->execute([$_POST['position_name']]);
            $id = $pdo->lastInsertId();
            
            // Log the activity
            logActivity($_SESSION['user_id'], 'Created Position', 'position', $id, 
                "Created new position: " . $_POST['position_name']);
            
            echo json_encode(['success' => true, 'message' => 'Position added successfully', 'id' => $id]);
        } elseif ($_POST['action'] === 'edit_position') {
            $stmt = $pdo->prepare("UPDATE positions SET position_name = ? WHERE id = ?");
            $stmt->execute([$_POST['position_name'], $_POST['id']]);
            
            // Log the activity
            logActivity($_SESSION['user_id'], 'Updated Position', 'position', $_POST['id'], 
                "Updated position to: " . $_POST['position_name']);
            
            echo json_encode(['success' => true, 'message' => 'Position updated successfully']);
        } elseif ($_POST['action'] === 'delete_position') {
            // Check if position is being used by any jobs
            $checkStmt = $pdo->prepare("SELECT COUNT(*) as count FROM jobs WHERE pos_id = ?");
            $checkStmt->execute([$_POST['id']]);
            $count = $checkStmt->fetch()['count'];
            
            if ($count > 0) {
                echo json_encode(['success' => false, 'message' => "Cannot delete position - $count jobs are using it"]);
            } else {
                $stmt = $pdo->prepare("UPDATE positions SET is_active = FALSE WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                
                // Log the activity
                logActivity($_SESSION['user_id'], 'Deleted Position', 'position', $_POST['id'], 
                    "Deleted position ID: " . $_POST['id']);
                
                echo json_encode(['success' => true, 'message' => 'Position deleted successfully']);
            }
        } elseif ($_POST['action'] === 'delete_job') {
            // Get job details for audit log
            $jobStmt = $pdo->prepare("
                SELECT j.*, d.dept_name, p.position_name 
                FROM jobs j 
                LEFT JOIN departments d ON j.dept_id = d.id 
                LEFT JOIN positions p ON j.pos_id = p.id 
                WHERE j.id = ?
            ");
            $jobStmt->execute([$_POST['id']]);
            $job = $jobStmt->fetch();
            
            if ($job) {
                // Delete job requirements first
                $reqStmt = $pdo->prepare("DELETE FROM job_requirements WHERE jobId = ?");
                $reqStmt->execute([$_POST['id']]);
                
                // Delete the job
                $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                
                // Log the activity
                logActivity($_SESSION['user_id'], 'Deleted Job', 'job', $_POST['id'], 
                    "Deleted job: {$job['position_name']} in {$job['dept_name']}");
                
                echo json_encode(['success' => true, 'message' => 'Job deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Job not found']);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch data with JOINs to get department and position names
$jobs = $pdo->query("
    SELECT j.*, d.dept_name as department_name, p.position_name as position_name 
    FROM jobs j 
    LEFT JOIN departments d ON j.dept_id = d.id 
    LEFT JOIN positions p ON j.pos_id = p.id 
    ORDER BY j.postedAt DESC
")->fetchAll();

$departments = $pdo->query("SELECT * FROM departments WHERE is_active = TRUE ORDER BY dept_name")->fetchAll();
$positions = $pdo->query("SELECT * FROM positions WHERE is_active = TRUE ORDER BY position_name")->fetchAll();
?>

<!-- Success/Error Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo htmlspecialchars($_SESSION['success']); ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo htmlspecialchars($_SESSION['error']); ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Tabs Navigation -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
            <button onclick="showTab('jobs')" class="tab-btn active px-6 py-3 border-b-2 border-blue-500 font-medium text-blue-600 focus:outline-none" data-tab="jobs">
                <i class="fas fa-briefcase mr-2"></i>Job Postings
            </button>
            <button onclick="showTab('departments')" class="tab-btn px-6 py-3 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none" data-tab="departments">
                <i class="fas fa-building mr-2"></i>Departments
            </button>
            <button onclick="showTab('positions')" class="tab-btn px-6 py-3 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none" data-tab="positions">
                <i class="fas fa-user-tie mr-2"></i>Positions
            </button>
        </nav>
    </div>
</div>

<!-- Job Postings Tab -->
<div id="jobs-tab" class="tab-content">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">Job Postings Management</h3>
            <a href="../HR_staff/create_job.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New Job
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Position</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Department</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Salary Grade</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Posted</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Applications</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $job): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($job['position_name'] ?? 'Unknown'); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($job['plantillaItemNo'] ?? ''); ?></div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($job['department_name'] ?? 'Unassigned'); ?></td>
                            <td class="py-3 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($job['salaryGrade'] ?? ''); ?></td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-1 text-xs rounded-full 
                                    <?php echo $job['jobStatus'] === 'Open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo htmlspecialchars($job['jobStatus']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                <?php echo date('M d, Y', strtotime($job['postedAt'])); ?>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                <?php 
                                $appCount = $pdo->prepare("SELECT COUNT(*) as count FROM applications WHERE jobId = ?");
                                $appCount->execute([$job['jobId']]);
                                echo $appCount->fetch()['count'];
                                ?>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <a href="../HR_staff/edit_job.php?id=<?php echo $job['jobId']; ?>" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="confirmDeleteJob(<?php echo $job['jobId']; ?>, '<?php echo htmlspecialchars($job['position_name'] ?? 'Unknown'); ?>')" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Departments Tab -->
<div id="departments-tab" class="tab-content hidden">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">Departments Management</h3>
            <button onclick="showDepartmentForm()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Department
            </button>
        </div>
        
        <!-- Department Form (Hidden by default) -->
        <div id="departmentForm" class="hidden mb-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-semibold text-gray-800 mb-4">Add New Department</h4>
            <form id="departmentFormElement">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department Name</label>
                        <input type="text" name="dept_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Save Department
                    </button>
                    <button type="button" onclick="hideDepartmentForm()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Departments List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($departments as $dept): ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($dept['dept_name']); ?></h4>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editDepartment(<?php echo $dept['id']; ?>, '<?php echo htmlspecialchars($dept['dept_name']); ?>')" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="confirmDeleteDepartment(<?php echo $dept['id']; ?>, '<?php echo htmlspecialchars($dept['dept_name']); ?>')" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Positions Tab -->
<div id="positions-tab" class="tab-content hidden">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">Positions Management</h3>
            <button onclick="showPositionForm()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Position
            </button>
        </div>
        
        <!-- Position Form (Hidden by default) -->
        <div id="positionForm" class="hidden mb-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-semibold text-gray-800 mb-4">Add New Position</h4>
            <form id="positionFormElement">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position Name</label>
                        <input type="text" name="position_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Save Position
                    </button>
                    <button type="button" onclick="hidePositionForm()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Positions List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($positions as $position): ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($position['position_name']); ?></h4>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editPosition(<?php echo $position['id']; ?>, '<?php echo htmlspecialchars($position['position_name']); ?>')" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="confirmDeletePosition(<?php echo $position['id']; ?>, '<?php echo htmlspecialchars($position['position_name']); ?>')" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_sidebar_footer.php'; ?>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Confirm Deletion</h3>
                <p class="text-sm text-gray-600">This action cannot be undone.</p>
            </div>
        </div>
        <p id="confirmMessage" class="text-gray-700 mb-6"></p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeConfirmModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button id="confirmButton" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Delete
            </button>
        </div>
    </div>
</div>

<script>
// Tab functionality
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    activeBtn.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
}

// Department form functions
function showDepartmentForm() {
    document.getElementById('departmentForm').classList.remove('hidden');
}

function hideDepartmentForm() {
    document.getElementById('departmentForm').classList.add('hidden');
    document.getElementById('departmentFormElement').reset();
}

// Position form functions
function showPositionForm() {
    document.getElementById('positionForm').classList.remove('hidden');
}

function hidePositionForm() {
    document.getElementById('positionForm').classList.add('hidden');
    document.getElementById('positionFormElement').reset();
}

// Confirmation modal functions
function confirmDeleteJob(jobId, jobTitle) {
    document.getElementById('confirmMessage').textContent = `Are you sure you want to delete the job posting "${jobTitle}"? This will also delete all associated applications and cannot be undone.`;
    document.getElementById('confirmButton').onclick = function() {
        deleteJob(jobId);
        closeConfirmModal();
    };
    document.getElementById('confirmModal').classList.remove('hidden');
}

function confirmDeleteDepartment(deptId, deptName) {
    document.getElementById('confirmMessage').textContent = `Are you sure you want to delete the department "${deptName}"? This action cannot be undone.`;
    document.getElementById('confirmButton').onclick = function() {
        deleteDepartment(deptId);
        closeConfirmModal();
    };
    document.getElementById('confirmModal').classList.remove('hidden');
}

function confirmDeletePosition(posId, posName) {
    document.getElementById('confirmMessage').textContent = `Are you sure you want to delete the position "${posName}"? This action cannot be undone.`;
    document.getElementById('confirmButton').onclick = function() {
        deletePosition(posId);
        closeConfirmModal();
    };
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
}

// AJAX form submissions
document.getElementById('departmentFormElement').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'add_department');
    formData.append('ajax', '1');
    
    fetch('job_management.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});

document.getElementById('positionFormElement').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'add_position');
    formData.append('ajax', '1');
    
    fetch('job_management.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});

// Delete functions
function deleteJob(jobId) {
    const formData = new FormData();
    formData.append('action', 'delete_job');
    formData.append('id', jobId);
    formData.append('ajax', '1');
    
    fetch('job_management.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function deleteDepartment(deptId) {
    const formData = new FormData();
    formData.append('action', 'delete_department');
    formData.append('id', deptId);
    formData.append('ajax', '1');
    
    fetch('job_management.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function deletePosition(posId) {
    const formData = new FormData();
    formData.append('action', 'delete_position');
    formData.append('id', posId);
    formData.append('ajax', '1');
    
    fetch('job_management.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

// Edit functions
function editDepartment(id, name) {
    const newName = prompt('Department Name:', name);
    if (newName && newName !== name) {
        const formData = new FormData();
        formData.append('action', 'edit_department');
        formData.append('id', id);
        formData.append('dept_name', newName);
        formData.append('ajax', '1');
        
        fetch('job_management.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function editPosition(id, name) {
    const newName = prompt('Position Name:', name);
    if (newName && newName !== name) {
        const formData = new FormData();
        formData.append('action', 'edit_position');
        formData.append('id', id);
        formData.append('position_name', newName);
        formData.append('ajax', '1');
        
        fetch('job_management.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}
</script>
