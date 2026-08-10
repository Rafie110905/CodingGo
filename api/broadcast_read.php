<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$broadcast_id = $data['broadcast_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$broadcast_id) {
    echo json_encode(['success' => false, 'error' => 'Missing broadcast_id']);
    exit();
}

try {
    // Cek apakah sudah pernah dilihat
    $stmt = $pdo->prepare("SELECT 1 FROM broadcast_views WHERE broadcast_id = ? AND user_id = ?");
    $stmt->execute([$broadcast_id, $user_id]);
    
    if (!$stmt->fetch()) {
        // Insert record
        $stmt_insert = $pdo->prepare("INSERT INTO broadcast_views (broadcast_id, user_id) VALUES (?, ?)");
        $stmt_insert->execute([$broadcast_id, $user_id]);
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>
