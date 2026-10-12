<?php
// api/admin.php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

$action = $_GET['action'] ?? '';

try {
    if ($action === 'users') {
        $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "data" => $users]);
    } 
    elseif ($action === 'assessments') {
        $stmt = $pdo->query("
            SELECT a.id, u.name as user_name, a.ssid, a.security_type, a.created_at, ar.score, ar.status 
            FROM assessments a 
            JOIN users u ON a.user_id = u.id
            JOIN assessment_results ar ON a.id = ar.assessment_id 
            ORDER BY a.created_at DESC
        ");
        $assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "data" => $assessments]);
    }
    elseif ($action === 'delete_assessment') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM assessments WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "success", "message" => "Assessment deleted."]);
        }
    }
} catch(PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
