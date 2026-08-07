<?php
// Proteksi ketat: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'>
            <h1 style='color:var(--dash-text);'>Akses Ditolak</h1>
            <p style='color:var(--dash-text-muted);'>Anda tidak memiliki izin untuk mengakses halaman administrator.</p>
          </div>";
    exit();
}

require_once 'config/db.php';

// Handle Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_course') {
        $title = $_POST['title'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $thumbnail = $_POST['thumbnail'] ?? null;
        $theme_color = $_POST['theme_color'] ?? '#4361ee';
        
        $stmt = $pdo->prepare("INSERT INTO courses (title, category, description, thumbnail, theme_color, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $category, $description, $thumbnail, $theme_color, $_SESSION['user_id']]);
        header("Location: index.php?page=admin_courses");
        exit();
    } elseif ($action === 'delete_course') {
        $course_id = $_POST['course_id'];
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        header("Location: index.php?page=admin_courses");
        exit();
    }
}

// Ambil semua kursus
$stmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC");
$courses = $stmt->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1; display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
    <!-- Daftar Kursus -->
    <div>
        <div class="section-header" style="margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Manage Courses</h1>
                <p style="color: var(--dash-text-muted);">Kelola kelas, bab materi, dan soal ujian (kuis/challenge).</p>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php if (count($courses) === 0): ?>
                <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 3rem; text-align: center; border-radius: 16px;">
                    <h3 style="color: var(--dash-text-muted);">Belum ada kelas yang dibuat.</h3>
                </div>
            <?php endif; ?>

            <?php foreach ($courses as $c): ?>
            <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem;">
                <div style="display:flex; gap:1.5rem; flex:1;">
                    <?php if (!empty($c['thumbnail'])): ?>
                        <div style="flex-shrink:0;">
                            <img src="<?php echo htmlspecialchars($c['thumbnail']); ?>" alt="Thumbnail" style="width:120px; height:80px; object-fit:cover; border-radius:8px; border:1px solid var(--dash-border);">
                        </div>
                    <?php endif; ?>
                    <div>
                        <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 0.5rem;">
                            <h3 style="margin: 0; color: var(--dash-text);"><?php echo htmlspecialchars($c['title']); ?></h3>
                            <span style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; font-size: 0.75rem; padding: 4px 8px; border-radius: 6px; font-weight: 600;"><?php echo htmlspecialchars($c['category']); ?></span>
                        </div>
                    <p style="color: var(--dash-text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; max-width: 600px;"><?php echo htmlspecialchars($c['description']); ?></p>
                    
                    <div style="display: flex; gap: 12px;">
                        <a href="index.php?page=admin_modules&course_id=<?php echo $c['id']; ?>" style="background: rgba(99, 102, 241, 0.1); color: #6366f1; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.85rem; display: inline-block;">Kelola Bab Materi</a>
                        <a href="index.php?page=admin_exams&course_id=<?php echo $c['id']; ?>" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.85rem; display: inline-block;">Kelola Soal Ujian</a>
                    </div>
                </div>
            </div>
                
            <div style="display:flex; gap: 8px; flex-shrink:0;">
                    <a href="index.php?page=admin_courses_edit&id=<?php echo $c['id']; ?>" style="background: transparent; border: 1px solid #f59e0b; color: #f59e0b; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.85rem; text-decoration: none; transition: all 0.2s;">Edit</a>
                    <form method="POST" action="" style="margin:0;">
                        <input type="hidden" name="course_id" value="<?php echo $c['id']; ?>">
                        <button type="submit" name="action" value="delete_course" onclick="return confirm('Hapus kelas ini beserta semua materi dan soal di dalamnya?');" style="background: transparent; border: 1px solid #ef4444; color: #ef4444; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.85rem; transition: all 0.2s;">Hapus</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Form Tambah Kursus -->
    <div>
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; position: sticky; top: 2rem;">
            <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--dash-text);">Buat Kelas Baru</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_course">
                
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Judul Kelas</label>
                    <input type="text" name="title" required placeholder="Misal: HTML Fundamental" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                </div>
                
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Kategori</label>
                    <select name="category" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                        <option value="SD">SD (Sekolah Dasar)</option>
                        <option value="SMP">SMP (Sekolah Menengah Pertama)</option>
                        <option value="SMA">SMA (Sekolah Menengah Atas)</option>
                        <option value="Umum">Umum</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Deskripsi Singkat</label>
                    <textarea name="description" required rows="4" placeholder="Jelaskan apa yang akan dipelajari di kelas ini..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize: vertical;"></textarea>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">URL Thumbnail (Opsional)</label>
                    <input type="url" name="thumbnail" placeholder="https://contoh.com/gambar.jpg" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Warna Tema (Theme Color)</label>
                    <input type="color" name="theme_color" value="#4361ee" style="width: 100%; height:45px; padding: 0.25rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); cursor:pointer;">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--dash-primary); color: white; border: none; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer;">Simpan Kelas</button>
            </form>
        </div>
    </div>
</div>
