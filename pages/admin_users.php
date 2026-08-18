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

// Handle Action (Ubah Role / Hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? 0;
    
    // Jangan izinkan admin mengubah dirinya sendiri dari form ini untuk mencegah terkunci
    if ($user_id != $_SESSION['user_id'] && $user_id != 0) {
        if ($action === 'set_admin') {
            $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
            $stmt->execute([$user_id]);
        } elseif ($action === 'set_user') {
            $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?");
            $stmt->execute([$user_id]);
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
        }
    }

    if ($action === 'add_user') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';
        
        if ($name && $email && $password) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() == 0) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password, $role]);
            }
        }
    }
    // Refresh halaman untuk melihat perubahan
    header("Location: index.php?page=admin_users");
    exit();
}

// Ambil data semua pengguna
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$all_users = $stmt->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Manage Users (RBAC)</h1>
            <p style="color: var(--dash-text-muted);">Kelola data pengguna, hak akses admin, dan tingkat keamanan platform.</p>
        </div>
        <button onclick="document.getElementById('addUserModal').style.display='flex'" style="background: var(--dash-primary); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah User Manual
        </button>
    </div>

    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: rgba(0,0,0,0.02); border-bottom: 1px solid var(--dash-border);">
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase;">User</th>
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase;">Email</th>
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase;">Role</th>
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase;">Kategori</th>
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase;">Bergabung</th>
                        <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_users as $u): ?>
                    <?php 
                        $user_age = calculateAge($u['birth_date'] ?? '');
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
                                        ID: <?php echo $u['id']; ?> | 
                                        Umur: <?php echo $u['birth_date'] ? $user_age . ' Tahun' : 'Belum disetel'; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 1rem 1.5rem; color: var(--dash-text);"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td style="padding: 1rem 1.5rem;">
                            <?php if ($u['role'] === 'admin'): ?>
                                <span style="background: rgba(99, 102, 241, 0.1); color: #6366f1; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Admin</span>
                            <?php else: ?>
                                <span style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">User</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 1rem 1.5rem; color: var(--dash-text);">
                            <div style="font-size: 0.85rem; font-weight: 600;">
                                <?php echo implode(', ', $active_access); ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--dash-text-muted);">
                                Preferensi: <?php echo htmlspecialchars($u['category'] ?? '-'); ?>
                            </div>
                        </td>
                        <td style="padding: 1rem 1.5rem; color: var(--dash-text-muted); font-size: 0.9rem;">
                            <?php echo date('d M Y', strtotime($u['created_at'])); ?>
                        </td>
                        <td style="padding: 1rem 1.5rem;">
                            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 8px;">
                                <a href="index.php?page=admin_user_detail&id=<?php echo $u['id']; ?>" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; text-decoration: none; white-space: nowrap;">Detail</a>
                                <?php if ($u['id'] != $_SESSION['user_id']): // Jangan tampilkan tombol hapus untuk diri sendiri ?>
                                <form method="POST" action="" style="display: flex; gap: 8px; margin: 0;">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <?php if ($u['role'] === 'user'): ?>
                                        <button type="submit" name="action" value="set_admin" style="background: rgba(99, 102, 241, 0.1); color: #6366f1; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.8rem; white-space: nowrap;">Jadikan Admin</button>
                                    <?php else: ?>
                                        <button type="submit" name="action" value="set_user" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.8rem; white-space: nowrap;">Cabut Admin</button>
                                    <?php endif; ?>
                                    <button type="submit" name="action" value="delete" onclick="return confirm('Yakin ingin menghapus user ini secara permanen?');" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.8rem; white-space: nowrap;">Hapus</button>
                                </form>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: var(--dash-text-muted); font-style: italic; white-space: nowrap;">(Anda)</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div id="addUserModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: var(--dash-bg, #fff); width: 100%; max-width: 500px; border-radius: 16px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); position: relative;">
        <button onclick="document.getElementById('addUserModal').style.display='none'" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--dash-text-muted);">&times;</button>
        
        <h2 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--dash-text); font-size: 1.5rem;">Tambah User Baru</h2>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_user">
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dash-text); font-size: 0.9rem;">Nama Lengkap</label>
                <input type="text" name="name" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; font-family: inherit; box-sizing: border-box; background: var(--dash-bg); color: var(--dash-text);">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dash-text); font-size: 0.9rem;">Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; font-family: inherit; box-sizing: border-box; background: var(--dash-bg); color: var(--dash-text);">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dash-text); font-size: 0.9rem;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; font-family: inherit; box-sizing: border-box; background: var(--dash-bg); color: var(--dash-text);">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dash-text); font-size: 0.9rem;">Role</label>
                <select name="role" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; font-family: inherit; box-sizing: border-box; background: var(--dash-bg); color: var(--dash-text);">
                    <option value="user">User Biasa</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" style="padding: 0.75rem 1.5rem; border: 1px solid var(--dash-border); background: transparent; border-radius: 8px; font-weight: 600; cursor: pointer; color: var(--dash-text);">Batal</button>
                <button type="submit" style="padding: 0.75rem 1.5rem; border: none; background: var(--dash-primary); color: white; border-radius: 8px; font-weight: 600; cursor: pointer;">Simpan User</button>
            </div>
        </form>
    </div>
</div>
