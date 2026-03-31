<?php
// ajax_update_status.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ob_start();
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

function send_json_response($success, $message) {
    ob_clean();
    echo json_encode(['success' => $success, 'message' => $message]);
    exit();
}

// Check authorization
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    send_json_response(false, 'Unauthorized access');
}

// Get input data
$json_input = file_get_contents('php://input');
if ($json_input === false || empty($json_input)) {
    $json_input = json_encode($_POST);
}

$data = json_decode($json_input, true);
if (!$data) {
    send_json_response(false, 'Invalid input data');
}

// Validate required fields
$required_fields = ['applicationId', 'status', 'emailSubject', 'emailMessage', 'applicantEmail'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        send_json_response(false, "Missing field: $field");
    }
}

$applicationId = $data['applicationId'];
$newStatus = $data['status'];
$hrNotes = $data['hrNotes'] ?? '';
$emailSubject = $data['emailSubject'];
$emailMessage = $data['emailMessage'];
$applicantEmail = $data['applicantEmail'];
$applicantName = $data['applicantName'] ?? 'Applicant';

// Include required files
$base_path = dirname(__DIR__);
require_once $base_path . '/config/db.php';
require_once $base_path . '/config/mail_config.php';
require_once $base_path . '/PHPMailer/PHPMailer.php';
require_once $base_path . '/PHPMailer/SMTP.php';
require_once $base_path . '/PHPMailer/Exception.php';

try {
    $pdo->beginTransaction();
    
    // Send email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = 'tls';
    $mail->Port = SMTP_PORT;
    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    $mail->addAddress($applicantEmail, $applicantName);
    $mail->isHTML(false);
    $mail->Subject = $emailSubject;
    $mail->Body = $emailMessage;
    $mail->send();
    
    // Update database
    $stmt = $pdo->prepare("
        UPDATE applications 
        SET status = ?, hrNotes = ?, updatedBy = ? 
        WHERE applicationId = ?
    ");
    $stmt->execute([$newStatus, $hrNotes, $_SESSION['user_id'], $applicationId]);
    
    $pdo->commit();
    send_json_response(true, 'Email sent and status updated successfully');
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    send_json_response(false, 'Failed to send email: ' . $e->getMessage());
}
?>
