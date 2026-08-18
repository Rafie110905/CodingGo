<?php
// Proteksi ketat: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'><h1 style='color:var(--dash-text);'>Akses Ditolak</h1></div>";
    exit();
}

require_once 'config/db.php';

$exam_id = $_GET['exam_id'] ?? null;
if (!$exam_id) {
    header("Location: index.php?page=admin_courses");
    exit();
}

// Ambil info exam & course
$stmt = $pdo->prepare("SELECT e.*, c.title as course_title, c.id as course_id FROM exams e JOIN courses c ON e.course_id = c.id WHERE e.id = ?");
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();
if (!$exam) {
    echo "Ujian tidak ditemukan.";
    exit();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_question') {
        $question_type = $_POST['question_type'];
        $question_text = $_POST['question_text'];
        $points = $_POST['points'] ?: 10;
        
        $option_a = $_POST['option_a'] ?? null;
        $option_b = $_POST['option_b'] ?? null;
        $option_c = $_POST['option_c'] ?? null;
        $option_d = $_POST['option_d'] ?? null;
        $correct_answer = $_POST['correct_answer'];
        
        $stmt_ins = $pdo->prepare("INSERT INTO exam_questions (exam_id, question_type, question_text, option_a, option_b, option_c, option_d, correct_answer, points) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_ins->execute([$exam_id, $question_type, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $points]);

        $stmt_total = $pdo->prepare("SELECT COALESCE(SUM(points), 0) AS total_points FROM exam_questions WHERE exam_id = ?");
        $stmt_total->execute([$exam_id]);
        $total_points = (int)$stmt_total->fetchColumn();
        $target_pass = $total_points >= 70 ? 70 : $total_points;
        $pdo->prepare("UPDATE exams SET min_score_passing = ? WHERE id = ?")
            ->execute([$target_pass, $exam_id]);

        header("Location: index.php?page=admin_questions&exam_id=" . $exam_id);
        exit();
    } elseif ($action === 'delete_question') {
        $question_id = $_POST['question_id'];
        $stmt_del = $pdo->prepare("DELETE FROM exam_questions WHERE id = ? AND exam_id = ?");
        $stmt_del->execute([$question_id, $exam_id]);

        $stmt_total = $pdo->prepare("SELECT COALESCE(SUM(points), 0) AS total_points FROM exam_questions WHERE exam_id = ?");
        $stmt_total->execute([$exam_id]);
        $total_points = (int)$stmt_total->fetchColumn();
        $target_pass = $total_points >= 70 ? 70 : $total_points;
        $pdo->prepare("UPDATE exams SET min_score_passing = ? WHERE id = ?")
            ->execute([$target_pass, $exam_id]);

        header("Location: index.php?page=admin_questions&exam_id=" . $exam_id);
        exit();
    }
}

// Ambil semua soal
$stmt_q = $pdo->prepare("SELECT * FROM exam_questions WHERE exam_id = ? ORDER BY id ASC");
$stmt_q->execute([$exam_id]);
$questions = $stmt_q->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <div style="display:flex; align-items:center; gap: 0.5rem; margin-bottom:0.5rem;">
                <a href="index.php?page=admin_exams&course_id=<?php echo $exam['course_id']; ?>" style="color:var(--dash-text-muted); font-size:0.9rem;">&larr; Kembali ke Ujian</a>
            </div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.25rem;">Kelola Soal: <?php echo htmlspecialchars($exam['title']); ?></h1>
            <p style="color: var(--dash-primary); font-weight:600; font-size:1.1rem;"><?php echo htmlspecialchars($exam['course_title']); ?></p>
        </div>
    </div>

    <div class="dash-grid-fixed-right" style="display: grid;  gap: 2rem;">
        <!-- Daftar Soal -->
        <div>
            <?php if (count($questions) === 0): ?>
                <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 3rem; text-align: center; border-radius: 16px;">
                    <h3 style="color: var(--dash-text-muted);">Belum ada soal di ujian ini.</h3>
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:1.5rem;">
                    <?php foreach ($questions as $index => $q): ?>
                    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 12px; padding: 1.5rem; display:flex; justify-content:space-between; align-items:flex-start;">
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1rem;">
                                <div style="background:var(--dash-bg); width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; color:var(--dash-text-muted); font-size:0.85rem; border:1px solid var(--dash-border);">
                                    <?php echo $index + 1; ?>
                                </div>
                                <span style="background:rgba(99, 102, 241, 0.1); color:#6366f1; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:bold;">
                                    <?php echo $q['points']; ?> POIN
                                </span>
                            </div>
                            
                            <div style="margin-bottom:1.5rem; color:var(--dash-text); font-size:1rem; line-height:1.6;">
                                <?php echo nl2br(htmlspecialchars($q['question_text'])); ?>
                            </div>
                            
                            <?php if ($q['question_type'] === 'multiple_choice'): ?>
                            <div class="dash-grid-2" style="display:grid;  gap:0.75rem; margin-bottom:1rem;">
                                <?php 
                                $opts = ['a' => $q['option_a'], 'b' => $q['option_b'], 'c' => $q['option_c'], 'd' => $q['option_d']];
                                foreach ($opts as $key => $val):
                                    $is_correct = ($key === $q['correct_answer']);
                                    $bg = $is_correct ? 'rgba(34, 197, 94, 0.1)' : 'var(--dash-bg)';
                                    $border = $is_correct ? '1px solid #22c55e' : '1px solid var(--dash-border)';
                                ?>
                                <div style="background:<?php echo $bg; ?>; border:<?php echo $border; ?>; padding:0.75rem; border-radius:8px; display:flex; align-items:flex-start; gap:0.5rem;">
                                    <span style="font-weight:bold; color:var(--dash-text-muted);"><?php echo strtoupper($key); ?>.</span>
                                    <span style="color:var(--dash-text);"><?php echo htmlspecialchars($val); ?></span>
                                    <?php if($is_correct): ?>
                                    <svg fill="none" viewBox="0 0 24 24" stroke="#22c55e" width="18" style="margin-left:auto;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <form method="POST" action="" style="margin-left:1rem;">
                            <input type="hidden" name="question_id" value="<?php echo $q['id']; ?>">
                            <button type="submit" name="action" value="delete_question" onclick="return confirm('Hapus soal ini?');" style="background:transparent; border:none; color:#ef4444; cursor:pointer;" title="Hapus">
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
                <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--dash-text);">Buat Soal Baru</h3>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_question">
                    <input type="hidden" name="question_type" value="multiple_choice"> <!-- Default multiple choice for now -->
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Pertanyaan</label>
                        <textarea name="question_text" required rows="4" placeholder="Tulis soal di sini..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize: vertical;"></textarea>
                    </div>
                    
                    <div style="margin-bottom: 0.75rem;">
                        <label style="display: block; margin-bottom: 0.25rem; font-size: 0.8rem; color: var(--dash-text-muted);">Pilihan A</label>
                        <input type="text" name="option_a" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--dash-border); border-radius: 6px; background: var(--dash-bg); color: var(--dash-text);">
                    </div>
                    <div style="margin-bottom: 0.75rem;">
                        <label style="display: block; margin-bottom: 0.25rem; font-size: 0.8rem; color: var(--dash-text-muted);">Pilihan B</label>
                        <input type="text" name="option_b" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--dash-border); border-radius: 6px; background: var(--dash-bg); color: var(--dash-text);">
                    </div>
                    <div style="margin-bottom: 0.75rem;">
                        <label style="display: block; margin-bottom: 0.25rem; font-size: 0.8rem; color: var(--dash-text-muted);">Pilihan C</label>
                        <input type="text" name="option_c" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--dash-border); border-radius: 6px; background: var(--dash-bg); color: var(--dash-text);">
                    </div>
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.25rem; font-size: 0.8rem; color: var(--dash-text-muted);">Pilihan D</label>
                        <input type="text" name="option_d" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--dash-border); border-radius: 6px; background: var(--dash-bg); color: var(--dash-text);">
                    </div>
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Kunci Jawaban</label>
                        <select name="correct_answer" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                            <option value="a">A</option>
                            <option value="b">B</option>
                            <option value="c">C</option>
                            <option value="d">D</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Bobot Poin</label>
                        <input type="number" name="points" value="10" min="1" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    </div>
                    
                    <button type="submit" style="width: 100%; background: #6366f1; color: white; border: none; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer;">Simpan Soal</button>
                </form>
            </div>
        </div>
    </div>
</div>
