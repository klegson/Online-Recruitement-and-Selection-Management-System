<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Board') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['applicationId'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$applicationId = $_POST['applicationId'];
$boardMemberId = $_SESSION['user_id'];

try {
    // Check if application exists and is qualified
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE applicationId = ? AND status = 'Qualified'");
    $stmt->execute([$applicationId]);
    $application = $stmt->fetch();

    if (!$application) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Application not found or not qualified']);
        exit();
    }

    // Check if already recommended
    $stmt = $pdo->prepare("SELECT * FROM board_recommendations WHERE applicationId = ? AND boardMemberId = ?");
    $stmt->execute([$applicationId, $boardMemberId]);
    $existing = $stmt->fetch();

    if ($existing) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'You have already recommended this applicant']);
        exit();
    }

    // Create board_recommendations table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS board_recommendations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            applicationId INT NOT NULL,
            boardMemberId INT NOT NULL,
            recommendationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            notes TEXT,
            FOREIGN KEY (applicationId) REFERENCES applications(applicationId),
            FOREIGN KEY (boardMemberId) REFERENCES users(userId),
            UNIQUE KEY unique_recommendation (applicationId, boardMemberId)
        )
    ");

    // Add recommendation
    $stmt = $pdo->prepare("
        INSERT INTO board_recommendations (applicationId, boardMemberId, notes) 
        VALUES (?, ?, 'Recommended for hiring by Board member')
    ");
    $stmt->execute([$applicationId, $boardMemberId]);

    // Update application status if enough recommendations (e.g., 3 board members)
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM board_recommendations WHERE applicationId = ?");
    $stmt->execute([$applicationId]);
    $recommendationCount = $stmt->fetch()['count'];

    if ($recommendationCount >= 3) {
        // Update status to 'Recommended'
        $updateStmt = $pdo->prepare("UPDATE applications SET status = 'Recommended' WHERE applicationId = ?");
        $updateStmt->execute([$applicationId]);
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => 'Applicant recommended successfully!',
        'recommendationCount' => $recommendationCount
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
