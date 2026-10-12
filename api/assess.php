<?php
// api/assess.php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in."]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['ssid']) || !isset($data['sec_type'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request. SSID and Security Type are required."]);
    exit;
}

$userId = $_SESSION['user_id'];
$ssid = $data['ssid'];
$secType = $data['sec_type'];

$score = 100;
$recommendations = [];
$checks = [];

// 1. WPA3 Check
if ($data['wpa3_enabled'] === 'Yes') {
    $checks['wpa3'] = 'PASS';
} else {
    $checks['wpa3'] = 'FAIL';
    $score -= 25;
    $recommendations[] = "WPA3 is disabled. Enable WPA3 to protect against offline dictionary attacks.";
}

// 2. PMF Check
if ($data['pmf'] === 'Required') {
    $checks['pmf'] = 'PASS';
} else if ($data['pmf'] === 'Optional') {
    $checks['pmf'] = 'WARNING';
    $score -= 10;
    $recommendations[] = "PMF is set to Optional. Set Protected Management Frames to Required for better deauthentication protection.";
} else {
    $checks['pmf'] = 'FAIL';
    $score -= 20;
    $recommendations[] = "PMF is disabled. Enable Protected Management Frames immediately.";
}

// 3. Password Check
if ($data['pass_strength'] === 'Strong') {
    $checks['password'] = 'PASS';
} else if ($data['pass_strength'] === 'Medium') {
    $checks['password'] = 'WARNING';
    $score -= 5;
    $recommendations[] = "Medium password detected. It is recommended to use a stronger Wi-Fi password (at least 12 characters, mixed case, symbols).";
} else {
    $checks['password'] = 'FAIL';
    $score -= 15;
    $recommendations[] = "Weak password detected. Use a strong Wi-Fi password immediately to prevent cracking.";
}

// 4. Firmware Check
if ($data['fw_updated'] === 'Yes') {
    $checks['firmware'] = 'PASS';
} else {
    $checks['firmware'] = 'FAIL';
    $score -= 15;
    $recommendations[] = "Firmware is outdated. Update the access point firmware to patch known vulnerabilities.";
}

// 5. Enterprise Checks (if applicable)
if ($secType === 'WPA3-Enterprise') {
    // RADIUS
    if ($data['radius'] === 'Configured') {
        $checks['radius'] = 'PASS';
    } else {
        $checks['radius'] = 'FAIL';
        $score -= 25;
        $recommendations[] = "RADIUS is not configured. Configure a RADIUS server for secure Enterprise authentication.";
    }

    // Certificate
    if ($data['certificate'] === 'Valid') {
        $checks['certificate'] = 'PASS';
    } else {
        $checks['certificate'] = 'FAIL';
        $score -= 20;
        $recommendations[] = "Certificate is invalid. Use a valid and trusted certificate for EAP authentication.";
    }

    // Auth Method
    if ($data['auth_method'] === 'EAP-TLS') {
        $checks['auth_method'] = 'PASS';
    } else if ($data['auth_method'] === 'PEAP') {
        $checks['auth_method'] = 'WARNING';
        $score -= 5;
        $recommendations[] = "PEAP is used. Consider upgrading to EAP-TLS for stronger certificate-based authentication.";
    } else {
        $checks['auth_method'] = 'FAIL';
        $score -= 15;
        $recommendations[] = "Weak authentication method selected. Use EAP-TLS or PEAP.";
    }
}

// Cap score at 0
if ($score < 0) $score = 0;

$status = 'Secure';
if ($score < 70) $status = 'Poor';
else if ($score < 90) $status = 'Needs Improvement';

$details = json_encode(['checks' => $checks, 'inputs' => $data]);

try {
    // 1. Insert Assessment
    $stmt = $pdo->prepare("INSERT INTO assessments (user_id, ssid, security_type) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $ssid, $secType]);
    $assessmentId = $pdo->lastInsertId();

    // 2. Insert Results
    $stmt2 = $pdo->prepare("INSERT INTO assessment_results (assessment_id, score, status, details) VALUES (?, ?, ?, ?)");
    $stmt2->execute([$assessmentId, $score, $status, $details]);

    // 3. Insert Recommendations
    $stmt3 = $pdo->prepare("INSERT INTO recommendations (assessment_id, recommendation_text) VALUES (?, ?)");
    foreach ($recommendations as $rec) {
        $stmt3->execute([$assessmentId, $rec]);
    }

    echo json_encode(["status" => "success", "assessment_id" => $assessmentId]);
} catch(PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
