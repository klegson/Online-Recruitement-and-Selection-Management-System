<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    header("Location: ../login.php");
    exit();
}

// Handle CRUD operations
$message = '';
$messageType = '';

// Create new document type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO document_types (name, description) VALUES (?, ?)");
            $stmt->execute([$name, $description]);
            $message = "Document type created successfully!";
            $messageType = "success";
        } catch (Exception $e) {
            $message = "Error creating document type: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $message = "Document type name is required!";
        $messageType = "error";
    }
}

// Update document type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("UPDATE document_types SET name = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $description, $id]);
            $message = "Document type updated successfully!";
            $messageType = "success";
        } catch (Exception $e) {
            $message = "Error updating document type: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $message = "Document type name is required!";
        $messageType = "error";
    }
}

// Delete document type
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Check if document type is being used by any applications
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM application_files WHERE documentTypeId = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetch()['count'];
        
        if ($count > 0) {
            $message = "Cannot delete document type - it is being used by " . $count . " application(s)";
            $messageType = "error";
        } else {
            $stmt = $pdo->prepare("DELETE FROM document_types WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Document type deleted successfully!";
            $messageType = "success";
        }
    } catch (Exception $e) {
        $message = "Error deleting document type: " . $e->getMessage();
        $messageType = "error";
    }
}

// Fetch all document types
$stmt = $pdo->query("SELECT * FROM document_types ORDER BY name");
$documentTypes = $stmt->fetchAll();

// Get document type for editing
$editingType = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM document_types WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $editingType = $stmt->fetch();
}

include('../includes/header.php');
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manage Document Types</h2>
        <a href="hr_dashboard.php" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Message Display -->
    <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 rounded <?= $messageType === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Add/Edit Form -->
    <div class="bg-gray-50 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">
            <?= $editingType ? 'Edit Document Type' : 'Add New Document Type' ?>
        </h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="<?= $editingType ? 'update' : 'create' ?>">
            <?php if ($editingType): ?>
                <input type="hidden" name="id" value="<?= $editingType['id'] ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Document Type Name *</label>
                    <input type="text" name="name" required
                           value="<?= $editingType ? htmlspecialchars($editingType['name']) : '' ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., Resume, Transcript, Certificate">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Optional description of this document type"><?= $editingType ? htmlspecialchars($editingType['description']) : '' ?></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i><?= $editingType ? 'Update' : 'Create' ?> Document Type
                </button>
                <?php if ($editingType): ?>
                    <a href="manage_document_types.php" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Document Types List -->
    <div>
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Existing Document Types</h3>
        
        <?php if (empty($documentTypes)): ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-file-alt text-4xl mb-3"></i>
                <p>No document types found. Create your first document type above.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($documentTypes as $type): ?>
                            <?php 
                            // Get usage count
                            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM application_files WHERE documentTypeId = ?");
                            $stmt->execute([$type['id']]);
                            $usageCount = $stmt->fetch()['count'];
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($type['name']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">
                                        <?= !empty($type['description']) ? htmlspecialchars($type['description']) : '<em class="text-gray-400">No description</em>' ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900"><?= $usageCount ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="?action=edit&id=<?= $type['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <?php if ($usageCount == 0): ?>
                                        <a href="?action=delete&id=<?= $type['id'] ?>" 
                                           onclick="return confirm('Are you sure you want to delete this document type?')"
                                           class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400 cursor-not-allowed" title="Cannot delete - document type is in use">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
