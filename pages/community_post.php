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
$my_uid = (int)($_SESSION['user_id'] ?? 0);
$stmt = $pdo->prepare("SELECT fp.*, u.name, u.picture, u.profile_title, u.profile_color,
                       (SELECT vote_type FROM forum_votes WHERE target_type='post' AND target_id=fp.id AND user_id=?) as my_vote
                       FROM forum_posts fp JOIN users u ON fp.user_id = u.id WHERE fp.id = ?");
$stmt->execute([$my_uid, $post_id]);
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
$stmt_rep = $pdo->prepare("SELECT fr.*, u.name, u.picture, u.profile_title, u.profile_color,
                           (SELECT vote_type FROM forum_votes WHERE target_type='reply' AND target_id=fr.id AND user_id=?) as my_vote
                           FROM forum_replies fr JOIN users u ON fr.user_id = u.id 
                           WHERE fr.post_id = ? ORDER BY fr.is_accepted DESC, fr.created_at ASC");
$stmt_rep->execute([$my_uid, $post_id]);
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
            <div style="flex-shrink:0; cursor:pointer; position:relative; width:60px; height:60px;" onclick="showUserProfile(<?php echo (int)$post['user_id']; ?>)" title="Lihat profil <?php echo htmlspecialchars($post['name']); ?>">
                <?php if (!empty($post['picture'])): ?>
                    <img src="<?php echo htmlspecialchars($post['picture']); ?>" alt="<?php echo htmlspecialchars($post['name']); ?>" style="width: 60px; height: 60px; border-radius: 50%; border:3px solid <?php echo $p_border; ?>; object-fit:cover; position:absolute; inset:0;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <?php endif; ?>
                <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--dash-primary); color: white; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: bold; border:3px solid <?php echo $p_border; ?>; position:absolute; inset:0; display:<?php echo empty($post['picture']) ? 'flex' : 'none'; ?>;">
                    <?php echo substr(htmlspecialchars($post['name']), 0, 1); ?>
                </div>
            </div>
            
            <div style="flex:1;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-weight:700; font-size:1.1rem; color:var(--dash-text); cursor:pointer;" onclick="showUserProfile(<?php echo (int)$post['user_id']; ?>)"><?php echo htmlspecialchars($post['name']); ?></span>
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
        <div id="raw-post-content" style="display:none;"><?php echo htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?></div>
        <div id="rendered-post-content" class="markdown-body" style="background: transparent; color: var(--dash-text); font-family: inherit;"></div>

        <div data-vote-wrap style="display:flex; gap:1.5rem; align-items:center; margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid var(--dash-border);">
            <button type="button" data-vote-btn="up" onclick="voteOnTarget('post', <?php echo (int)$post['id']; ?>, 'up', this)" style="display:flex; align-items:center; gap:6px; background:none; border:none; cursor:pointer; padding:0; color:<?php echo ($post['my_vote'] === 'up') ? '#3b82f6' : 'var(--dash-text-muted)'; ?>; font-size:0.9rem; font-weight:<?php echo ($post['my_vote'] === 'up') ? '700' : '500'; ?>;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" /></svg>
                Suka (<span data-vote-count="up"><?php echo $post['upvotes'] ?? 0; ?></span>)
            </button>
            <button type="button" data-vote-btn="down" onclick="voteOnTarget('post', <?php echo (int)$post['id']; ?>, 'down', this)" style="display:flex; align-items:center; gap:6px; background:none; border:none; cursor:pointer; padding:0; color:<?php echo ($post['my_vote'] === 'down') ? '#ef4444' : 'var(--dash-text-muted)'; ?>; font-size:0.9rem; font-weight:<?php echo ($post['my_vote'] === 'down') ? '700' : '500'; ?>;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" style="transform:scaleY(-1);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" /></svg>
                Tidak Suka (<span data-vote-count="down"><?php echo $post['downvotes'] ?? 0; ?></span>)
            </button>
        </div>
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
                    <div style="flex-shrink:0; cursor:pointer; position:relative; width:45px; height:45px;" onclick="showUserProfile(<?php echo (int)$r['user_id']; ?>)" title="Lihat profil <?php echo htmlspecialchars($r['name']); ?>">
                        <?php if (!empty($r['picture'])): ?>
                            <img src="<?php echo htmlspecialchars($r['picture']); ?>" alt="<?php echo htmlspecialchars($r['name']); ?>" style="width: 45px; height: 45px; border-radius: 50%; border:2px solid <?php echo $r_border; ?>; object-fit:cover; position:absolute; inset:0;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <?php endif; ?>
                        <div style="width: 45px; height: 45px; border-radius: 50%; background: var(--dash-primary); color: white; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold; border:2px solid <?php echo $r_border; ?>; position:absolute; inset:0; display:<?php echo empty($r['picture']) ? 'flex' : 'none'; ?>;">
                            <?php echo substr(htmlspecialchars($r['name']), 0, 1); ?>
                        </div>
                    </div>
                    
                    <div style="flex:1;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-weight:600; color:var(--dash-text); cursor:pointer;" onclick="showUserProfile(<?php echo (int)$r['user_id']; ?>)"><?php echo htmlspecialchars($r['name']); ?></span>
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

                <div id="raw-reply-content-<?php echo $r['id']; ?>" style="display:none;"><?php echo htmlspecialchars($r['content'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="markdown-body reply-markdown" data-target="reply-<?php echo $r['id']; ?>" style="background: transparent; color: var(--dash-text); font-family: inherit; padding-left:calc(45px + 1rem);"></div>

                <div data-vote-wrap style="display:flex; gap:1.25rem; align-items:center; margin-top:0.75rem; padding-left:calc(45px + 1rem);">
                    <button type="button" data-vote-btn="up" onclick="voteOnTarget('reply', <?php echo (int)$r['id']; ?>, 'up', this)" style="display:flex; align-items:center; gap:5px; background:none; border:none; cursor:pointer; padding:0; color:<?php echo ($r['my_vote'] === 'up') ? '#3b82f6' : 'var(--dash-text-muted)'; ?>; font-size:0.8rem; font-weight:<?php echo ($r['my_vote'] === 'up') ? '700' : '500'; ?>;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" /></svg>
                        <span data-vote-count="up"><?php echo $r['upvotes'] ?? 0; ?></span>
                    </button>
                    <button type="button" data-vote-btn="down" onclick="voteOnTarget('reply', <?php echo (int)$r['id']; ?>, 'down', this)" style="display:flex; align-items:center; gap:5px; background:none; border:none; cursor:pointer; padding:0; color:<?php echo ($r['my_vote'] === 'down') ? '#ef4444' : 'var(--dash-text-muted)'; ?>; font-size:0.8rem; font-weight:<?php echo ($r['my_vote'] === 'down') ? '700' : '500'; ?>;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" style="transform:scaleY(-1);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" /></svg>
                        <span data-vote-count="down"><?php echo $r['downvotes'] ?? 0; ?></span>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- FORM REPLY -->
    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem;">
        <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--dash-text);">Berikan Balasan</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="reply">
            <div style="margin-bottom: 1rem;">
                <textarea name="content" required rows="4" placeholder="Tulis jawaban atau komentar Anda di sini... Contoh: ![gambar](https://example.com/image.jpg)" style="width: 100%; padding: 1rem; border: 1px solid var(--dash-border); border-radius: 12px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize: vertical;"></textarea>
            </div>
            <button type="submit" style="background: var(--dash-primary); color: white; border: none; padding: 0.8rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer;">Kirim Balasan</button>
        </form>
    </div>

</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.2.0/github-markdown-dark.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css">
<script src="https://cdn.jsdelivr.net/npm/marked@4.3.0/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>

<style>
    .markdown-body { background: transparent !important; color: var(--dash-text); font-family: inherit !important; font-size: 1rem; line-height: 1.8; }
    .markdown-body pre { background-color: #1e1e1e !important; border: 1px solid var(--dash-border); border-radius: 8px; }
    .markdown-body pre, .markdown-body pre code { font-family: 'Fira Code', 'Consolas', monospace; color: #abb2bf !important; }
    .markdown-body p code, .markdown-body li code, .markdown-body h1 code, .markdown-body h2 code, .markdown-body h3 code { color: #d63384; background: rgba(214, 51, 132, 0.1); padding: 0.2em 0.4em; border-radius: 4px; font-size: 0.9em; font-family: 'Fira Code', 'Consolas', monospace; }
    .markdown-body a { color: #3b82f6; text-decoration: none; }
    .markdown-body a:hover { text-decoration: underline; }
    .markdown-body img { border-radius: 8px; max-width: 100%; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .markdown-body pre code span { color: inherit; }
    .reply-markdown { margin-top: 0.75rem; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.marked && window.hljs) {
            marked.setOptions({
                breaks: true,
                gfm: true,
                highlight: function(code, lang) {
                    const language = hljs.getLanguage(lang) ? lang : 'plaintext';
                    return hljs.highlight(code, { language }).value;
                }
            });

            const postRaw = document.getElementById('raw-post-content');
            if (postRaw) {
                const rendered = document.getElementById('rendered-post-content');
                rendered.innerHTML = marked.parse(postRaw.textContent || postRaw.innerText || '');
            }

            document.querySelectorAll('.reply-markdown').forEach(function (el) {
                const targetId = el.getAttribute('data-target');
                const raw = document.getElementById('raw-reply-content-' + targetId.replace('reply-', ''));
                if (raw) {
                    el.innerHTML = marked.parse(raw.textContent || raw.innerText || '');
                }
            });
        }
    });
</script>