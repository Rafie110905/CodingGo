<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit();
}

require_once 'config/db.php';

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $birth_date = $_POST['birth_date'] ?? null;
    $category = $_POST['category'] ?? 'Umum';
    $picture = trim($_POST['picture'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($name) || empty($email)) {
        $error_msg = "Nama dan Email tidak boleh kosong.";
    } else {
        // Cek apakah email sudah dipakai orang lain
        $stmt_chk = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt_chk->execute([$email, $user_id]);
        if ($stmt_chk->fetch()) {
            $error_msg = "Email tersebut sudah digunakan oleh akun lain.";
        } else {
            // Update DB
            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt_upd = $pdo->prepare("UPDATE users SET name=?, email=?, birth_date=?, category=?, picture=?, password=? WHERE id=?");
                $stmt_upd->execute([$name, $email, $birth_date, $category, $picture, $hashed, $user_id]);
            } else {
                $stmt_upd = $pdo->prepare("UPDATE users SET name=?, email=?, birth_date=?, category=?, picture=? WHERE id=?");
                $stmt_upd->execute([$name, $email, $birth_date, $category, $picture, $user_id]);
            }

            // Handle Gamification unlocks if submitted
            if (isset($_POST['profile_color']) && ($user['total_badges'] >= 3 || $_SESSION['user_role'] === 'admin')) {
                $p_color = $_POST['profile_color'];
                $pdo->prepare("UPDATE users SET profile_color=? WHERE id=?")->execute([$p_color, $user_id]);
            }
            if (isset($_POST['profile_title']) && ($user['total_badges'] >= 5 || $_SESSION['user_role'] === 'admin')) {
                $p_title = trim($_POST['profile_title']);
                $pdo->prepare("UPDATE users SET profile_title=? WHERE id=?")->execute([$p_title, $user_id]);
            }
            
            // Update session if needed
            $_SESSION['user_name'] = $name;
            if (!empty($picture)) {
                $_SESSION['user_picture'] = $picture;
            }
            
            $success_msg = "Profil berhasil diperbarui!";
            
            // Reload user data
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        }
    }
}
?>

<div class="dash-left" style="grid-column: 1 / -1; max-width: 800px; margin: 0 auto; width: 100%;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Pengaturan Profil</h1>
            <p style="color: var(--dash-text-muted);">Kelola informasi pribadi dan pengaturan akun Anda.</p>
        </div>
    </div>

    <?php if ($success_msg): ?>
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600;">
            <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error_msg): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600;">
            <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem;">
        <form method="POST" action="">
            
            <div style="display:flex; align-items:center; gap: 2rem; margin-bottom: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid var(--dash-border);">
                <?php if (!empty($user['picture'])): ?>
                    <img src="<?php echo htmlspecialchars($user['picture']); ?>" alt="Profile" style="width: 100px; height: 100px; border-radius: 50%; border:3px solid var(--dash-primary); object-fit:cover;">
                <?php else: ?>
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--dash-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: bold; border:3px solid var(--dash-border);">
                        <?php echo substr(htmlspecialchars($user['name']), 0, 1); ?>
                    </div>
                <?php endif; ?>
                <div style="flex:1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">URL Foto Profil Baru (Opsional)</label>
                    <input type="url" name="picture" value="<?php echo htmlspecialchars($user['picture'] ?? ''); ?>" placeholder="https://..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    <p style="font-size:0.75rem; color:var(--dash-text-muted); margin-top:0.5rem;">Kosongkan jika tidak ingin mengubah foto profil.</p>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Nama Lengkap</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Alamat Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="<?php echo htmlspecialchars($user['birth_date'] ?? ''); ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; color-scheme: dark;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Kategori Pendidikan</label>
                    <select name="category" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                        <option value="SD" <?php echo ($user['category'] === 'SD') ? 'selected' : ''; ?>>SD Sederajat</option>
                        <option value="SMP" <?php echo ($user['category'] === 'SMP') ? 'selected' : ''; ?>>SMP Sederajat</option>
                        <option value="SMA" <?php echo ($user['category'] === 'SMA') ? 'selected' : ''; ?>>SMA Sederajat</option>
                        <option value="Umum" <?php echo ($user['category'] === 'Umum') ? 'selected' : ''; ?>>Umum / Mahasiswa</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 2.5rem; padding-top: 1.5rem; border-top: 1px solid var(--dash-border);">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Ubah Password (Opsional)</label>
                <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah password" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
            </div>

            <div style="margin-bottom: 2.5rem; padding: 2rem; background:linear-gradient(135deg, rgba(67, 97, 238, 0.05) 0%, rgba(67, 97, 238, 0.15) 100%); border-radius: 16px; border: 1px solid rgba(67, 97, 238, 0.2);">
                <h3 style="margin-top:0; color:var(--dash-primary); display:flex; align-items:center; gap:8px;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                    Kustomisasi Profil (Gamifikasi)
                </h3>
                <p style="font-size:0.85rem; color:var(--dash-text-muted); margin-bottom:1.5rem;">Selesaikan course dan dapatkan lebih banyak Badge untuk membuka fitur eksklusif ini! (Badge Anda saat ini: <strong><?php echo $user['total_badges']; ?></strong>)</p>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div style="<?php echo ($user['total_badges'] < 3 && $_SESSION['user_role'] !== 'admin') ? 'opacity:0.5; pointer-events:none;' : ''; ?>">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                            Warna Border Profil
                            <?php if($user['total_badges'] < 3 && $_SESSION['user_role'] !== 'admin'): ?> <span style="color:#ef4444; font-size:0.7rem;">(Butuh 3 Badge)</span> <?php else: ?> <span style="color:#10b981; font-size:0.7rem;">(Unlocked!)</span> <?php endif; ?>
                        </label>
                        <input type="color" name="profile_color" value="<?php echo htmlspecialchars($user['profile_color'] ?? '#4361ee'); ?>" style="width: 100%; height:45px; padding: 0.25rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); cursor:pointer;">
                    </div>
                    
                    <div style="<?php echo ($user['total_badges'] < 5 && $_SESSION['user_role'] !== 'admin') ? 'opacity:0.5; pointer-events:none;' : ''; ?>">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                            Gelar Profil Khusus
                            <?php if($user['total_badges'] < 5 && $_SESSION['user_role'] !== 'admin'): ?> <span style="color:#ef4444; font-size:0.7rem;">(Butuh 5 Badge)</span> <?php else: ?> <span style="color:#10b981; font-size:0.7rem;">(Unlocked!)</span> <?php endif; ?>
                        </label>
                        <input type="text" name="profile_title" value="<?php echo htmlspecialchars($user['profile_title'] ?? 'Novice Coder'); ?>" placeholder="Contoh: Algo Master" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content: flex-end; gap: 1rem;">
                <button type="submit" style="background: var(--dash-primary); color: white; border: none; padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.2s;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
