<?php
// api/results.php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(["status" => "error", "message" => "No ID provided"]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT a.ssid, a.security_type, a.created_at, ar.score, ar.status, ar.details 
        FROM assessments a 
        JOIN assessment_results ar ON a.id = ar.assessment_id 
        WHERE a.id = ? AND a.user_id = ?
    ");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(["status" => "error", "message" => "Assessment not found"]);
        exit;
    }

    $stmt2 = $pdo->prepare("SELECT recommendation_text FROM recommendations WHERE assessment_id = ?");
    $stmt2->execute([$id]);
    $recs = $stmt2->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        "status" => "success",
        "data" => $result,
        "recommendations" => $recs
    ]);

} catch(PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
