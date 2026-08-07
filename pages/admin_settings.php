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

// Fungsi Helper untuk mengambil setting
function getSetting($pdo, $key, $default = '') {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Daftar keys yang diizinkan untuk disimpan
    $allowed_keys = ['app_name', 'contact_email', 'maintenance_mode', 'enable_registration_google', 'enable_registration_manual'];
    
    foreach ($allowed_keys as $key) {
        $val = $_POST[$key] ?? '';
        
        // Cek apakah key sudah ada
        $stmt = $pdo->prepare("SELECT 1 FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        if ($stmt->fetch()) {
            $update = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $update->execute([$val, $key]);
        } else {
            $insert = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $insert->execute([$key, $val]);
        }
    }
    
    $success_message = "Pengaturan berhasil disimpan!";
}

// Ambil nilai saat ini
$app_name = getSetting($pdo, 'app_name', 'CodingGo');
$contact_email = getSetting($pdo, 'contact_email', 'support@codinggo.com');
$maintenance_mode = getSetting($pdo, 'maintenance_mode', '0');
$enable_registration_google = getSetting($pdo, 'enable_registration_google', '1');
$enable_registration_manual = getSetting($pdo, 'enable_registration_manual', '1');
?>

<div class="dash-left" style="grid-column: 1 / -1; max-width: 800px; margin: 0 auto;">
    <div class="section-header" style="margin-bottom: 2rem; border-bottom: 1px solid var(--dash-border); padding-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Admin Settings</h1>
            <p style="color: var(--dash-text-muted);">Kelola konfigurasi global untuk seluruh platform CodingGo.</p>
        </div>
    </div>

    <?php if (isset($success_message)): ?>
        <div style="background: rgba(34, 197, 94, 0.1); color: #16a34a; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem; margin-bottom: 2rem;">
            <h3 style="color: var(--dash-text); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.25rem;">Informasi Dasar</h3>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: var(--dash-text);">Nama Aplikasi</label>
                <input type="text" name="app_name" value="<?php echo htmlspecialchars($app_name); ?>" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; font-size: 1rem;">
                <p style="font-size: 0.75rem; color: var(--dash-text-muted); margin-top: 0.5rem;">Nama ini akan ditampilkan pada tab browser dan di header email sistem.</p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: var(--dash-text);">Email Dukungan (Support)</label>
                <input type="email" name="contact_email" value="<?php echo htmlspecialchars($contact_email); ?>" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; font-size: 1rem;">
            </div>
        </div>

        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem; margin-bottom: 2rem;">
            <h3 style="color: var(--dash-text); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.25rem;">Pengaturan Sistem</h3>
            
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid var(--dash-border);">
                <div>
                    <div style="font-weight: 600; color: var(--dash-text); margin-bottom: 4px;">Pendaftaran Pengguna Baru (Google)</div>
                    <div style="font-size: 0.8rem; color: var(--dash-text-muted);">Izinkan pengguna baru untuk mendaftar via Google.</div>
                </div>
                <label style="display:flex; align-items:center; cursor:pointer;">
                    <select name="enable_registration_google" style="padding: 0.5rem; border: 1px solid var(--dash-border); border-radius: 6px; background: var(--dash-bg); color: var(--dash-text);">
                        <option value="1" <?php echo $enable_registration_google === '1' ? 'selected' : ''; ?>>Diizinkan</option>
                        <option value="0" <?php echo $enable_registration_google === '0' ? 'selected' : ''; ?>>Ditutup</option>
                    </select>
                </label>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid var(--dash-border);">
                <div>
                    <div style="font-weight: 600; color: var(--dash-text); margin-bottom: 4px;">Pendaftaran Pengguna Baru (Manual)</div>
                    <div style="font-size: 0.8rem; color: var(--dash-text-muted);">Izinkan pengguna baru untuk mendaftar menggunakan Email/Password.</div>
                </div>
                <label style="display:flex; align-items:center; cursor:pointer;">
                    <select name="enable_registration_manual" style="padding: 0.5rem; border: 1px solid var(--dash-border); border-radius: 6px; background: var(--dash-bg); color: var(--dash-text);">
                        <option value="1" <?php echo $enable_registration_manual === '1' ? 'selected' : ''; ?>>Diizinkan</option>
                        <option value="0" <?php echo $enable_registration_manual === '0' ? 'selected' : ''; ?>>Ditutup</option>
                    </select>
                </label>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 0;">
                <div>
                    <div style="font-weight: 600; color: #ef4444; margin-bottom: 4px;">Mode Perbaikan (Maintenance)</div>
                    <div style="font-size: 0.8rem; color: var(--dash-text-muted);">Hanya admin yang bisa mengakses platform saat mode ini aktif.</div>
                </div>
                <label style="display:flex; align-items:center; cursor:pointer;">
                    <select name="maintenance_mode" style="padding: 0.5rem; border: 1px solid var(--dash-border); border-radius: 6px; background: var(--dash-bg); color: var(--dash-text);">
                        <option value="0" <?php echo $maintenance_mode === '0' ? 'selected' : ''; ?>>Nonaktif</option>
                        <option value="1" <?php echo $maintenance_mode === '1' ? 'selected' : ''; ?>>Aktif</option>
                    </select>
                </label>
            </div>
        </div>

        <div style="text-align: right;">
            <button type="submit" style="background: var(--dash-primary); color: white; border: none; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.2s;">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
