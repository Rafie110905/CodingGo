<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$exam_id = $_GET['id'] ?? null;
if (!$exam_id) {
    echo "Ujian tidak ditemukan.";
    exit();
}

// Ambil info ujian
$stmt = $pdo->prepare("SELECT e.*, c.title as course_title FROM exams e JOIN courses c ON e.course_id = c.id WHERE e.id = ?");
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();
if (!$exam) {
    echo "Ujian tidak ditemukan.";
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = $exam['course_id'];

// Ambil soal-soal
$stmt_q = $pdo->prepare("SELECT * FROM exam_questions WHERE exam_id = ? ORDER BY id ASC");
$stmt_q->execute([$exam_id]);
$questions = $stmt_q->fetchAll();

// Cek apakah user sudah pernah mengerjakan ujian ini
$stmt_res = $pdo->prepare("SELECT * FROM exam_results WHERE user_id = ? AND exam_id = ? ORDER BY attempt_date DESC LIMIT 1");
$stmt_res->execute([$user_id, $exam_id]);
$result = $stmt_res->fetch();

// Hitung total poin maksimal
$max_score = 0;
foreach ($questions as $q) {
    $max_score += $q['points'];
}

$show_result = false;
$score = 0;
$passed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_exam') {
    // Penilaian
    $score = 0;
    foreach ($questions as $q) {
        $q_id = $q['id'];
        $user_answer = $_POST['q_' . $q_id] ?? '';
        if ($user_answer === $q['correct_answer']) {
            $score += $q['points'];
        }
    }
    
    // Normalisasi skor ke skala 100 jika mau, atau biarkan poin absolut
    $passed = ($score >= $exam['min_score_passing']);
    $passed_val = $passed ? 1 : 0;
    
    // Simpan ke exam_results
    $stmt_ins = $pdo->prepare("INSERT INTO exam_results (user_id, exam_id, score, passed) VALUES (?, ?, ?, ?)");
    $stmt_ins->execute([$user_id, $exam_id, $score, $passed_val]);
    
    $new_certificate = null;

    if ($passed) {
        // Cek apakah sudah dapat badge course ini
        // (Sistem Badge Disederhanakan: Berikan badge "Master of {CourseName}")
        $stmt_b = $pdo->prepare("SELECT id FROM badges WHERE name LIKE ?");
        $badge_name = "Master of " . $exam['course_title'];
        $stmt_b->execute([$badge_name]);
        $badge = $stmt_b->fetch();
        
        if (!$badge) {
            // Create badge (kolom harus sesuai skema tabel `badges`)
            $pdo->prepare("INSERT INTO badges (name, description, icon_url, requirement_type, requirement_value) VALUES (?, ?, ?, ?, ?)")
                ->execute([$badge_name, "Lulus ujian " . $exam['title'], null, 'exam', $exam_id]);
            $badge_id = $pdo->lastInsertId();
        } else {
            $badge_id = $badge['id'];
        }
        
        // Cek user sudah punya belum
        $stmt_ub = $pdo->prepare("SELECT * FROM user_badges WHERE user_id = ? AND badge_id = ?");
        $stmt_ub->execute([$user_id, $badge_id]);
        if (!$stmt_ub->fetch()) {
            $pdo->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)")->execute([$user_id, $badge_id]);
            // Update jumlah badge & XP di profil user
            $pdo->prepare("UPDATE users SET total_badges = total_badges + 1, xp_points = xp_points + 50 WHERE id = ?")->execute([$user_id]);
        }

        // === Terbitkan Sertifikat otomatis jika seluruh materi course juga sudah selesai ===
        $stmt_total_mat = $pdo->prepare("SELECT COUNT(*) as total FROM materials WHERE course_id = ?");
        $stmt_total_mat->execute([$course_id]);
        $total_materi = $stmt_total_mat->fetch()['total'];

        $stmt_done_mat = $pdo->prepare("SELECT COUNT(*) as total FROM user_progress up JOIN materials m ON up.material_id = m.id WHERE up.user_id = ? AND m.course_id = ? AND up.status = 'completed'");
        $stmt_done_mat->execute([$user_id, $course_id]);
        $selesai_materi = $stmt_done_mat->fetch()['total'];

        if ($total_materi > 0 && $selesai_materi >= $total_materi) {
            $stmt_cert = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ? AND course_id = ?");
            $stmt_cert->execute([$user_id, $course_id]);
            $existing_cert = $stmt_cert->fetch();

            if (!$existing_cert) {
                $cert_code = 'CGO-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6)) . '-' . $course_id . $user_id;
                $pdo->prepare("INSERT INTO certificates (certificate_code, user_id, course_id) VALUES (?, ?, ?)")
                    ->execute([$cert_code, $user_id, $course_id]);
                $new_certificate = $cert_code;
            } else {
                $new_certificate = $existing_cert['certificate_code'];
            }
        }
    }
    
    $show_result = true;
} elseif ($result) {
    // Tampilkan hasil ujian sebelumnya jika baru saja load halaman (tapi tidak habis post)
    // Bisa di-comment jika ingin siswa langsung bisa retake tanpa lihat hasil lama
}
?>

<div class="container" style="padding: 2rem 0; max-width: 800px;">
    
    <div style="display:flex; align-items:center; gap: 0.5rem; margin-bottom: 2rem;">
        <a href="index.php?page=course_detail&id=<?php echo $course_id; ?>" style="color:var(--text-muted); font-size:0.9rem; text-decoration:none;">&larr; Kembali ke Silabus</a>
    </div>

    <?php if ($show_result): ?>
        <!-- LAYAR HASIL UJIAN -->
        <div style="background: var(--bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 4rem 2rem; text-align: center; margin-bottom: 2rem;">
            
            <?php if ($passed): ?>
                <div style="width: 100px; height: 100px; background: rgba(16, 185, 129, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem auto;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="#10b981" width="50"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h1 style="color: var(--text); font-size: 2.5rem; margin-bottom: 0.5rem;">LULUS! 🎉</h1>
                <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 2rem;">Luar biasa! Anda telah membuktikan pemahaman Anda.</p>
                
                <div style="background: rgba(245, 158, 11, 0.1); border: 1px dashed #f59e0b; display: inline-flex; flex-direction:column; align-items:center; padding: 1.5rem 3rem; border-radius: 16px; margin-bottom: 2rem;">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">🏆</div>
                    <div style="color: #d97706; font-weight: bold; font-size: 1.1rem;">BADGE TERBUKA</div>
                    <div style="color: #92400e;">Master of <?php echo htmlspecialchars($exam['course_title']); ?></div>
                </div>

                <?php if (!empty($new_certificate)): ?>
                <div style="background: rgba(16, 185, 129, 0.1); border: 1px dashed #10b981; display: inline-flex; flex-direction:column; align-items:center; padding: 1.5rem 3rem; border-radius: 16px; margin-bottom: 2rem; margin-left: 1rem;">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">📜</div>
                    <div style="color: #059669; font-weight: bold; font-size: 1.1rem;">SERTIFIKAT TERBIT</div>
                    <div style="color: #047857; font-size: 0.85rem;">Semua materi & ujian kelas ini tuntas!</div>
                    <a href="index.php?page=sertifikat" style="margin-top:0.75rem; color:#059669; font-weight:600; font-size:0.85rem; text-decoration:none;">Lihat Sertifikat &rarr;</a>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div style="width: 100px; height: 100px; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem auto;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="#ef4444" width="50"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h1 style="color: var(--text); font-size: 2.5rem; margin-bottom: 0.5rem;">BELUM LULUS</h1>
                <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 2rem;">Jangan menyerah! Coba pelajari ulang materi dan ulangi ujiannya.</p>
            <?php endif; ?>
            
            <div style="display:flex; justify-content:center; gap: 4rem; margin-bottom: 3rem;">
                <div>
                    <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem;">Skor Anda</div>
                    <div style="font-size: 2.5rem; font-weight: bold; color: <?php echo $passed ? '#10b981' : '#ef4444'; ?>;"><?php echo $score; ?></div>
                </div>
                <div>
                    <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem;">KKM</div>
                    <div style="font-size: 2.5rem; font-weight: bold; color: var(--text);"><?php echo $exam['min_score_passing']; ?></div>
                </div>
            </div>

            <div style="display:flex; justify-content:center; gap: 1rem;">
                <a href="index.php?page=course_detail&id=<?php echo $course_id; ?>" style="background: var(--bg-hover); color: var(--text); border: 1px solid var(--border-color); padding: 1rem 2rem; border-radius: 12px; font-weight: 600; text-decoration: none;">Kembali ke Silabus</a>
                <?php if (!$passed): ?>
                <a href="index.php?page=course_exam&id=<?php echo $exam_id; ?>" style="background: var(--primary); color: white; border: none; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; text-decoration: none;">Ulangi Ujian</a>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <!-- LAYAR SOAL UJIAN -->
        <div style="background: var(--bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 3rem; margin-bottom: 2rem;">
            <div style="text-align:center; margin-bottom: 3rem; border-bottom: 1px solid var(--border-color); padding-bottom: 2rem;">
                <h1 style="font-size: 2rem; color: var(--text); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($exam['title']); ?></h1>
                <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($exam['course_title']); ?></p>
                <div style="display:inline-flex; gap: 2rem;">
                    <div style="background: rgba(99, 102, 241, 0.1); color: #6366f1; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.9rem;">
                        Total Soal: <?php echo count($questions); ?>
                    </div>
                    <div style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.9rem;">
                        KKM: <?php echo $exam['min_score_passing']; ?> Poin
                    </div>
                </div>
            </div>

            <?php if (count($questions) === 0): ?>
                <div style="text-align:center; padding: 3rem; color: var(--text-muted);">Belum ada soal untuk ujian ini.</div>
            <?php else: ?>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="submit_exam">
                    
                    <div style="display:flex; flex-direction:column; gap: 3rem; margin-bottom: 3rem;">
                        <?php foreach ($questions as $index => $q): ?>
                        <div>
                            <div style="display:flex; gap: 1rem; margin-bottom: 1.5rem;">
                                <div style="background: var(--bg-hover); width: 32px; height: 32px; border-radius: 50%; display:flex; align-items:center; justify-content:center; font-weight:bold; color:var(--text); border:1px solid var(--border-color); flex-shrink: 0;">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div>
                                    <h3 style="margin:0 0 0.5rem 0; color:var(--text); font-size:1.1rem; line-height: 1.6;">
                                        <?php echo nl2br(htmlspecialchars($q['question_text'])); ?>
                                    </h3>
                                    <div style="color:var(--text-muted); font-size:0.85rem; font-weight:600;">Bobot: <?php echo $q['points']; ?> Poin</div>
                                </div>
                            </div>
                            
                            <div style="display:grid; grid-template-columns: 1fr; gap: 0.75rem; padding-left: 3rem;">
                                <?php 
                                $opts = ['a' => $q['option_a'], 'b' => $q['option_b'], 'c' => $q['option_c'], 'd' => $q['option_d']];
                                foreach ($opts as $key => $val):
                                ?>
                                <label style="background: var(--bg); border: 1px solid var(--border-color); padding: 1rem 1.5rem; border-radius: 12px; cursor: pointer; display:flex; align-items:center; gap: 1rem; transition: all 0.2s;" class="exam-option">
                                    <input type="radio" name="q_<?php echo $q['id']; ?>" value="<?php echo $key; ?>" required style="width: 18px; height: 18px; cursor: pointer;">
                                    <span style="font-weight:bold; color:var(--text-muted);"><?php echo strtoupper($key); ?>.</span>
                                    <span style="color:var(--text); font-size: 1rem;"><?php echo htmlspecialchars($val); ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <style>
                        .exam-option:hover { background: var(--bg-hover); border-color: var(--primary); }
                        .exam-option:has(input:checked) { background: rgba(59, 130, 246, 0.05); border-color: var(--primary); }
                    </style>

                    <div style="border-top: 1px solid var(--border-color); padding-top: 2rem; text-align:right;">
                        <button type="submit" onclick="return confirm('Sudah yakin dengan jawaban Anda?');" style="background: var(--primary); color: white; border: none; padding: 1rem 3rem; border-radius: 12px; font-weight: 600; font-size: 1.1rem; cursor: pointer; display:inline-flex; align-items:center; gap:8px;">
                            Kumpulkan Ujian
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
