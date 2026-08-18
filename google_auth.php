<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['credential'])) {
    $jwt = $_POST['credential'];
    
    // Verifikasi token menggunakan endpoint tokeninfo Google
    $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $jwt;
    
    // Gunakan file_get_contents untuk mengambil data
    $response = @file_get_contents($url);
    
    if ($response) {
        $payload = json_decode($response, true);
        
        // Pastikan token valid (tidak ada error dan audience cocok dengan Client ID jika di-cek, 
        // tapi di versi murni ini kita percayakan validasi ke endpoint tokeninfo Google)
        if (isset($payload['email'])) {
            // Koneksi database
            require_once 'config/db.php';
            
            $google_id = $payload['sub']; // Unique Google ID
            $name = $payload['name'];
            $email = $payload['email'];
            $picture = $payload['picture'] ?? '';
            
            // Cek apakah user sudah ada
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            // Determine action (login vs register)
            $action = $_GET['action'] ?? '';
            
            if (!$user) {
                // Jika sedang login tapi belum terdaftar, arahkan ke login dengan error
                if ($action === 'login') {
                    header('Location: login.php?error=google_not_registered');
                    exit();
                }

                // Cek apakah pendaftaran Google diizinkan
                $stmt_reg = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'enable_registration_google'");
                $stmt_reg->execute();
                $reg_setting = $stmt_reg->fetch();
                if ($reg_setting && $reg_setting['setting_value'] === '0') {
                    header('Location: login.php?error=registration_closed');
                    exit();
                }

                // Register user baru
                $stmt = $pdo->prepare("INSERT INTO users (google_id, name, email, picture) VALUES (?, ?, ?, ?)");
                $stmt->execute([$google_id, $name, $email, $picture]);
                
                // Ambil data user yang baru dibuat
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
            } else {
                // Update last login & picture
                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW(), picture = ? WHERE id = ?");
                $stmt->execute([$picture, $user['id']]);
            }

            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_picture'] = $user['picture'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_category'] = $user['category'];
            
            // Redirect ke setup profil jika belum ada tanggal lahir
            if (empty($user['birth_date'])) {
                header('Location: index.php?page=setup_profile');
                exit();
            }
            
            // Redirect ke dashboard
            header('Location: index.php?page=dashboard');
            exit();
        }
    }
}

// Jika gagal atau bukan POST, kembalikan ke login
header('Location: login.php?error=google_auth_failed');
exit();
?>
