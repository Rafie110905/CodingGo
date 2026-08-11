<?php
require_once 'config/db.php';

// Ambil semua user (termasuk admin) diurutkan berdasarkan XP tertinggi (dinamis, gabungan materi + turnamen), lalu badge terbanyak (dinamis)
$stmt = $pdo->query("SELECT u.id, u.name, u.picture, u.profile_title, u.profile_color,
                     (
                        (SELECT COALESCE(SUM(m.xp_reward), 0) FROM user_progress up JOIN materials m ON up.material_id = m.id WHERE up.user_id = u.id AND up.status = 'completed') +
                        (SELECT COALESCE(SUM(cc.xp_reward), 0) FROM championship_completed_challenges ccc JOIN championship_challenges cc ON ccc.challenge_id = cc.id WHERE ccc.user_id = u.id)
                     ) as calc_xp,
                     (SELECT COUNT(*) FROM user_badges ub WHERE ub.user_id = u.id) as calc_badges
                     FROM users u 
                     ORDER BY calc_xp DESC, calc_badges DESC, u.name ASC");
$leaderboard = $stmt->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1; max-width:900px; margin: 0 auto; width:100%;">
    
    <div style="text-align:center; margin-bottom: 3rem;">
        <h1 style="font-size: 2.5rem; color: var(--dash-text); margin-bottom: 0.5rem; display:flex; justify-content:center; align-items:center; gap:12px;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#f59e0b" width="40"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            Global Leaderboard
        </h1>
        <p style="color: var(--dash-text-muted); font-size: 1.1rem;">Raih XP sebanyak mungkin dengan menyelesaikan materi dan ujian untuk mencapai posisi puncak!</p>
    </div>

    <!-- Peringkat List -->
    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
        
        <div style="display:grid; grid-template-columns: 80px 1fr 120px 120px; gap: 1rem; padding: 1.5rem; background: rgba(0,0,0,0.02); border-bottom: 1px solid var(--dash-border); font-weight: 700; color: var(--dash-text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
            <div style="text-align:center;">Peringkat</div>
            <div>Programmer</div>
            <div style="text-align:center;">Total Badges</div>
            <div style="text-align:right;">XP Points</div>
        </div>

        <?php if (count($leaderboard) === 0): ?>
            <div style="padding: 3rem; text-align: center; color: var(--dash-text-muted);">
                Belum ada data untuk ditampilkan.
            </div>
        <?php else: ?>
            <?php foreach ($leaderboard as $index => $u): ?>
                <?php
                    $rank = $index + 1;
                    $bg_color = "transparent";
                    $rank_style = "color: var(--dash-text-muted); font-weight:700; font-size:1.2rem;";
                    
                    if ($rank === 1) {
                        $bg_color = "linear-gradient(to right, rgba(251, 191, 36, 0.1), transparent)";
                        $rank_style = "color: #f59e0b; font-weight:900; font-size:1.8rem; text-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);";
                        $medal = "🥇";
                    } elseif ($rank === 2) {
                        $bg_color = "linear-gradient(to right, rgba(156, 163, 175, 0.1), transparent)";
                        $rank_style = "color: #9ca3af; font-weight:800; font-size:1.5rem;";
                        $medal = "🥈";
                    } elseif ($rank === 3) {
                        $bg_color = "linear-gradient(to right, rgba(180, 83, 9, 0.1), transparent)";
                        $rank_style = "color: #b45309; font-weight:800; font-size:1.4rem;";
                        $medal = "🥉";
                    } else {
                        $medal = "#" . $rank;
                    }

                    $border_color = !empty($u['profile_color']) ? htmlspecialchars($u['profile_color']) : 'transparent';
                ?>
                <div style="display:grid; grid-template-columns: 80px 1fr 120px 120px; gap: 1rem; padding: 1.25rem 1.5rem; align-items:center; border-bottom: 1px solid var(--dash-border); background: <?php echo $bg_color; ?>; transition: background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.02)'" onmouseout="this.style.background='<?php echo $bg_color; ?>'">
                    
                    <!-- Rank -->
                    <div style="text-align:center; <?php echo $rank_style; ?>">
                        <?php echo $medal; ?>
                    </div>
                    
                    <!-- Profile Info -->
                    <div style="display:flex; align-items:center; gap: 1rem;">
                        <div style="flex-shrink:0;">
                            <?php if (!empty($u['picture'])): ?>
                                <img src="<?php echo htmlspecialchars($u['picture']); ?>" style="width: 48px; height: 48px; border-radius: 50%; border:2px solid <?php echo $border_color; ?>; object-fit:cover;">
                            <?php else: ?>
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--dash-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold; border:2px solid <?php echo $border_color; ?>;">
                                    <?php echo substr(htmlspecialchars($u['name']), 0, 1); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="font-weight:700; color:var(--dash-text); font-size:1.1rem;"><?php echo htmlspecialchars($u['name']); ?></span>
                                <?php if($rank === 1): ?>
                                    <span style="font-size:0.8rem;" title="Sang Juara Bertahan">👑</span>
                                <?php endif; ?>
                            </div>
                            <?php if(!empty($u['profile_title'])): ?>
                                <div style="font-size:0.8rem; color:<?php echo $border_color !== 'transparent' ? $border_color : 'var(--dash-text-muted)'; ?>; font-weight:600; margin-top:4px;">
                                    <?php echo htmlspecialchars($u['profile_title']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Badges -->
                    <div style="text-align:center; display:flex; justify-content:center; align-items:center; gap:6px;">
                        <span style="font-weight:700; color:var(--dash-text); font-size:1.2rem;"><?php echo $u['calc_badges']; ?></span>
                        <svg fill="none" viewBox="0 0 24 24" stroke="#f59e0b" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>

                    <!-- XP -->
                    <div style="text-align:right; display:flex; justify-content:flex-end; align-items:center; gap:6px; color:#10b981; font-weight:800; font-size:1.2rem;">
                        <?php echo number_format($u['calc_xp'], 0, ',', '.'); ?>
                        <span style="font-size:0.8rem; font-weight:700; color:#059669;">XP</span>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
    </div>
</div>
