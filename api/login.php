<?php
// api/login.php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($_GET['check'])) {
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        echo json_encode(["status" => "success", "message" => "Logged in"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
    }
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    echo json_encode(["status" => "success", "message" => "Logged out"]);
    exit;
}

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(["status" => "error", "message" => "Email and password required"]);
    exit;
}

$email = $data['email'];
$password = md5($data['password']); // Using MD5 as per our DB script

try {
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ? AND password_hash = ?");
    $stmt->execute([$email, $password]);
    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        echo json_encode(["status" => "success", "message" => "Login successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
    }
} catch(PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
