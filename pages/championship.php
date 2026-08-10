<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'join') {
        $championship_id = $_POST['championship_id'];
        
        // Cek apakah sudah join
        $stmt_check = $pdo->prepare("SELECT 1 FROM championship_participants WHERE championship_id = ? AND user_id = ?");
        $stmt_check->execute([$championship_id, $user_id]);
        if (!$stmt_check->fetch()) {
            $pdo->prepare("INSERT INTO championship_participants (championship_id, user_id) VALUES (?, ?)")->execute([$championship_id, $user_id]);
        }
        header("Location: index.php?page=championship_detail&id=" . $championship_id);
        exit();
    }
}

// Fetch Active & Upcoming Championships
$stmt = $pdo->query("SELECT * FROM championships WHERE status IN ('active', 'upcoming') ORDER BY start_date ASC");
$championships = $stmt->fetchAll();

// Check which ones user has joined
$joined = [];
$stmt_joined = $pdo->prepare("SELECT championship_id FROM championship_participants WHERE user_id = ?");
$stmt_joined->execute([$user_id]);
while ($row = $stmt_joined->fetch()) {
    $joined[] = $row['championship_id'];
}
?>

<div class="dash-left" style="grid-column: 1 / -1; max-width: 1000px; margin: 0 auto; width: 100%;">
    <div style="text-align: center; margin-bottom: 3rem; position: relative;">
        <h1 style="font-size: 2.2rem; color: var(--dash-text); margin-bottom: 0.5rem; display:flex; align-items:center; justify-content:center; gap:12px;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#f59e0b" width="36"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
            Coding Championship
        </h1>
        <p style="color: var(--dash-text-muted); font-size: 1.05rem; max-width: 600px; margin: 0 auto;">
            Ikuti turnamen musiman, kumpulkan XP terbanyak, dan jadilah juara di Leaderboard!
        </p>
    </div>

    <!-- TAB NAVIGASI SEDERHANA -->
    <div style="display:flex; justify-content:center; gap:1rem; margin-bottom:3rem;">
        <button onclick="document.getElementById('view-events').style.display='block'; document.getElementById('view-champions').style.display='none'; this.style.background='var(--dash-primary)'; this.style.color='white'; document.getElementById('btn-champ').style.background='var(--dash-sidebar)'; document.getElementById('btn-champ').style.color='var(--dash-text)';" id="btn-events" style="background:var(--dash-primary); color:white; border:1px solid var(--dash-border); padding:0.75rem 1.5rem; border-radius:8px; font-weight:600; cursor:pointer; font-size:1rem;">
            Daftar Turnamen
        </button>
        <button onclick="document.getElementById('view-champions').style.display='block'; document.getElementById('view-events').style.display='none'; this.style.background='var(--dash-primary)'; this.style.color='white'; document.getElementById('btn-events').style.background='var(--dash-sidebar)'; document.getElementById('btn-events').style.color='var(--dash-text)';" id="btn-champ" style="background:var(--dash-sidebar); color:var(--dash-text); border:1px solid var(--dash-border); padding:0.75rem 1.5rem; border-radius:8px; font-weight:600; cursor:pointer; font-size:1rem;">
            🏆 Hall of Fame
        </button>
    </div>

    <!-- VIEW EVENTS -->
    <div id="view-events">
        <?php if (count($championships) === 0): ?>
            <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 4rem; text-align: center; border-radius: 16px;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🏆</div>
                <h3 style="color: var(--dash-text); margin-top:0;">Belum Ada Turnamen</h3>
                <p style="color: var(--dash-text-muted);">Saat ini belum ada turnamen yang aktif. Tunggu info selanjutnya dari Admin!</p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
                <?php foreach ($championships as $c): ?>
                    <?php 
                        $is_joined = in_array($c['id'], $joined);
                        $is_active = $c['status'] === 'active';
                    ?>
                    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <div style="height: 10px; background: <?php echo $is_active ? 'linear-gradient(90deg, #10b981, #3b82f6)' : '#64748b'; ?>;"></div>
                        <div style="padding: 2rem;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                                <span style="background: <?php echo $is_active ? 'rgba(16, 185, 129, 0.1)' : 'rgba(100, 116, 139, 0.1)'; ?>; color: <?php echo $is_active ? '#10b981' : '#64748b'; ?>; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                                    <?php echo strtoupper($c['status']); ?>
                                </span>
                            </div>
                            
                            <h2 style="font-size: 1.5rem; color: var(--dash-text); margin-top: 0; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($c['title']); ?></h2>
                            <p style="color: var(--dash-text-muted); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1.5rem;">
                                <?php echo htmlspecialchars($c['description']); ?>
                            </p>
                            
                            <div style="background: var(--dash-bg); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                                <div style="display:flex; align-items:center; gap:8px; margin-bottom:0.5rem; font-size:0.85rem; color:var(--dash-text);">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" style="color:var(--dash-primary);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <strong>Mulai:</strong> <?php echo date('d M Y', strtotime($c['start_date'])); ?>
                                </div>
                                <div style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:var(--dash-text);">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" style="color:#ef4444;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <strong>Selesai:</strong> <?php echo date('d M Y', strtotime($c['end_date'])); ?>
                                </div>
                            </div>

                            <?php if ($is_joined): ?>
                                <a href="index.php?page=championship_detail&id=<?php echo $c['id']; ?>" class="btn btn-primary" style="display:block; text-align:center; background: var(--dash-primary); color: white; padding: 0.85rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                                    Buka Dashboard Turnamen &rarr;
                                </a>
                            <?php else: ?>
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="action" value="join">
                                    <input type="hidden" name="championship_id" value="<?php echo $c['id']; ?>">
                                    <button type="submit" class="btn btn-primary" style="width:100%; background: #10b981; color: white; border:none; padding: 0.85rem; border-radius: 8px; cursor:pointer; font-weight: 600; font-size:1rem;">
                                        Ikuti Turnamen Ini
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- VIEW CHAMPIONS -->
    <div id="view-champions" style="display:none;">
        <?php
        // Top performers berdasarkan XP (dinamis termasuk championship) dan badge (dinamis), semua role termasuk admin
        $stmt_champ_top10 = $pdo->query("SELECT u.id, u.name, u.picture, u.profile_title, u.profile_color, u.category,
                             (
                                (SELECT COALESCE(SUM(m.xp_reward), 0) FROM user_progress up JOIN materials m ON up.material_id = m.id WHERE up.user_id = u.id AND up.status = 'completed') +
                                (SELECT COALESCE(SUM(cc.xp_reward), 0) FROM championship_completed_challenges ccc JOIN championship_challenges cc ON ccc.challenge_id = cc.id WHERE ccc.user_id = u.id)
                             ) as calc_xp,
                             (SELECT COUNT(*) FROM user_badges ub WHERE ub.user_id = u.id) as calc_badges
                             FROM users u
                             ORDER BY calc_xp DESC, calc_badges DESC, u.name ASC
                             LIMIT 10");
        $champ_top10 = $stmt_champ_top10->fetchAll();
        $champ_first = $champ_top10[0] ?? null;

        // Siswa yang paling banyak lulus ujian (Master Badges)
        $stmt_champ_master = $pdo->query("SELECT u.id, u.name, u.picture, u.profile_color, COUNT(ub.id) as total_master
                             FROM user_badges ub
                             JOIN badges b ON ub.badge_id = b.id
                             JOIN users u ON ub.user_id = u.id
                             WHERE b.requirement_type = 'exam'
                             GROUP BY u.id
                             ORDER BY total_master DESC
                             LIMIT 8");
        $champ_masters = $stmt_champ_master->fetchAll();
        ?>

        <?php if ($champ_first): ?>
        <!-- Banner Juara Utama -->
        <div style="background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); border-radius: 24px; padding: 2.5rem; margin-bottom: 2.5rem; display:flex; align-items:center; gap: 2rem; color: white; position: relative; overflow: hidden;">
            <div style="position:absolute; top:-40px; right:-40px; width:180px; height:180px; background:rgba(255,255,255,0.15); border-radius:50%;"></div>
            <div style="width: 90px; height: 90px; border-radius: 50%; background: rgba(255,255,255,0.25); backdrop-filter: blur(10px); display:flex; align-items:center; justify-content:center; font-weight:800; font-size: 2rem; flex-shrink:0; border: 3px solid rgba(255,255,255,0.5); position:relative; z-index:1;">
                <?php if (!empty($champ_first['picture'])): ?>
                    <img src="<?php echo htmlspecialchars($champ_first['picture']); ?>" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                <?php else: ?>
                    <?php echo substr(htmlspecialchars($champ_first['name']), 0, 1); ?>
                <?php endif; ?>
            </div>
            <div style="position:relative; z-index:1;">
                <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; opacity:0.9; margin-bottom:0.35rem;">🏆 Programmer Peringkat #1 Saat Ini</div>
                <h2 style="color:#fff; margin:0 0 0.35rem 0; font-size:1.6rem;"><?php echo htmlspecialchars($champ_first['name']); ?></h2>
                <p style="color:rgba(255,255,255,0.9); margin:0;"><?php echo number_format($champ_first['calc_xp'], 0, ',', '.'); ?> XP &middot; <?php echo $champ_first['calc_badges']; ?> Badge &middot; Jenjang <?php echo htmlspecialchars($champ_first['category'] ?? 'Umum'); ?></p>
            </div>
        </div>
        <?php else: ?>
        <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 3rem; text-align: center; border-radius: 16px; margin-bottom: 2.5rem;">
            <h3 style="color: var(--dash-text-muted);">Belum ada data champion. Selesaikan materi dan ujian untuk menjadi yang pertama!</h3>
        </div>
        <?php endif; ?>

        <!-- Podium Top 3 -->
        <?php if (count($champ_top10) >= 1): ?>
        <div style="display:flex; align-items:flex-end; justify-content:center; gap: 1.25rem; margin-bottom: 3rem; flex-wrap: wrap;">
            <?php
            $order = [1, 0, 2]; // silver, gold, bronze urutan tampil
            $medalStyle = [
                0 => ['label' => '🥇', 'border' => '#f59e0b', 'size' => '82px', 'pad' => '2rem'],
                1 => ['label' => '🥈', 'border' => '#9ca3af', 'size' => '68px', 'pad' => '1.5rem'],
                2 => ['label' => '🥉', 'border' => '#b45309', 'size' => '68px', 'pad' => '1.5rem'],
            ];
            foreach ($order as $i):
                if (!isset($champ_top10[$i])) continue;
                $u = $champ_top10[$i];
                $m = $medalStyle[$i];
            ?>
            <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 20px; padding: <?php echo $m['pad']; ?> 1.25rem 1.5rem 1.25rem; text-align:center; width: 190px; <?php echo $i === 0 ? 'box-shadow: 0 0 0 3px rgba(245,158,11,0.18); border-color:#f59e0b;' : ''; ?>">
                <div style="font-size:1.5rem; margin-bottom:0.5rem;"><?php echo $m['label']; ?></div>
                <div style="width:<?php echo $m['size']; ?>; height:<?php echo $m['size']; ?>; border-radius:50%; margin:0 auto 0.75rem auto; background: var(--dash-primary); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.25rem; border:3px solid <?php echo $m['border']; ?>; overflow:hidden;">
                    <?php if (!empty($u['picture'])): ?>
                        <img src="<?php echo htmlspecialchars($u['picture']); ?>" style="width:100%; height:100%; object-fit:cover;">
                    <?php else: ?>
                        <?php echo substr(htmlspecialchars($u['name']), 0, 1); ?>
                    <?php endif; ?>
                </div>
                <div style="font-weight:700; font-size:0.95rem; color:var(--dash-text); margin-bottom:0.2rem;"><?php echo htmlspecialchars($u['name']); ?></div>
                <div style="font-size:0.8rem; color:var(--dash-text-muted);"><?php echo number_format($u['calc_xp'], 0, ',', '.'); ?> XP</div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Top 10 List -->
        <div class="section-header" style="margin-bottom: 1.25rem;">
            <h2>Top 10 Champions</h2>
            <a href="index.php?page=leaderboard">Lihat Leaderboard Lengkap</a>
        </div>
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 0.5rem; margin-bottom: 2.5rem;">
            <?php if (count($champ_top10) === 0): ?>
                <div style="padding: 2rem; text-align:center; color: var(--dash-text-muted);">Belum ada data.</div>
            <?php endif; ?>
            <?php foreach ($champ_top10 as $index => $u): ?>
                <?php
                    $rank = $index + 1;
                    $rank_bg = 'var(--dash-bg)';
                    $rank_color = 'var(--dash-text-muted)';
                    if ($rank === 1) { $rank_color = '#f59e0b'; }
                    elseif ($rank === 2) { $rank_color = '#9ca3af'; }
                    elseif ($rank === 3) { $rank_color = '#b45309'; }
                ?>
                <div style="display:flex; align-items:center; gap:1rem; padding:0.85rem 1rem; border-radius:12px;">
                    <div style="width:32px; height:32px; border-radius:50%; background:<?php echo $rank_bg; ?>; color:<?php echo $rank_color; ?>; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.85rem; flex-shrink:0;"><?php echo $rank; ?></div>
                    <div style="width:38px; height:38px; border-radius:50%; background:var(--dash-primary); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.85rem; flex-shrink:0; overflow:hidden;">
                        <?php if (!empty($u['picture'])): ?>
                            <img src="<?php echo htmlspecialchars($u['picture']); ?>" style="width:100%; height:100%; object-fit:cover;">
                        <?php else: ?>
                            <?php echo substr(htmlspecialchars($u['name']), 0, 1); ?>
                        <?php endif; ?>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-weight:600; font-size:0.9rem; color:var(--dash-text);"><?php echo htmlspecialchars($u['name']); ?></div>
                        <div style="font-size:0.78rem; color:var(--dash-text-muted);"><?php echo htmlspecialchars($u['profile_title'] ?? 'Novice Coder'); ?> &middot; Jenjang <?php echo htmlspecialchars($u['category'] ?? 'Umum'); ?></div>
                    </div>
                    <div style="font-weight:700; color:var(--dash-primary); font-size:0.9rem; flex-shrink:0;"><?php echo number_format($u['calc_xp'], 0, ',', '.'); ?> XP</div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Master Badges -->
        <div class="section-header" style="margin-bottom: 1.25rem;">
            <h2>Siswa dengan Badge "Master" Terbanyak</h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
            <?php if (count($champ_masters) === 0): ?>
                <div style="grid-column: 1/-1; background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 2.5rem; text-align: center; border-radius: 16px; color: var(--dash-text-muted);">
                    Belum ada siswa yang lulus ujian dan meraih badge Master. Jadilah yang pertama!
                </div>
            <?php endif; ?>
            <?php foreach ($champ_masters as $m): ?>
            <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; text-align:center; display:flex; flex-direction:column; align-items:center; gap:0.5rem;">
                <div style="width:56px; height:56px; border-radius:50%; background: var(--dash-primary); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.1rem; overflow:hidden;">
                    <?php if (!empty($m['picture'])): ?>
                        <img src="<?php echo htmlspecialchars($m['picture']); ?>" style="width:100%; height:100%; object-fit:cover;">
                    <?php else: ?>
                        <?php echo substr(htmlspecialchars($m['name']), 0, 1); ?>
                    <?php endif; ?>
                </div>
                <div style="font-weight:700; font-size:0.9rem; color:var(--dash-text);"><?php echo htmlspecialchars($m['name']); ?></div>
                <div style="display:flex; align-items:center; gap:4px; color:#f59e0b; font-weight:600; font-size:0.8rem;">
                    🏆 <?php echo $m['total_master']; ?> Master Badge
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Cara Menjadi Champion -->
        <div class="section-header" style="margin-bottom: 1.25rem;">
            <h2>Cara Menjadi Coding Champion</h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
            <div class="course-card" style="padding: 1.5rem;">
                <div style="width:44px; height:44px; border-radius:12px; background:#eff6ff; color:#3b82f6; display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div class="course-title">Selesaikan Materi & Ujian</div>
                <div class="course-desc">Setiap materi dan ujian yang lulus menambah XP dan badge di profilmu.</div>
            </div>
            <div class="course-card" style="padding: 1.5rem;">
                <div style="width:44px; height:44px; border-radius:12px; background:#fff7ed; color:#f59e0b; display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <div class="course-title">Kumpulkan XP Sebanyak Mungkin</div>
                <div class="course-desc">Semakin banyak XP, posisimu di leaderboard dan podium Champions makin tinggi.</div>
            </div>
            <div class="course-card" style="padding: 1.5rem;">
                <div style="width:44px; height:44px; border-radius:12px; background:#f5f3ff; color:#8b5cf6; display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <div class="course-title">Aktif di Komunitas</div>
                <div class="course-desc">Bantu jawab diskusi di Community untuk mendapat upvote dan reputasi tambahan.</div>
            </div>
        </div>
    </div>
</div>
