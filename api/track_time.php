<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// Read course_id from JSON payload if present
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$course_id = isset($data['course_id']) ? (int)$data['course_id'] : 0;

try {
    // Cek apakah data untuk hari ini dan course ini sudah ada
    $stmt = $pdo->prepare("SELECT id FROM user_learning_time WHERE user_id = ? AND log_date = ? AND course_id = ?");
    $stmt->execute([$user_id, $today, $course_id]);
    $log = $stmt->fetch();

    if ($log) {
        // Jika sudah ada, tambah 10 detik
        $pdo->prepare("UPDATE user_learning_time SET time_spent = time_spent + 10 WHERE id = ?")->execute([$log['id']]);
    } else {
        // Jika belum ada, buat entri baru untuk hari ini dengan 10 detik
        $pdo->prepare("INSERT INTO user_learning_time (user_id, log_date, course_id, time_spent) VALUES (?, ?, ?, 10)")->execute([$user_id, $today, $course_id]);
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>
