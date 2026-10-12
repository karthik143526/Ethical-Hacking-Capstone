<?php
// api/history.php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT a.id, a.ssid, a.security_type, a.created_at, ar.score, ar.status 
        FROM assessments a 
        JOIN assessment_results ar ON a.id = ar.assessment_id 
        WHERE a.user_id = ?
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $history
    ]);
} catch(PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
