<?php
// Proteksi ketat: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'>
            <h1 style='color:var(--dash-text);'>Akses Ditolak</h1>
          </div>";
    exit();
}

require_once 'config/db.php';

$detail_user_id = $_GET['id'] ?? null;
if (!$detail_user_id) {
    echo "Pengguna tidak ditemukan.";
    exit();
}

// Ambil info user
$stmt_u = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt_u->execute([$detail_user_id]);
$usr = $stmt_u->fetch();

if (!$usr) {
    echo "Pengguna tidak ditemukan.";
    exit();
}

// Handle RBAC Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_rbac') {
    if (isset($_POST['use_auto']) && $_POST['use_auto'] === '1') {
        $new_allowed = null;
    } else {
        $allowed = $_POST['allowed_cats'] ?? [];
        $new_allowed = empty($allowed) ? null : implode(',', $allowed);
    }
    
    $stmt_upd = $pdo->prepare("UPDATE users SET allowed_categories = ? WHERE id = ?");
    $stmt_upd->execute([$new_allowed, $detail_user_id]);
    
    // Refresh data
    $stmt_u->execute([$detail_user_id]);
    $usr = $stmt_u->fetch();
}

$user_age = calculateAge($usr['birth_date'] ?? '');
$current_allowed_db = $usr['allowed_categories'];
$is_auto = empty($current_allowed_db);
$current_allowed_arr = $is_auto ? [] : array_map('trim', explode(',', $current_allowed_db));

// Hitung Kelas Aktif
$stmt_ac = $pdo->prepare("SELECT COUNT(DISTINCT m.course_id) as total FROM user_progress up JOIN materials m ON up.material_id = m.id WHERE up.user_id = ?");
$stmt_ac->execute([$detail_user_id]);
$active_courses = $stmt_ac->fetch()['total'] ?? 0;

// Hitung Materi Selesai
$stmt_mc = $pdo->prepare("SELECT COUNT(*) as total FROM user_progress WHERE user_id = ? AND status = 'completed'");
$stmt_mc->execute([$detail_user_id]);
$completed_materials = $stmt_mc->fetch()['total'] ?? 0;

// Ambil History Ujian
$stmt_er = $pdo->prepare("SELECT er.*, e.title as exam_title, c.title as course_title 
                          FROM exam_results er 
                          JOIN exams e ON er.exam_id = e.id 
                          JOIN courses c ON e.course_id = c.id 
                          WHERE er.user_id = ? 
                          ORDER BY er.attempt_date DESC");
$stmt_er->execute([$detail_user_id]);
$exam_history = $stmt_er->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div style="display:flex; align-items:center; gap: 0.5rem; margin-bottom: 2rem;">
        <a href="index.php?page=admin_users" style="color:var(--dash-text-muted); font-size:0.9rem; text-decoration:none;">&larr; Kembali ke Manage Users</a>
    </div>

    <div class="section-header" style="margin-bottom: 2rem;">
        <div style="display:flex; align-items:center; gap:1.5rem;">
            <?php if (!empty($usr['picture'])): ?>
                <img src="<?php echo htmlspecialchars($usr['picture']); ?>" alt="Profile" style="width: 80px; height: 80px; border-radius: 50%; border:3px solid var(--dash-border);">
            <?php else: ?>
                <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--dash-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; border:3px solid var(--dash-border);">
                    <?php echo substr(htmlspecialchars($usr['name']), 0, 1); ?>
                </div>
            <?php endif; ?>
            
            <div>
                <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.25rem;"><?php echo htmlspecialchars($usr['name']); ?></h1>
                <p style="color: var(--dash-text-muted); margin:0;">
                    ID: <?php echo $usr['id']; ?> | <?php echo htmlspecialchars($usr['email']); ?><br>
                    Umur: <b><?php echo $usr['birth_date'] ? $user_age . ' Tahun' : 'Tidak disetel'; ?></b>
                </p>
            </div>
        </div>
    </div>
    
    <!-- RBAC Section -->
    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem; color: var(--dash-text);">Hak Akses Kategori Kelas (RBAC)</h3>
        <p style="color: var(--dash-text-muted); font-size:0.95rem; margin-bottom: 1rem;">
            Secara bawaan, hak akses kelas diatur otomatis berdasarkan umur (SD: <13, SMP: 13-15, SMA: 16-18, Umum: >18). Admin dapat mengubah hak akses pengguna secara manual di bawah ini.
        </p>
        
        <form method="POST">
            <input type="hidden" name="action" value="update_rbac">
            <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; font-weight: 600; color: var(--dash-text);">
                <input type="checkbox" name="use_auto" value="1" <?php echo $is_auto ? 'checked' : ''; ?> onchange="document.getElementById('manual-cats').style.display = this.checked ? 'none' : 'block';">
                Gunakan Akses Otomatis (Berdasarkan Umur)
            </label>
            
            <div id="manual-cats" style="display: <?php echo $is_auto ? 'none' : 'block'; ?>; margin-bottom: 1.5rem; background: rgba(0,0,0,0.02); padding: 1rem; border-radius: 8px; border: 1px solid var(--dash-border);">
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    <?php 
                    $cats = ['SD', 'SMP', 'SMA', 'Umum'];
                    foreach($cats as $c): 
                        $checked = in_array($c, $current_allowed_arr) ? 'checked' : '';
                    ?>
                    <label style="display: flex; align-items: center; gap: 0.25rem; color: var(--dash-text);">
                        <input type="checkbox" name="allowed_cats[]" value="<?php echo $c; ?>" <?php echo $checked; ?>> <?php echo $c; ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <button type="submit" style="background: var(--dash-primary); color: white; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; cursor: pointer;">Simpan Hak Akses</button>
        </form>
    </div>

    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 3rem;">
        <div class="stat-card">
            <div class="stat-val"><?php echo $active_courses; ?></div>
            <div class="stat-label">Kelas Berjalan</div>
        </div>
        <div class="stat-card">
            <div class="stat-val"><?php echo $completed_materials; ?></div>
            <div class="stat-label">Materi Selesai</div>
        </div>
        <div class="stat-card" style="border-top:4px solid var(--dash-warning);">
            <div class="stat-val"><?php echo number_format($usr['xp_points']); ?></div>
            <div class="stat-label">XP Points</div>
        </div>
        <div class="stat-card" style="border-top:4px solid #f59e0b;">
            <div class="stat-val"><?php echo number_format($usr['total_badges']); ?></div>
            <div class="stat-label">Total Badges</div>
        </div>
    </div>

    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2>Riwayat Ujian & Evaluasi</h2>
    </div>
    
    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: rgba(0,0,0,0.02); border-bottom: 1px solid var(--dash-border);">
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase;">Ujian</th>
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase;">Kelas</th>
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase;">Skor</th>
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase;">Status</th>
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase;">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($exam_history) === 0): ?>
                    <tr><td colspan="5" style="padding: 2rem; text-align:center; color:var(--dash-text-muted);">Belum ada riwayat ujian.</td></tr>
                    <?php else: ?>
                        <?php foreach ($exam_history as $eh): ?>
                        <tr style="border-bottom: 1px solid var(--dash-border);">
                            <td style="padding: 1rem 1.5rem; font-weight: 600; color: var(--dash-text);"><?php echo htmlspecialchars($eh['exam_title']); ?></td>
                            <td style="padding: 1rem 1.5rem; color: var(--dash-text-muted);"><?php echo htmlspecialchars($eh['course_title']); ?></td>
                            <td style="padding: 1rem 1.5rem; font-weight: bold; color: <?php echo $eh['passed'] ? '#10b981' : '#ef4444'; ?>;"><?php echo $eh['score']; ?></td>
                            <td style="padding: 1rem 1.5rem;">
                                <?php if ($eh['passed']): ?>
                                    <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Lulus</span>
                                <?php else: ?>
                                    <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Gagal</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem 1.5rem; color: var(--dash-text-muted); font-size: 0.9rem;">
                                <?php echo date('d M Y, H:i', strtotime($eh['attempt_date'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
