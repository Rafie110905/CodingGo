<?php
// Proteksi ketat: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'><h1 style='color:var(--dash-text);'>Akses Ditolak</h1></div>";
    exit();
}

require_once 'config/db.php';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $status = $_POST['status'];
        
        $stmt = $pdo->prepare("INSERT INTO championships (title, description, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $start_date, $end_date, $status]);
        $championship_id = $pdo->lastInsertId();
        
        // Notify all students
        $notif_title = "Turnamen Baru: " . $title;
        $notif_msg = "Ikuti Go Championship terbaru dan menangkan XP serta Badge spesial!";
        $notif_link = "index.php?page=championship_detail&id=" . $championship_id;
        $stmt_notif = $pdo->prepare("INSERT INTO user_notifications (user_id, type, title, message, link_url) SELECT id, 'system', ?, ?, ? FROM users WHERE role = 'student'");
        $stmt_notif->execute([$notif_title, $notif_msg, $notif_link]);
        
        header("Location: index.php?page=admin_championship&success=added");
        exit();
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $status = $_POST['status'];
        
        $stmt = $pdo->prepare("UPDATE championships SET title=?, description=?, start_date=?, end_date=?, status=? WHERE id=?");
        $stmt->execute([$title, $description, $start_date, $end_date, $status, $id]);
        
        header("Location: index.php?page=admin_championship&success=edited");
        exit();
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        
        // Hapus juga challenges & participants terkait
        $pdo->prepare("DELETE FROM championship_challenges WHERE championship_id=?")->execute([$id]);
        $pdo->prepare("DELETE FROM championship_participants WHERE championship_id=?")->execute([$id]);
        $pdo->prepare("DELETE FROM championships WHERE id=?")->execute([$id]);
        
        header("Location: index.php?page=admin_championship&success=deleted");
        exit();
    }
}

// Ambil semua data championship
$stmt = $pdo->query("SELECT * FROM championships ORDER BY created_at DESC");
$championships = $stmt->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.25rem;">Manage Coding Championship</h1>
            <p style="color: var(--dash-text-muted);">Kelola musim turnamen dan tantangan khusus untuk siswa.</p>
        </div>
        <button onclick="document.getElementById('modal-add').style.display='flex';" class="btn btn-primary" style="background: var(--dash-primary); color:white; padding: 0.75rem 1.5rem; border:none; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:8px;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Buat Turnamen Baru
        </button>
    </div>

    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 12px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(0,0,0,0.02); border-bottom: 1px solid var(--dash-border);">
                    <th style="padding: 1rem; color: var(--dash-text-muted); font-size: 0.85rem; font-weight: 600;">TURNAMEN</th>
                    <th style="padding: 1rem; color: var(--dash-text-muted); font-size: 0.85rem; font-weight: 600;">PERIODE</th>
                    <th style="padding: 1rem; color: var(--dash-text-muted); font-size: 0.85rem; font-weight: 600;">STATUS</th>
                    <th style="padding: 1rem; color: var(--dash-text-muted); font-size: 0.85rem; font-weight: 600; text-align: right;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($championships) === 0): ?>
                <tr>
                    <td colspan="4" style="padding: 2rem; text-align: center; color: var(--dash-text-muted);">Belum ada turnamen.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($championships as $c): ?>
                    <tr style="border-bottom: 1px solid var(--dash-border);">
                        <td style="padding: 1rem;">
                            <div style="font-weight: 600; color: var(--dash-text);"><?php echo htmlspecialchars($c['title']); ?></div>
                            <div style="font-size: 0.85rem; color: var(--dash-text-muted);"><?php echo htmlspecialchars(substr($c['description'], 0, 50)); ?>...</div>
                        </td>
                        <td style="padding: 1rem; color: var(--dash-text);">
                            <div><?php echo date('d M Y, H:i', strtotime($c['start_date'])); ?></div>
                            <div style="font-size:0.8rem; color:var(--dash-text-muted);">s/d <?php echo date('d M Y, H:i', strtotime($c['end_date'])); ?></div>
                        </td>
                        <td style="padding: 1rem;">
                            <?php if($c['status'] == 'active'): ?>
                                <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Active</span>
                            <?php elseif($c['status'] == 'upcoming'): ?>
                                <span style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Upcoming</span>
                            <?php else: ?>
                                <span style="background: rgba(100, 116, 139, 0.1); color: #64748b; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Ended</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="index.php?page=admin_championship_detail&id=<?php echo $c['id']; ?>" style="background: var(--dash-primary); color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">Kelola</a>
                                <button onclick="editModal(<?php echo htmlspecialchars(json_encode($c)); ?>)" style="background: transparent; border: 1px solid var(--dash-border); color: var(--dash-text); padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 600;">Edit</button>
                                <form method="POST" style="margin:0;" onsubmit="return confirm('Hapus turnamen ini permanen?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                    <button type="submit" style="background: transparent; border: 1px solid #ef4444; color: #ef4444; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 600;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add -->
<div id="modal-add" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--dash-sidebar); width:90%; max-width:500px; border-radius:16px; padding:2rem; position:relative;">
        <h2 style="margin-top:0; color:var(--dash-text);">Buat Turnamen Baru</h2>
        <button onclick="document.getElementById('modal-add').style.display='none';" style="position:absolute; top:1rem; right:1rem; background:transparent; border:none; font-size:1.5rem; color:var(--dash-text-muted); cursor:pointer;">&times;</button>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Judul Turnamen</label>
                <input type="text" name="title" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Deskripsi Singkat</label>
                <textarea name="description" required rows="3" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);"></textarea>
            </div>
            <div class="dash-grid-2" style="display:grid;  gap:1rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Tanggal Mulai</label>
                    <input type="datetime-local" name="start_date" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Tanggal Berakhir</label>
                    <input type="datetime-local" name="end_date" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                </div>
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Status</label>
                <select name="status" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                    <option value="upcoming">Upcoming (Belum Mulai)</option>
                    <option value="active">Active (Sedang Berjalan)</option>
                    <option value="ended">Ended (Sudah Selesai)</option>
                </select>
            </div>
            <button type="submit" style="width:100%; padding:1rem; background:var(--dash-primary); color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer;">Simpan Turnamen</button>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--dash-sidebar); width:90%; max-width:500px; border-radius:16px; padding:2rem; position:relative;">
        <h2 style="margin-top:0; color:var(--dash-text);">Edit Turnamen</h2>
        <button onclick="document.getElementById('modal-edit').style.display='none';" style="position:absolute; top:1rem; right:1rem; background:transparent; border:none; font-size:1.5rem; color:var(--dash-text-muted); cursor:pointer;">&times;</button>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Judul Turnamen</label>
                <input type="text" name="title" id="edit_title" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Deskripsi Singkat</label>
                <textarea name="description" id="edit_description" required rows="3" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);"></textarea>
            </div>
            <div class="dash-grid-2" style="display:grid;  gap:1rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Tanggal Mulai</label>
                    <input type="datetime-local" name="start_date" id="edit_start_date" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Tanggal Berakhir</label>
                    <input type="datetime-local" name="end_date" id="edit_end_date" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                </div>
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Status</label>
                <select name="status" id="edit_status" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                    <option value="upcoming">Upcoming (Belum Mulai)</option>
                    <option value="active">Active (Sedang Berjalan)</option>
                    <option value="ended">Ended (Sudah Selesai)</option>
                </select>
            </div>
            <button type="submit" style="width:100%; padding:1rem; background:var(--dash-primary); color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer;">Update Turnamen</button>
        </form>
    </div>
</div>

<script>
function editModal(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_title').value = data.title;
    document.getElementById('edit_description').value = data.description;
    
    // Format datetime-local: YYYY-MM-DDThh:mm
    document.getElementById('edit_start_date').value = data.start_date.replace(' ', 'T').substring(0, 16);
    document.getElementById('edit_end_date').value = data.end_date.replace(' ', 'T').substring(0, 16);
    
    document.getElementById('edit_status').value = data.status;
    document.getElementById('modal-edit').style.display = 'flex';
}
</script>
