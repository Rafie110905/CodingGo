<?php
require_once 'config/db.php';
require_once 'includes/materi_icons.php';

$category = $_GET['category'] ?? 'Semua';

// Ambil user saat ini untuk cek akses RBAC
$stmt_user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->execute([$_SESSION['user_id']]);
$current_user = $stmt_user->fetch();

$has_access = hasCategoryAccess($current_user, $category);

// Ambil kelas berdasarkan kategori jika memiliki akses
if ($has_access) {
    if ($category === 'Semua') {
        $allowed = getUserAllowedCategories($current_user);
        if ($current_user['role'] === 'admin') {
            // Admin sees all
            $stmt = $pdo->query("
                SELECT c.*, 
                       (SELECT AVG(rating) FROM course_ratings cr WHERE cr.course_id = c.id) as avg_rating,
                       (SELECT COUNT(*) FROM course_ratings cr2 WHERE cr2.course_id = c.id) as total_ratings
                FROM courses c ORDER BY created_at DESC
            ");
            $courses = $stmt->fetchAll();
        } else if (!empty($allowed)) {
            $placeholders = implode(',', array_fill(0, count($allowed), '?'));
            $stmt = $pdo->prepare("
                SELECT c.*, 
                       (SELECT AVG(rating) FROM course_ratings cr WHERE cr.course_id = c.id) as avg_rating,
                       (SELECT COUNT(*) FROM course_ratings cr2 WHERE cr2.course_id = c.id) as total_ratings
                FROM courses c WHERE c.category IN ($placeholders) ORDER BY created_at DESC
            ");
            $stmt->execute($allowed);
            $courses = $stmt->fetchAll();
        } else {
            $courses = [];
        }
    } else {
        $stmt = $pdo->prepare("
            SELECT c.*, 
                   (SELECT AVG(rating) FROM course_ratings cr WHERE cr.course_id = c.id) as avg_rating,
                   (SELECT COUNT(*) FROM course_ratings cr2 WHERE cr2.course_id = c.id) as total_ratings
            FROM courses c WHERE c.category = ? ORDER BY created_at DESC
        ");
        $stmt->execute([$category]);
        $courses = $stmt->fetchAll();
    }
} else {
    $courses = [];
}
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Program Belajar: <?php echo htmlspecialchars($category); ?></h1>
            <p style="color: var(--dash-text-muted);">Jelajahi kelas-kelas terbaik untuk kategori ini.</p>
        </div>
    </div>

    <?php if (!$has_access): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px dashed #ef4444; padding: 4rem; text-align: center; border-radius: 16px;">
            <div style="width: 64px; height: 64px; background: rgba(239, 68, 68, 0.2); color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="32"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
            <h3 style="color: #ef4444; margin-bottom: 0.5rem; font-size:1.5rem;">Akses Ditolak</h3>
            <p style="color: var(--dash-text); font-weight:600; font-size:1.1rem; max-width:500px; margin: 0 auto;">Anda tidak memiliki izin untuk mengakses kelas di tingkat <b><?php echo htmlspecialchars($category); ?></b>.</p>
            <p style="color: var(--dash-text-muted); margin-top:0.5rem; font-size:0.95rem;">Hak akses Anda disesuaikan secara otomatis berdasarkan umur (tanggal lahir) atau telah diatur oleh Administrator.</p>
        </div>
    <?php elseif (count($courses) === 0): ?>
        <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 4rem; text-align: center; border-radius: 16px;">
            <div style="width: 64px; height: 64px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="32"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
            </div>
            <h3 style="color: var(--dash-text); margin-bottom: 0.5rem;">Belum Ada Kelas</h3>
            <p style="color: var(--dash-text-muted);">Saat ini belum ada kelas yang tersedia untuk kategori <b><?php echo htmlspecialchars($category); ?></b>.</p>
        </div>
    <?php else: ?>
        <div class="courses-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
            <?php foreach ($courses as $c): ?>
            <a href="index.php?page=course_detail&id=<?php echo $c['id']; ?>" style="text-decoration:none; color:inherit; display:block;">
                <div class="course-card" style="transition: transform 0.2s, box-shadow 0.2s; height: 100%; display: flex; flex-direction: column;">
                    <?php 
                        // Warna gradient konsisten berdasarkan judul kelas (bukan acak tiap reload)
                        $gradients = [
                            'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)',
                            'linear-gradient(135deg, #0f766e 0%, #14b8a6 100%)',
                            'linear-gradient(135deg, #4338ca 0%, #6366f1 100%)',
                            'linear-gradient(135deg, #b45309 0%, #f59e0b 100%)',
                            'linear-gradient(135deg, #be123c 0%, #e11d48 100%)'
                        ];
                        $gIndex = abs(crc32($c['title'])) % count($gradients);
                        $bg = $gradients[$gIndex];
                        if (!empty($c['thumbnail'])) {
                            // If we have an actual image URL, use it
                            $bg = 'url(' . htmlspecialchars($c['thumbnail']) . ') center/cover';
                        }
                    ?>
                    <div class="course-img" style="background: <?php echo $bg; ?>; height: 140px; display:flex; align-items:center; justify-content:center; color:white; font-size:2rem; font-weight:bold;">
                        <?php if(empty($c['thumbnail'])) echo renderCourseBadge($c['title'], 68); ?>
                    </div>
                    <div class="course-body" style="flex:1; display:flex; flex-direction:column;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                            <span class="course-tag" style="margin-bottom:0;"><?php echo htmlspecialchars($c['category']); ?></span>
                            <?php if (!empty($c['total_ratings'])): ?>
                                <span style="font-size:0.75rem; color:#eab308; font-weight:600;">⭐ <?php echo round($c['avg_rating'], 1); ?> (<?php echo $c['total_ratings']; ?>)</span>
                            <?php else: ?>
                                <span style="font-size:0.7rem; color:var(--dash-text-muted);">Belum ada rating</span>
                            <?php endif; ?>
                        </div>
                        <div class="course-title" style="margin-bottom:0.5rem; font-size:1.1rem; line-height:1.4;"><?php echo htmlspecialchars($c['title']); ?></div>
                        <div class="course-desc" style="flex:1; margin-bottom:1rem;"><?php echo htmlspecialchars(substr($c['description'], 0, 100)) . '...'; ?></div>
                        
                        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--dash-border); padding-top:1rem; margin-top:auto;">
                            <span style="font-size:0.85rem; color:var(--dash-text-muted); font-weight:600;">Lihat Detail &rarr;</span>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.course-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px -8px rgba(0,0,0,0.15);
}
</style>
