<?php
session_start();

// Proteksi: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Akses ditolak']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    
    // Validasi error upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Gagal mengunggah file. Kode error: ' . $file['error']]);
        exit();
    }
    
    // Direktori upload
    $upload_dir = '../uploads/media/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Ambil info file
    $file_info = pathinfo($file['name']);
    $ext = strtolower($file_info['extension']);
    
    // Daftar ekstensi yang diperbolehkan (aman)
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'pdf', 'zip', 'rar', 'txt', 'csv'];
    
    if (!in_array($ext, $allowed_ext)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Format file tidak diizinkan.']);
        exit();
    }
    
    // Buat nama file unik
    $filename = uniqid('media_') . '.' . $ext;
    $target_path = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // Return JSON sesuai format yang diharapkan EasyMDE
        // Asumsi aplikasi berjalan di root web server (misal: http://localhost/CodingGo/)
        // Kita menggunakan path relatif dari root aplikasi
        
        // Coba deteksi base URL secara dinamis
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        
        // Karena script ini ada di folder /api/, kita ambil dirname sebanyak 2 level (ke root)
        $script_dir = dirname($_SERVER['SCRIPT_NAME']); // misal: /CodingGo/api
        $base_url = $protocol . "://" . $host . dirname($script_dir); // misal: http://localhost/CodingGo
        
        if (substr($base_url, -1) !== '/') {
            $base_url .= '/';
        }
        
        $file_url = $base_url . 'uploads/media/' . $filename;
        
        header('Content-Type: application/json');
        echo json_encode([
            'data' => [
                'filePath' => $file_url
            ]
        ]);
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Gagal menyimpan file ke direktori uploads.']);
        exit();
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Permintaan tidak valid.']);
    exit();
}
?>
