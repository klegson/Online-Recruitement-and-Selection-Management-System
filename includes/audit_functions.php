<?php
/**
 * Audit Functions - Centralized logging system for security and activity tracking
 */

require_once __DIR__ . '/../config/db.php';

/**
 * Log user activity to audit_logs table
 * 
 * @param int $userId User ID performing the action
 * @param string $action Type of action (e.g., 'Created Job', 'Updated Application Status')
 * @param string $entityType Type of entity (e.g., 'job', 'application', 'user')
 * @param int|null $entityId ID of the entity being acted upon
 * @param string $details Detailed description of the action
 * @return bool Success status
 */
function logActivity($userId, $action, $entityType, $entityId = null, $details = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (userId, action, details, ipAddress, userAgent, timestamp) 
            VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ");
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        // Include entity info in details
        $fullDetails = $details;
        if ($entityType && $entityId) {
            $fullDetails .= " [Entity: {$entityType} #{$entityId}]";
        }
        
        return $stmt->execute([
            $userId,
            $action,
            $fullDetails,
            $ipAddress,
            $userAgent
        ]);
    } catch (Exception $e) {
        error_log("Audit log error: " . $e->getMessage());
        return false;
    }
}

/**
 * Log login attempts to login_history table
 * 
 * @param int|null $userId User ID (null for failed attempts)
 * @param string $userName Display name of the user
 * @param string $email Email address used
 * @param string $status 'Success' or 'Failed'
 * @param string|null $failureReason Reason for failure (e.g., 'invalid_password', 'user_not_found')
 * @return bool Success status
 */
function logLoginAttempt($userId, $userName, $email, $status, $failureReason = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO login_history (user_id, user_name, email, status, failure_reason, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ");
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        return $stmt->execute([
            $userId,
            $userName,
            $email,
            $status,
            $failureReason,
            $ipAddress,
            $userAgent
        ]);
    } catch (PDOException $e) {
        // Log error but don't break the application
        error_log("Login history error: " . $e->getMessage());
        return false;
    }
}

/**
 * Convenience function for logging job creation
 */
function logJobCreation($userId, $jobId, $positionName, $departmentName) {
    return logActivity($userId, 'Created Job', 'job', $jobId, 
        "Created new job posting: $positionName in $departmentName");
}

/**
 * Convenience function for logging job updates
 */
function logJobUpdate($userId, $jobId, $details) {
    return logActivity($userId, 'Updated Job', 'job', $jobId, $details);
}

/**
 * Convenience function for logging job deletion
 */
function logJobDeletion($userId, $jobId, $positionName, $departmentName) {
    return logActivity($userId, 'Deleted Job', 'job', $jobId, 
        "Deleted job: $positionName in $departmentName");
}

/**
 * Convenience function for logging user creation
 */
function logUserCreation($userId, $newUserId, $newUserName, $role) {
    return logActivity($userId, 'Created User', 'user', $newUserId, 
        "Created new $role account: $newUserName");
}

/**
 * Convenience function for logging user updates
 */
function logUserUpdate($userId, $targetUserId, $details) {
    return logActivity($userId, 'Updated User', 'user', $targetUserId, $details);
}

/**
 * Convenience function for logging application status changes
 */
function logApplicationStatusChange($userId, $applicationId, $oldStatus, $newStatus, $applicantName) {
    return logActivity($userId, 'Updated Application Status', 'application', $applicationId, 
        "Changed application status from '$oldStatus' to '$newStatus' for applicant: $applicantName");
}

/**
 * Get audit logs with filtering options
 * 
 * @param array $filters Optional filters (user_id, action, entity_type, date_from, date_to)
 * @param int $limit Number of records to return
 * @param int $offset Offset for pagination
 * @return array Array of audit log records
 */
function getAuditLogs($filters = [], $limit = 50, $offset = 0) {
    global $pdo;
    
    try {
        $sql = "
            SELECT al.*, u.firstName, u.lastName, u.userRole 
            FROM audit_logs al 
            LEFT JOIN users u ON al.userId = u.userId 
            WHERE 1=1
        ";
        $params = [];
        
        // Apply filters
        if (!empty($filters['userId'])) {
            $sql .= " AND al.userId = ?";
            $params[] = $filters['userId'];
        }
        
        if (!empty($filters['action'])) {
            $sql .= " AND al.action LIKE ?";
            $params[] = '%' . $filters['action'] . '%';
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND al.timestamp >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND al.timestamp <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY al.timestamp DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching audit logs: " . $e->getMessage());
        return [];
    }
}

/**
 * Get login history with filtering options
 * 
 * @param array $filters Optional filters (user_id, status, date_from, date_to)
 * @param int $limit Number of records to return
 * @param int $offset Offset for pagination
 * @return array Array of login history records
 */
function getLoginHistory($filters = [], $limit = 50, $offset = 0) {
    global $pdo;
    
    try {
        $sql = "
            SELECT lh.*, u.firstName, u.lastName, u.userRole 
            FROM login_history lh 
            LEFT JOIN users u ON lh.user_id = u.userId 
            WHERE 1=1
        ";
        $params = [];
        
        // Apply filters
        if (!empty($filters['user_id'])) {
            $sql .= " AND lh.user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND lh.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND lh.created_at >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND lh.created_at <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY lh.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching login history: " . $e->getMessage());
        return [];
    }
}

/**
 * Count total audit logs for pagination
 */
function countAuditLogs($filters = []) {
    global $pdo;
    
    try {
        $sql = "SELECT COUNT(*) as count FROM audit_logs WHERE 1=1";
        $params = [];
        
        // Apply same filters as getAuditLogs
        if (!empty($filters['userId'])) {
            $sql .= " AND userId = ?";
            $params[] = $filters['userId'];
        }
        
        if (!empty($filters['action'])) {
            $sql .= " AND action LIKE ?";
            $params[] = '%' . $filters['action'] . '%';
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND timestamp >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND timestamp <= ?";
            $params[] = $filters['date_to'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch()['count'];
    } catch (PDOException $e) {
        error_log("Error counting audit logs: " . $e->getMessage());
        return 0;
    }
}

/**
 * Count total login history records for pagination
 */
function countLoginHistory($filters = []) {
    global $pdo;
    
    try {
        $sql = "SELECT COUNT(*) as count FROM login_history WHERE 1=1";
        $params = [];
        
        // Apply same filters as getLoginHistory
        if (!empty($filters['user_id'])) {
            $sql .= " AND user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND created_at >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND created_at <= ?";
            $params[] = $filters['date_to'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch()['count'];
    } catch (PDOException $e) {
        error_log("Error counting login history: " . $e->getMessage());
        return 0;
    }
}
?>
