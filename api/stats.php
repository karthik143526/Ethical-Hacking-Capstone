<?php
// api/stats.php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Total assessments
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM assessments WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total = $stmt->fetchColumn();

    // Secure networks (Score >= 90)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM assessment_results ar 
        JOIN assessments a ON ar.assessment_id = a.id 
        WHERE a.user_id = ? AND ar.status = 'Secure'
    ");
    $stmt->execute([$user_id]);
    $secure = $stmt->fetchColumn();

    // Problems found (Score < 90)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM assessment_results ar 
        JOIN assessments a ON ar.assessment_id = a.id 
        WHERE a.user_id = ? AND ar.status != 'Secure'
    ");
    $stmt->execute([$user_id]);
    $problems = $stmt->fetchColumn();

    // Avg Score
    $stmt = $pdo->prepare("
        SELECT AVG(score) FROM assessment_results ar 
        JOIN assessments a ON ar.assessment_id = a.id 
        WHERE a.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $avg = round($stmt->fetchColumn() ?? 0);

    echo json_encode([
        "status" => "success",
        "total" => $total,
        "secure" => $secure,
        "problems" => $problems,
        "avg" => $avg
    ]);

} catch(PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
