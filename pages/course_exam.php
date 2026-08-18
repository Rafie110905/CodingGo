<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';
require_once 'includes/materi_icons.php';

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
$review_questions = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_exam') {
    // Penilaian
    $score = 0;
    foreach ($questions as $q) {
        $q_id = $q['id'];
        $user_answer = $_POST['q_' . $q_id] ?? '';
        $is_correct = ($user_answer !== '' && $user_answer === $q['correct_answer']);
        if ($is_correct) {
            $score += $q['points'];
        }

        $review_questions[] = [
            'question_text' => $q['question_text'],
            'selected_answer' => $user_answer,
            'correct_answer' => $q['correct_answer'],
            'points' => $q['points'],
            'is_correct' => $is_correct,
            'option_a' => $q['option_a'],
            'option_b' => $q['option_b'],
            'option_c' => $q['option_c'],
            'option_d' => $q['option_d'],
        ];
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
            
            // Notification: Exam passed and Badge earned
            $notif_title = "Lulus Ujian & Badge Baru!";
            $notif_msg = "Selamat! Anda lulus ujian '" . $exam['title'] . "', mendapatkan +50 XP, dan meraih Badge '" . $badge_name . "'.";
            $notif_link = "index.php?page=my_achievements&tab=xp_history";
            $pdo->prepare("INSERT INTO user_notifications (user_id, type, title, message, link_url) VALUES (?, 'system', ?, ?, ?)")->execute([$user_id, $notif_title, $notif_msg, $notif_link]);
        } else {
            // Give XP anyway for passing, but maybe no badge notification
            $pdo->prepare("UPDATE users SET xp_points = xp_points + 50 WHERE id = ?")->execute([$user_id]);
            
            // Notification: Exam passed
            $notif_title = "Lulus Ujian!";
            $notif_msg = "Anda lulus ujian '" . $exam['title'] . "' dan mendapatkan +50 XP.";
            $notif_link = "index.php?page=my_achievements&tab=xp_history";
            $pdo->prepare("INSERT INTO user_notifications (user_id, type, title, message, link_url) VALUES (?, 'system', ?, ?, ?)")->execute([$user_id, $notif_title, $notif_msg, $notif_link]);
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
                
                // Notification: Certificate generated
                $notif_title_cert = "Sertifikat Kelulusan!";
                $notif_msg_cert = "Sertifikat untuk kursus '" . $exam['course_title'] . "' telah terbit. Cek halaman Sertifikat.";
                $notif_link_cert = "index.php?page=sertifikat";
                $pdo->prepare("INSERT INTO user_notifications (user_id, type, title, message, link_url) VALUES (?, 'system', ?, ?, ?)")->execute([$user_id, $notif_title_cert, $notif_msg_cert, $notif_link_cert]);
                
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

<style>
    .exam-wrap { --accent: #6366f1; }
    .exam-topbar {
        position: sticky; top: 0; z-index: 20; backdrop-filter: blur(8px);
        background: color-mix(in srgb, var(--bg) 88%, transparent);
        border-bottom: 1px solid var(--border-color);
        padding: 0.9rem 0; margin-bottom: 2rem;
    }
    .exam-topbar-inner { display:flex; align-items:center; gap:1rem; }
    .exam-progress-track { flex:1; height: 10px; background: var(--bg-hover); border-radius: 99px; overflow:hidden; border: 1px solid var(--border-color); }
    .exam-progress-fill { height:100%; width:0%; border-radius:99px; background: linear-gradient(90deg,#6366f1,#22c55e); transition: width .35s ease; }
    .exam-progress-label { font-size:0.82rem; font-weight:700; color:var(--text-muted); white-space:nowrap; }

    .exam-hero {
        background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(34,197,94,0.06));
        border: 1px solid var(--border-color); border-radius: 22px; padding: 2.4rem;
        margin-bottom: 2rem; display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;
    }
    .exam-hero h1 { font-size: 1.75rem; color: var(--text); margin: 0 0 .35rem 0; }
    .exam-hero p { color: var(--text-muted); margin:0; }
    .exam-chip { display:inline-flex; align-items:center; gap:6px; padding: 7px 14px; border-radius: 99px; font-weight:700; font-size:0.82rem; }

    .qnav { display:flex; flex-wrap:wrap; gap:8px; margin-bottom: 2rem; }
    .qnav-dot {
        width: 34px; height: 34px; border-radius: 10px; border:1px solid var(--border-color);
        background: var(--bg); color: var(--text-muted); font-size:0.8rem; font-weight:700;
        display:flex; align-items:center; justify-content:center; cursor:pointer; transition: all .2s;
    }
    .qnav-dot:hover { border-color: var(--primary); color: var(--primary); transform: translateY(-2px); }
    .qnav-dot.answered { background: rgba(34,197,94,0.12); border-color:#22c55e; color:#16a34a; }

    .q-card {
        background: var(--bg); border: 1px solid var(--border-color); border-radius: 18px;
        padding: 1.75rem; margin-bottom: 1.5rem; scroll-margin-top: 90px;
        border-left: 4px solid var(--accent-color, #6366f1);
        transition: box-shadow .2s ease;
    }
    .q-card:hover { box-shadow: 0 8px 22px rgba(0,0,0,0.05); }
    .q-num-badge {
        width: 34px; height: 34px; border-radius: 10px; flex-shrink:0;
        background: var(--accent-color, #6366f1); color:#fff; font-weight:800;
        display:flex; align-items:center; justify-content:center; font-size:0.95rem;
    }
    .exam-option {
        background: var(--bg); border: 1px solid var(--border-color); padding: 0.9rem 1.25rem;
        border-radius: 12px; cursor: pointer; display:flex; align-items:center; gap: .9rem;
        transition: all 0.15s ease; position: relative;
    }
    .exam-option:hover { background: var(--bg-hover); border-color: var(--primary); transform: translateX(2px); }
    .exam-option:has(input:checked) { background: rgba(99,102,241,0.08); border-color: var(--primary); box-shadow: 0 0 0 1px var(--primary) inset; }
    .exam-option input { position:absolute; opacity:0; width:0; height:0; }
    .opt-letter {
        width: 28px; height: 28px; border-radius: 50%; flex-shrink:0; display:flex; align-items:center; justify-content:center;
        background: var(--bg-hover); color: var(--text-muted); font-weight:700; font-size:0.8rem; border:1px solid var(--border-color);
        transition: all .15s ease;
    }
    .exam-option:has(input:checked) .opt-letter { background: var(--primary); color:#fff; border-color: var(--primary); }
    .opt-check { margin-left:auto; color: var(--primary); display:none; flex-shrink:0; }
    .exam-option:has(input:checked) .opt-check { display:flex; }

    .submit-btn {
        background: linear-gradient(135deg,#6366f1,#4f46e5); color: white; border: none;
        padding: 1.05rem 2.6rem; border-radius: 14px; font-weight: 700; font-size: 1.05rem;
        cursor: pointer; display:inline-flex; align-items:center; gap:10px;
        box-shadow: 0 10px 25px rgba(99,102,241,0.35); transition: transform .15s ease;
    }
    .submit-btn:hover { transform: translateY(-2px); }

    .score-ring {
        width: 150px; height: 150px; border-radius: 50%; display:flex; align-items:center; justify-content:center;
        margin: 0 auto 1.5rem auto; position:relative;
    }
    .score-ring-inner {
        width: 120px; height: 120px; border-radius:50%; background: var(--bg);
        display:flex; flex-direction:column; align-items:center; justify-content:center;
    }
    @keyframes popIn { 0% { transform: scale(.7); opacity:0; } 100% { transform: scale(1); opacity:1; } }
    .pop-in { animation: popIn .45s cubic-bezier(.34,1.56,.64,1); }

    @media (max-width: 640px) {
        .exam-hero { padding: 1.5rem; }
        .q-card { padding: 1.25rem; }
        .exam-hero h1 { font-size: 1.35rem; }
    }
</style>

<div class="container exam-wrap" style="padding: 0 0 2rem 0; max-width: 800px;">

    <?php if ($show_result): ?>
        <div style="padding-top:2rem; display:flex; align-items:center; gap: 0.5rem; margin-bottom: 1.5rem;">
            <a href="index.php?page=course_detail&id=<?php echo $course_id; ?>" style="color:var(--text-muted); font-size:0.9rem; text-decoration:none;">&larr; Kembali ke Silabus</a>
        </div>

        <!-- LAYAR HASIL UJIAN -->
        <div class="pop-in" style="background: var(--bg); border: 1px solid var(--border-color); border-radius: 24px; padding: 3.5rem 2rem; text-align: center; margin-bottom: 2rem;">

            <?php
                $pct = $max_score > 0 ? round(($score / $max_score) * 100) : 0;
                $ring_color = $passed ? '#10b981' : '#ef4444';
            ?>
            <div class="score-ring" style="background: conic-gradient(<?php echo $ring_color; ?> <?php echo $pct; ?>%, var(--bg-hover) 0);">
                <div class="score-ring-inner">
                    <div style="font-size:1.9rem; font-weight:800; color:<?php echo $ring_color; ?>;"><?php echo $pct; ?>%</div>
                    <div style="font-size:0.7rem; color:var(--text-muted); font-weight:600;">SKOR</div>
                </div>
            </div>

            <?php if ($passed): ?>
                <h1 style="color: var(--text); font-size: 2.2rem; margin-bottom: 0.5rem;">LULUS! 🎉</h1>
                <p style="color: var(--text-muted); font-size: 1.05rem; margin-bottom: 2rem;">Luar biasa! Anda telah membuktikan pemahaman Anda.</p>

                <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:1rem; margin-bottom: 1rem;">
                    <div style="background: rgba(245, 158, 11, 0.1); border: 1px dashed #f59e0b; display: inline-flex; flex-direction:column; align-items:center; padding: 1.25rem 2.5rem; border-radius: 16px;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.4rem;">🏆</div>
                        <div style="color: #d97706; font-weight: bold; font-size: 1rem;">BADGE TERBUKA</div>
                        <div style="color: #92400e; font-size:0.9rem;">Master of <?php echo htmlspecialchars($exam['course_title']); ?></div>
                    </div>

                    <?php if (!empty($new_certificate)): ?>
                    <div style="background: rgba(16, 185, 129, 0.1); border: 1px dashed #10b981; display: inline-flex; flex-direction:column; align-items:center; padding: 1.25rem 2.5rem; border-radius: 16px;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.4rem;">📜</div>
                        <div style="color: #059669; font-weight: bold; font-size: 1rem;">SERTIFIKAT TERBIT</div>
                        <div style="color: #047857; font-size: 0.82rem;">Semua materi & ujian kelas ini tuntas!</div>
                        <a href="index.php?page=sertifikat" style="margin-top:0.6rem; color:#059669; font-weight:600; font-size:0.82rem; text-decoration:none;">Lihat Sertifikat &rarr;</a>
                    </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <h1 style="color: var(--text); font-size: 2.2rem; margin-bottom: 0.5rem;">BELUM LULUS</h1>
                <p style="color: var(--text-muted); font-size: 1.05rem; margin-bottom: 1.5rem;">Jangan menyerah! Coba pelajari ulang materi dan ulangi ujiannya. 💪</p>
            <?php endif; ?>

            <div style="display:flex; justify-content:center; gap: 3rem; margin: 2rem 0;">
                <div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.4rem;">Skor Anda</div>
                    <div style="font-size: 2.1rem; font-weight: bold; color: <?php echo $passed ? '#10b981' : '#ef4444'; ?>;"><?php echo $score; ?> <span style="font-size:1rem; color:var(--text-muted); font-weight:600;">/ <?php echo $max_score; ?></span></div>
                </div>
                <div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.4rem;">KKM</div>
                    <div style="font-size: 2.1rem; font-weight: bold; color: var(--text);"><?php echo $exam['min_score_passing']; ?></div>
                </div>
            </div>

            <div style="margin: 2rem 0 2.5rem; text-align:left;">
                <h2 style="font-size: 1.5rem; color: var(--text); margin-bottom: 1rem;">Detail Jawaban Soal</h2>
                <?php foreach ($review_questions as $index => $review): ?>
                    <?php
                        $selected_label = strtoupper($review['selected_answer'] ?? '');
                        $correct_label = strtoupper($review['correct_answer'] ?? '');
                        $option_map = ['a' => $review['option_a'], 'b' => $review['option_b'], 'c' => $review['option_c'], 'd' => $review['option_d']];
                        $selected_text = $option_map[$review['selected_answer']] ?? 'Tidak dijawab';
                        $correct_text = $option_map[$review['correct_answer']] ?? 'Belum ada kunci';
                    ?>
                    <div style="background: var(--bg); border: 1px solid var(--border-color); border-radius: 14px; padding: 1rem 1.1rem; margin-bottom: 0.9rem;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:0.75rem; flex-wrap:wrap; margin-bottom: 0.65rem;">
                            <div style="font-weight: 700; color: var(--text);">Soal <?php echo $index + 1; ?></div>
                            <span style="padding: 0.3rem 0.7rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; background: <?php echo $review['is_correct'] ? 'rgba(16,185,129,0.12)' : 'rgba(239,68,68,0.12)'; ?>; color: <?php echo $review['is_correct'] ? '#10b981' : '#ef4444'; ?>;">
                                <?php echo $review['is_correct'] ? 'BENAR' : 'SALAH'; ?>
                            </span>
                        </div>
                        <p style="margin: 0 0 0.75rem; color: var(--text); line-height: 1.5; font-size: 0.96rem;">
                            <?php echo nl2br(htmlspecialchars($review['question_text'])); ?>
                        </p>
                        <div style="display:grid; gap: 0.55rem; font-size: 0.9rem;">
                            <div style="color: var(--text-muted);">
                                <strong style="color: var(--text);">Jawaban Anda:</strong>
                                <span style="color: <?php echo $review['is_correct'] ? '#10b981' : '#ef4444'; ?>; font-weight:600;">
                                    <?php echo htmlspecialchars($selected_label ? $selected_label . '. ' . $selected_text : 'Tidak dijawab'); ?>
                                </span>
                            </div>
                            <div style="color: var(--text-muted);">
                                <strong style="color: var(--text);">Jawaban Benar:</strong>
                                <span style="color: #10b981; font-weight:600;">
                                    <?php echo htmlspecialchars($correct_label . '. ' . $correct_text); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="display:flex; justify-content:center; gap: 1rem; flex-wrap:wrap;">
                <a href="index.php?page=course_detail&id=<?php echo $course_id; ?>" style="background: var(--bg-hover); color: var(--text); border: 1px solid var(--border-color); padding: 1rem 2rem; border-radius: 12px; font-weight: 600; text-decoration: none;">Kembali ke Silabus</a>
                <?php if (!$passed): ?>
                <a href="index.php?page=course_exam&id=<?php echo $exam_id; ?>" style="background: var(--primary); color: white; border: none; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; text-decoration: none;">Ulangi Ujian</a>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>

        <?php if (count($questions) > 0): ?>
        <div class="exam-topbar">
            <div class="container exam-topbar-inner" style="max-width: 800px; padding: 0;">
                <a href="index.php?page=course_detail&id=<?php echo $course_id; ?>" style="color:var(--text-muted); flex-shrink:0;" title="Kembali ke Silabus">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div class="exam-progress-track"><div class="exam-progress-fill" id="examProgressBar"></div></div>
                <span class="exam-progress-label" id="examProgressLabel">0 / <?php echo count($questions); ?> soal terjawab</span>
            </div>
        </div>
        <?php else: ?>
        <div style="padding-top:2rem; display:flex; align-items:center; gap: 0.5rem; margin-bottom: 1.5rem;">
            <a href="index.php?page=course_detail&id=<?php echo $course_id; ?>" style="color:var(--text-muted); font-size:0.9rem; text-decoration:none;">&larr; Kembali ke Silabus</a>
        </div>
        <?php endif; ?>

        <!-- LAYAR SOAL UJIAN -->
        <div class="exam-hero">
            <?php echo renderMateriIcon($exam['course_title'], 64, '16px'); ?>
            <div style="flex:1; min-width:200px;">
                <h1><?php echo htmlspecialchars($exam['title']); ?></h1>
                <p><?php echo htmlspecialchars($exam['course_title']); ?></p>
            </div>
            <div style="display:flex; gap: 0.75rem; flex-wrap:wrap;">
                <div class="exam-chip" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <?php echo count($questions); ?> Soal
                </div>
                <div class="exam-chip" style="background: rgba(34, 197, 94, 0.12); color: #16a34a;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    KKM <?php echo $exam['min_score_passing']; ?>
                </div>
            </div>
        </div>

        <?php if (count($questions) === 0): ?>
            <div style="text-align:center; padding: 3rem; color: var(--text-muted); background: var(--bg); border: 1px solid var(--border-color); border-radius: 16px;">Belum ada soal untuk ujian ini.</div>
        <?php else: ?>

            <!-- Navigasi Cepat Soal -->
            <div class="qnav">
                <?php foreach ($questions as $index => $q): ?>
                    <div class="qnav-dot" data-qid="<?php echo $q['id']; ?>" data-index="<?php echo $index; ?>" title="Soal <?php echo $index + 1; ?>"><?php echo $index + 1; ?></div>
                <?php endforeach; ?>
            </div>

            <form method="POST" action="" id="examForm">
                <input type="hidden" name="action" value="submit_exam">

                <?php
                $palette = ['#6366f1', '#ec4899', '#22c55e', '#f59e0b', '#0ea5e9', '#8b5cf6'];
                foreach ($questions as $index => $q):
                    $accent = $palette[$index % count($palette)];
                ?>
                <div class="q-card" id="qblock-<?php echo $index; ?>" style="--accent-color: <?php echo $accent; ?>;">
                    <div style="display:flex; gap: 1rem; margin-bottom: 1.25rem;">
                        <div class="q-num-badge" style="--accent-color: <?php echo $accent; ?>; background: <?php echo $accent; ?>;"><?php echo $index + 1; ?></div>
                        <div style="flex:1;">
                            <h3 style="margin:0 0 0.4rem 0; color:var(--text); font-size:1.08rem; line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($q['question_text'])); ?>
                            </h3>
                            <div style="color:var(--text-muted); font-size:0.8rem; font-weight:700;">⭐ Bobot: <?php echo $q['points']; ?> Poin</div>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr; gap: 0.65rem; padding-left: 3rem;">
                        <?php
                        $opts = ['a' => $q['option_a'], 'b' => $q['option_b'], 'c' => $q['option_c'], 'd' => $q['option_d']];
                        foreach ($opts as $key => $val):
                        ?>
                        <label class="exam-option">
                            <input type="radio" name="q_<?php echo $q['id']; ?>" value="<?php echo $key; ?>" required>
                            <span class="opt-letter"><?php echo strtoupper($key); ?></span>
                            <span style="color:var(--text); font-size: 0.98rem;"><?php echo htmlspecialchars($val); ?></span>
                            <span class="opt-check">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                            </span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div style="border-top: 1px solid var(--border-color); padding-top: 2rem; text-align:right;">
                    <button type="submit" class="submit-btn" onclick="return confirm('Sudah yakin dengan jawaban Anda?');">
                        Kumpulkan Ujian
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                    </button>
                </div>
            </form>

            <script>
            (function () {
                const totalQuestions = <?php echo count($questions); ?>;
                const form = document.getElementById('examForm');
                if (!form) return;
                const dots = document.querySelectorAll('.qnav-dot');
                const progressBar = document.getElementById('examProgressBar');
                const progressLabel = document.getElementById('examProgressLabel');

                function countAnswered() {
                    const groups = {};
                    form.querySelectorAll('input[type=radio]:checked').forEach(r => { groups[r.name] = true; });
                    return Object.keys(groups).length;
                }

                function refresh() {
                    const answered = countAnswered();
                    const pct = totalQuestions ? Math.round((answered / totalQuestions) * 100) : 0;
                    if (progressBar) progressBar.style.width = pct + '%';
                    if (progressLabel) progressLabel.textContent = answered + ' / ' + totalQuestions + ' soal terjawab';
                    dots.forEach(dot => {
                        const qId = dot.getAttribute('data-qid');
                        const checked = form.querySelector('input[name="q_' + qId + '"]:checked');
                        dot.classList.toggle('answered', !!checked);
                    });
                }

                form.addEventListener('change', refresh);
                dots.forEach(dot => {
                    dot.addEventListener('click', () => {
                        const target = document.getElementById('qblock-' + dot.getAttribute('data-index'));
                        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                });

                refresh();
            })();
            </script>
        <?php endif; ?>
    <?php endif; ?>
</div>
