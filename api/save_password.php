<?php
/**
 * SecureVault Extension API
 * Handles saving passwords from the Chrome Extension
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');

require_once "../config/db.php";
require_once "../includes/functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? ($data['api_key'] ?? '');

if (empty($api_key)) {
    echo json_encode(['success' => false, 'message' => 'API Key required']);
    exit;
}

// Validate API Key and get User ID
// For now, we'll look for the master_password_hash as a simple "API Key" or 
// we can use a dedicated column. Let's check the users table.
$stmt = $pdo->prepare("SELECT id FROM users WHERE master_password_hash = :key LIMIT 1");
$stmt->execute([':key' => $api_key]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Invalid API Key']);
    exit;
}

$user_id = $user['id'];
$service_name = cleanInput($data['service_name'] ?? '');
$username = cleanInput($data['username'] ?? '');
$password_enc = $data['password_enc'] ?? ''; // This should be encrypted by the extension!
$category = cleanInput($data['category'] ?? 'General');

if (empty($service_name) || empty($password_enc)) {
    echo json_encode(['success' => false, 'message' => 'Service name and password are required']);
    exit;
}

try {
    $sql = "INSERT INTO vault_items (user_id, service_name, username, password_enc, category) 
            VALUES (:user_id, :service_name, :username, :password_enc, :category)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':user_id' => $user_id,
        ':service_name' => $service_name,
        ':username' => $username,
        ':password_enc' => $password_enc,
        ':category' => $category
    ]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Password saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save password']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
