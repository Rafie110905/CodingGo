<?php
require_once 'config/db.php';

// Handle New Post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'new_post') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $user_id = $_SESSION['user_id'];
        
        if (!empty($title) && !empty($content)) {
            $stmt = $pdo->prepare("INSERT INTO forum_posts (user_id, title, content) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $title, $content]);
            header("Location: index.php?page=community");
            exit();
        }
    } elseif ($_POST['action'] === 'delete_post') {
        $post_id_to_delete = $_POST['post_id'];
        
        // Verify admin or owner
        $stmt_check = $pdo->prepare("SELECT user_id FROM forum_posts WHERE id = ?");
        $stmt_check->execute([$post_id_to_delete]);
        $owner_id = $stmt_check->fetchColumn();
        
        if ($owner_id == $_SESSION['user_id'] || $_SESSION['user_role'] === 'admin') {
            $pdo->prepare("DELETE FROM forum_posts WHERE id = ?")->execute([$post_id_to_delete]);
        }
        header("Location: index.php?page=community");
        exit();
    }
}

// Fetch all posts with user data
$my_uid = (int)($_SESSION['user_id'] ?? 0);
$stmt = $pdo->prepare("SELECT fp.*, u.name, u.picture, u.profile_title, u.profile_color, 
                     (SELECT COUNT(*) FROM forum_replies WHERE post_id = fp.id) as reply_count,
                     (SELECT vote_type FROM forum_votes WHERE target_type='post' AND target_id=fp.id AND user_id=?) as my_vote
                     FROM forum_posts fp 
                     JOIN users u ON fp.user_id = u.id 
                     ORDER BY fp.created_at DESC");
$stmt->execute([$my_uid]);
$posts = $stmt->fetchAll();

// Fetch Top 10 Leaderboard (Dynamic/Real-time)
$stmt_top = $pdo->query("SELECT u.id, u.name, u.picture, u.profile_title, u.profile_color,
                         (
                            (SELECT COALESCE(SUM(m.xp_reward), 0) FROM user_progress up JOIN materials m ON up.material_id = m.id WHERE up.user_id = u.id AND up.status = 'completed') +
                            (SELECT COALESCE(SUM(cc.xp_reward), 0) FROM championship_completed_challenges ccc JOIN championship_challenges cc ON ccc.challenge_id = cc.id WHERE ccc.user_id = u.id)
                         ) as xp_points,
                         (SELECT COUNT(*) FROM user_badges ub WHERE ub.user_id = u.id) as total_badges
                         FROM users u 
                         ORDER BY xp_points DESC, total_badges DESC, u.name ASC LIMIT 10");
$top10 = $stmt_top->fetchAll();
?>

<div class="dash-left dash-grid-sidebar\" style="grid-column: 1 / -1; display: grid;  gap: 2rem;">
    <!-- Feed -->
    <div>
        <div class="section-header" style="margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Komunitas CodingGo</h1>
                <p style="color: var(--dash-text-muted);">Berdiskusi, bertanya, dan pamerkan pencapaian serta Badge Anda di sini!</p>
            </div>
        </div>

        <!-- Post list -->
        <div class="community-list" style="display: flex; flex-direction: column; gap: 1rem;">
            <?php if (count($posts) === 0): ?>
                <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 3rem; text-align: center; border-radius: 16px;">
                    <h3 style="color: var(--dash-text-muted);">Belum ada diskusi. Jadilah yang pertama!</h3>
                </div>
            <?php endif; ?>

            <?php foreach ($posts as $p): ?>
                <?php
                // Fetch badges for this user
                $stmt_b = $pdo->prepare("SELECT b.icon_url, b.name FROM user_badges ub JOIN badges b ON ub.badge_id = b.id WHERE ub.user_id = ?");
                $stmt_b->execute([$p['user_id']]);
                $user_badges = $stmt_b->fetchAll();
                $border_color = !empty($p['profile_color']) ? htmlspecialchars($p['profile_color']) : 'transparent';
                ?>
                <div class="community-post" style="position:relative;">
                    <div style="background: var(--dash-sidebar); border: 1px solid <?php echo $p['is_official'] ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius: 16px; padding: 1.5rem; transition: transform 0.2s, box-shadow 0.2s; <?php echo $p['is_official'] ? 'box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);' : ''; ?>" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 25px -5px rgba(0,0,0,0.05)'" onmouseout="this.style.transform='none'; this.style.boxShadow='<?php echo $p['is_official'] ? '0 0 0 2px rgba(59, 130, 246, 0.15)' : 'none'; ?>'">

                        
                        <div style="display:flex; gap:1rem; align-items:flex-start; margin-bottom:1rem;">
                            <!-- Avatar -->
                            <div style="flex-shrink:0; cursor:pointer; position:relative; width:50px; height:50px;" onclick="showUserProfile(<?php echo (int)$p['user_id']; ?>)" title="Lihat profil <?php echo htmlspecialchars($p['name']); ?>">
                                <?php if (!empty($p['picture'])): ?>
                                    <img src="<?php echo htmlspecialchars($p['picture']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" style="width: 50px; height: 50px; border-radius: 50%; border:3px solid <?php echo $border_color; ?>; object-fit:cover; position:absolute; inset:0;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <?php endif; ?>
                                <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--dash-primary); color: white; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; border:3px solid <?php echo $border_color; ?>; position:absolute; inset:0; display:<?php echo empty($p['picture']) ? 'flex' : 'none'; ?>;">
                                    <?php echo substr(htmlspecialchars($p['name']), 0, 1); ?>
                                </div>
                            </div>
                            
                            <!-- Author Info -->
                            <div style="flex:1;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="font-weight:600; color:var(--dash-text); cursor:pointer;" onclick="showUserProfile(<?php echo (int)$p['user_id']; ?>)"><?php echo htmlspecialchars($p['name']); ?></span>
                                    <?php if(!empty($p['profile_title'])): ?>
                                        <span style="background:rgba(0,0,0,0.05); padding:2px 8px; border-radius:12px; font-size:0.7rem; color:<?php echo $border_color !== 'transparent' ? $border_color : 'var(--dash-text-muted)'; ?>; font-weight:600;">
                                            <?php echo htmlspecialchars($p['profile_title']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size:0.75rem; color:var(--dash-text-muted); margin-bottom:0.5rem;">
                                    <?php echo date('d M Y, H:i', strtotime($p['created_at'])); ?>
                                </div>
                                
                                <!-- Badges Showcase -->
                                <?php if(count($user_badges) > 0): ?>
                                    <div style="display:flex; gap:4px; flex-wrap:wrap;">
                                        <?php 
                                        $max_badges = 5;
                                        $displayed_badges = array_slice($user_badges, 0, $max_badges);
                                        $extra_badges = count($user_badges) - $max_badges;
                                        foreach($displayed_badges as $b): 
                                        ?>
                                            <?php if(!empty($b['icon_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($b['icon_url']); ?>" title="<?php echo htmlspecialchars($b['name']); ?>" style="width:24px; height:24px; object-fit:contain; filter:drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
                                            <?php else: ?>
                                                <div title="<?php echo htmlspecialchars($b['name']); ?>" style="width:24px; height:24px; border-radius:50%; background:#f59e0b; color:white; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold;">B</div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php if ($extra_badges > 0): ?>
                                            <div style="width:24px; height:24px; border-radius:50%; background:var(--dash-border); color:var(--dash-text-muted); display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold;">+<?php echo $extra_badges; ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_id'] == $p['user_id']): ?>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <a href="index.php?page=community_edit&type=post&id=<?php echo $p['id']; ?>" style="color:#3b82f6; padding:4px;" title="Edit Postingan">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form method="POST" action="" style="margin:0;" onsubmit="return confirm('Yakin ingin menghapus postingan ini?');">
                                        <input type="hidden" name="action" value="delete_post">
                                        <input type="hidden" name="post_id" value="<?php echo $p['id']; ?>">
                                        <button type="submit" style="background:transparent; border:none; color:#ef4444; cursor:pointer; padding:4px;" title="Hapus Postingan">
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Post Content -->
                        <h3 style="margin: 0 0 0.5rem 0; font-size:1.1rem;">
                            <a href="index.php?page=community_post&id=<?php echo $p['id']; ?>" style="color:var(--dash-text); text-decoration:none; display:block;"><?php echo htmlspecialchars($p['title']); ?></a>
                        </h3>
                        <p style="color:var(--dash-text-muted); font-size:0.9rem; line-height:1.6; margin-bottom:1rem; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                            <?php echo htmlspecialchars($p['content']); ?>
                        </p>

                        <!-- Interaction Stats -->
                        <div style="display:flex; gap:1.5rem; align-items:center;" data-vote-wrap onclick="event.stopPropagation();">
                            <button type="button" data-vote-btn="up" onclick="voteOnTarget('post', <?php echo (int)$p['id']; ?>, 'up', this)" style="display:flex; align-items:center; gap:6px; background:none; border:none; cursor:pointer; padding:0; color:<?php echo ($p['my_vote'] === 'up') ? '#3b82f6' : 'var(--dash-text-muted)'; ?>; font-size:0.85rem; font-weight:<?php echo ($p['my_vote'] === 'up') ? '700' : '500'; ?>;">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" /></svg>
                                <span data-vote-count="up"><?php echo $p['upvotes'] ?? 0; ?></span>
                            </button>
                            <button type="button" data-vote-btn="down" onclick="voteOnTarget('post', <?php echo (int)$p['id']; ?>, 'down', this)" style="display:flex; align-items:center; gap:6px; background:none; border:none; cursor:pointer; padding:0; color:<?php echo ($p['my_vote'] === 'down') ? '#ef4444' : 'var(--dash-text-muted)'; ?>; font-size:0.85rem; font-weight:<?php echo ($p['my_vote'] === 'down') ? '700' : '500'; ?>;">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" style="transform:scaleY(-1);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" /></svg>
                                <span data-vote-count="down"><?php echo $p['downvotes'] ?? 0; ?></span>
                            </button>
                            <div style="display:flex; align-items:center; gap:6px; color:var(--dash-text-muted); font-size:0.85rem; font-weight:500;">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                                <?php echo $p['reply_count']; ?> Balasan
                            </div>
                            <?php if($p['is_solved']): ?>
                            <div style="display:flex; align-items:center; gap:6px; color:#10b981; font-size:0.85rem; font-weight:600; margin-left:auto;">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Terpecahkan
                            </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Sidebar Komunitas -->
    <div style="position: sticky; top: 2rem; display: flex; flex-direction: column; gap: 1.5rem; align-self: start;">
        
        <!-- Form Buat Post & Gamifikasi -->
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem;">
            <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--dash-text);">Buat Diskusi Baru</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="new_post">
                
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Judul / Topik</label>
                    <input type="text" name="title" required placeholder="Misal: Tanya Error HTML..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Isi Diskusi</label>
                    <textarea name="content" required rows="6" placeholder="Jelaskan pertanyaan atau bagikan pencapaian Anda..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize: vertical;"></textarea>
                </div>
                
                <button type="submit" style="width: 100%; background: var(--dash-primary); color: white; border: none; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                    Kirim Postingan
                </button>
            </form>

            <div style="margin-top:2rem; padding:1.5rem; background:rgba(245, 158, 11, 0.1); border-radius:12px; border:1px solid rgba(245, 158, 11, 0.2);">
                <h4 style="margin:0 0 0.5rem 0; color:#d97706; font-size:0.9rem;">Gamifikasi Corner 🏆</h4>
                <p style="margin:0; font-size:0.8rem; color:#b45309; line-height:1.5;">Dapatkan Upvote dari pengguna lain untuk meraih Badge khusus "Top Contributor". Gunakan fitur kustomisasi profil untuk tampil beda!</p>
            </div>
        </div>
        
        <!-- Widget Top 10 Leaderboard -->
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1rem;">
                <h3 style="margin: 0; color: var(--dash-text); font-size:1rem; display:flex; align-items:center; gap:8px;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="#10b981" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    Top 10 Coder
                </h3>
                <a href="index.php?page=leaderboard" style="font-size:0.75rem; color:var(--dash-primary); text-decoration:none; font-weight:600;">Lihat Semua</a>
            </div>
            
            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                <?php foreach($top10 as $index => $u): ?>
                    <?php 
                        $rank = $index + 1;
                        $rank_color = "var(--dash-text-muted)";
                        if($rank == 1) $rank_color = "#f59e0b"; // Gold
                        else if($rank == 2) $rank_color = "#9ca3af"; // Silver
                        else if($rank == 3) $rank_color = "#b45309"; // Bronze
                        
                        $border_color = !empty($u['profile_color']) ? htmlspecialchars($u['profile_color']) : 'transparent';
                    ?>
                    <div style="display:flex; align-items:center; gap:10px; padding:0.5rem; border-radius:8px; transition:background 0.2s;" onmouseover="this.style.background='var(--dash-bg)'" onmouseout="this.style.background='transparent'">
                        <div style="width:24px; text-align:center; font-weight:800; font-size:0.85rem; color:<?php echo $rank_color; ?>;"><?php echo $rank; ?></div>
                        <div style="flex-shrink:0;">
                            <?php if (!empty($u['picture'])): ?>
                                <img src="<?php echo htmlspecialchars($u['picture']); ?>" style="width: 32px; height: 32px; border-radius: 50%; border:2px solid <?php echo $border_color; ?>; object-fit:cover;">
                            <?php else: ?>
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--dash-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: bold; border:2px solid <?php echo $border_color; ?>;">
                                    <?php echo substr(htmlspecialchars($u['name']), 0, 1); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-weight:600; color:var(--dash-text); font-size:0.85rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo htmlspecialchars($u['name']); ?></div>
                            <div style="color:#10b981; font-weight:700; font-size:0.75rem;"><?php echo number_format($u['xp_points'], 0, ',', '.'); ?> XP</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>