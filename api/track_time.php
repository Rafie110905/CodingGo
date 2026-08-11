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

try {
    // Cek apakah data untuk hari ini sudah ada
    $stmt = $pdo->prepare("SELECT id FROM user_learning_time WHERE user_id = ? AND log_date = ?");
    $stmt->execute([$user_id, $today]);
    $log = $stmt->fetch();

    if ($log) {
        // Jika sudah ada, tambah 1 menit (karena ping dikirim tiap menit)
        $pdo->prepare("UPDATE user_learning_time SET time_spent = time_spent + 1 WHERE id = ?")->execute([$log['id']]);
    } else {
        // Jika belum ada, buat entri baru untuk hari ini dengan 1 menit
        $pdo->prepare("INSERT INTO user_learning_time (user_id, log_date, time_spent) VALUES (?, ?, 1)")->execute([$user_id, $today]);
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>
