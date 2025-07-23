<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
