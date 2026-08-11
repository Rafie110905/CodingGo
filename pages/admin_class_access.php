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
    $user_id = $_POST['user_id'] ?? 0;
    
    if ($action === 'update_rbac') {
        if (isset($_POST['use_auto']) && $_POST['use_auto'] === '1') {
            $new_allowed = null;
        } else {
            $allowed = $_POST['allowed_cats'] ?? [];
            $new_allowed = empty($allowed) ? null : implode(',', $allowed);
        }
        
        $stmt_upd = $pdo->prepare("UPDATE users SET allowed_categories = ? WHERE id = ?");
        $stmt_upd->execute([$new_allowed, $user_id]);
    }
    
    // Refresh halaman untuk melihat perubahan
    header("Location: index.php?page=admin_class_access");
    exit();
}

// Ambil data semua pengguna
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$all_users = $stmt->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Manage Class Access</h1>
            <p style="color: var(--dash-text-muted);">Kelola batas umur dan hak akses setiap siswa terhadap kategori kelas secara massal.</p>
        </div>
    </div>

    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: rgba(0,0,0,0.02); border-bottom: 1px solid var(--dash-border);">
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase;">User & Umur</th>
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase;">Hak Akses Aktif</th>
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase; text-align: right;">Edit Hak Akses Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_users as $u): ?>
                    <?php 
                        $user_age = calculateAge($u['birth_date'] ?? '');
                        $current_allowed_db = $u['allowed_categories'];
                        $is_auto = empty($current_allowed_db);
                        $current_allowed_arr = $is_auto ? [] : array_map('trim', explode(',', $current_allowed_db));
                        
                        // Menentukan hak akses yang aktif (baik manual maupun otomatis)
                        $active_access = getUserAllowedCategories($u);
                    ?>
                    <tr style="border-bottom: 1px solid var(--dash-border);">
                        <td style="padding: 1rem 1.5rem;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <?php if (!empty($u['picture'])): ?>
                                    <img src="<?php echo htmlspecialchars($u['picture']); ?>" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%;">
                                <?php else: ?>
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--dash-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                        <?php echo substr(htmlspecialchars($u['name']), 0, 1); ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div style="font-weight: 600; color: var(--dash-text);"><?php echo htmlspecialchars($u['name']); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--dash-text-muted);">
                                        Umur: <?php echo $u['birth_date'] ? $user_age . ' Tahun' : 'Belum disetel'; ?> 
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 1rem 1.5rem;">
                            <div style="margin-bottom: 0.35rem;">
                                <?php if ($is_auto): ?>
                                    <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">Otomatis (Umur)</span>
                                <?php else: ?>
                                    <span style="background: rgba(245, 158, 11, 0.1); color: #d97706; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">Manual Override</span>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--dash-text); font-weight: 600;">
                                <?php echo implode(', ', $active_access); ?>
                            </div>
                        </td>
                        <td style="padding: 1rem 1.5rem; text-align: right;">
                            <form method="POST" action="" style="display:flex; flex-direction:column; gap:0.5rem; align-items:flex-end; margin:0;">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <input type="hidden" name="action" value="update_rbac">
                                
                                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: flex-end; align-items:center;">
                                    <label style="font-size:0.8rem; display:flex; align-items:center; gap:0.25rem; color:var(--dash-text); cursor:pointer;">
                                        <input type="checkbox" name="use_auto" value="1" <?php echo $is_auto ? 'checked' : ''; ?> title="Gunakan pengaturan umur"> Auto
                                    </label>
                                    <div style="width:1px; height:14px; background:var(--dash-border);"></div>
                                    <?php 
                                    $cats = ['SD', 'SMP', 'SMA', 'Umum'];
                                    foreach($cats as $c): 
                                        $checked = in_array($c, $current_allowed_arr) ? 'checked' : '';
                                    ?>
                                    <label style="font-size:0.8rem; display:flex; align-items:center; gap:0.25rem; color:var(--dash-text); cursor:pointer;">
                                        <input type="checkbox" name="allowed_cats[]" value="<?php echo $c; ?>" <?php echo $checked; ?>> <?php echo $c; ?>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                                <button type="submit" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: none; padding: 4px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.75rem; margin-top:4px;">Simpan</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
