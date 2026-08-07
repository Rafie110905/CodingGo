<?php
// Proteksi ketat: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'><h1 style='color:var(--dash-text);'>Akses Ditolak</h1></div>";
    exit();
}

require_once 'config/db.php';

$module_id = $_GET['id'] ?? null;
if (!$module_id) {
    header("Location: index.php?page=admin_courses");
    exit();
}

// Ambil info materi
$stmt = $pdo->prepare("SELECT * FROM materials WHERE id = ?");
$stmt->execute([$module_id]);
$module = $stmt->fetch();
if (!$module) {
    echo "Materi tidak ditemukan.";
    exit();
}

// Handle Form Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content_type = $_POST['content_type'];
    $content_text = $_POST['content_text'];
    $video_url = $_POST['video_url'] ?? null;
    $xp_reward = $_POST['xp_reward'] ?: 50;
    $unlock_keyword = !empty($_POST['unlock_keyword']) ? trim($_POST['unlock_keyword']) : null;
    $thumbnail = $_POST['thumbnail'] ?? null;
    
    $stmt_upd = $pdo->prepare("UPDATE materials SET title = ?, content_type = ?, content_text = ?, video_url = ?, xp_reward = ?, unlock_keyword = ?, thumbnail = ? WHERE id = ?");
    $stmt_upd->execute([$title, $content_type, $content_text, $video_url, $xp_reward, $unlock_keyword, $thumbnail, $module_id]);
    
    header("Location: index.php?page=admin_modules&course_id=" . $module['course_id']);
    exit();
}
?>

<div class="dash-left" style="grid-column: 1 / -1; max-width: 800px; margin: 0 auto;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <div style="display:flex; align-items:center; gap: 0.5rem; margin-bottom:0.5rem;">
                <a href="index.php?page=admin_modules&course_id=<?php echo $module['course_id']; ?>" style="color:var(--dash-text-muted); font-size:0.9rem;">&larr; Kembali ke Daftar Materi</a>
            </div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.25rem;">Edit Bab Materi</h1>
        </div>
    </div>

    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem;">
        <form method="POST" action="">
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Judul Bab</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($module['title']); ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">URL Thumbnail (Opsional)</label>
                <input type="url" name="thumbnail" value="<?php echo htmlspecialchars($module['thumbnail'] ?? ''); ?>" placeholder="Misal: https://contoh.com/gambar.jpg" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                <?php if (!empty($module['thumbnail'])): ?>
                    <img src="<?php echo htmlspecialchars($module['thumbnail']); ?>" style="max-height: 80px; margin-top: 10px; border-radius:8px;">
                <?php endif; ?>
            </div>
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Tipe Konten</label>
                <select name="content_type" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    <option value="mixed" <?php echo $module['content_type'] === 'mixed' ? 'selected' : ''; ?>>Teks & Video (Kombinasi)</option>
                    <option value="text" <?php echo $module['content_type'] === 'text' ? 'selected' : ''; ?>>Teks / Artikel / Kode</option>
                    <option value="video" <?php echo $module['content_type'] === 'video' ? 'selected' : ''; ?>>Video URL (YouTube)</option>
                </select>
            </div>
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">URL Video YouTube (Opsional)</label>
                <input type="text" name="video_url" value="<?php echo htmlspecialchars($module['video_url'] ?? ''); ?>" placeholder="Masukkan ID atau URL Video (contoh: dQw4w9WgXcQ)" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Isi Materi Teks</label>
                <textarea name="content_text" rows="8" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($module['content_text'] ?? ''); ?></textarea>
            </div>
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">XP Reward</label>
                <input type="number" name="xp_reward" value="<?php echo htmlspecialchars($module['xp_reward']); ?>" min="0" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 2rem; padding: 1rem; background: rgba(59, 130, 246, 0.05); border: 1px dashed var(--dash-primary); border-radius: 8px;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-primary);">Materi Hunting (Kata Kunci)</label>
                <p style="font-size:0.75rem; color:var(--dash-text-muted); margin-bottom:0.75rem;">Kosongkan jika materi ini bebas diakses. Jika diisi, siswa harus menemukan kata kunci ini di bab sebelumnya.</p>
                <input type="text" name="unlock_keyword" value="<?php echo htmlspecialchars($module['unlock_keyword'] ?? ''); ?>" placeholder="Contoh: variabel" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-primary); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
            </div>
            
            <button type="submit" style="width: 100%; background: #f59e0b; color: white; border: none; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer;">Simpan Perubahan Bab</button>
        </form>
    </div>
</div>
