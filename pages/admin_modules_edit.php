<?php
// Proteksi ketat: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'><h1 style='color:var(--dash-text);'>Akses Ditolak</h1></div>";
    exit();
}

require_once 'config/db.php';

$module_id = $_GET['id'] ?? null;
if (!$module_id) {
    header("Location: index.php?page=admin_courses");
    exit();
}

// Ambil info materi
$stmt = $pdo->prepare("SELECT * FROM materials WHERE id = ?");
$stmt->execute([$module_id]);
$module = $stmt->fetch();
if (!$module) {
    echo "Materi tidak ditemukan.";
    exit();
}

// Handle Form Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content_type = $_POST['content_type'];
    $content_text = $_POST['content_text'];
    $video_url = $_POST['video_url'] ?? null;
    $xp_reward = $_POST['xp_reward'] ?: 50;
    $unlock_keyword = !empty($_POST['unlock_keyword']) ? trim($_POST['unlock_keyword']) : null;
    $thumbnail = $_POST['thumbnail'] ?? null;
    $attachment_file = $module['attachment_file'] ?? null;
    
    // Handle File Upload untuk Bahan Ajar
    if (isset($_FILES['attachment_file']) && $_FILES['attachment_file']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/materials/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $ext = pathinfo($_FILES['attachment_file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('bahan_') . '.' . $ext;
        if (move_uploaded_file($_FILES['attachment_file']['tmp_name'], $upload_dir . $filename)) {
            $attachment_file = $upload_dir . $filename;
        }
    }
    
    $stmt_upd = $pdo->prepare("UPDATE materials SET title = ?, content_type = ?, content_text = ?, video_url = ?, xp_reward = ?, unlock_keyword = ?, thumbnail = ?, attachment_file = ? WHERE id = ?");
    $stmt_upd->execute([$title, $content_type, $content_text, $video_url, $xp_reward, $unlock_keyword, $thumbnail, $attachment_file, $module_id]);
    
    header("Location: index.php?page=admin_modules&course_id=" . $module['course_id']);
    exit();
}
?>

<div class="dash-left" style="grid-column: 1 / -1; max-width: 800px; margin: 0 auto; width: 100%;">
    <div id="editor-wrapper" style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem; position: sticky; top: 2rem;">
        <div class="section-header" style="margin-bottom: 2rem;">
            <div>
                <div style="display:flex; align-items:center; gap: 0.5rem; margin-bottom:0.5rem;">
                    <a href="index.php?page=admin_modules&course_id=<?php echo $module['course_id']; ?>" style="color:var(--dash-text-muted); font-size:0.9rem;">&larr; Kembali ke Daftar Materi</a>
                </div>
                <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.25rem;">Edit Bab Materi</h1>
            </div>
        </div>

        <form method="POST" action="" enctype="multipart/form-data">
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Judul Bab</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($module['title']); ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">URL Thumbnail (Opsional)</label>
                <input type="url" name="thumbnail" value="<?php echo htmlspecialchars($module['thumbnail'] ?? ''); ?>" placeholder="Misal: https://contoh.com/gambar.jpg" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                <?php if (!empty($module['thumbnail'])): ?>
                    <img src="<?php echo htmlspecialchars($module['thumbnail']); ?>" style="max-height: 80px; margin-top: 10px; border-radius:8px;">
                <?php endif; ?>
            </div>
            
            <div style="margin-bottom: 1.25rem; padding: 1.5rem; border: 2px dashed var(--dash-border); border-radius: 12px; text-align: center; background: rgba(0,0,0,0.02);">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600; color: var(--dash-text);">File Bahan Ajar (Opsional)</label>
                <p style="font-size: 0.8rem; color: var(--dash-text-muted); margin-bottom: 1rem;">Upload file (PDF, ZIP, PPT, DOCX) agar siswa dapat mengunduhnya.</p>
                <input type="file" name="attachment_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar" style="display: block; margin: 0 auto; color: var(--dash-text);">
                <?php if (!empty($module['attachment_file'])): ?>
                    <div style="margin-top: 1rem; padding: 0.5rem; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 6px; display: inline-block; font-size: 0.85rem;">
                        <i class="fa fa-check-circle"></i> File tersimpan: <?php echo basename($module['attachment_file']); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Tipe Konten</label>
                <select name="content_type" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    <option value="mixed" <?php echo $module['content_type'] === 'mixed' ? 'selected' : ''; ?>>Teks & Video (Kombinasi)</option>
                    <option value="text" <?php echo $module['content_type'] === 'text' ? 'selected' : ''; ?>>Teks / Artikel / Kode</option>
                    <option value="video" <?php echo $module['content_type'] === 'video' ? 'selected' : ''; ?>>Video URL (YouTube)</option>
                </select>
            </div>
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">URL Video YouTube (Opsional)</label>
                <input type="text" name="video_url" value="<?php echo htmlspecialchars($module['video_url'] ?? ''); ?>" placeholder="Masukkan ID atau URL Video (contoh: dQw4w9WgXcQ)" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Isi Materi Teks (Mendukung Markdown)</label>
                <textarea name="content_text" id="content_text" rows="8" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($module['content_text'] ?? ''); ?></textarea>
            </div>
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">XP Reward</label>
                <input type="number" name="xp_reward" value="<?php echo htmlspecialchars($module['xp_reward']); ?>" min="0" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 2rem; padding: 1rem; background: rgba(59, 130, 246, 0.05); border: 1px dashed var(--dash-primary); border-radius: 8px;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-primary);">Materi Hunting (Kata Kunci)</label>
                <p style="font-size:0.75rem; color:var(--dash-text-muted); margin-bottom:0.75rem;">Kosongkan jika materi ini bebas diakses. Jika diisi, siswa harus menemukan kata kunci ini di bab sebelumnya.</p>
                <input type="text" name="unlock_keyword" value="<?php echo htmlspecialchars($module['unlock_keyword'] ?? ''); ?>" placeholder="Contoh: variabel" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-primary); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
            </div>
            
            <button type="submit" style="width: 100%; background: #f59e0b; color: white; border: none; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer;">Simpan Perubahan Bab</button>
        </form>
    </div>
</div>

<!-- EasyMDE CSS & JS & FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<!-- Library Markdown & Code Highlighting (Sama seperti di Frontend) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.2.0/github-markdown-dark.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css">
<script src="https://cdn.jsdelivr.net/npm/marked@4.3.0/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
<style>
    /* Kostumisasi EasyMDE menyesuaikan dark mode */
    .editor-toolbar { border-color: var(--dash-border) !important; opacity: 0.9; white-space: nowrap; overflow-x: auto; overflow-y: hidden; }
    .editor-toolbar::-webkit-scrollbar { height: 6px; }
    .editor-toolbar::-webkit-scrollbar-thumb { background-color: var(--dash-text-muted); border-radius: 4px; }
    .editor-toolbar button { color: var(--dash-text) !important; display: inline-block; }
    .editor-toolbar button.active, .editor-toolbar button:hover { background: var(--dash-sidebar) !important; }
    .CodeMirror { border-color: var(--dash-border) !important; background: var(--dash-bg) !important; color: var(--dash-text) !important; font-size: 1.05rem; }
    
    /* Fix Fullscreen agar berada di bawah navbar dashboard */
    .editor-toolbar.fullscreen { z-index: 999999 !important; background: var(--dash-bg) !important; top: 85px !important; position: fixed !important; left: 0 !important; width: 100% !important; }
    .CodeMirror-fullscreen { z-index: 999999 !important; background: var(--dash-bg) !important; top: 135px !important; position: fixed !important; left: 0 !important; bottom: 0 !important; }
    .editor-preview-side, .editor-preview-active-side { z-index: 999999 !important; background: var(--dash-bg) !important; top: 135px !important; position: fixed !important; right: 0 !important; bottom: 0 !important; width: 50% !important; border-left: 2px solid var(--dash-text-muted) !important; }
    
    /* Fix Normal Preview secara Universal (baik fullscreen maupun tidak) */
    .editor-preview-active { z-index: 9999999 !important; background: var(--dash-bg) !important; display: block !important; opacity: 1 !important; visibility: visible !important; position: absolute !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; min-height: 100% !important; }
    body.is-fullscreen .editor-preview-active { position: fixed !important; top: 135px !important; left: 0 !important; width: 100% !important; height: auto !important; bottom: 0 !important; }
    
    /* Body class saat fullscreen aktif untuk meruntuhkan stacking context dan menyembunyikan sidebar */
    body.is-fullscreen .dash-sidebar, body:has(.editor-toolbar.fullscreen) .dash-sidebar { display: none !important; }
    body.is-fullscreen #editor-wrapper, body:has(.editor-toolbar.fullscreen) #editor-wrapper { position: static !important; z-index: 999999 !important; }
    /* Kostumisasi Markdown Body untuk menyatu dengan tema */
    .editor-preview.markdown-body { background: transparent !important; color: var(--dash-text); font-family: inherit !important; font-size: 1rem; line-height: 1.8; }
    .editor-preview.markdown-body pre { background-color: #1e1e1e !important; border: 1px solid var(--dash-border); border-radius: 8px; }
    .editor-preview.markdown-body pre, .editor-preview.markdown-body pre code { font-family: 'Fira Code', 'Consolas', monospace; color: #abb2bf !important; }
    .editor-preview.markdown-body p code, .editor-preview.markdown-body li code, .editor-preview.markdown-body h1 code, .editor-preview.markdown-body h2 code, .editor-preview.markdown-body h3 code { color: #d63384; background: rgba(214, 51, 132, 0.1); padding: 0.2em 0.4em; border-radius: 4px; font-size: 0.9em; font-family: 'Fira Code', 'Consolas', monospace; }
    .editor-preview.markdown-body a { color: #3b82f6; text-decoration: none; }
    .editor-preview.markdown-body a:hover { text-decoration: underline; }
    .editor-preview.markdown-body img { border-radius: 8px; max-width: 100%; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .editor-preview.markdown-body pre code span { color: inherit; }
</style>
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
<script>
    // Konfigurasi Marked.js untuk preview dengan fail-safe
    marked.setOptions({
        highlight: function(code, lang) {
            try {
                const language = hljs.getLanguage(lang) ? lang : 'plaintext';
                return hljs.highlight(code, { language }).value;
            } catch (e) {
                return code; // Fallback aman
            }
        },
        breaks: true,
        gfm: true
    });

    const mde = new EasyMDE({
        element: document.getElementById('content_text'),
        autoDownloadFontAwesome: false,
        uploadImage: true,
        imageUploadEndpoint: 'api/upload_media.php',
        imageMaxSize: 52428800, // 50MB
        imageAccept: 'image/png, image/jpeg, image/gif, image/webp, application/pdf, application/zip, application/x-rar-compressed',
        spellChecker: false,
        promptURLs: true,
        minHeight: "300px",
        maxHeight: "500px",
        toolbar: ["bold", "italic", "heading", "|", "code", "quote", "unordered-list", "ordered-list", "|", "link", "image", "|", "preview", "side-by-side", "fullscreen", "|", "guide"],
        previewClass: ["editor-preview", "markdown-body"],
        previewRender: function(plainText) {
            try {
                if (typeof marked.parse === 'function') {
                    return marked.parse(plainText);
                }
                return marked(plainText);
            } catch (e) {
                console.error("Preview Error:", e);
                return "<div style='color:red; padding:2rem;'>Gagal merender preview: " + e.message + "</div>";
            }
        }
    });
    
    // Workaround super-kuat untuk Stacking Context
    setInterval(function() {
        if (mde.isFullscreenActive() || document.querySelector('.editor-toolbar.fullscreen')) {
            document.body.classList.add('is-fullscreen');
        } else {
            document.body.classList.remove('is-fullscreen');
        }
    }, 100);
</script>
