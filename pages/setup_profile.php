<?php
// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

require_once 'config/db.php';

// Ambil data user saat ini
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Jika sudah ada tanggal lahir, langsung ke dashboard
if (!empty($user['birth_date'])) {
    header('Location: index.php?page=dashboard');
    exit();
}

// Proses Form
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $birth_date = $_POST['birth_date'] ?? '';
    
    if (empty($birth_date)) {
        $error = 'Tanggal lahir wajib diisi!';
    } else {
        // Hitung umur untuk menentukan kategori
        $birth_date_obj = new DateTime($birth_date);
        $today = new DateTime();
        $age = $today->diff($birth_date_obj)->y;
        
        $category = 'Umum';
        if ($age >= 6 && $age <= 12) {
            $category = 'SD';
        } elseif ($age >= 13 && $age <= 15) {
            $category = 'SMP';
        } elseif ($age >= 16 && $age <= 18) {
            $category = 'SMA';
        }
        
        // Update database
        $stmt = $pdo->prepare("UPDATE users SET birth_date = ?, category = ? WHERE id = ?");
        if ($stmt->execute([$birth_date, $category, $user['id']])) {
            $_SESSION['user_category'] = $category;
            header('Location: index.php?page=dashboard');
            exit();
        } else {
            $error = 'Gagal menyimpan data.';
        }
    }
}
?>

<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--bg-main);">
    <div style="background: var(--bg-card); padding: 3rem; border-radius: 24px; box-shadow: var(--shadow-lg); max-width: 500px; width: 100%; border: 1px solid var(--border);">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem auto; border-radius: 50%; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="40"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <h2 style="font-size: 1.75rem; margin-bottom: 0.5rem; color: var(--text-main);">Kapan ulang tahunmu?</h2>
            <p style="color: var(--text-muted);">Kami butuh tanggal lahirmu untuk menyesuaikan materi belajar yang paling pas buat kamu (SD/SMP/SMA/Umum).</p>
        </div>

        <?php if ($error): ?>
            <div style="background: #fef2f2; color: #ef4444; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: center; font-weight: 500; font-size: 0.9rem;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem; color: var(--text-main);">Tanggal Lahir</label>
                <input type="date" name="birth_date" required style="width: 100%; padding: 1rem; border: 1px solid var(--border); border-radius: 12px; font-size: 1rem; outline: none; background: transparent; color: var(--text-main);">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem; justify-content: center; border: none; cursor: pointer;">
                Lanjutkan ke Dashboard &rarr;
            </button>
        </form>
    </div>
</div>
