<?php
// Proteksi ketat: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'><h1 style='color:var(--dash-text);'>Akses Ditolak</h1></div>";
    exit();
}

require_once 'config/db.php';

$course_id = $_GET['id'] ?? null;
if (!$course_id) {
    header("Location: index.php?page=admin_courses");
    exit();
}

// Ambil data kelas
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    echo "Kelas tidak ditemukan.";
    exit();
}

// Handle Form Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $thumbnail = $_POST['thumbnail'] ?? null;
    $theme_color = $_POST['theme_color'] ?? '#4361ee';
    
    $stmt = $pdo->prepare("UPDATE courses SET title=?, category=?, description=?, thumbnail=?, theme_color=? WHERE id=?");
    $stmt->execute([$title, $category, $description, $thumbnail, $theme_color, $course_id]);
    
    header("Location: index.php?page=admin_courses");
    exit();
}
?>

<div class="dash-left" style="grid-column: 1 / -1; max-width: 800px; margin: 0 auto;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <div style="display:flex; align-items:center; gap: 0.5rem; margin-bottom:0.5rem;">
                <a href="index.php?page=admin_courses" style="color:var(--dash-text-muted); font-size:0.9rem;">&larr; Kembali ke Daftar Kelas</a>
            </div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.25rem;">Edit Kelas</h1>
        </div>
    </div>

    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem;">
        <form method="POST" action="">
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Judul Kelas</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($course['title']); ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Kategori</label>
                <select name="category" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    <option value="SD" <?php echo $course['category'] === 'SD' ? 'selected' : ''; ?>>SD (Sekolah Dasar)</option>
                    <option value="SMP" <?php echo $course['category'] === 'SMP' ? 'selected' : ''; ?>>SMP (Sekolah Menengah Pertama)</option>
                    <option value="SMA" <?php echo $course['category'] === 'SMA' ? 'selected' : ''; ?>>SMA (Sekolah Menengah Atas)</option>
                    <option value="Umum" <?php echo $course['category'] === 'Umum' ? 'selected' : ''; ?>>Umum</option>
                </select>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Deskripsi Singkat</label>
                <textarea name="description" required rows="5" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($course['description']); ?></textarea>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">URL Thumbnail (Opsional)</label>
                <input type="url" name="thumbnail" value="<?php echo htmlspecialchars($course['thumbnail'] ?? ''); ?>" placeholder="https://contoh.com/gambar.jpg" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Warna Tema (Theme Color)</label>
                <input type="color" name="theme_color" value="<?php echo htmlspecialchars($course['theme_color'] ?? '#4361ee'); ?>" style="width: 100%; height:45px; padding: 0.25rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); cursor:pointer;">
            </div>
            
            <div style="margin-bottom: 2rem;">
                <?php if (!empty($course['thumbnail'])): ?>
                    <div style="margin-top: 1rem; border: 1px solid var(--dash-border); border-radius: 8px; padding: 0.5rem; display: inline-block;">
                        <img src="<?php echo htmlspecialchars($course['thumbnail'] ?? ''); ?>" alt="Thumbnail" style="max-width: 200px; max-height: 120px; border-radius: 4px; object-fit: cover;">
                    </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" style="width: 100%; background: #f59e0b; color: white; border: none; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer;">Simpan Perubahan</button>
        </form>
    </div>
</div>
