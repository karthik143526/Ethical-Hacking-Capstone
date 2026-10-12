<?php
// api/auth.php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

$action = $_GET['action'] ?? '';

if ($action === 'register') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password]);
        echo json_encode(["status" => "success", "message" => "Registration successful. You can now login."]);
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            echo json_encode(["status" => "error", "message" => "Email already exists."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        }
    }
} 
elseif ($action === 'login') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            echo json_encode(["status" => "success", "role" => $user['role']]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
        }
    } catch(PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}
elseif ($action === 'logout') {
    session_destroy();
    echo json_encode(["status" => "success"]);
}
elseif ($action === 'check') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode([
            "status" => "success", 
            "user_id" => $_SESSION['user_id'], 
            "name" => $_SESSION['user_name'],
            "role" => $_SESSION['user_role']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Not logged in."]);
    }
}
?>
