<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config/db.php';
require_once 'includes/materi_icons.php';

$user_id = $_SESSION['user_id'];
$active_tab = $_GET['tab'] ?? 'completed'; // completed, ongoing, badges
$selected_course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : null;

// Ambil data untuk tab yang aktif
$items = [];
if ($active_tab === 'completed' || $active_tab === 'ongoing') {
    $status_filter = $active_tab === 'completed' ? 'completed' : 'started';
    $stmt = $pdo->prepare("
        SELECT m.id, m.title as material_title, m.thumbnail as material_thumbnail, m.content_type, m.xp_reward, 
               c.id as course_id, c.title as course_title, c.category as course_category, c.thumbnail as course_thumbnail, c.description as course_description,
               up.completed_at, up.status,
               (SELECT AVG(rating) FROM course_ratings WHERE course_id = c.id) as avg_rating,
               (SELECT COUNT(id) FROM course_ratings WHERE course_id = c.id) as total_ratings
        FROM user_progress up
        JOIN materials m ON up.material_id = m.id
        JOIN courses c ON m.course_id = c.id
        WHERE up.user_id = ? AND up.status = ?
        ORDER BY up.id DESC
    ");
    $stmt->execute([$user_id, $status_filter]);
    $items = $stmt->fetchAll();
} else if ($active_tab === 'badges') {
    $stmt = $pdo->prepare("
        SELECT b.id, b.name, b.description, b.icon_url, ub.earned_at
        FROM user_badges ub
        JOIN badges b ON ub.badge_id = b.id
        WHERE ub.user_id = ?
        ORDER BY ub.earned_at DESC
    ");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll();
} else if ($active_tab === 'xp_history') {
    $stmt = $pdo->prepare("
        SELECT 'material' as type, m.title as title, m.xp_reward as xp, up.completed_at as date 
        FROM user_progress up 
        JOIN materials m ON up.material_id = m.id 
        WHERE up.user_id = ? AND up.status = 'completed'
        
        UNION ALL
        
        SELECT 'exam' as type, e.title as title, 50 as xp, er.attempt_date as date 
        FROM exam_results er 
        JOIN exams e ON er.exam_id = e.id 
        WHERE er.user_id = ? AND er.passed = 1
        
        UNION ALL
        
        SELECT 'challenge' as type, cc.title as title, cc.xp_reward as xp, ccc.completed_at as date 
        FROM championship_completed_challenges ccc 
        JOIN championship_challenges cc ON ccc.challenge_id = cc.id 
        WHERE ccc.user_id = ?
        
        ORDER BY date DESC
    ");
    $stmt->execute([$user_id, $user_id, $user_id]);
    $items = $stmt->fetchAll();
}
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Detail Pencapaian</h1>
            <p style="color: var(--dash-text-muted);">Lihat seluruh koleksi materi yang sedang dipelajari, diselesaikan, dan lencana yang dikumpulkan.</p>
        </div>
    </div>

    <!-- Custom Tabs Navigation -->
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 1px solid var(--dash-border);">
        <a href="index.php?page=my_achievements&tab=completed" style="text-decoration: none; padding: 1rem 1.5rem; font-weight: 600; color: <?php echo $active_tab === 'completed' ? '#3b82f6' : 'var(--dash-text-muted)'; ?>; border-bottom: 3px solid <?php echo $active_tab === 'completed' ? '#3b82f6' : 'transparent'; ?>; transition: all 0.2s;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" style="vertical-align: middle; margin-right: 4px; margin-top: -2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Modul Selesai
        </a>
        <a href="index.php?page=my_achievements&tab=ongoing" style="text-decoration: none; padding: 1rem 1.5rem; font-weight: 600; color: <?php echo $active_tab === 'ongoing' ? '#3b82f6' : 'var(--dash-text-muted)'; ?>; border-bottom: 3px solid <?php echo $active_tab === 'ongoing' ? '#3b82f6' : 'transparent'; ?>; transition: all 0.2s;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" style="vertical-align: middle; margin-right: 4px; margin-top: -2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
            Materi Berjalan
        </a>
        <a href="index.php?page=my_achievements&tab=badges" style="text-decoration: none; padding: 1rem 1.5rem; font-weight: 600; color: <?php echo $active_tab === 'badges' ? '#f59e0b' : 'var(--dash-text-muted)'; ?>; border-bottom: 3px solid <?php echo $active_tab === 'badges' ? '#f59e0b' : 'transparent'; ?>; transition: all 0.2s;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" style="vertical-align: middle; margin-right: 4px; margin-top: -2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
            Koleksi Badges
        </a>
        <a href="index.php?page=my_achievements&tab=xp_history" style="text-decoration: none; padding: 1rem 1.5rem; font-weight: 600; color: <?php echo $active_tab === 'xp_history' ? '#10b981' : 'var(--dash-text-muted)'; ?>; border-bottom: 3px solid <?php echo $active_tab === 'xp_history' ? '#10b981' : 'transparent'; ?>; transition: all 0.2s;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" style="vertical-align: middle; margin-right: 4px; margin-top: -2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            Riwayat XP
        </a>
    </div>

    <!-- Content Area -->
    <?php if (empty($items)): ?>
        <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 4rem; text-align: center; border-radius: 16px;">
            <div style="width: 64px; height: 64px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="32"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
            </div>
            <h3 style="color: var(--dash-text); margin-bottom: 0.5rem;">Data Kosong</h3>
            <p style="color: var(--dash-text-muted);">Belum ada data yang bisa ditampilkan di kategori ini.</p>
        </div>
    <?php else: ?>
        <?php if ($active_tab === 'completed' || $active_tab === 'ongoing'): 
            // Group items by course
            $grouped_items = [];
            foreach ($items as $item) {
                if (!isset($grouped_items[$item['course_id']])) {
                    $grouped_items[$item['course_id']] = [
                        'course_id' => $item['course_id'],
                        'course_title' => $item['course_title'],
                        'course_category' => $item['course_category'],
                        'course_thumbnail' => $item['course_thumbnail'],
                        'course_description' => $item['course_description'],
                        'avg_rating' => $item['avg_rating'],
                        'total_ratings' => $item['total_ratings'],
                        'materials' => []
                    ];
                }
                $grouped_items[$item['course_id']]['materials'][] = $item;
            }
        ?>
            <?php if ($selected_course_id && isset($grouped_items[$selected_course_id])): ?>
                <?php $group = $grouped_items[$selected_course_id]; ?>
                <div style="margin-bottom: 2rem;">
                    <a href="index.php?page=my_achievements&tab=<?php echo $active_tab; ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; color: var(--dash-text-muted); font-size: 0.9rem; margin-bottom: 1rem; font-weight: 600; padding: 8px 16px; background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 8px; transition: color 0.2s, background 0.2s;" onmouseover="this.style.color='var(--dash-primary)'; this.style.borderColor='var(--dash-primary)';" onmouseout="this.style.color='var(--dash-text-muted)'; this.style.borderColor='var(--dash-border)';">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali ke Daftar Course
                    </a>
                    
                    <h2 style="font-size: 1.4rem; color: var(--dash-text); margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--dash-border);">
                        <span style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; font-size: 0.8rem; padding: 4px 8px; border-radius: 6px; margin-right: 8px; vertical-align: middle;"><?php echo htmlspecialchars($group['course_category']); ?></span>
                        <?php echo htmlspecialchars($group['course_title']); ?>
                    </h2>
                    
                    <div class="courses-grid" style="grid-template-columns: repeat(auto-fill, minmax(min(100%, 250px), 1fr)); gap: 1.5rem;">
                        <?php foreach ($group['materials'] as $item): ?>
                            <a href="index.php?page=course_learn&id=<?php echo $item['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                                <div class="course-card" style="transition: transform 0.2s, box-shadow 0.2s; height: 100%; display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--dash-border);">
                                    <div style="height: 140px; background: var(--dash-sidebar); display: flex; align-items: center; justify-content: center; position: relative;">
                                        <?php if (!empty($item['material_thumbnail'])): ?>
                                            <img src="<?php echo htmlspecialchars($item['material_thumbnail']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?php echo renderMateriIcon($item['material_title'], 64); ?>
                                        <?php endif; ?>
                                        <div style="position: absolute; top: 10px; right: 10px; background: <?php echo $item['status'] === 'completed' ? '#10b981' : '#3b82f6'; ?>; color: white; padding: 4px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">
                                            <?php echo $item['status'] === 'completed' ? 'Selesai' : 'Berjalan'; ?>
                                        </div>
                                    </div>
                                    <div class="course-body" style="flex: 1; display: flex; flex-direction: column;">
                                        <div class="course-title" style="font-size: 1.05rem; margin-bottom: 0.5rem; line-height: 1.4;">
                                            <?php echo htmlspecialchars($item['material_title']); ?>
                                        </div>
                                        <div style="margin-top: auto; display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--dash-border);">
                                            <div style="font-size: 0.75rem; color: var(--dash-text-muted);">
                                                <?php if ($item['status'] === 'completed' && $item['completed_at']): ?>
                                                    Selesai pada <?php echo date('d M Y', strtotime($item['completed_at'])); ?>
                                                <?php else: ?>
                                                    Tipe: <?php echo ucfirst($item['content_type']); ?>
                                                <?php endif; ?>
                                            </div>
                                            <div style="font-weight: 700; color: #f59e0b; font-size: 0.85rem;">+<?php echo $item['xp_reward']; ?> XP</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="courses-grid" style="grid-template-columns: repeat(auto-fill, minmax(min(100%, 280px), 1fr)); gap: 1.5rem;">
                    <?php foreach ($grouped_items as $course_id => $group): ?>
                        <a href="index.php?page=my_achievements&tab=<?php echo $active_tab; ?>&course_id=<?php echo $course_id; ?>" style="text-decoration:none; color:inherit; display:block;">
                            <div class="course-card" style="transition: transform 0.2s, box-shadow 0.2s; height: 100%; display: flex; flex-direction: column; position: relative;">
                                <?php 
                                    $gradients = [
                                        'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)',
                                        'linear-gradient(135deg, #0f766e 0%, #14b8a6 100%)',
                                        'linear-gradient(135deg, #4338ca 0%, #6366f1 100%)',
                                        'linear-gradient(135deg, #b45309 0%, #f59e0b 100%)',
                                        'linear-gradient(135deg, #be123c 0%, #e11d48 100%)'
                                    ];
                                    $gIndex = abs(crc32($group['course_title'])) % count($gradients);
                                    $bg = $gradients[$gIndex];
                                    if (!empty($group['course_thumbnail'])) {
                                        $bg = 'url(' . htmlspecialchars($group['course_thumbnail']) . ') center/cover';
                                    }
                                ?>
                                <div class="course-img" style="background: <?php echo $bg; ?>; height: 140px; display:flex; align-items:center; justify-content:center; color:white; font-size:2rem; font-weight:bold;">
                                    <?php if(empty($group['course_thumbnail'])) echo renderCourseBadge($group['course_title'], 68); ?>
                                </div>
                                <div style="position: absolute; top: 10px; right: 10px; background: <?php echo $active_tab === 'completed' ? '#10b981' : '#3b82f6'; ?>; color: white; padding: 4px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">
                                    <?php echo count($group['materials']); ?> Materi
                                </div>
                                <div class="course-body" style="flex:1; display:flex; flex-direction:column;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                                        <span class="course-tag" style="margin-bottom:0;"><?php echo htmlspecialchars($group['course_category']); ?></span>
                                        <?php if (!empty($group['total_ratings'])): ?>
                                            <span style="font-size:0.75rem; color:#eab308; font-weight:600;">⭐ <?php echo round($group['avg_rating'], 1); ?> (<?php echo $group['total_ratings']; ?>)</span>
                                        <?php else: ?>
                                            <span style="font-size:0.7rem; color:var(--dash-text-muted);">Belum ada rating</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="course-title" style="margin-bottom:0.5rem; font-size:1.1rem; line-height:1.4;"><?php echo htmlspecialchars($group['course_title']); ?></div>
                                    <div class="course-desc" style="flex:1; margin-bottom:1rem;"><?php echo htmlspecialchars(mb_substr($group['course_description'] ?? '', 0, 100)) . '...'; ?></div>
                                    
                                    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--dash-border); padding-top:1rem; margin-top:auto;">
                                        <span style="font-size:0.85rem; color:var(--dash-text-muted); font-weight:600;">Lihat Detail &rarr;</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php elseif ($active_tab === 'badges'): ?>
            <div class="courses-grid" style="grid-template-columns: repeat(auto-fill, minmax(min(100%, 250px), 1fr)); gap: 1.5rem;">
                <?php foreach ($items as $badge): ?>
                    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; text-align: center; display: flex; flex-direction: column; align-items: center; transition: transform 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                        <?php if (!empty($badge['icon_url'])): ?>
                            <img src="<?php echo htmlspecialchars($badge['icon_url']); ?>" alt="Badge" style="width: 80px; height: 80px; object-fit: contain; margin-bottom: 1rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
                        <?php else: ?>
                            <div style="width: 80px; height: 80px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="40"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                            </div>
                        <?php endif; ?>
                        <div style="font-weight: 700; color: var(--dash-text); font-size: 1.1rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($badge['name']); ?></div>
                        <div style="color: var(--dash-text-muted); font-size: 0.85rem; margin-bottom: 1rem; flex: 1;"><?php echo htmlspecialchars($badge['description']); ?></div>
                        <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                            Diperoleh <?php echo date('d M Y', strtotime($badge['earned_at'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($active_tab === 'xp_history'): ?>
            <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <div style="position: relative;">
                    <!-- Timeline vertical line -->
                    <div style="position: absolute; left: 15px; top: 0; bottom: 0; width: 2px; background: var(--dash-border);"></div>
                    
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <?php foreach ($items as $item): ?>
                            <div style="display: flex; gap: 1rem; position: relative;">
                                <?php
                                    $icon_color = '#3b82f6';
                                    $icon_bg = 'rgba(59, 130, 246, 0.1)';
                                    $icon_svg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />';
                                    $action_text = 'Menyelesaikan Materi';
                                    
                                    if ($item['type'] === 'exam') {
                                        $icon_color = '#10b981';
                                        $icon_bg = 'rgba(16, 185, 129, 0.1)';
                                        $icon_svg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />';
                                        $action_text = 'Lulus Ujian';
                                    } elseif ($item['type'] === 'challenge') {
                                        $icon_color = '#f59e0b';
                                        $icon_bg = 'rgba(245, 158, 11, 0.1)';
                                        $icon_svg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />';
                                        $action_text = 'Menyelesaikan Tantangan';
                                    }
                                ?>
                                <div style="width: 32px; height: 32px; background: <?php echo $icon_bg; ?>; color: <?php echo $icon_color; ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; z-index: 2; flex-shrink: 0; margin-top: 4px; box-shadow: 0 0 0 4px var(--dash-sidebar);">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><?php echo $icon_svg; ?></svg>
                                </div>
                                <div style="flex: 1; background: var(--dash-sidebar); border: 1px solid var(--dash-border); padding: 1rem 1.5rem; border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.05)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem; gap: 1rem;">
                                        <div>
                                            <div style="font-size: 0.8rem; color: var(--dash-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.25rem;"><?php echo $action_text; ?></div>
                                            <div style="font-weight: 700; color: var(--dash-text); font-size: 1.1rem; line-height: 1.3;"><?php echo htmlspecialchars($item['title']); ?></div>
                                        </div>
                                        <div style="font-weight: 800; color: #10b981; font-size: 1.1rem; background: rgba(16, 185, 129, 0.1); padding: 4px 12px; border-radius: 8px; flex-shrink: 0;">
                                            +<?php echo $item['xp']; ?> XP
                                        </div>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--dash-text-muted);">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" style="vertical-align: middle; margin-right: 4px; margin-top: -2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <?php echo date('d M Y, H:i', strtotime($item['date'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.course-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px -8px rgba(0,0,0,0.15);
}
</style>
