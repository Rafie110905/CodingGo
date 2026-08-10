<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';
require_once 'includes/materi_icons.php';

$material_id = $_GET['id'] ?? null;
if (!$material_id) {
    echo "Materi tidak ditemukan.";
    exit();
}

// Ambil info materi
$stmt = $pdo->prepare("SELECT m.*, c.title as course_title FROM materials m JOIN courses c ON m.course_id = c.id WHERE m.id = ?");
$stmt->execute([$material_id]);
$material = $stmt->fetch();
if (!$material) {
    echo "Materi tidak ditemukan.";
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = $material['course_id'];

// Cek progress user untuk materi ini
$stmt_prog = $pdo->prepare("SELECT * FROM user_progress WHERE user_id = ? AND material_id = ?");
$stmt_prog->execute([$user_id, $material_id]);
$progress = $stmt_prog->fetch();

$is_unlocked = false;
$is_completed = false;

if ($progress) {
    $is_unlocked = true;
    if ($progress['status'] === 'completed') {
        $is_completed = true;
    }
} else {
    // Jika tidak ada unlock_keyword, otomatis terbuka
    if (empty($material['unlock_keyword'])) {
        $is_unlocked = true;
        // Tandai started
        $pdo->prepare("INSERT INTO user_progress (user_id, material_id, status) VALUES (?, ?, 'started')")->execute([$user_id, $material_id]);
    }
}

$error_clue = '';

// Handle aksi submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'unlock') {
        $input_clue = trim($_POST['clue'] ?? '');
        if (strtolower($input_clue) === strtolower($material['unlock_keyword'])) {
            // Berhasil unlock!
            $pdo->prepare("INSERT INTO user_progress (user_id, material_id, status) VALUES (?, ?, 'started')")->execute([$user_id, $material_id]);
            // Refresh
            header("Location: index.php?page=course_learn&id=" . $material_id . "&success=unlocked");
            exit();
        } else {
            $error_clue = 'Kata kunci salah. Coba baca lagi materi sebelumnya dengan teliti!';
        }
    } elseif ($action === 'complete') {
        if ($is_unlocked && !$is_completed) {
            $pdo->prepare("UPDATE user_progress SET status = 'completed', completed_at = NOW() WHERE user_id = ? AND material_id = ?")->execute([$user_id, $material_id]);
            
            // Tambahkan XP (sementara XP disimpan di table users - tapi kita blm punya kolom xp di users, asumsikan ada atau kita update badges nanti)
            // Cek apakah ada materi selanjutnya
            $stmt_next = $pdo->prepare("SELECT id FROM materials WHERE course_id = ? AND order_index > ? ORDER BY order_index ASC LIMIT 1");
            $stmt_next->execute([$course_id, $material['order_index']]);
            $next_mat = $stmt_next->fetch();
            
            if ($next_mat) {
                header("Location: index.php?page=course_learn&id=" . $next_mat['id']);
            } else {
                header("Location: index.php?page=course_detail&id=" . $course_id . "&status=all_materials_completed");
            }
            exit();
        }
    }
}

// Ambil semua materi untuk sidebar
$stmt_all = $pdo->prepare("SELECT id, title, unlock_keyword FROM materials WHERE course_id = ? ORDER BY order_index ASC");
$stmt_all->execute([$course_id]);
$all_materials = $stmt_all->fetchAll();
?>

<div class="container" style="padding: 2rem 0;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 2rem;">
        <div>
            <a href="index.php?page=course_detail&id=<?php echo $course_id; ?>" style="color:var(--text-muted); font-size:0.9rem; text-decoration:none;">&larr; Kembali ke Silabus</a>
            <h1 style="font-size: 1.5rem; color: var(--text); margin-top:0.5rem;"><?php echo htmlspecialchars($material['course_title']); ?></h1>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
        
        <!-- Sidebar Daftar Materi -->
        <div style="background: var(--bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem; align-self: start; position: sticky; top: 2rem;">
            <h3 style="font-size: 1rem; color: var(--text); margin-top: 0; margin-bottom: 1rem;">Daftar Bab</h3>
            <div style="display:flex; flex-direction:column; gap:0.5rem;">
                <?php foreach ($all_materials as $index => $am): ?>
                <?php
                    $is_current = ($am['id'] == $material_id);
                    $style = $is_current ? 'background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-left: 3px solid #3b82f6;' : 'color: var(--text-muted);';
                ?>
                <a href="index.php?page=course_learn&id=<?php echo $am['id']; ?>" style="text-decoration:none; padding: 0.6rem 0.75rem; border-radius: 8px; font-size: 0.85rem; font-weight: 500; <?php echo $style; ?> display:flex; align-items:center; gap:10px;">
                    <?php echo renderMateriIcon($am['title'], 28, '8px'); ?>
                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; flex:1;">Bab <?php echo $index + 1; ?>: <?php echo htmlspecialchars($am['title']); ?></span>
                    <?php if (!empty($am['unlock_keyword'])): ?>
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div style="background: var(--bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 3rem; min-height: 500px;">
            <?php if (isset($_GET['success']) && $_GET['success'] === 'unlocked'): ?>
                <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #10b981; text-align:center; font-weight:bold;">
                    🎉 Selamat! Anda berhasil memecahkan teka-teki dan membuka materi ini!
                </div>
            <?php endif; ?>
            
            <?php if (!$is_unlocked): ?>
                <!-- TERKUNCI (CLUE FINDER) -->
                <div style="text-align:center; padding: 4rem 2rem;">
                    <div style="width:80px; height:80px; background:rgba(239, 68, 68, 0.1); color:#ef4444; border-radius:50%; display:flex; align-items:center; justify-content:center; margin: 0 auto 1.5rem auto;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="40"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <h2 style="font-size: 2rem; color: var(--text); margin-bottom: 1rem;">Materi Terkunci</h2>
                    <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 500px; margin: 0 auto 2rem auto; line-height: 1.6;">
                        Materi ini disandikan oleh sistem Gamifikasi. Temukan <b>Kata Kunci Rahasia</b> yang tersembunyi di materi sebelumnya untuk membukanya!
                    </p>
                    
                    <?php if ($error_clue): ?>
                        <div style="color: #ef4444; font-size: 0.95rem; margin-bottom: 1rem; font-weight:600;"><?php echo $error_clue; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" style="max-width: 400px; margin: 0 auto; display:flex; flex-direction:column; gap:1rem;">
                        <input type="hidden" name="action" value="unlock">
                        <input type="text" name="clue" placeholder="Masukkan kata kunci rahasia..." required style="width: 100%; padding: 1rem; border: 2px dashed var(--border-color); border-radius: 12px; background: var(--bg); color: var(--text); font-size: 1.1rem; text-align:center;">
                        <button type="submit" style="background: var(--primary); color: white; border: none; padding: 1rem; border-radius: 12px; font-weight: 600; font-size: 1.1rem; cursor: pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                            Buka Materi
                        </button>
                    </form>
                </div>

            <?php else: ?>
                <!-- KONTEN MATERI -->
                <div style="display:flex; align-items:center; gap:1rem; margin-bottom: 1rem;">
                    <?php echo renderMateriIcon($material['title'], 56, '14px'); ?>
                    <h1 style="font-size: 2.2rem; color: var(--text); margin: 0;"><?php echo htmlspecialchars($material['title']); ?></h1>
                    <?php if ($is_completed): ?>
                        <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 4px 12px; border-radius: 8px; font-weight: bold; font-size: 0.85rem; display:flex; align-items:center; gap:4px;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Selesai
                        </span>
                    <?php endif; ?>
                </div>
                
                <div style="color: var(--primary); font-weight: 600; margin-bottom: 2.5rem;">+<?php echo $material['xp_reward']; ?> XP Reward</div>
                
                <!-- Area Konten -->
                <div class="content-body" style="font-size:1.1rem; line-height:1.8; color:var(--dash-text); margin-bottom:3rem;">
                    <?php if (!empty($material['video_url'])): ?>
                        <?php
                        // Ekstrak YouTube ID
                        $yt_id = '';
                        $url = $material['video_url'];
                        if (strpos($url, 'v=') !== false) {
                            parse_str(parse_url($url, PHP_URL_QUERY), $vars);
                            $yt_id = $vars['v'] ?? '';
                        } elseif (strpos($url, 'youtu.be/') !== false) {
                            $yt_id = basename(parse_url($url, PHP_URL_PATH));
                        } else {
                            $yt_id = $url; // Asumsi input langsung ID
                        }
                        ?>
                        <?php if ($yt_id): ?>
                            <div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden; border-radius:12px; margin-bottom:2rem; box-shadow:0 10px 25px rgba(0,0,0,0.1);">
                                <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($yt_id); ?>" style="position:absolute; top:0; left:0; width:100%; height:100%; border:none;" allowfullscreen></iframe>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!empty($material['content_text'])): ?>
                        <div style="white-space: pre-wrap; font-family: inherit;"><?php echo htmlspecialchars($material['content_text']); ?></div>
                    <?php endif; ?>
                </div>

                <div style="border-top: 1px solid var(--border-color); padding-top: 2rem; display:flex; justify-content:space-between; align-items:center;">
                    <div style="color:var(--text-muted); font-size:0.9rem;">
                        <?php if (!$is_completed): ?>
                            Baca dengan teliti. Mungkin ada <b>Kata Kunci Rahasia</b> untuk materi selanjutnya!
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="complete">
                        <?php if ($is_completed): ?>
                            <button type="button" disabled style="background: var(--bg-hover); color: #10b981; border: 1px solid #10b981; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; font-size: 1rem; cursor: not-allowed; display:flex; align-items:center; gap:8px;">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Materi Telah Diselesaikan
                            </button>
                        <?php else: ?>
                            <button type="submit" style="background: #10b981; color: white; border: none; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; font-size: 1rem; cursor: pointer; display:flex; align-items:center; gap:8px;">
                                Tandai Selesai & Lanjut &rarr;
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
