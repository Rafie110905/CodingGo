<?php
require_once 'config/db.php';

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? null;

if (!$id || !in_array($type, ['post', 'reply'])) {
    header("Location: index.php?page=community");
    exit();
}

$title = '';
$content = '';
$redirect_url = 'index.php?page=community';

// Fetch current data
if ($type === 'post') {
    $stmt = $pdo->prepare("SELECT * FROM forum_posts WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if ($item) {
        $title = $item['title'];
        $content = $item['content'];
        $owner_id = $item['user_id'];
        $redirect_url = "index.php?page=community_post&id=" . $id;
    }
} else {
    $stmt = $pdo->prepare("SELECT * FROM forum_replies WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if ($item) {
        $content = $item['content'];
        $owner_id = $item['user_id'];
        $redirect_url = "index.php?page=community_post&id=" . $item['post_id'];
    }
}

if (!$item) {
    echo "Item tidak ditemukan.";
    exit();
}

// Verify authorization
if ($owner_id != $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin') {
    echo "Anda tidak memiliki akses untuk mengedit ini.";
    exit();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_content = trim($_POST['content']);
    
    if ($type === 'post') {
        $new_title = trim($_POST['title']);
        if (!empty($new_title) && !empty($new_content)) {
            $pdo->prepare("UPDATE forum_posts SET title = ?, content = ? WHERE id = ?")->execute([$new_title, $new_content, $id]);
            header("Location: " . $redirect_url);
            exit();
        }
    } else {
        if (!empty($new_content)) {
            $pdo->prepare("UPDATE forum_replies SET content = ? WHERE id = ?")->execute([$new_content, $id]);
            header("Location: " . $redirect_url);
            exit();
        }
    }
}
?>

<div class="dash-left" style="grid-column: 1 / -1; max-width: 800px; margin: 0 auto; width: 100%;">
    <div style="margin-bottom: 2rem;">
        <a href="<?php echo htmlspecialchars($redirect_url); ?>" style="color:var(--dash-text-muted); text-decoration:none; display:inline-flex; align-items:center; gap:8px; font-size:0.9rem;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </div>

    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem;">
        <h2 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--dash-text);">
            Edit <?php echo $type === 'post' ? 'Diskusi' : 'Balasan'; ?>
        </h2>
        
        <form method="POST" action="">
            <?php if ($type === 'post'): ?>
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600; color: var(--dash-text);">Judul</label>
                    <input type="text" name="title" required value="<?php echo htmlspecialchars($title); ?>" style="width: 100%; padding: 0.85rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; font-size: 1rem;">
                </div>
            <?php endif; ?>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600; color: var(--dash-text);">Isi Konten</label>
                <textarea name="content" required rows="8" style="width: 100%; padding: 0.85rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize: vertical; font-size: 1rem;"><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            
            <button type="submit" style="background: var(--dash-primary); color: white; border: none; padding: 1rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
