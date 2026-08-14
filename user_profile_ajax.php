<?php
// Endpoint AJAX: mengembalikan data profil publik + badge seorang user dalam format JSON.
// Dipanggil lewat fetch('user_profile_ajax.php?user_id=...') saat avatar/nama user diklik.

session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

// Hanya boleh diakses oleh user yang sudah login
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Silakan login terlebih dahulu.']);
    exit();
}

$user_id = $_GET['user_id'] ?? null;
if (!$user_id || !ctype_digit((string)$user_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID user tidak valid.']);
    exit();
}

// Ambil data profil publik (jangan pernah kembalikan kolom password/email di endpoint publik ini)
$stmt = $pdo->prepare("SELECT id, name, picture, profile_title, profile_color, banner_gif, category, 
                               xp_points, streak_days, total_badges, created_at 
                        FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'User tidak ditemukan.']);
    exit();
}

// Ambil semua badge yang sudah didapat user ini
$stmt_b = $pdo->prepare("SELECT b.id, b.name, b.description, b.icon_url, ub.earned_at 
                          FROM user_badges ub 
                          JOIN badges b ON ub.badge_id = b.id 
                          WHERE ub.user_id = ? 
                          ORDER BY ub.earned_at DESC");
$stmt_b->execute([$user_id]);
$badges = $stmt_b->fetchAll();

echo json_encode([
    'id' => $user['id'],
    'name' => $user['name'],
    'picture' => $user['picture'],
    'profile_title' => $user['profile_title'],
    'profile_color' => $user['profile_color'],
    'banner_gif' => $user['banner_gif'],
    'category' => $user['category'],
    'xp_points' => (int)$user['xp_points'],
    'streak_days' => (int)$user['streak_days'],
    'total_badges' => (int)$user['total_badges'],
    'joined_at' => $user['created_at'],
    'badges' => $badges,
]);