<?php
// Proteksi ketat: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'><h1 style='color:var(--dash-text);'>Akses Ditolak</h1></div>";
    exit();
}

require_once 'config/db.php';

$course_id = $_GET['course_id'] ?? null;
if (!$course_id) {
    header("Location: index.php?page=admin_courses");
    exit();
}

// Ambil info course
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();
if (!$course) {
    echo "Kelas tidak ditemukan.";
    exit();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_exam') {
        $title = $_POST['title'];
        $type = $_POST['type'];
        $min_score_passing = $_POST['min_score_passing'] ?: 70;
        
        $stmt_ins = $pdo->prepare("INSERT INTO exams (course_id, title, type, min_score_passing) VALUES (?, ?, ?, ?)");
        $stmt_ins->execute([$course_id, $title, $type, $min_score_passing]);
        header("Location: index.php?page=admin_exams&course_id=" . $course_id);
        exit();
    } elseif ($action === 'delete_exam') {
        $exam_id = $_POST['exam_id'];
        $stmt_del = $pdo->prepare("DELETE FROM exams WHERE id = ? AND course_id = ?");
        $stmt_del->execute([$exam_id, $course_id]);
        header("Location: index.php?page=admin_exams&course_id=" . $course_id);
        exit();
    }
}

// Ambil semua ujian
$stmt_ex = $pdo->prepare("SELECT * FROM exams WHERE course_id = ? ORDER BY id ASC");
$stmt_ex->execute([$course_id]);
$exams = $stmt_ex->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <div style="display:flex; align-items:center; gap: 0.5rem; margin-bottom:0.5rem;">
                <a href="index.php?page=admin_courses" style="color:var(--dash-text-muted); font-size:0.9rem;">&larr; Kembali ke Kelas</a>
            </div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.25rem;">Kelola Ujian / Kuis</h1>
            <p style="color: #22c55e; font-weight:600; font-size:1.1rem;"><?php echo htmlspecialchars($course['title']); ?></p>
        </div>
    </div>

    <div class="dash-grid-fixed-right" style="display: grid;  gap: 2rem;">
        <!-- Daftar Ujian -->
        <div>
            <?php if (count($exams) === 0): ?>
                <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 3rem; text-align: center; border-radius: 16px;">
                    <h3 style="color: var(--dash-text-muted);">Belum ada ujian di kelas ini.</h3>
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:1rem;">
                    <?php foreach ($exams as $e): ?>
                    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 12px; padding: 1.5rem; display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.5rem;">
                                <h3 style="margin:0; color:var(--dash-text); font-size:1.2rem;"><?php echo htmlspecialchars($e['title']); ?></h3>
                                <span style="background:rgba(34, 197, 94, 0.1); color:#22c55e; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:bold;">
                                    <?php echo strtoupper($e['type']); ?>
                                </span>
                            </div>
                            
                            <div style="color:var(--dash-text-muted); font-size:0.85rem; margin-bottom:1rem;">
                                KKM (Nilai Kelulusan): <span style="font-weight:600; color:var(--dash-text);"><?php echo $e['min_score_passing']; ?> Poin</span>
                            </div>

                            <a href="index.php?page=admin_questions&exam_id=<?php echo $e['id']; ?>" style="background: var(--dash-primary); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; display:inline-block;">Tambah/Edit Soal</a>
                        </div>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="exam_id" value="<?php echo $e['id']; ?>">
                            <button type="submit" name="action" value="delete_exam" onclick="return confirm('Hapus ujian ini beserta semua soal di dalamnya?');" style="background:transparent; border:none; color:#ef4444; cursor:pointer;" title="Hapus">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Form Tambah -->
        <div>
            <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; position: sticky; top: 2rem;">
                <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--dash-text);">Buat Ujian Baru</h3>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_exam">
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Judul Ujian</label>
                        <input type="text" name="title" required placeholder="Misal: Kuis Dasar HTML" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    </div>
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Tipe Evaluasi</label>
                        <select name="type" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                            <option value="quiz">Kuis Pilihan Ganda</option>
                            <option value="challenge">Tantangan (Challenge)</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Nilai Minimum (KKM)</label>
                        <p style="font-size:0.75rem; color:var(--dash-text-muted); margin-bottom:0.75rem;">Siswa harus mencapai poin ini untuk mendapatkan status Lulus & menerima Badge.</p>
                        <input type="number" name="min_score_passing" value="70" min="0" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    </div>
                    
                    <button type="submit" style="width: 100%; background: #22c55e; color: white; border: none; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer;">Simpan Ujian</button>
                </form>
            </div>
        </div>
    </div>
</div>
