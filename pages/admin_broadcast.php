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

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $title = trim($_POST['title']);
        $message = trim($_POST['message']);
        $type = $_POST['type'];
        $display_mode = $_POST['display_mode'] ?? 'once';
        $stmt = $pdo->prepare("INSERT INTO broadcasts (title, message, type, display_mode, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$title, $message, $type, $display_mode]);
        
        // Notify all students
        $notif_title = "Pesan Broadcast: " . $title;
        $notif_msg = "Terdapat pesan pengumuman baru dari Admin.";
        $notif_link = "index.php?page=dashboard";
        $stmt_notif = $pdo->prepare("INSERT INTO user_notifications (user_id, type, title, message, link_url) SELECT id, 'system', ?, ?, ? FROM users WHERE role = 'student'");
        $stmt_notif->execute([$notif_title, $notif_msg, $notif_link]);
        
        $_SESSION['broadcast_msg'] = "Broadcast berhasil dikirim!";
    } elseif ($_POST['action'] === 'toggle') {
        $id = $_POST['broadcast_id'];
        $stmt = $pdo->prepare("UPDATE broadcasts SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['broadcast_msg'] = "Status broadcast berhasil diubah!";
    } elseif ($_POST['action'] === 'delete') {
        $id = $_POST['broadcast_id'];
        $stmt = $pdo->prepare("DELETE FROM broadcasts WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['broadcast_msg'] = "Broadcast berhasil dihapus!";
    }
    
    // Redirect to prevent form resubmission on refresh
    header("Location: index.php?page=admin_broadcast");
    exit();
}

$success_message = '';
if (isset($_SESSION['broadcast_msg'])) {
    $success_message = $_SESSION['broadcast_msg'];
    unset($_SESSION['broadcast_msg']);
}

// Get all broadcasts
$stmt = $pdo->query("SELECT * FROM broadcasts ORDER BY created_at DESC");
$broadcasts = $stmt->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1; max-width: 1000px; margin: 0 auto;">
    <div class="section-header" style="margin-bottom: 2rem; border-bottom: 1px solid var(--dash-border); padding-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem; display:flex; align-items:center; gap:10px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="28" style="color:var(--dash-primary);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                Manajemen Broadcast
            </h1>
            <p style="color: var(--dash-text-muted);">Kirim pemberitahuan atau pengumuman pop-up ke seluruh siswa.</p>
        </div>
    </div>

    <?php if (isset($success_message)): ?>
        <div style="background: rgba(34, 197, 94, 0.1); color: #16a34a; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <div class="dash-grid-sidebar-rev" style="display: grid;  gap: 2rem;">
        <!-- Form -->
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; height: fit-content;">
            <h3 style="margin-top:0; color:var(--dash-text); margin-bottom:1rem;">Kirim Broadcast Baru</h3>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size:0.9rem; font-weight:600; color:var(--dash-text);">Judul Pemberitahuan</label>
                    <input type="text" name="title" required placeholder="Contoh: Maintenance Server" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size:0.9rem; font-weight:600; color:var(--dash-text);">Pesan Lengkap</label>
                    <textarea name="message" required rows="4" placeholder="Ketik pesan yang ingin disampaikan..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize:vertical;"></textarea>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size:0.9rem; font-weight:600; color:var(--dash-text);">Tipe Pesan (Warna & Ikon)</label>
                    <select name="type" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text);">
                        <option value="info">🔵 Informasi (Info)</option>
                        <option value="success">🟢 Sukses (Berita Baik)</option>
                        <option value="warning">🟠 Peringatan (Warning)</option>
                    </select>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size:0.9rem; font-weight:600; color:var(--dash-text);">Sifat Tampil</label>
                    <select name="display_mode" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text);">
                        <option value="once">Sekali Saja (Hilang setelah diklik Mengerti)</option>
                        <option value="always">Selalu Tampil (Terus muncul selama berstatus Aktif)</option>
                    </select>
                </div>
                
                <button type="submit" style="width: 100%; background: var(--dash-primary); color: white; border: none; padding: 0.85rem; border-radius: 8px; font-weight: 600; cursor: pointer;">
                     Kirim Sekarang
                </button>
            </form>
        </div>

        <!-- Tabel Riwayat -->
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; overflow: hidden;">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--dash-border);">
                <h3 style="margin: 0; color:var(--dash-text);">Riwayat Broadcast</h3>
            </div>
            
            <?php if (count($broadcasts) === 0): ?>
                <div style="padding: 2rem; text-align: center; color: var(--dash-text-muted);">
                    Belum ada broadcast yang dikirim.
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: rgba(0,0,0,0.02); border-bottom: 1px solid var(--dash-border);">
                                <th style="padding: 1rem; color: var(--dash-text-muted); font-size:0.85rem;">WAKTU & STATUS</th>
                                <th style="padding: 1rem; color: var(--dash-text-muted); font-size:0.85rem;">PESAN</th>
                                <th style="padding: 1rem; color: var(--dash-text-muted); font-size:0.85rem; text-align:right;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($broadcasts as $b): ?>
                            <tr style="border-bottom: 1px solid var(--dash-border);">
                                <td style="padding: 1rem; vertical-align: top;">
                                    <div style="font-size:0.8rem; color:var(--dash-text-muted); margin-bottom:4px;">
                                        <?php echo date('d M Y, H:i', strtotime($b['created_at'])); ?>
                                    </div>
                                    <?php if ($b['is_active']): ?>
                                        <span style="background: rgba(34, 197, 94, 0.1); color: #16a34a; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Aktif</span>
                                    <?php else: ?>
                                        <span style="background: rgba(100, 116, 139, 0.1); color: #64748b; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="font-weight:600; color:var(--dash-text); margin-bottom:4px; display:flex; align-items:center; gap:6px;">
                                        <?php
                                            if($b['type'] === 'info') echo '🔵';
                                            if($b['type'] === 'success') echo '🟢';
                                            if($b['type'] === 'warning') echo '🟠';
                                        ?>
                                        <?php echo htmlspecialchars($b['title']); ?>
                                    </div>
                                    <div style="font-size:0.85rem; color:var(--dash-text-muted); line-height:1.4;">
                                        <?php echo nl2br(htmlspecialchars($b['message'])); ?>
                                    </div>
                                    <?php
                                        // Count views
                                        $stmt_views = $pdo->prepare("SELECT COUNT(*) FROM broadcast_views WHERE broadcast_id = ?");
                                        $stmt_views->execute([$b['id']]);
                                        $views = $stmt_views->fetchColumn();
                                    ?>
                                    <div style="font-size:0.75rem; color:var(--dash-primary); margin-top:8px; font-weight:600; display:flex; align-items:center; gap:8px;">
                                        <span>Dilihat oleh <?php echo $views; ?> siswa</span>
                                        <span style="color:var(--dash-border);">|</span>
                                        <span style="color: <?php echo $b['display_mode'] === 'always' ? '#ef4444' : '#64748b'; ?>;">
                                            Sifat: <?php echo $b['display_mode'] === 'always' ? 'Selalu Tampil' : 'Sekali Saja'; ?>
                                        </span>
                                    </div>
                                </td>
                                <td style="padding: 1rem; text-align:right; vertical-align: top;">
                                    <div style="display:flex; justify-content:flex-end; gap:8px;">
                                        <!-- Preview -->
                                        <button type="button" title="Tampilkan/Preview" onclick='showBroadcastModal(<?php echo $b['id']; ?>, <?php echo htmlspecialchars(json_encode($b['title']), ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars(json_encode($b['message']), ENT_QUOTES, 'UTF-8'); ?>, "<?php echo $b['type']; ?>", true)' style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
                                            Tampilkan
                                        </button>
                                        
                                        <!-- Toggle Status -->
                                        <form method="POST" style="margin:0;">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="broadcast_id" value="<?php echo $b['id']; ?>">
                                            <button type="submit" title="<?php echo $b['is_active'] ? 'Nonaktifkan' : 'Aktifkan'; ?>" style="background: rgba(0,0,0,0.05); color: var(--dash-text); border: none; padding: 6px; border-radius: 6px; cursor: pointer;">
                                                <?php if($b['is_active']): ?>
                                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                <?php else: ?>
                                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                        
                                        <!-- Delete -->
                                        <form method="POST" style="margin:0;" onsubmit="return confirm('Yakin ingin menghapus broadcast ini secara permanen?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="broadcast_id" value="<?php echo $b['id']; ?>">
                                            <button type="submit" title="Hapus" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: none; padding: 6px; border-radius: 6px; cursor: pointer;">
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
