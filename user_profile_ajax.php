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

// Ambil data profil publik beserta efek gamifikasi
$stmt = $pdo->prepare("SELECT u.id, u.name, u.picture, u.profile_title, u.profile_color, u.banner_gif, u.category, 
                               u.xp_points, u.streak_days, u.total_badges, u.created_at,
                               u.custom_status, u.status_emoji,
                               f.value AS avatar_frame_css,
                               n.value AS name_effect_css,
                               p.value AS profile_effect_class,
                               c.value AS card_border_css,
                               bg.value AS card_background_css,
                               ce.value AS cursor_effect_class,
                               be.value AS badge_effect_css,
                               ea.value AS entrance_anim_class
                        FROM users u
                        LEFT JOIN gamification_perks f ON u.avatar_frame_id = f.id
                        LEFT JOIN gamification_perks n ON u.name_effect_id = n.id
                        LEFT JOIN gamification_perks p ON u.profile_effect_id = p.id
                        LEFT JOIN gamification_perks c ON u.card_border_id = c.id
                        LEFT JOIN gamification_perks bg ON u.card_background_id = bg.id
                        LEFT JOIN gamification_perks ce ON u.cursor_effect_id = ce.id
                        LEFT JOIN gamification_perks be ON u.badge_effect_id = be.id
                        LEFT JOIN gamification_perks ea ON u.entrance_anim_id = ea.id
                        WHERE u.id = ?");
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
    'custom_status' => $user['custom_status'],
    'status_emoji' => $user['status_emoji'],
    'avatar_frame_css' => $user['avatar_frame_css'],
    'name_effect_css' => $user['name_effect_css'],
    'profile_effect_class' => $user['profile_effect_class'],
    'card_border_css' => $user['card_border_css'],
    'card_background_css' => $user['card_background_css'],
    'cursor_effect_class' => $user['cursor_effect_class'],
    'badge_effect_css' => $user['badge_effect_css'],
    'entrance_anim_class' => $user['entrance_anim_class'],
    'badges' => $badges
]);