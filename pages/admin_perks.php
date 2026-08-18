<?php
// Proteksi ketat: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'><h1 style='color:var(--dash-text);'>Akses Ditolak</h1></div>";
    exit();
}

require_once 'config/db.php';

// Handle Add/Delete Perk
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Handle File Upload if exists
    $uploaded_val = null;
    if (isset($_FILES['banner_file']) && $_FILES['banner_file']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/gif', 'image/jpeg', 'image/png', 'video/mp4'];
        if (in_array($_FILES['banner_file']['type'], $allowed)) {
            $ext = pathinfo($_FILES['banner_file']['name'], PATHINFO_EXTENSION);
            $filename = 'banner_' . time() . '.' . $ext;
            $dest = 'src/img/' . $filename;
            if (move_uploaded_file($_FILES['banner_file']['tmp_name'], $dest)) {
                $uploaded_val = $filename;
            }
        }
    }
    
    if ($action === 'add') {
        $type = $_POST['type'];
        $name = $_POST['name'];
        $value = $uploaded_val ?? $_POST['value'];
        $req_badges = $_POST['required_badges'];
        
        $stmt = $pdo->prepare("INSERT INTO gamification_perks (type, name, value, required_badges) VALUES (?, ?, ?, ?)");
        $stmt->execute([$type, $name, $value, $req_badges]);
        header("Location: index.php?page=admin_perks");
        exit();
    } elseif ($action === 'edit') {
        $id = $_POST['perk_id'];
        $type = $_POST['type'];
        $name = $_POST['name'];
        $value = $uploaded_val ?? $_POST['value'];
        $req_badges = $_POST['required_badges'];
        
        $stmt = $pdo->prepare("UPDATE gamification_perks SET type=?, name=?, value=?, required_badges=? WHERE id=?");
        $stmt->execute([$type, $name, $value, $req_badges, $id]);
        header("Location: index.php?page=admin_perks");
        exit();
    } elseif ($action === 'delete') {
        $id = $_POST['perk_id'];
        $stmt = $pdo->prepare("DELETE FROM gamification_perks WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: index.php?page=admin_perks");
        exit();
    }
}

// Get all perks
$stmt = $pdo->query("SELECT * FROM gamification_perks ORDER BY required_badges ASC");
$perks = $stmt->fetchAll();

// Handle Edit Mode
$edit_perk = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM gamification_perks WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_perk = $stmt->fetch();
}
?>

<div class="dash-header" style="grid-column: 1 / -1; margin-bottom: 2rem;">
    <h1 class="dash-title">🎁 Kelola Gamification Perks</h1>
    <p class="dash-subtitle">Atur bingkai avatar, efek nama, dan kustomisasi kosmetik lainnya yang bisa di-unlock user.</p>
</div>

<div class="dash-grid-fixed-right" style="grid-column: 1 / -1; display: grid;  gap: 2rem; align-items: start;">
    
    <!-- Kiri: Live Preview & Table -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        <!-- Live Preview Card -->
        <div>
            <h3 style="color:var(--dash-text); margin-bottom:15px; display:flex; align-items:center; gap:8px;">
                👀 Live Preview
            </h3>
            
            <div id="preview-card" style="background:var(--dash-sidebar, #1e293b); border:1px solid var(--dash-border, #334155); border-radius:16px; width:100%; max-width:450px; overflow:hidden; position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
                <div id="preview-profile-effect" style="position:absolute; inset:0; pointer-events:none; z-index:1; border-radius:16px; overflow:hidden;"></div>
                
                <div id="preview-banner" style="position:relative; min-height:160px; width:100%; background:linear-gradient(135deg, #3b82f633, var(--dash-sidebar, #1e293b)); background-size:cover; background-position:center; display:flex; align-items:flex-end; border-radius:16px 16px 0 0; overflow:hidden; transition:all 0.3s ease;">
                    <video id="preview-video-bg" autoplay loop muted playsinline style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; pointer-events:none; filter:contrast(1.05) saturate(1.15); display:none;"></video>
                    <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.35) 55%, rgba(0,0,0,0.05) 100%); z-index:1;"></div>
                    <div style="position:relative; display:flex; align-items:center; gap:14px; padding:16px 20px 18px 20px; width:100%; box-sizing:border-box; z-index:2;">
                        
                        <div style="position:relative; width:70px; height:70px; flex-shrink:0;">
                            <div id="preview-avatar" style="width:100%; height:100%; border-radius:50%; background:#3b82f6; color:white; display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:bold; border:4px solid var(--dash-sidebar, #1e293b); transition:all 0.3s ease;">A</div>
                        </div>

                        <div style="min-width:0;">
                            <h2 id="preview-name" style="margin:0; font-size:1.2rem; line-height:1.25; color:#ffffff; text-shadow:0 1px 4px rgba(0,0,0,0.6); transition:all 0.3s ease;">Admin User</h2>
                            <div style="margin-top:4px;"><span style="display:inline-block; background:rgba(0,0,0,0.35); padding:3px 12px; border-radius:12px; font-size:0.7rem; font-weight:700; color:#ffffff;">System Admin</span></div>
                            <div style="font-size:0.75rem; color:rgba(255,255,255,0.9); background:rgba(0,0,0,0.4); padding:4px 10px; border-radius:8px; margin-top:6px; display:inline-block; border:1px solid rgba(255,255,255,0.1);">
                                🚀 Sedang uji coba efek...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Background overlay untuk card_background -->
                <div id="preview-card-bg" style="position:absolute; inset:0; z-index:1; border-radius:0 0 16px 16px; pointer-events:none;"></div>

                <div style="padding:1.5rem; text-align:center; position:relative; z-index:2;">
                    <div style="display:flex; justify-content:center; gap:1.5rem; margin-bottom:1.25rem; padding-bottom:1rem; border-bottom:1px solid var(--dash-border, #334155);">
                        <div><div style="font-weight:700; color:var(--dash-text, #f1f5f9); font-size:1.1rem;">1500</div><div style="font-size:0.7rem; color:var(--dash-text-muted, #94a3b8);">XP</div></div>
                        <div><div style="font-weight:700; color:var(--dash-text, #f1f5f9); font-size:1.1rem;">30</div><div style="font-size:0.7rem; color:var(--dash-text-muted, #94a3b8);">Badge</div></div>
                    </div>
                    
                    <!-- Dummy Badges for Live Preview -->
                    <div style="margin-bottom:1rem;">
                        <div style="font-size:0.7rem; color:var(--dash-text-muted); margin-bottom:5px;">Badges</div>
                        <div style="display:flex; justify-content:center; gap:8px; flex-wrap:wrap;">
                            <div class="preview-badge" style="display:flex; align-items:center; justify-content:center; background:var(--dash-bg, #0f172a); border:1px solid var(--dash-border, #334155); border-radius:8px; padding:6px; transition:all 0.2s;">
                                <div style="width:24px; height:24px; border-radius:6px; background:#f59e0b; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:0.8rem;">1</div>
                            </div>
                            <div class="preview-badge" style="display:flex; align-items:center; justify-content:center; background:var(--dash-bg, #0f172a); border:1px solid var(--dash-border, #334155); border-radius:8px; padding:6px; transition:all 0.2s;">
                                <div style="width:24px; height:24px; border-radius:6px; background:#3b82f6; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:0.8rem;">2</div>
                            </div>
                            <div class="preview-badge" style="display:flex; align-items:center; justify-content:center; background:var(--dash-bg, #0f172a); border:1px solid var(--dash-border, #334155); border-radius:8px; padding:6px; transition:all 0.2s;">
                                <div style="width:24px; height:24px; border-radius:6px; background:#10b981; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:0.8rem;">3</div>
                            </div>
                        </div>
                    </div>

                    <div style="color:var(--dash-text-muted, #94a3b8); font-size:0.8rem;">
                        <p style="margin:0;">Ketik CSS di form samping untuk melihat hasilnya secara real-time pada kartu profil ini!</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- List Perks -->
        <div style="background:var(--dash-bg); padding:20px; border-radius:15px; border:1px solid var(--dash-border);">
            <h3 style="color:var(--dash-text); margin-bottom:15px;">Daftar Perks Tersedia</h3>
            
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; text-align:left;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--dash-border); color:var(--dash-text-muted);">
                            <th style="padding:10px;">Tipe</th>
                            <th style="padding:10px;">Nama</th>
                            <th style="padding:10px;">Preview</th>
                            <th style="padding:10px;">Syarat Badge</th>
                            <th style="padding:10px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($perks)): ?>
                            <tr><td colspan="5" style="padding:15px; text-align:center; color:var(--dash-text-muted);">Belum ada perk kosmetik.</td></tr>
                        <?php else: ?>
                            <?php foreach($perks as $b): ?>
                                <tr style="border-bottom:1px solid var(--dash-border); color:var(--dash-text);">
                                    <td style="padding:10px;">
                                        <span style="font-size:0.8rem; background:var(--dash-sidebar); padding:3px 8px; border-radius:4px; font-family:monospace;">
                                            <?php echo htmlspecialchars($b['type']); ?>
                                        </span>
                                    </td>
                                    <td style="padding:10px; font-weight:bold;"><?php echo htmlspecialchars($b['name']); ?></td>
                                    <td style="padding:10px;">
                                        <?php if($b['type'] === 'avatar_frame'): ?>
                                            <div style="width:40px; height:40px; border-radius:50%; background:var(--dash-sidebar); margin:auto; <?php echo htmlspecialchars($b['value']); ?>"></div>
                                        <?php elseif($b['type'] === 'name_effect'): ?>
                                            <span style="font-size:1.1rem; <?php echo htmlspecialchars($b['value']); ?>">Nama User</span>
                                        <?php elseif($b['type'] === 'profile_effect'): ?>
                                            <?php 
                                            $val = $b['value'];
                                            if (preg_match('/^https?:\/\//i', $val)) {
                                                if (preg_match('/\.mp4($|\?)/i', $val)) {
                                                    echo '<video src="'.htmlspecialchars($val).'" autoplay loop muted playsinline style="width:60px; height:30px; border-radius:8px; object-fit:cover; border:1px solid var(--dash-border);"></video>';
                                                } else {
                                                    echo '<div style="width:60px; height:30px; border-radius:8px; background-image:url(\''.htmlspecialchars($val).'\'); background-position:center; background-size:cover; border:1px solid var(--dash-border);"></div>';
                                                }
                                            } elseif (preg_match('/\.(gif|jpg|jpeg|png|mp4)$/i', $val)) {
                                                if (preg_match('/\.mp4$/i', $val)) {
                                                    echo '<video src="src/img/'.htmlspecialchars($val).'" autoplay loop muted playsinline style="width:60px; height:30px; border-radius:8px; object-fit:cover; border:1px solid var(--dash-border);"></video>';
                                                } else {
                                                    echo '<div style="width:60px; height:30px; border-radius:8px; background-image:url(\'src/img/'.htmlspecialchars($val).'\'); background-position:center; background-size:cover; border:1px solid var(--dash-border);"></div>';
                                                }
                                            } else {
                                                echo '<div class="profile-effect-'.htmlspecialchars($val).'" style="width:60px; height:30px; border-radius:8px; background-color:#1e293b; border:1px solid var(--dash-border);"></div>';
                                            }
                                            ?>
                                        <?php elseif($b['type'] === 'banner_gif'): ?>
                                            <?php 
                                            $val = $b['value'];
                                            if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
                                                if (str_ends_with($val, '.mp4')) {
                                                    echo '<video src="'.htmlspecialchars($val).'" autoplay loop muted style="width:60px; height:30px; border-radius:8px; object-fit:cover;"></video>';
                                                } else {
                                                    echo '<div style="width:60px; height:30px; border-radius:8px; background-image:url(\''.htmlspecialchars($val).'\'); background-position:center; background-size:cover; border:1px solid var(--dash-border);"></div>';
                                                }
                                            } else {
                                                echo '<div style="width:60px; height:30px; border-radius:8px; background-image:url(\'src/img/'.htmlspecialchars($val).'\'); background-position:center; background-size:cover; border:1px solid var(--dash-border);"></div>';
                                            }
                                            ?>
                                        <?php elseif($b['type'] === 'card_border'): ?>
                                            <div style="width:40px; height:30px; border-radius:4px; <?php echo htmlspecialchars($b['value']); ?>"></div>
                                        <?php elseif($b['type'] === 'card_background'): ?>
                                            <div style="width:60px; height:30px; border-radius:8px; <?php echo htmlspecialchars($b['value']); ?> border:1px solid var(--dash-border);"></div>
                                        <?php elseif($b['type'] === 'cursor_effect'): ?>
                                            <div style="width:30px; height:30px; border-radius:4px; background:var(--dash-sidebar); display:flex; align-items:center; justify-content:center; border:1px solid var(--dash-border); <?php echo htmlspecialchars($b['value']); ?>">👆</div>
                                        <?php elseif($b['type'] === 'badge_effect'): ?>
                                            <div style="width:30px; height:30px; border-radius:8px; background:var(--dash-sidebar); border:2px solid var(--dash-border); margin:auto; display:flex; align-items:center; justify-content:center; <?php echo htmlspecialchars($b['value']); ?>">🏆</div>
                                        <?php elseif($b['type'] === 'entrance_anim'): ?>
                                            <div style="font-size:0.8rem; background:rgba(99, 102, 241, 0.2); color:#818cf8; padding:4px 8px; border-radius:4px; text-align:center;">▶️ Play</div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:10px; color:#f59e0b; font-weight:bold;">
                                        🏆 <?php echo $b['required_badges']; ?>
                                    </td>
                                    <td style="padding:10px;">
                                        <div style="display:flex; gap:8px; align-items:center;">
                                            <button type="button" id="btn-apply-<?php echo htmlspecialchars($b['type']); ?>-<?php echo md5($b['value']); ?>" data-type="<?php echo htmlspecialchars($b['type'], ENT_QUOTES); ?>" data-val="<?php echo htmlspecialchars($b['value'], ENT_QUOTES); ?>" onclick="window.terapkanEfek(this.dataset.type, this.dataset.val, this.id)" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.8rem; white-space: nowrap;">Terapkan</button>
                                            <a href="index.php?page=admin_perks&edit_id=<?php echo $b['id']; ?>" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; text-decoration: none; white-space: nowrap;">Edit</a>
                                            <form method="POST" action="index.php?page=admin_perks" onsubmit="return confirm('Hapus perk ini?');" style="margin:0;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="perk_id" value="<?php echo $b['id']; ?>">
                                                <button type="submit" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.8rem; white-space: nowrap;">Hapus</button>
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
        
    </div>

    <!-- Kanan: Form Add/Edit -->
    <div>
        <div style="background:var(--dash-bg); padding:20px; border-radius:15px; border:1px solid var(--dash-border);">
            <div style="background:var(--dash-sidebar, #1e293b); border:1px solid var(--dash-border, #334155); border-radius:16px; padding:2rem; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
            <form method="POST" action="index.php?page=admin_perks" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $edit_perk ? 'edit' : 'add'; ?>">
                <?php if ($edit_perk): ?>
                    <input type="hidden" name="perk_id" value="<?php echo $edit_perk['id']; ?>">
                <?php endif; ?>
                
                <div style="margin-bottom:15px;">
                    <label style="display:block; color:var(--dash-text-muted); font-size:0.85rem; margin-bottom:5px;">Tipe Perk</label>
                    <select name="type" id="type-select" required style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-sidebar); color:var(--dash-text);">
                        <option value="avatar_frame" <?php echo ($edit_perk && $edit_perk['type'] === 'avatar_frame') ? 'selected' : ''; ?>>Avatar Frame (Bingkai Profil)</option>
                        <option value="name_effect" <?php echo ($edit_perk && $edit_perk['type'] === 'name_effect') ? 'selected' : ''; ?>>Name Effect (Efek Nama)</option>
                        <option value="profile_effect" <?php echo ($edit_perk && $edit_perk['type'] === 'profile_effect') ? 'selected' : ''; ?>>Profile Effect (Efek Layar Profil)</option>
                        <option value="banner_gif" <?php echo ($edit_perk && $edit_perk['type'] === 'banner_gif') ? 'selected' : ''; ?>>Banner Animasi Profil (URL / File)</option>
                        <option value="card_border" <?php echo ($edit_perk && $edit_perk['type'] === 'card_border') ? 'selected' : ''; ?>>Border Kartu Profil (CSS)</option>
                        <option value="card_background" <?php echo ($edit_perk && $edit_perk['type'] === 'card_background') ? 'selected' : ''; ?>>Tema Latar Kartu (Card Background)</option>
                        <option value="cursor_effect" <?php echo ($edit_perk && $edit_perk['type'] === 'cursor_effect') ? 'selected' : ''; ?>>Efek Kursor (Cursor Trails)</option>
                        <option value="badge_effect" <?php echo ($edit_perk && $edit_perk['type'] === 'badge_effect') ? 'selected' : ''; ?>>Animasi Badge (Badge Effect)</option>
                        <option value="entrance_anim" <?php echo ($edit_perk && $edit_perk['type'] === 'entrance_anim') ? 'selected' : ''; ?>>Animasi Masuk (Entrance Anim)</option>
                    </select>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; color:var(--dash-text-muted); font-size:0.85rem; margin-bottom:5px;">Nama Perk</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($edit_perk['name'] ?? ''); ?>" required placeholder="Misal: Rainbow Glow" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-sidebar); color:var(--dash-text);">
                </div>

                <div style="margin-bottom:15px; position:relative;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:5px;">
                        <label style="color:var(--dash-text-muted); font-size:0.85rem;">Nilai / CSS String / URL</label>
                        <button type="button" id="toggle-builder" style="background:none; border:none; color:var(--dash-primary); font-size:0.8rem; cursor:pointer; text-decoration:underline;">Tampilkan Visual Builder</button>
                    </div>

                    <!-- VISUAL BUILDER CONTROLS -->
                    <div id="visual-builder-panel" style="display:none; padding:15px; background:rgba(0,0,0,0.1); border:1px dashed var(--dash-border); border-radius:8px; margin-bottom:10px;">
                        <!-- Avatar Frame Builder -->
                        <div id="builder-avatar_frame" class="builder-section" style="display:none;">
                            <div style="display:flex; gap:10px; margin-bottom:10px;">
                                <div style="flex:1;">
                                    <label style="font-size:0.75rem; color:var(--dash-text-muted);">Warna Border</label>
                                    <input type="color" id="b-af-color" value="#ef4444" style="width:100%; height:30px; border:none; border-radius:4px; cursor:pointer; background:none;">
                                </div>
                                <div style="flex:1;">
                                    <label style="font-size:0.75rem; color:var(--dash-text-muted);">Ketebalan (px)</label>
                                    <input type="number" id="b-af-size" value="4" style="width:100%; padding:5px; border-radius:4px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                                </div>
                            </div>
                            <div style="display:flex; gap:10px;">
                                <div style="flex:1;">
                                    <label style="font-size:0.75rem; color:var(--dash-text-muted);">Warna Glow/Shadow</label>
                                    <input type="color" id="b-af-shadow-color" value="#ef4444" style="width:100%; height:30px; border:none; border-radius:4px; cursor:pointer; background:none;">
                                </div>
                                <div style="flex:1;">
                                    <label style="font-size:0.75rem; color:var(--dash-text-muted);">Besar Glow (px)</label>
                                    <input type="number" id="b-af-shadow-size" value="10" style="width:100%; padding:5px; border-radius:4px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                                </div>
                            </div>
                        </div>

                        <!-- Name Effect Builder -->
                        <div id="builder-name_effect" class="builder-section" style="display:none;">
                            <div style="display:flex; gap:10px; margin-bottom:10px;">
                                <div style="flex:1;">
                                    <label style="font-size:0.75rem; color:var(--dash-text-muted);">Warna Teks</label>
                                    <input type="color" id="b-ne-color" value="#3b82f6" style="width:100%; height:30px; border:none; border-radius:4px; cursor:pointer; background:none;">
                                </div>
                                <div style="flex:1;">
                                    <label style="font-size:0.75rem; color:var(--dash-text-muted);">Gaya Font</label>
                                    <select id="b-ne-weight" style="width:100%; padding:5px; border-radius:4px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                                        <option value="normal">Normal</option>
                                        <option value="bold">Tebal (Bold)</option>
                                    </select>
                                </div>
                            </div>
                            <div style="display:flex; gap:10px;">
                                <div style="flex:1;">
                                    <label style="font-size:0.75rem; color:var(--dash-text-muted);">Warna Shadow</label>
                                    <input type="color" id="b-ne-shadow-color" value="#3b82f6" style="width:100%; height:30px; border:none; border-radius:4px; cursor:pointer; background:none;">
                                </div>
                                <div style="flex:1;">
                                    <label style="font-size:0.75rem; color:var(--dash-text-muted);">Besar Shadow (px)</label>
                                    <input type="number" id="b-ne-shadow-size" value="5" style="width:100%; padding:5px; border-radius:4px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                                </div>
                            </div>
                        </div>

                        <!-- Card Border Builder -->
                        <div id="builder-card_border" class="builder-section" style="display:none;">
                            <div style="display:flex; gap:10px; margin-bottom:10px;">
                                <div style="flex:1;">
                                    <label style="font-size:0.75rem; color:var(--dash-text-muted);">Warna Border</label>
                                    <input type="color" id="b-cb-color" value="#3b82f6" style="width:100%; height:30px; border:none; border-radius:4px; cursor:pointer; background:none;">
                                </div>
                                <div style="flex:1;">
                                    <label style="font-size:0.75rem; color:var(--dash-text-muted);">Ketebalan Border (px)</label>
                                    <input type="number" id="b-cb-size" value="2" style="width:100%; padding:5px; border-radius:4px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                                </div>
                            </div>
                            <div style="display:flex; gap:10px;">
                                <div style="flex:1;">
                                    <label style="font-size:0.75rem; color:var(--dash-text-muted);">Warna Glow</label>
                                    <input type="color" id="b-cb-shadow-color" value="#3b82f6" style="width:100%; height:30px; border:none; border-radius:4px; cursor:pointer; background:none;">
                                </div>
                                <div style="flex:1;">
                                    <label style="font-size:0.75rem; color:var(--dash-text-muted);">Besar Glow (px)</label>
                                    <input type="number" id="b-cb-shadow-size" value="15" style="width:100%; padding:5px; border-radius:4px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                                </div>
                            </div>
                        </div>

                        <!-- URL Builder (Profile Effect / Banner / Background / Cursor) -->
                        <div id="builder-url" class="builder-section" style="display:none;">
                            <label style="font-size:0.75rem; color:var(--dash-text-muted);">URL Gambar / GIF / MP4 / CSS Preset</label>
                            <input type="text" id="b-url-val" placeholder="https://..." style="width:100%; padding:8px; border-radius:4px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                            <p style="font-size:0.7rem; color:#f59e0b; margin-top:5px; margin-bottom:15px;">Mendukung format gambar, animasi .gif, atau .mp4 dari internet.</p>

                            <label style="font-size:0.75rem; color:var(--dash-text-muted); font-weight:bold;">ATAU Upload File Lokal (Banner/Profile Effect)</label>
                            <input type="file" name="banner_file" accept="image/gif,image/jpeg,image/png,video/mp4" style="width:100%; padding:8px; border-radius:4px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text); margin-top:5px;">
                        </div>

                        <!-- Entrance Anim Builder -->
                        <div id="builder-entrance_anim" class="builder-section" style="display:none;">
                            <label style="font-size:0.75rem; color:var(--dash-text-muted);">Pilih Animasi Masuk</label>
                            <select id="b-ea-type" style="width:100%; padding:10px; border-radius:4px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                                <option value="modalFadeIn">Fade In (Default)</option>
                                <option value="modalZoomIn">Zoom In Bouncy</option>
                                <option value="modalSlideUp">Slide Up</option>
                                <option value="modal3DFlip">3D Flip</option>
                            </select>
                        </div>
                    </div>
                    <!-- END VISUAL BUILDER -->

                    <textarea name="value" id="css-textarea" placeholder="Tuliskan kode CSS untuk efek nama/frame, identifier profile effect, atau styling border kartu" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-sidebar); color:var(--dash-text); height:80px; resize:vertical; font-family:monospace;"><?php echo htmlspecialchars($edit_perk['value'] ?? ''); ?></textarea>
                    <p style="font-size:0.75rem; color:var(--dash-text-muted); margin-top:5px;" id="css-hint">Contoh (Frame): <code>border: 4px solid red; box-shadow: 0 0 10px red;</code></p>
                </div>
                
                <div style="margin-bottom:15px;">
                    <label style="display:block; color:var(--dash-text-muted); font-size:0.85rem; margin-bottom:5px;">Syarat Unlock (Jumlah Badge)</label>
                    <input type="number" name="required_badges" value="<?php echo htmlspecialchars($edit_perk['required_badges'] ?? '15'); ?>" required min="1" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-sidebar); color:var(--dash-text);">
                </div>
                
                <div style="display:flex; gap:10px;">
                    <button type="submit" style="flex:1; padding:10px; background:var(--dash-primary); color:white; border:none; border-radius:8px; cursor:pointer; font-weight:bold;">
                        <?php echo $edit_perk ? 'Simpan Perubahan' : 'Tambahkan Perk'; ?>
                    </button>
                    <?php if ($edit_perk): ?>
                        <a href="index.php?page=admin_perks" style="padding:10px 15px; background:var(--dash-border); color:var(--dash-text); border-radius:8px; text-decoration:none;">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Duplikat animasi dari footer_dash.php agar preview di Admin berjalan */
.profile-effect-matrix {
    background-image: 
        radial-gradient(circle, rgba(34, 197, 94, 0.4) 0%, transparent 70%),
        repeating-linear-gradient(0deg, rgba(34, 197, 94, 0.2) 0px, transparent 2px, transparent 4px);
    background-size: 100% 100%, 100% 4px;
    animation: matrix-scan 2s linear infinite;
}
@keyframes matrix-scan {
    0% { background-position: 0 0, 0 0; }
    100% { background-position: 0 0, 0 100%; }
}

.profile-effect-snow {
    background-image: 
        radial-gradient(circle at 10px 10px, rgba(255,255,255,0.8) 0, transparent 3px),
        radial-gradient(circle at 40px 30px, rgba(255,255,255,0.8) 0, transparent 3px),
        radial-gradient(circle at 70px 60px, rgba(255,255,255,0.8) 0, transparent 3px),
        radial-gradient(circle at 20px 80px, rgba(255,255,255,0.8) 0, transparent 3px);
    background-size: 100px 100px;
    animation: snow-fall 4s linear infinite;
}
@keyframes snow-fall {
    0% { background-position: 0 0, 0 0, 0 0, 0 0; }
    100% { background-position: 100px 100px, 50px 150px, -50px 200px, 0 100px; }
}

@keyframes rainbow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 69, 0, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(255, 69, 0, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 69, 0, 0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.querySelector('select[name="type"]');
    const valueTextarea = document.getElementById('css-textarea');
    const previewAvatar = document.getElementById('preview-avatar');
    const previewName = document.getElementById('preview-name');
    const previewEffect = document.getElementById('preview-profile-effect');
    const previewBanner = document.getElementById('preview-banner');
    const previewVideoBg = document.getElementById('preview-video-bg');
    const previewCard = document.getElementById('preview-card');

    // Builder Elements
    const toggleBuilderBtn = document.getElementById('toggle-builder');
    const builderPanel = document.getElementById('visual-builder-panel');
    const cssHint = document.getElementById('css-hint');
    let builderMode = false;

    function hexToRgb(hex) {
        let result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? `${parseInt(result[1], 16)}, ${parseInt(result[2], 16)}, ${parseInt(result[3], 16)}` : '0, 0, 0';
    }

    function updateFromBuilder() {
        if (!builderMode) return;
        const type = typeSelect.value;
        let generatedValue = '';

        if (type === 'avatar_frame') {
            const color = document.getElementById('b-af-color').value;
            const size = document.getElementById('b-af-size').value;
            const shadowColor = document.getElementById('b-af-shadow-color').value;
            const shadowSize = document.getElementById('b-af-shadow-size').value;
            generatedValue = `border: ${size}px solid ${color}; box-shadow: 0 0 ${shadowSize}px rgba(${hexToRgb(shadowColor)}, 0.8);`;
        } else if (type === 'name_effect') {
            const color = document.getElementById('b-ne-color').value;
            const weight = document.getElementById('b-ne-weight').value;
            const shadowColor = document.getElementById('b-ne-shadow-color').value;
            const shadowSize = document.getElementById('b-ne-shadow-size').value;
            generatedValue = `color: ${color}; font-weight: ${weight}; text-shadow: 0 0 ${shadowSize}px rgba(${hexToRgb(shadowColor)}, 1);`;
        } else if (type === 'profile_effect' || type === 'banner_gif') {
            generatedValue = document.getElementById('b-url-val').value.trim();
        } else if (type === 'card_background') {
            const url = document.getElementById('b-url-val').value.trim();
            generatedValue = `background-image: url('${url}'); background-size: cover; background-position: center;`;
        } else if (type === 'cursor_effect') {
            const url = document.getElementById('b-url-val').value.trim();
            generatedValue = `cursor: url('${url}'), auto;`;
        } else if (type === 'card_border') {
            const color = document.getElementById('b-cb-color').value;
            const size = document.getElementById('b-cb-size').value;
            const shadowColor = document.getElementById('b-cb-shadow-color').value;
            const shadowSize = document.getElementById('b-cb-shadow-size').value;
            generatedValue = `border: ${size}px solid ${color}; box-shadow: 0 0 ${shadowSize}px rgba(${hexToRgb(shadowColor)}, 0.4);`;
        } else if (type === 'badge_effect') {
            const color = document.getElementById('b-cb-color').value;
            const shadowColor = document.getElementById('b-cb-shadow-color').value;
            const shadowSize = document.getElementById('b-cb-shadow-size').value;
            generatedValue = `border-color: ${color}; box-shadow: 0 0 ${shadowSize}px rgba(${hexToRgb(shadowColor)}, 0.8);`;
        } else if (type === 'entrance_anim') {
            const anim = document.getElementById('b-ea-type').value;
            generatedValue = `animation: ${anim} 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;`;
        }

        valueTextarea.value = generatedValue;
        updatePreview();
    }

    function switchBuilderType() {
        document.querySelectorAll('.builder-section').forEach(el => el.style.display = 'none');
        const type = typeSelect.value;
        if (type === 'avatar_frame') {
            document.getElementById('builder-avatar_frame').style.display = 'block';
            cssHint.innerHTML = "Contoh: <code>border: 4px solid red; box-shadow: 0 0 10px red;</code>";
        } else if (type === 'name_effect') {
            document.getElementById('builder-name_effect').style.display = 'block';
            cssHint.innerHTML = "Contoh: <code>color: red; text-shadow: 0 0 5px red;</code>";
        } else if (type === 'card_border' || type === 'badge_effect') {
            document.getElementById('builder-card_border').style.display = 'block';
            cssHint.innerHTML = "Gunakan builder untuk mengatur warna dan glow.";
        } else if (type === 'entrance_anim') {
            document.getElementById('builder-entrance_anim').style.display = 'block';
            cssHint.innerHTML = "Contoh: <code>animation: modalZoomIn 0.5s ease;</code>";
        } else {
            document.getElementById('builder-url').style.display = 'block';
            if(type === 'card_background') cssHint.innerHTML = "Masukkan URL GIF untuk Background Kartu.";
            else if(type === 'cursor_effect') cssHint.innerHTML = "Masukkan URL gambar kursor (maks 32x32) untuk efek jejak.";
            else cssHint.innerHTML = "Contoh: <code>https://example.com/banner.gif</code> atau <code>matrix</code>";
        }
        if(builderMode) updateFromBuilder();
    }

    toggleBuilderBtn.addEventListener('click', () => {
        builderMode = !builderMode;
        if (builderMode) {
            builderPanel.style.display = 'block';
            valueTextarea.style.display = 'none';
            toggleBuilderBtn.innerText = 'Tutup Visual Builder (Manual CSS)';
            updateFromBuilder();
        } else {
            builderPanel.style.display = 'none';
            valueTextarea.style.display = 'block';
            toggleBuilderBtn.innerText = 'Tampilkan Visual Builder';
        }
    });

    document.querySelectorAll('#visual-builder-panel input, #visual-builder-panel select').forEach(el => {
        el.addEventListener('input', updateFromBuilder);
        el.addEventListener('change', updateFromBuilder);
    });

    typeSelect.addEventListener('change', () => {
        switchBuilderType();
        updatePreview();
    });

    // State for live preview
    document.getElementById('b-cb-color').addEventListener('input', updateFromBuilder);
    document.getElementById('b-cb-size').addEventListener('input', updateFromBuilder);
    document.getElementById('b-cb-shadow-color').addEventListener('input', updateFromBuilder);
    document.getElementById('b-cb-shadow-size').addEventListener('input', updateFromBuilder);

    let currentEffects = {
        avatar_frame: '',
        name_effect: '',
        profile_effect: '',
        banner_gif: '',
        card_border: '',
        card_background: '',
        cursor_effect: '',
        badge_effect: '',
        entrance_anim: ''
    };

    function applyEffect(type, value) {
        if (type) {
            currentEffects[type] = value;
        }

        // Reset all styles to base
        previewCard.style.cssText = 'background:var(--dash-sidebar, #1e293b); border:1px solid var(--dash-border, #334155); border-radius:16px; width:100%; max-width:450px; overflow:hidden; position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.2); transition:all 0.3s ease;';
        previewAvatar.style.cssText = 'width:100%; height:100%; border-radius:50%; background:#3b82f6; color:white; display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:bold; border:4px solid var(--dash-sidebar, #1e293b); transition:all 0.3s ease;';
        previewName.style.cssText = 'margin:0; font-size:1.2rem; line-height:1.25; color:#ffffff; text-shadow:0 1px 4px rgba(0,0,0,0.6); transition:all 0.3s ease;';
        previewEffect.className = '';
        previewEffect.style.backgroundImage = '';
        previewEffect.innerHTML = '';
        previewBanner.style.backgroundImage = '';
        previewBanner.style.background = 'linear-gradient(135deg, #3b82f633, var(--dash-sidebar, #1e293b))';
        previewVideoBg.style.display = 'none';
        previewVideoBg.src = '';
        
        const cardBgOverlay = document.getElementById('preview-card-bg');
        if (cardBgOverlay) cardBgOverlay.style.cssText = 'position:absolute; inset:0; z-index:1; border-radius:0 0 16px 16px; pointer-events:none;';
        
        document.querySelectorAll('.preview-badge').forEach(badge => {
            badge.style.cssText = 'display:flex; align-items:center; justify-content:center; background:var(--dash-bg, #0f172a); border:1px solid var(--dash-border, #334155); border-radius:8px; padding:6px; transition:all 0.2s;';
        });

        // Apply Avatar Frame
        if (currentEffects.avatar_frame) {
            previewAvatar.style.cssText += currentEffects.avatar_frame;
        }
        
        // Apply Name Effect
        if (currentEffects.name_effect) {
            previewName.style.cssText += currentEffects.name_effect;
        }

        // Apply Profile Effect
        if (currentEffects.profile_effect) {
            let eff = currentEffects.profile_effect;
            if (/^https?:\/\//i.test(eff)) {
                if(/\.mp4($|\?)/i.test(eff)) {
                    previewEffect.innerHTML = `<video src="${eff}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover; opacity:0.7;"></video>`;
                } else {
                    previewEffect.innerHTML = `<img src="${eff}" style="width:100%; height:100%; object-fit:cover; opacity:0.7;">`;
                }
            } else if (/\.(gif|jpg|jpeg|png|mp4)$/i.test(eff)) {
                if(/\.mp4$/i.test(eff)) {
                    previewEffect.innerHTML = `<video src="src/img/${eff}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover; opacity:0.7;"></video>`;
                } else {
                    previewEffect.innerHTML = `<img src="src/img/${eff}" style="width:100%; height:100%; object-fit:cover; opacity:0.7;">`;
                }
            } else {
                previewEffect.className = 'profile-effect-' + eff.replace(/[^a-zA-Z0-9_-]/g, '');
            }
        }
        
        // Apply Card Border & Cursor
        let cardBase = 'background:var(--dash-sidebar, #1e293b); border-radius:16px; width:100%; max-width:450px; overflow:hidden; position:relative; transition:all 0.3s ease; ';
        
        if (currentEffects.card_border) cardBase += currentEffects.card_border + ' ';
        if (currentEffects.cursor_effect) cardBase += currentEffects.cursor_effect + ' ';
        
        previewCard.style.cssText = cardBase;
        
        // Handle Entrance Animation (force reflow so it replays)
        if (currentEffects.entrance_anim) {
            previewCard.style.animation = 'none';
            void previewCard.offsetWidth; // trigger reflow
            previewCard.style.cssText = cardBase + ' ' + currentEffects.entrance_anim;
        }
        
        // Apply Card Background
        if (currentEffects.card_background && cardBgOverlay) {
            cardBgOverlay.style.cssText += currentEffects.card_background;
        }

        // Apply Badge Effect
        if (currentEffects.badge_effect) {
            document.querySelectorAll('.preview-badge').forEach(badge => {
                badge.style.cssText += ' ' + currentEffects.badge_effect;
            });
        }

        // Apply Banner GIF
        if (currentEffects.banner_gif) {
            let ban = currentEffects.banner_gif;
            if (/^https?:\/\//i.test(ban)) {
                if(/\.mp4($|\?)/i.test(ban)) {
                    previewVideoBg.src = ban;
                    previewVideoBg.style.display = 'block';
                } else {
                    previewBanner.style.backgroundImage = 'url("' + ban + '")';
                    previewBanner.style.backgroundSize = 'cover';
                }
            } else {
                if(/\.mp4($|\?)/i.test(ban)) {
                    previewVideoBg.src = 'src/img/' + ban;
                    previewVideoBg.style.display = 'block';
                } else {
                    previewBanner.style.backgroundImage = 'url("src/img/' + ban + '")';
                    previewBanner.style.backgroundSize = 'cover';
                }
            }
        }
    }


    window.terapkanEfek = function(type, value, btnId) {
        if (currentEffects[type] === value) {
            // Batal Terapkan
            applyEffect(type, '');
            let btn = document.getElementById(btnId);
            if (btn) {
                btn.innerText = 'Terapkan';
                btn.style.background = 'rgba(16, 185, 129, 0.1)';
                btn.style.color = '#10b981';
            }
        } else {
            // Terapkan Baru
            applyEffect(type, value);
            // Reset semua tombol di tipe yang sama
            document.querySelectorAll(`button[id^="btn-apply-${type}-"]`).forEach(el => {
                el.innerText = 'Terapkan';
                el.style.background = 'rgba(16, 185, 129, 0.1)';
                el.style.color = '#10b981';
            });
            // Ubah tombol yang di-klik menjadi Batal Terapkan
            let btn = document.getElementById(btnId);
            if (btn) {
                btn.innerText = 'Batal Terapkan';
                btn.style.background = 'rgba(245, 158, 11, 0.1)';
                btn.style.color = '#f59e0b';
            }
        }
        window.scrollTo({ top: 0, behavior: 'smooth' }); // Scroll ke atas agar preview terlihat
    };

    valueTextarea.addEventListener('input', updatePreview);

    // Initial run
    switchBuilderType();
    updatePreview();
});
</script>
