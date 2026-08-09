<?php
ob_start();
session_start();

require_once 'config/db.php';
require_once 'includes/auth_helpers.php';

// Cek Maintenance Mode
$is_maintenance = false;
try {
    $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'maintenance_mode'");
    $res = $stmt->fetch();
    if ($res && $res['setting_value'] === '1') {
        $is_maintenance = true;
    }
} catch (PDOException $e) {
    // Abaikan jika tabel belum dibuat
}

$user_role = $_SESSION['user_role'] ?? 'user';

// Jika maintenance aktif dan bukan admin, paksa ke halaman maintenance
if ($is_maintenance && $user_role !== 'admin') {
    $page = 'maintenance';
} else {
    $page = $_GET['page'] ?? 'landing';
}

// Routing map
$public_pages = ['landing', 'setup_profile', 'maintenance', 'course_detail', 'course_learn', 'course_exam', 'certificate_view'];
$dashboard_pages = [
    'dashboard', 
    'course_list',
    'user_exams',
    'user_settings',
    'community',
    'community_post',
    'community_edit',
    'leaderboard',
    'champions',
    'sertifikat',
    'admin_users',
    'admin_user_detail',
    'admin_settings', 
    'admin_courses', 
    'admin_courses_edit',
    'admin_modules',
    'admin_modules_edit',
    'admin_exams',
    'admin_badges',
    'admin_questions',
    'admin_championship', 
    'admin_community', 
    'admin_broadcast'
];

if (in_array($page, $dashboard_pages)) {
    // PROTEKSI DASHBOARD
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }
    
    // HEADER DASHBOARD
    include 'includes/head.php';
    echo '<div class="dashboard-layout">';
    include 'includes/sidebar.php';
    echo '<div class="main-wrapper">';
    include 'includes/topbar.php';
    echo '<main class="main-content">';
    include 'pages/' . $page . '.php';
    echo '</main>';
    echo '</div>'; // end main-wrapper
    echo '</div>'; // end dashboard-layout
    include 'includes/footer_dash.php';
    
} elseif (in_array($page, $public_pages)) {
    // HALAMAN PUBLIK
    include 'includes/head.php';
    include 'includes/nav_public.php';
    include 'pages/' . $page . '.php';
    include 'includes/footer.php';
} else {
    // 404
    echo "<h1>404 Not Found</h1>";
}
?>