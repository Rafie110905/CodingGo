<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

// Gamification perks removed (moved to user_customization.php)

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

            // Gamification logic removed (moved to user_customization.php)
            
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

<style>
    /* Override khusus halaman pengaturan profil: Ubah menjadi scroll body normal agar background putih tidak pernah terpotong */
    html, body {
        height: auto !important;
        overflow-y: auto !important;
    }
    .dashboard-layout {
        height: auto !important;
        min-height: 100vh !important;
        overflow: visible !important;
    }
    .dash-sidebar {
        height: auto !important;
        min-height: 100vh !important;
    }
    .main-wrapper {
        background: var(--dash-sidebar) !important;
        height: auto !important;
        min-height: 100vh !important;
        overflow-y: visible !important;
    }
    .dash-topbar {
        background: var(--dash-sidebar) !important;
    }
    .main-content {
        background: var(--dash-sidebar) !important;
        height: auto !important;
        min-height: calc(100vh - 80px) !important;
        padding: 0 !important;
        display: block !important;
    }
    .dash-left {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 2rem 4rem 2rem;
    }
</style>

<div class="dash-left">
    <div class="section-header" style="margin-bottom: 2rem; flex-shrink: 0;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Pengaturan Profil</h1>
            <p style="color: var(--dash-text-muted);">Kelola informasi pribadi dan pengaturan akun Anda.</p>
        </div>
    </div>

    <?php if ($success_msg): ?>
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600; flex-shrink: 0;">
            <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error_msg): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600; flex-shrink: 0;">
            <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <div>
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

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                <button type="submit" style="padding: 0.75rem 2rem; background: var(--dash-primary); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>