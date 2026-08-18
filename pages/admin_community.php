<?php
// Proteksi ketat: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'>
            <h1 style='color:var(--dash-text);'>Akses Ditolak</h1>
            <p style='color:var(--dash-text-muted);'>Anda tidak memiliki izin untuk mengakses halaman administrator.</p>
          </div>";
    exit();
}

require_once 'config/db.php';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'new_official_post') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $user_id = $_SESSION['user_id'];
        
        if (!empty($title) && !empty($content)) {
            $stmt = $pdo->prepare("INSERT INTO forum_posts (user_id, title, content, is_official) VALUES (?, ?, ?, 1)");
            $stmt->execute([$user_id, $title, $content]);
            $success_message = "Postingan Official berhasil diterbitkan!";
        }
    } elseif ($_POST['action'] === 'delete_post') {
        $post_id_to_delete = $_POST['post_id'];
        $pdo->prepare("DELETE FROM forum_posts WHERE id = ?")->execute([$post_id_to_delete]);
        $success_message = "Postingan berhasil dihapus.";
    }
}

// Fetch all posts with user data
$stmt = $pdo->query("SELECT fp.*, u.name, u.picture, 
                     (SELECT COUNT(*) FROM forum_replies WHERE post_id = fp.id) as reply_count 
                     FROM forum_posts fp 
                     JOIN users u ON fp.user_id = u.id 
                     ORDER BY fp.is_official DESC, fp.created_at DESC");
$posts = $stmt->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1; max-width: 1000px; margin: 0 auto;">
    <div class="section-header" style="margin-bottom: 2rem; border-bottom: 1px solid var(--dash-border); padding-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem; display:flex; align-items:center; gap:10px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="28" style="color:var(--dash-primary);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                Community Admin
            </h1>
            <p style="color: var(--dash-text-muted);">Kelola forum diskusi dan posting pemberitahuan atas nama CodingGo Official.</p>
        </div>
    </div>

    <?php if (isset($success_message)): ?>
        <div style="background: rgba(34, 197, 94, 0.1); color: #16a34a; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <div class="dash-grid-sidebar-rev" style="display: grid;  gap: 2rem;">
        
        <!-- Form Post Official -->
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; height: fit-content; position:sticky; top:2rem;">
            <h3 style="margin-top:0; color:var(--dash-text); margin-bottom:1rem; display:flex; align-items:center; gap:8px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="#f59e0b" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                Buat Postingan Official
            </h3>
            <p style="font-size:0.85rem; color:var(--dash-text-muted); margin-bottom:1.5rem;">Postingan ini akan di-pin dan menggunakan identitas "CodingGo Official".</p>
            
            <form method="POST">
                <input type="hidden" name="action" value="new_official_post">
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size:0.9rem; font-weight:600; color:var(--dash-text);">Judul / Topik</label>
                    <input type="text" name="title" required placeholder="Judul Pengumuman" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size:0.9rem; font-weight:600; color:var(--dash-text);">Isi Pesan</label>
                    <textarea name="content" required rows="6" placeholder="Ketik isi diskusi / pengumuman..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize:vertical;"></textarea>
                </div>
                
                <button type="submit" style="width: 100%; background: #f59e0b; color: white; border: none; padding: 0.85rem; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Publikasikan
                </button>
            </form>
        </div>

        <!-- Tabel Riwayat Komunitas -->
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; overflow: hidden;">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--dash-border);">
                <h3 style="margin: 0; color:var(--dash-text);">Manajemen Topik</h3>
            </div>
            
            <?php if (count($posts) === 0): ?>
                <div style="padding: 2rem; text-align: center; color: var(--dash-text-muted);">
                    Belum ada diskusi di komunitas.
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: rgba(0,0,0,0.02); border-bottom: 1px solid var(--dash-border);">
                                <th style="padding: 1rem; color: var(--dash-text-muted); font-size:0.85rem; width:60%;">TOPIK & PENULIS</th>
                                <th style="padding: 1rem; color: var(--dash-text-muted); font-size:0.85rem; text-align:center;">STATS</th>
                                <th style="padding: 1rem; color: var(--dash-text-muted); font-size:0.85rem; text-align:right;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $p): ?>
                            <tr style="border-bottom: 1px solid var(--dash-border); <?php echo $p['is_official'] ? 'background: rgba(245, 158, 11, 0.05);' : ''; ?>">
                                <td style="padding: 1rem;">
                                    <div style="font-weight:600; color:var(--dash-text); margin-bottom:4px; font-size:0.95rem;">
                                        <a href="index.php?page=community_post&id=<?php echo $p['id']; ?>" style="color:inherit; text-decoration:none;">
                                            <?php if($p['is_official']): ?>
                                                <span style="color:#f59e0b; margin-right:4px;" title="Official Post">📌</span>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($p['title']); ?>
                                        </a>
                                    </div>
                                    <div style="font-size:0.8rem; color:var(--dash-text-muted); display:flex; align-items:center; gap:6px;">
                                        Oleh: 
                                        <?php if($p['is_official']): ?>
                                            <span style="color:#f59e0b; font-weight:600; display:flex; align-items:center; gap:2px;">CodingGo Official <svg fill="currentColor" viewBox="0 0 20 20" width="14"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($p['name']); ?>
                                        <?php endif; ?>
                                        &middot; <?php echo date('d M Y', strtotime($p['created_at'])); ?>
                                    </div>
                                </td>
                                <td style="padding: 1rem; text-align:center;">
                                    <div style="font-size:0.85rem; color:var(--dash-text-muted);">
                                        <div><strong style="color:var(--dash-text);"><?php echo $p['upvotes']; ?></strong> Up</div>
                                        <div><strong style="color:var(--dash-text);"><?php echo $p['reply_count']; ?></strong> Balasan</div>
                                    </div>
                                </td>
                                <td style="padding: 1rem; text-align:right;">
                                    <div style="display:flex; justify-content:flex-end; gap:8px;">
                                        <a href="index.php?page=community_post&id=<?php echo $p['id']; ?>" title="Lihat Postingan" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: none; padding: 6px; border-radius: 6px; text-decoration:none;">
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        <form method="POST" style="margin:0;" onsubmit="return confirm('Yakin ingin menghapus diskusi ini?');">
                                            <input type="hidden" name="action" value="delete_post">
                                            <input type="hidden" name="post_id" value="<?php echo $p['id']; ?>">
                                            <button type="submit" title="Hapus Postingan" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: none; padding: 6px; border-radius: 6px; cursor: pointer;">
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
