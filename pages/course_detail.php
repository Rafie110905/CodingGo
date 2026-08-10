<?php
require_once 'config/db.php';
require_once 'includes/materi_icons.php';

$course_id = $_GET['id'] ?? null;
if (!$course_id) {
    echo "<div class='container' style='padding:4rem 0; text-align:center;'><h3>Kelas tidak ditemukan.</h3></div>";
    exit();
}

// Ambil info kelas
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();
if (!$course) {
    echo "<div class='container' style='padding:4rem 0; text-align:center;'><h3>Kelas tidak ditemukan.</h3></div>";
    exit();
}

// Ambil jumlah bab materi
$stmt_mat = $pdo->prepare("SELECT COUNT(*) as total, SUM(xp_reward) as total_xp FROM materials WHERE course_id = ?");
$stmt_mat->execute([$course_id]);
$mat_info = $stmt_mat->fetch();

// Ambil bab materi
$stmt_m = $pdo->prepare("SELECT * FROM materials WHERE course_id = ? ORDER BY order_index ASC");
$stmt_m->execute([$course_id]);
$materials = $stmt_m->fetchAll();

// Ambil info ujian
$stmt_ex = $pdo->prepare("SELECT * FROM exams WHERE course_id = ? ORDER BY id ASC");
$stmt_ex->execute([$course_id]);
$exams = $stmt_ex->fetchAll();

// Jika user sudah login, cek progress dan akses RBAC
$is_logged_in = isset($_SESSION['user_id']);
$completed_materials = [];
$has_access = false;
if ($is_logged_in) {
    $stmt_prog = $pdo->prepare("SELECT material_id FROM user_progress WHERE user_id = ? AND status = 'completed'");
    $stmt_prog->execute([$_SESSION['user_id']]);
    $completed_materials = $stmt_prog->fetchAll(PDO::FETCH_COLUMN);
    
    // Cek Akses
    $stmt_u = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt_u->execute([$_SESSION['user_id']]);
    $current_user = $stmt_u->fetch();
    $has_access = hasCategoryAccess($current_user, $course['category']);
} else {
    // Pengunjung publik boleh melihat halaman detail, tapi tombolnya akan diarahkan ke login
    $has_access = true;
}
?>

<?php
    $theme_color = $course['theme_color'] ?? '#3b82f6';
?>
<div class="container" style="padding: 2rem 0;">
    <div style="background: var(--bg); border: 1px solid <?php echo $theme_color; ?>40; border-radius: 20px; padding: 3rem; margin-bottom: 2rem; position: relative; overflow: hidden; box-shadow: 0 10px 30px <?php echo $theme_color; ?>15;">
        <!-- Dekorasi Background Dinamis berdasarkan Tema -->
        <div style="position: absolute; top: -50px; right: -50px; width: 250px; height: 250px; background: <?php echo $theme_color; ?>30; border-radius: 50%; filter: blur(50px);"></div>
        <div style="position: absolute; bottom: -50px; left: -50px; width: 200px; height: 200px; background: <?php echo $theme_color; ?>20; border-radius: 50%; filter: blur(40px);"></div>
        
        <div style="position: relative; z-index: 1; display:flex; flex-wrap:wrap; gap:2rem; align-items:center;">
            
            <div style="flex:1; min-width:300px;">
                <div style="display:flex; align-items:center; gap: 0.5rem; margin-bottom: 1.5rem;">
                <a href="index.php" style="color:var(--text-muted); font-size:0.9rem; text-decoration:none;">&larr; Kembali ke Beranda</a>
            </div>
            
            <span style="background: <?php echo $theme_color; ?>20; color: <?php echo $theme_color; ?>; padding: 6px 14px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; margin-bottom: 1rem; display: inline-block;">
                <?php echo htmlspecialchars($course['category']); ?>
            </span>
            <h1 style="font-size: 2.5rem; color: var(--text); margin-bottom: 1rem; line-height: 1.2;"><?php echo htmlspecialchars($course['title']); ?></h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 600px; margin-bottom: 2rem; line-height: 1.6;">
                <?php echo nl2br(htmlspecialchars($course['description'])); ?>
            </p>
            
            <div style="display: flex; gap: 1.5rem; margin-bottom: 2.5rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted);">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    <span><?php echo $mat_info['total']; ?> Bab Materi</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted);">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                    <span><?php echo count($exams); ?> Ujian Akhir</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #10b981; font-weight:600;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    <span>Total <?php echo $mat_info['total_xp'] ?? 0; ?> XP</span>
                </div>
            </div>
            
            <?php if ($is_logged_in): ?>
                <?php if (!$has_access): ?>
                    <button disabled style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px dashed #ef4444; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; font-size: 1.1rem; cursor: not-allowed;">Akses Terkunci (Batas Umur)</button>
                <?php elseif (count($materials) > 0): ?>
                    <a href="index.php?page=course_learn&id=<?php echo $materials[0]['id']; ?>" style="background: <?php echo $theme_color; ?>; color: white; border: none; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; font-size: 1.1rem; text-decoration: none; display: inline-block; box-shadow: 0 4px 15px <?php echo $theme_color; ?>60; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                        Mulai Petualangan Belajar &rarr;
                    </a>
                <?php else: ?>
                    <button disabled style="background: var(--bg-hover); color: var(--text-muted); border: 1px solid var(--border-color); padding: 1rem 2rem; border-radius: 12px; font-weight: 600; font-size: 1.1rem; cursor: not-allowed;">Materi Belum Tersedia</button>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" style="background: <?php echo $theme_color; ?>; color: white; border: none; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; font-size: 1.1rem; text-decoration: none; display: inline-block; box-shadow: 0 4px 15px <?php echo $theme_color; ?>60;">
                    Masuk untuk Mulai Belajar
                </a>
            <?php endif; ?>
            
            </div>
            
            <?php if(!empty($course['thumbnail'])): ?>
            <div style="flex-shrink:0; text-align:center;">
                <img src="<?php echo htmlspecialchars($course['thumbnail']); ?>" alt="Course Thumbnail" style="width: 100%; max-width: 400px; height: auto; border-radius: 16px; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1); border: 1px solid <?php echo $theme_color; ?>30; object-fit:cover; transform: perspective(1000px) rotateY(-5deg); transition: transform 0.3s;" onmouseover="this.style.transform='perspective(1000px) rotateY(0deg)'" onmouseout="this.style.transform='perspective(1000px) rotateY(-5deg)'">
            </div>
            <?php endif; ?>

        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Silabus -->
        <div>
            <h2 style="font-size: 1.5rem; color: var(--text); margin-bottom: 1.5rem;">Silabus Kelas (Materi Hunting)</h2>
            <div style="display:flex; flex-direction:column; gap:1rem;">
                <?php foreach ($materials as $index => $m): ?>
                <?php 
                    $is_completed = in_array($m['id'], $completed_materials);
                    // Materi bisa diakses jika tidak ada password, ATAU jika dia sudah selesai.
                    // Di UI silabus, kita beri tanda gembok jika dia punya unlock_keyword
                    $has_password = !empty($m['unlock_keyword']);
                ?>
                <div class="materi-card" style="background: var(--bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; display:flex; align-items:center; gap:1rem; <?php echo $is_completed ? 'border-color: #10b981;' : ''; ?>">
                    <div style="position:relative; flex-shrink:0;">
                        <?php if (!empty($m['thumbnail'])): ?>
                            <div style="width: 48px; height: 48px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color);">
                                <img src="<?php echo htmlspecialchars($m['thumbnail']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <?php echo renderMateriIcon($m['title'], 48); ?>
                        <?php endif; ?>
                        <?php if ($is_completed): ?>
                            <div style="position:absolute; bottom:-4px; right:-4px; width:20px; height:20px; background:#10b981; border-radius:50%; border:2px solid var(--bg); display:flex; align-items:center; justify-content:center;">
                                <svg fill="none" viewBox="0 0 24 24" stroke="white" width="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="flex:1;">
                        <h3 style="margin:0; color:var(--text); font-size:1.1rem; display:flex; align-items:center; gap:0.5rem;">
                            <?php echo htmlspecialchars($m['title']); ?>
                            <?php if ($has_password && !$is_completed): ?>
                                <svg fill="none" viewBox="0 0 24 24" stroke="#ef4444" width="16" title="Terkunci - Butuh Kata Kunci"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            <?php endif; ?>
                        </h3>
                        <div style="color:var(--text-muted); font-size:0.85rem; margin-top:0.25rem; display:flex; gap:1rem;">
                            <span>Tipe: <?php echo ucfirst($m['content_type']); ?></span>
                            <span style="color:#10b981; font-weight:600;">+<?php echo $m['xp_reward']; ?> XP</span>
                        </div>
                    </div>
                    <div>
                        <?php if ($has_access): ?>
                        <a href="index.php?page=course_learn&id=<?php echo $m['id']; ?>" style="color: var(--primary); font-weight:600; text-decoration:none; font-size:0.9rem;">
                            <?php echo $is_completed ? 'Pelajari Ulang' : 'Masuk Bab'; ?> &rarr;
                        </a>
                        <?php else: ?>
                        <span style="color: var(--text-muted); font-size:0.9rem;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" style="vertical-align:middle; margin-top:-2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg> Terkunci
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (count($exams) > 0): ?>
                <h3 style="font-size: 1.2rem; color: var(--text); margin-top: 1rem; margin-bottom: 0.5rem;">Ujian Akhir (Boss Fight)</h3>
                <?php foreach ($exams as $e): ?>
                <div style="background: linear-gradient(to right, rgba(239, 68, 68, 0.05), rgba(239, 68, 68, 0.01)); border: 1px dashed #ef4444; border-radius: 12px; padding: 1.5rem; display:flex; align-items:center; gap:1rem;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); color: #ef4444; display:flex; align-items:center; justify-content:center;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                    </div>
                    <div style="flex:1;">
                        <h3 style="margin:0; color:var(--text); font-size:1.1rem;"><?php echo htmlspecialchars($e['title']); ?></h3>
                        <div style="color:var(--text-muted); font-size:0.85rem; margin-top:0.25rem;">
                            KKM Kelulusan: <?php echo $e['min_score_passing']; ?> Poin
                        </div>
                    </div>
                    <div>
                        <?php if ($has_access): ?>
                        <a href="index.php?page=course_exam&id=<?php echo $e['id']; ?>" style="color: #ef4444; font-weight:600; text-decoration:none; font-size:0.9rem;">
                            Ikuti Ujian &rarr;
                        </a>
                        <?php else: ?>
                        <span style="color: var(--text-muted); font-size:0.9rem;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" style="vertical-align:middle; margin-top:-2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg> Terkunci
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar / Info Gamifikasi -->
        <div>
            <div style="background: var(--bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; position: sticky; top: 2rem;">
                <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--text); display:flex; align-items:center; gap:8px;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="#f59e0b" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    Materi Hunting
                </h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1rem;">
                    Kelas ini menggunakan sistem <b>Gamifikasi</b>. Beberapa materi akan terkunci. Anda harus membaca materi sebelumnya dengan seksama untuk menemukan <b>Kata Kunci Rahasia</b> agar bisa membuka materi selanjutnya.
                </p>
                <div style="background: rgba(245, 158, 11, 0.1); padding: 1rem; border-radius: 8px; border: 1px dashed #f59e0b;">
                    <div style="font-weight: 600; color: #d97706; margin-bottom: 0.25rem; font-size: 0.9rem;">Hadiah Akhir:</div>
                    <div style="color: #92400e; font-size: 0.85rem;">Selesaikan semua materi dan ujian untuk mendapatkan Badge Eksklusif di profilmu!</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .materi-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .materi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
</style>
