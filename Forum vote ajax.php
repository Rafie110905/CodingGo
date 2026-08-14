<?php
// Endpoint AJAX: menangani upvote (like) dan downvote (dislike) untuk post & balasan forum komunitas.
// Dipanggil lewat fetch dari voteOnTarget() di includes/footer_dash.php.

session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Silakan login terlebih dahulu.']);
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$target_type = $_POST['target_type'] ?? '';
$target_id = $_POST['target_id'] ?? '';
$vote_type = $_POST['vote_type'] ?? '';

if (!in_array($target_type, ['post', 'reply'], true) || !ctype_digit((string)$target_id) || !in_array($vote_type, ['up', 'down'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Permintaan tidak valid.']);
    exit();
}

$target_id = (int)$target_id;
$table = $target_type === 'post' ? 'forum_posts' : 'forum_replies';

// Pastikan target-nya memang ada
$stmt_check = $pdo->prepare("SELECT id FROM {$table} WHERE id = ?");
$stmt_check->execute([$target_id]);
if (!$stmt_check->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'Konten tidak ditemukan.']);
    exit();
}

// Cek apakah user ini sudah pernah vote target ini
$stmt_existing = $pdo->prepare("SELECT id, vote_type FROM forum_votes WHERE target_type = ? AND target_id = ? AND user_id = ?");
$stmt_existing->execute([$target_type, $target_id, $user_id]);
$existing = $stmt_existing->fetch();

if (!$existing) {
    // Belum pernah vote -> tambah vote baru
    $pdo->prepare("INSERT INTO forum_votes (target_type, target_id, user_id, vote_type) VALUES (?, ?, ?, ?)")
        ->execute([$target_type, $target_id, $user_id, $vote_type]);
    $column = $vote_type === 'up' ? 'upvotes' : 'downvotes';
    $pdo->prepare("UPDATE {$table} SET {$column} = {$column} + 1 WHERE id = ?")->execute([$target_id]);
    $user_vote = $vote_type;

} elseif ($existing['vote_type'] === $vote_type) {
    // Klik tombol yang sama lagi -> batalkan vote (toggle off)
    $pdo->prepare("DELETE FROM forum_votes WHERE id = ?")->execute([$existing['id']]);
    $column = $vote_type === 'up' ? 'upvotes' : 'downvotes';
    $pdo->prepare("UPDATE {$table} SET {$column} = GREATEST(0, {$column} - 1) WHERE id = ?")->execute([$target_id]);
    $user_vote = null;

} else {
    // Ganti pilihan (misal sebelumnya like, sekarang dislike)
    $pdo->prepare("UPDATE forum_votes SET vote_type = ? WHERE id = ?")->execute([$vote_type, $existing['id']]);
    $old_column = $existing['vote_type'] === 'up' ? 'upvotes' : 'downvotes';
    $new_column = $vote_type === 'up' ? 'upvotes' : 'downvotes';
    $pdo->prepare("UPDATE {$table} SET {$old_column} = GREATEST(0, {$old_column} - 1), {$new_column} = {$new_column} + 1 WHERE id = ?")->execute([$target_id]);
    $user_vote = $vote_type;
}

// Ambil angka terbaru untuk dikembalikan ke frontend
$has_downvotes_col = $target_type === 'post' || true; // forum_replies sekarang juga punya downvotes setelah migrasi
$stmt_final = $pdo->prepare("SELECT upvotes, downvotes FROM {$table} WHERE id = ?");
$stmt_final->execute([$target_id]);
$final = $stmt_final->fetch();

echo json_encode([
    'upvotes' => (int)($final['upvotes'] ?? 0),
    'downvotes' => (int)($final['downvotes'] ?? 0),
    'user_vote' => $user_vote,
]);