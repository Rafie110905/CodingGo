<?php
require_once 'config/db.php';

$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    header("Location: index.php?page=community");
    exit();
}

// Handle Reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'reply') {
        $content = trim($_POST['content']);
        $user_id = $_SESSION['user_id'];
        
        if (!empty($content)) {
            $stmt = $pdo->prepare("INSERT INTO forum_replies (post_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$post_id, $user_id, $content]);
            header("Location: index.php?page=community_post&id=" . $post_id);
            exit();
        }
    } elseif ($_POST['action'] === 'accept_reply') {
        // Hanya author post yang bisa accept
        $reply_id = $_POST['reply_id'];
        
        // Cek author
        $stmt_check = $pdo->prepare("SELECT user_id FROM forum_posts WHERE id = ?");
        $stmt_check->execute([$post_id]);
        if ($stmt_check->fetchColumn() == $_SESSION['user_id']) {
            $pdo->prepare("UPDATE forum_replies SET is_accepted = 1 WHERE id = ?")->execute([$reply_id]);
            $pdo->prepare("UPDATE forum_posts SET is_solved = 1 WHERE id = ?")->execute([$post_id]);
        }
        header("Location: index.php?page=community_post&id=" . $post_id);
        exit();
    } elseif ($_POST['action'] === 'delete_post') {
        $stmt_check = $pdo->prepare("SELECT user_id FROM forum_posts WHERE id = ?");
        $stmt_check->execute([$post_id]);
        if ($stmt_check->fetchColumn() == $_SESSION['user_id'] || $_SESSION['user_role'] === 'admin') {
            $pdo->prepare("DELETE FROM forum_posts WHERE id = ?")->execute([$post_id]);
            header("Location: index.php?page=community");
            exit();
        }
    } elseif ($_POST['action'] === 'delete_reply') {
        $reply_id = $_POST['reply_id'];
        $stmt_check = $pdo->prepare("SELECT user_id FROM forum_replies WHERE id = ?");
        $stmt_check->execute([$reply_id]);
        if ($stmt_check->fetchColumn() == $_SESSION['user_id'] || $_SESSION['user_role'] === 'admin') {
            $pdo->prepare("DELETE FROM forum_replies WHERE id = ?")->execute([$reply_id]);
            header("Location: index.php?page=community_post&id=" . $post_id);
            exit();
        }
    }
}

// Get main post
$stmt = $pdo->prepare("SELECT fp.*, u.name, u.picture, u.profile_title, u.profile_color 
                       FROM forum_posts fp JOIN users u ON fp.user_id = u.id WHERE fp.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    echo "Postingan tidak ditemukan.";
    exit();
}

// Get post author badges
$stmt_pb = $pdo->prepare("SELECT b.icon_url, b.name FROM user_badges ub JOIN badges b ON ub.badge_id = b.id WHERE ub.user_id = ?");
$stmt_pb->execute([$post['user_id']]);
$post_badges = $stmt_pb->fetchAll();
$p_border = !empty($post['profile_color']) ? htmlspecialchars($post['profile_color']) : 'transparent';

// Get replies
$stmt_rep = $pdo->prepare("SELECT fr.*, u.name, u.picture, u.profile_title, u.profile_color 
                           FROM forum_replies fr JOIN users u ON fr.user_id = u.id 
                           WHERE fr.post_id = ? ORDER BY fr.is_accepted DESC, fr.created_at ASC");
$stmt_rep->execute([$post_id]);
$replies = $stmt_rep->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1; max-width:900px; margin: 0 auto; width:100%;">
    
    <div style="margin-bottom: 2rem;">
        <a href="index.php?page=community" style="color:var(--dash-text-muted); text-decoration:none; display:inline-flex; align-items:center; gap:8px; font-size:0.9rem;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali ke Komunitas
        </a>
    </div>

    <!-- MAIN POST -->
    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem; margin-bottom: 2rem;">
        <div style="display:flex; gap:1.5rem; align-items:flex-start; margin-bottom:1.5rem; padding-bottom:1.5rem; border-bottom:1px solid var(--dash-border);">
            <div style="flex-shrink:0;">
                <?php if (!empty($post['picture'])): ?>
                    <img src="<?php echo htmlspecialchars($post['picture']); ?>" alt="Profile" style="width: 60px; height: 60px; border-radius: 50%; border:3px solid <?php echo $p_border; ?>; object-fit:cover;">
                <?php else: ?>
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--dash-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: bold; border:3px solid <?php echo $p_border; ?>;">
                        <?php echo substr(htmlspecialchars($post['name']), 0, 1); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="flex:1;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-weight:700; font-size:1.1rem; color:var(--dash-text);"><?php echo htmlspecialchars($post['name']); ?></span>
                    <?php if(!empty($post['profile_title'])): ?>
                        <span style="background:rgba(0,0,0,0.05); padding:4px 12px; border-radius:12px; font-size:0.75rem; color:<?php echo $p_border !== 'transparent' ? $p_border : 'var(--dash-text-muted)'; ?>; font-weight:700;">
                            <?php echo htmlspecialchars($post['profile_title']); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div style="font-size:0.8rem; color:var(--dash-text-muted); margin-bottom:0.75rem;">
                    <?php echo date('d M Y, H:i', strtotime($post['created_at'])); ?>
                </div>
                
                <?php if(count($post_badges) > 0): ?>
                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                        <?php foreach($post_badges as $b): ?>
                            <?php if(!empty($b['icon_url'])): ?>
                                <img src="<?php echo htmlspecialchars($b['icon_url']); ?>" title="<?php echo htmlspecialchars($b['name']); ?>" style="width:28px; height:28px; object-fit:contain; filter:drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
                            <?php else: ?>
                                <div title="<?php echo htmlspecialchars($b['name']); ?>" style="width:28px; height:28px; border-radius:50%; background:#f59e0b; color:white; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:bold;">B</div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_id'] == $post['user_id']): ?>
                <div style="display:flex; gap:8px;">
                    <a href="index.php?page=community_edit&type=post&id=<?php echo $post['id']; ?>" style="background:rgba(59, 130, 246, 0.1); border:1px solid rgba(59, 130, 246, 0.2); color:#3b82f6; text-decoration:none; border-radius:8px; padding:6px 12px; font-weight:600; font-size:0.85rem; display:flex; align-items:center; gap:6px; transition:all 0.2s;" onmouseover="this.style.background='#3b82f6'; this.style.color='white'" onmouseout="this.style.background='rgba(59, 130, 246, 0.1)'; this.style.color='#3b82f6'">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Edit
                    </a>
                    <form method="POST" action="" style="margin:0;" onsubmit="return confirm('Yakin ingin menghapus postingan ini?');">
                        <input type="hidden" name="action" value="delete_post">
                        <button type="submit" style="background:rgba(239, 68, 68, 0.1); border:1px solid rgba(239, 68, 68, 0.2); color:#ef4444; border-radius:8px; padding:6px 12px; font-weight:600; font-size:0.85rem; cursor:pointer; display:flex; align-items:center; gap:6px; transition:all 0.2s;" onmouseover="this.style.background='#ef4444'; this.style.color='white'" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'; this.style.color='#ef4444'">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            Hapus Diskusi
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <h1 style="margin: 0 0 1rem 0; color:var(--dash-text); font-size:1.5rem; line-height:1.4;"><?php echo htmlspecialchars($post['title']); ?></h1>
        <div style="color:var(--dash-text); font-size:1.05rem; line-height:1.8; white-space:pre-wrap;"><?php echo htmlspecialchars($post['content']); ?></div>
    </div>

    <!-- REPLIES -->
    <h3 style="color:var(--dash-text); margin-bottom:1.5rem;"><?php echo count($replies); ?> Balasan</h3>
    
    <div style="display:flex; flex-direction:column; gap:1.5rem; margin-bottom:3rem;">
        <?php foreach ($replies as $r): ?>
            <?php
                $stmt_rb = $pdo->prepare("SELECT b.icon_url, b.name FROM user_badges ub JOIN badges b ON ub.badge_id = b.id WHERE ub.user_id = ?");
                $stmt_rb->execute([$r['user_id']]);
                $r_badges = $stmt_rb->fetchAll();
                $r_border = !empty($r['profile_color']) ? htmlspecialchars($r['profile_color']) : 'transparent';
            ?>
            <div style="background: var(--dash-sidebar); border: 1px solid <?php echo $r['is_accepted'] ? '#10b981' : 'var(--dash-border)'; ?>; border-radius: 16px; padding: 1.5rem; <?php echo $r['is_accepted'] ? 'box-shadow: 0 4px 20px rgba(16, 185, 129, 0.1);' : ''; ?>">
                
                <?php if($r['is_accepted']): ?>
                    <div style="color:#10b981; font-size:0.85rem; font-weight:700; display:flex; align-items:center; gap:6px; margin-bottom:1rem; background:rgba(16, 185, 129, 0.1); padding:6px 12px; border-radius:8px; display:inline-flex;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Jawaban Terbaik
                    </div>
                <?php endif; ?>

                <div style="display:flex; gap:1rem; align-items:flex-start; margin-bottom:1rem;">
                    <div style="flex-shrink:0;">
                        <?php if (!empty($r['picture'])): ?>
                            <img src="<?php echo htmlspecialchars($r['picture']); ?>" style="width: 45px; height: 45px; border-radius: 50%; border:2px solid <?php echo $r_border; ?>; object-fit:cover;">
                        <?php else: ?>
                            <div style="width: 45px; height: 45px; border-radius: 50%; background: var(--dash-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold; border:2px solid <?php echo $r_border; ?>;">
                                <?php echo substr(htmlspecialchars($r['name']), 0, 1); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="flex:1;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-weight:600; color:var(--dash-text);"><?php echo htmlspecialchars($r['name']); ?></span>
                            <?php if(!empty($r['profile_title'])): ?>
                                <span style="background:rgba(0,0,0,0.05); padding:2px 8px; border-radius:12px; font-size:0.65rem; color:<?php echo $r_border !== 'transparent' ? $r_border : 'var(--dash-text-muted)'; ?>; font-weight:600;">
                                    <?php echo htmlspecialchars($r['profile_title']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div style="font-size:0.75rem; color:var(--dash-text-muted); margin-bottom:0.5rem;">
                            <?php echo date('d M Y, H:i', strtotime($r['created_at'])); ?>
                        </div>
                        
                        <?php if(count($r_badges) > 0): ?>
                            <div style="display:flex; gap:4px; flex-wrap:wrap;">
                                <?php foreach($r_badges as $b): ?>
                                    <?php if(!empty($b['icon_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($b['icon_url']); ?>" title="<?php echo htmlspecialchars($b['name']); ?>" style="width:20px; height:20px; object-fit:contain;">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display:flex; gap:8px; align-items:center;">
                        <!-- Accept Answer Button (Only for Post Author) -->
                        <?php if($_SESSION['user_id'] == $post['user_id'] && !$post['is_solved']): ?>
                            <form method="POST" action="" style="margin:0;">
                                <input type="hidden" name="action" value="accept_reply">
                                <input type="hidden" name="reply_id" value="<?php echo $r['id']; ?>">
                                <button type="submit" style="background:rgba(16, 185, 129, 0.1); color:#10b981; border:1px solid #10b981; padding:6px 12px; border-radius:8px; font-size:0.8rem; font-weight:600; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='#10b981'; this.style.color='white'" onmouseout="this.style.background='rgba(16, 185, 129, 0.1)'; this.style.color='#10b981'">
                                    Tandai Solusi
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_id'] == $r['user_id']): ?>
                            <a href="index.php?page=community_edit&type=reply&id=<?php echo $r['id']; ?>" style="color:#3b82f6; padding:4px;" title="Edit Balasan">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>
                            <form method="POST" action="" style="margin:0;" onsubmit="return confirm('Yakin ingin menghapus balasan ini?');">
                                <input type="hidden" name="action" value="delete_reply">
                                <input type="hidden" name="reply_id" value="<?php echo $r['id']; ?>">
                                <button type="submit" style="background:transparent; border:none; color:#ef4444; cursor:pointer; padding:4px;" title="Hapus Balasan">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="color:var(--dash-text); font-size:1rem; line-height:1.6; white-space:pre-wrap; padding-left:calc(45px + 1rem);"><?php echo htmlspecialchars($r['content']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- FORM REPLY -->
    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem;">
        <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--dash-text);">Berikan Balasan</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="reply">
            <div style="margin-bottom: 1rem;">
                <textarea name="content" required rows="4" placeholder="Tulis jawaban atau komentar Anda di sini..." style="width: 100%; padding: 1rem; border: 1px solid var(--dash-border); border-radius: 12px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize: vertical;"></textarea>
            </div>
            <button type="submit" style="background: var(--dash-primary); color: white; border: none; padding: 0.8rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer;">Kirim Balasan</button>
        </form>
    </div>

</div>
