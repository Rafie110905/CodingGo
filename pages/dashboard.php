<?php
require_once 'config/db.php';
require_once 'includes/materi_icons.php';

// Ambil data user terbaru dari DB
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id'] ?? 0]);
$user_db = $stmt->fetch();

if (!$user_db) {
    // Fallback jika tidak ada di DB (harus login lagi)
    header('Location: login.php');
    exit();
}

// Redirect jika belum punya tanggal lahir
if (empty($user_db['birth_date'])) {
    header('Location: index.php?page=setup_profile');
    exit();
}

$short_name = explode(' ', htmlspecialchars($user_db['name']))[0];
$xp = $user_db['xp_points'];
$streak = $user_db['streak_days'];
$badges = $user_db['total_badges'];

// Hitung Kelas Aktif (distinct course_id dari materi yang pernah diakses)
$stmt_ac = $pdo->prepare("SELECT COUNT(DISTINCT m.course_id) as total FROM user_progress up JOIN materials m ON up.material_id = m.id WHERE up.user_id = ?");
$stmt_ac->execute([$_SESSION['user_id']]);
$active_courses = $stmt_ac->fetch()['total'] ?? 0;

// Hitung Materi Selesai
$stmt_mc = $pdo->prepare("SELECT COUNT(*) as total FROM user_progress WHERE user_id = ? AND status = 'completed'");
$stmt_mc->execute([$_SESSION['user_id']]);
$completed_materials = $stmt_mc->fetch()['total'] ?? 0;

// Hitung Progress Mingguan (7 Hari Terakhir)
$stmt_week = $pdo->prepare("SELECT COALESCE(SUM(time_spent), 0) FROM user_learning_time WHERE user_id = ? AND log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$stmt_week->execute([$_SESSION['user_id']]);
$weekly_minutes = (int)$stmt_week->fetchColumn();
$weekly_target = $user_db['weekly_target'] ?? 600; // Target dinamis dari database (default 600 menit)
$weekly_progress_percent = min(100, round(($weekly_minutes / $weekly_target) * 100));
$stroke_offset = 283 - (283 * ($weekly_progress_percent / 100));
$remaining_minutes = max(0, $weekly_target - $weekly_minutes);
$rem_hours = floor($remaining_minutes / 60);
$rem_mins = $remaining_minutes % 60;

// Ambil Jadwal Mendatang (Championships)
$stmt_upc = $pdo->query("SELECT title, start_date, status FROM championships WHERE status IN ('active', 'upcoming') ORDER BY start_date ASC LIMIT 2");
$upcoming_events = $stmt_upc->fetchAll();

// Ambil Broadcast Informasi Terbaru
$stmt_b = $pdo->query("SELECT title, type, created_at FROM broadcasts WHERE is_active = 1 ORDER BY created_at DESC LIMIT 2");
$recent_broadcasts = $stmt_b->fetchAll();

// Gradient warna untuk kartu kelas yang tidak punya thumbnail (konsisten per judul)
$course_gradients = [
    'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)',
    'linear-gradient(135deg, #0f766e 0%, #14b8a6 100%)',
    'linear-gradient(135deg, #4338ca 0%, #6366f1 100%)',
    'linear-gradient(135deg, #b45309 0%, #f59e0b 100%)',
    'linear-gradient(135deg, #be123c 0%, #e11d48 100%)',
    'linear-gradient(135deg, #166534 0%, #22c55e 100%)',
];
function courseCardBg($course, $gradients) {
    if (!empty($course['thumbnail'])) {
        return 'url(' . htmlspecialchars($course['thumbnail']) . ') center/cover';
    }
    $idx = abs(crc32($course['title'])) % count($gradients);
    return $gradients[$idx];
}

// === Rekomendasi Untukmu & Lanjutkan Belajar: Ambil hak akses kategori dulu ===
$allowed_categories = getUserAllowedCategories($user_db);

// === Lanjutkan Belajar: kelas yang sudah pernah diakses user, urut dari yang terakhir disentuh ===
$continue_courses = [];
if (!empty($allowed_categories)) {
    $placeholders = implode(',', array_fill(0, count($allowed_categories), '?'));
    $stmt_cont = $pdo->prepare("
        SELECT c.id, c.title, c.category, c.description, c.thumbnail, c.theme_color,
               (SELECT COUNT(*) FROM materials m2 WHERE m2.course_id = c.id) AS total_materials,
               (SELECT COUNT(*) FROM user_progress up2
                    JOIN materials m3 ON up2.material_id = m3.id
                    WHERE m3.course_id = c.id AND up2.user_id = ? AND up2.status = 'completed') AS completed_materials,
               MAX(up.id) AS max_id,
               MAX(up.completed_at) AS max_completed_at
        FROM courses c
        JOIN materials m ON m.course_id = c.id
        JOIN user_progress up ON up.material_id = m.id AND up.user_id = ?
        WHERE c.category IN ($placeholders)
        GROUP BY c.id, c.title, c.category, c.description, c.thumbnail, c.theme_color
        HAVING completed_materials < total_materials OR total_materials = 0
        ORDER BY max_completed_at DESC, max_id DESC
        LIMIT 2
    ");
    $params = array_merge([$_SESSION['user_id'], $_SESSION['user_id']], $allowed_categories);
    $stmt_cont->execute($params);
    $continue_courses = $stmt_cont->fetchAll();
}

// === Rekomendasi Untukmu: Top Rated Courses ===
$recommended_courses = [];
if (!empty($allowed_categories)) {
    $placeholders = implode(',', array_fill(0, count($allowed_categories), '?'));
    $stmt_rec = $pdo->prepare("
        SELECT c.*,
               (SELECT AVG(rating) FROM course_ratings cr WHERE cr.course_id = c.id) AS avg_rating
        FROM courses c
        WHERE c.category IN ($placeholders)
        ORDER BY avg_rating IS NULL ASC, avg_rating DESC, c.created_at DESC
        LIMIT 4
    ");
    $stmt_rec->execute($allowed_categories);
    $recommended_courses = $stmt_rec->fetchAll();
}

// Ambil rata-rata rating & jumlah rating untuk kelas yang tampil (continue + rekomendasi) sekaligus
$rating_map = [];
$shown_course_ids = array_merge(
    array_column($continue_courses, 'id'),
    array_column($recommended_courses, 'id')
);
if (!empty($shown_course_ids)) {
    $ids_placeholders = implode(',', array_fill(0, count($shown_course_ids), '?'));
    $stmt_ratings = $pdo->prepare("
        SELECT course_id, AVG(rating) as avg_rating, COUNT(*) as total_ratings
        FROM course_ratings
        WHERE course_id IN ($ids_placeholders)
        GROUP BY course_id
    ");
    $stmt_ratings->execute($shown_course_ids);
    foreach ($stmt_ratings->fetchAll() as $row) {
        $rating_map[$row['course_id']] = [
            'avg' => round($row['avg_rating'], 1),
            'count' => (int)$row['total_ratings'],
        ];
    }
}
?>

<!-- Dashboard Main Grid -->
    <div class="dash-left">
        <div class="dash-welcome">
            <h1>Selamat datang kembali, <?php echo $short_name; ?>! 👋</h1>
            <p>Terus belajar dan tingkatkan skill coding-mu hari ini.</p>
        </div>

    <div class="stats-grid">
        <a href="index.php?page=my_achievements&tab=ongoing" style="text-decoration:none; color:inherit; display:block;">
            <div class="stat-card" style="transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 20px -8px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="width:36px; height:36px; background:#eff6ff; color:#3b82f6; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                </div>
                <div class="stat-val"><?php echo $active_courses; ?></div>
                <div class="stat-label">Kelas Berjalan</div>
            </div>
        </a>

        <a href="index.php?page=my_achievements&tab=completed" style="text-decoration:none; color:inherit; display:block;">
            <div class="stat-card" style="transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 20px -8px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="width:36px; height:36px; background:#f0fdf4; color:#22c55e; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div class="stat-val"><?php echo $completed_materials; ?></div>
                <div class="stat-label">Materi Selesai</div>
            </div>
        </a>

        <!-- XP Points (Tidak di-link) -->
        <div class="stat-card" style="border-top:4px solid var(--dash-warning);">
            <div style="width:40px; height:40px; background:rgba(247, 37, 133, 0.1); color:var(--dash-warning); border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:0.5rem;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
            </div>
            <div class="stat-val"><?php echo number_format($xp); ?></div>
            <div class="stat-label">XP Points <span style="color:var(--dash-text-muted); font-size:0.7rem; margin-left:4px;">Level <?php echo floor($xp / 100) + 1; ?></span></div>
        </div>

        <!-- Badges -->
        <a href="index.php?page=my_achievements&tab=badges" style="text-decoration:none; color:inherit; display:block;">
            <div class="stat-card" style="border-top:4px solid #f59e0b; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 20px -8px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                <div style="width:40px; height:40px; background:rgba(245, 158, 11, 0.1); color:#f59e0b; border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:0.5rem;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                </div>
                <div class="stat-val"><?php echo number_format($badges); ?></div>
                <div class="stat-label">Badges <span style="color:var(--dash-text-muted); font-size:0.7rem; margin-left:4px;">Koleksi</span></div>
            </div>
        </a>
    </div> <!-- End stats-grid -->

    <!-- Lanjutkan Belajar -->
    <div class="section-header">
                <h2>Lanjutkan Belajar</h2>
                <a href="index.php?page=course_list">Lihat Semua</a>
            </div>
            <?php if (empty($continue_courses)): ?>
            <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 2rem; text-align: center; border-radius: 16px; margin-bottom: 1.5rem;">
                <p style="color: var(--dash-text-muted); margin-bottom: 1rem;">Kamu belum mulai belajar kelas apa pun. Yuk, mulai kelas pertamamu!</p>
                <a href="index.php?page=course_list" style="display:inline-block; background:var(--dash-primary); color:white; padding:0.6rem 1.25rem; border-radius:8px; font-weight:600; text-decoration:none; font-size:0.85rem;">Jelajahi Kelas &rarr;</a>
            </div>
            <?php else: ?>
            <div class="courses-grid" style="grid-template-columns: repeat(2, 1fr);">
                <?php foreach ($continue_courses as $cc):
                    $percent = $cc['total_materials'] > 0 ? min(100, round(($cc['completed_materials'] / $cc['total_materials']) * 100)) : 0;
                    $bg = courseCardBg($cc, $course_gradients);
                    $cc_rating = $rating_map[$cc['id']] ?? null;
                ?>
                <a href="index.php?page=course_detail&id=<?php echo $cc['id']; ?>" style="text-decoration:none; color:inherit;">
                <div class="course-card">
                    <div class="course-img" style="background: <?php echo $bg; ?>; display:flex; align-items:center; justify-content:center;">
                        <?php if (empty($cc['thumbnail'])) echo renderCourseBadge($cc['title'], 64); ?>
                    </div>
                    <div class="course-body">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <span class="course-tag" style="margin-bottom:0;"><?php echo htmlspecialchars($cc['category']); ?></span>
                            <?php if ($cc_rating): ?>
                                <span style="font-size:0.75rem; color:#eab308; font-weight:600;">⭐ <?php echo $cc_rating['avg']; ?> (<?php echo $cc_rating['count']; ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="course-title"><?php echo htmlspecialchars($cc['title']); ?></div>
                        <div class="course-desc"><?php echo htmlspecialchars(mb_strimwidth($cc['description'], 0, 90, '...')); ?></div>
                        <div class="progress-wrap"><div class="progress-bar" style="width: <?php echo $percent; ?>%;"></div></div>
                        <div class="progress-text"><?php echo $percent; ?>%</div>
                    </div>
                </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Rekomendasi Untukmu -->
            <div class="section-header">
                <h2>Rekomendasi Untukmu</h2>
                <a href="index.php?page=course_list">Lihat Semua</a>
            </div>
            <?php if (empty($recommended_courses)): ?>
            <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 2rem; text-align: center; border-radius: 16px;">
                <p style="color: var(--dash-text-muted);">Belum ada rekomendasi kelas baru untukmu saat ini. Kamu sudah menjelajahi semua kelas yang tersedia! 🎉</p>
            </div>
            <?php else: ?>
            <div class="courses-grid" style="grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                <?php foreach ($recommended_courses as $rc):
                    $bg = courseCardBg($rc, $course_gradients);
                    $rc_rating = $rating_map[$rc['id']] ?? null;
                ?>
                <a href="index.php?page=course_detail&id=<?php echo $rc['id']; ?>" style="text-decoration:none; color:inherit;">
                <div class="course-card">
                    <div class="course-img" style="height:90px; background: <?php echo $bg; ?>; display:flex; align-items:center; justify-content:center;">
                        <?php if (empty($rc['thumbnail'])) echo renderCourseBadge($rc['title'], 56); ?>
                    </div>
                    <div class="course-body">
                        <div class="course-title" style="font-size:0.85rem;"><?php echo htmlspecialchars($rc['title']); ?></div>
                        <div class="course-desc" style="font-size:0.75rem; margin-bottom:0.5rem;"><?php echo htmlspecialchars($rc['category']); ?></div>
                        <?php if ($rc_rating): ?>
                            <div style="font-size:0.75rem; color:#eab308; font-weight:600;">⭐ <?php echo $rc_rating['avg']; ?> (<?php echo $rc_rating['count']; ?>)</div>
                        <?php else: ?>
                            <div style="font-size:0.75rem; color:var(--dash-text-muted);">Belum ada rating</div>
                        <?php endif; ?>
                    </div>
                </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div style="background:linear-gradient(90deg, #4361ee 0%, #3a0ca3 100%); border-radius:16px; padding:1.5rem 2rem; display:flex; justify-content:space-between; align-items:center; color:white;">
                <div style="display:flex; align-items:center; gap:1rem;">
                    <div style="width:48px; height:48px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                    </div>
                    <div>
                        <h3 style="margin-bottom:0.25rem;">Ikuti Challenge Mingguan</h3>
                        <p style="font-size:0.85rem; color:rgba(255,255,255,0.8);">Selesaikan challenge dan dapatkan XP ekstra!</p>
                    </div>
                </div>
                <button style="background:white; color:#3a0ca3; padding:0.75rem 1.5rem; border-radius:8px; border:none; font-weight:600; cursor:pointer;">Lihat Challenge &rarr;</button>
            </div>
        </div> <!-- End dash-left -->

        <div class="dash-right">
            <!-- Progress Mingguan -->
            <div class="widget-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <h3 class="widget-title" style="margin:0;">Progress Mingguan</h3>
                    <span style="font-size:0.75rem; color:var(--dash-text-muted); background:var(--dash-bg); padding:4px 8px; border-radius:4px;">Minggu Ini &or;</span>
                </div>
                
                <div style="display:flex; justify-content:center; margin-bottom:1.5rem; position:relative;">
                    <svg viewBox="0 0 100 100" style="width:140px; height:140px;">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="var(--dash-border)" stroke-width="10" />
                        <circle cx="50" cy="50" r="45" fill="none" stroke="var(--dash-primary)" stroke-width="10" stroke-dasharray="283" stroke-dashoffset="<?php echo $stroke_offset; ?>" stroke-linecap="round" transform="rotate(-90 50 50)" />
                    </svg>
                    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); text-align:center;">
                        <div style="font-size:1.75rem; font-weight:800; color:var(--dash-text);"><?php echo $weekly_progress_percent; ?>%</div>
                        <div style="font-size:0.6rem; color:var(--dash-text-muted); text-transform:uppercase;">Target Mingguan</div>
                    </div>
                </div>
                
                <div style="text-align:center; font-size:0.85rem; color:var(--dash-text-muted); margin-bottom:1.5rem;">
                    Target: <?php echo ($weekly_target / 60); ?> jam belajar<br>
                    <?php if($remaining_minutes <= 0): ?>
                        <span style="color:var(--dash-primary); font-weight:600;">Target Tercapai! 🎉</span>
                    <?php else: ?>
                        Waktu tersisa: <?php echo $rem_hours; ?> jam <?php echo $rem_mins; ?> menit
                    <?php endif; ?>
                </div>
                <a href="index.php?page=statistics" style="display:block; text-align:center; width:100%; background:rgba(67, 97, 238, 0.1); color:var(--dash-primary); border:none; padding:0.75rem; border-radius:8px; font-weight:600; cursor:pointer; text-decoration:none;">Lihat Progress Detail &rarr;</a>
            </div>

            <!-- Jadwal Mendatang -->
            <div class="widget-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <h3 class="widget-title" style="margin:0;">Info & Jadwal</h3>
                    <a href="index.php?page=championship" style="font-size:0.75rem; color:var(--dash-primary); text-decoration:none; font-weight:500;">Lihat Semua</a>
                </div>
                
                <div style="display:flex; flex-direction:column; gap:1rem;">
                    <?php if (empty($upcoming_events) && empty($recent_broadcasts)): ?>
                        <div style="text-align:center; padding:1rem; color:var(--dash-text-muted); font-size:0.85rem; background:var(--dash-bg); border-radius:8px;">
                            Tidak ada jadwal dalam waktu dekat.
                        </div>
                    <?php else: ?>
                        <?php foreach($upcoming_events as $event): 
                            $dateStr = date('d M Y, H:i', strtotime($event['start_date']));
                            $statusLabel = ($event['status'] == 'active') ? 'Sedang Berlangsung' : 'Akan Datang';
                            $color = ($event['status'] == 'active') ? '#ef4444' : '#3b82f6';
                            $bg = ($event['status'] == 'active') ? '#fef2f2' : '#eff6ff';
                        ?>
                        <div style="display:flex; gap:12px; align-items:flex-start; padding:12px; border:1px solid var(--dash-border); border-radius:12px;">
                            <div style="background:<?php echo $bg; ?>; color:<?php echo $color; ?>; padding:8px; border-radius:8px;">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:0.85rem; color:var(--dash-text); margin-bottom:4px;"><?php echo htmlspecialchars($event['title']); ?></div>
                                <div style="font-size:0.75rem; color:var(--dash-text-muted);"><?php echo $dateStr; ?> - <span style="color:<?php echo $color; ?>;"><?php echo $statusLabel; ?></span></div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php foreach($recent_broadcasts as $bc): 
                            $dateStr = date('d M', strtotime($bc['created_at']));
                            // Tentukan warna berdasarkan tipe broadcast
                            $bcColor = '#22c55e'; $bcBg = '#f0fdf4'; $icon = 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9';
                            if($bc['type'] == 'warning') { $bcColor = '#f59e0b'; $bcBg = '#fffbeb'; $icon = 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'; }
                            else if($bc['type'] == 'error') { $bcColor = '#ef4444'; $bcBg = '#fef2f2'; }
                        ?>
                        <div style="display:flex; gap:12px; align-items:flex-start; padding:12px; border:1px solid var(--dash-border); border-radius:12px;">
                            <div style="background:<?php echo $bcBg; ?>; color:<?php echo $bcColor; ?>; padding:8px; border-radius:8px;">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $icon; ?>" /></svg>
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:0.85rem; color:var(--dash-text); margin-bottom:4px;"><?php echo htmlspecialchars($bc['title']); ?></div>
                                <div style="font-size:0.75rem; color:var(--dash-text-muted);"><?php echo $dateStr; ?> - Info Pengumuman</div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div> <!-- End dash-right -->

    </div> <!-- End dashboard-layout -->
</div> <!-- End main-wrapper -->