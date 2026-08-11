<?php
// Proteksi ketat: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'><h1 style='color:var(--dash-text);'>Akses Ditolak</h1></div>";
    exit();
}

require_once 'config/db.php';

$course_id = $_GET['course_id'] ?? null;
if (!$course_id) {
    header("Location: index.php?page=admin_courses");
    exit();
}

// Ambil info course
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();
if (!$course) {
    echo "Kelas tidak ditemukan.";
    exit();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_module') {
        $title = $_POST['title'];
        $content_type = $_POST['content_type'];
        $content_text = $_POST['content_text'] ?? '';
        $video_url = $_POST['video_url'] ?? '';
        $xp_reward = $_POST['xp_reward'] ?? 0;
        $unlock_keyword = !empty($_POST['unlock_keyword']) ? trim($_POST['unlock_keyword']) : null;
        $thumbnail = $_POST['thumbnail'] ?? null;
        $attachment_file = null;
        
        // Ambil order_index terakhir
        $stmt_order = $pdo->prepare("SELECT MAX(order_index) FROM materials WHERE course_id = ?");
        $stmt_order->execute([$course_id]);
        $last_order = $stmt_order->fetchColumn();
        $new_order = $last_order !== false ? $last_order + 1 : 1;
        
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
        
        $stmt_add = $pdo->prepare("INSERT INTO materials (course_id, title, content_type, content_text, video_url, xp_reward, unlock_keyword, order_index, thumbnail, attachment_file) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_add->execute([$course_id, $title, $content_type, $content_text, $video_url, $xp_reward, $unlock_keyword, $new_order, $thumbnail, $attachment_file]);
        
        header("Location: index.php?page=admin_modules&course_id=" . $course_id);
        exit();
    } elseif ($action === 'delete_module') {
        $module_id = $_POST['module_id'];
        $stmt_del = $pdo->prepare("DELETE FROM materials WHERE id = ? AND course_id = ?");
        $stmt_del->execute([$module_id, $course_id]);
        header("Location: index.php?page=admin_modules&course_id=" . $course_id);
        exit();
    }
}

// Ambil semua bab materi
$stmt_mat = $pdo->prepare("SELECT * FROM materials WHERE course_id = ? ORDER BY order_index ASC");
$stmt_mat->execute([$course_id]);
$materials = $stmt_mat->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <div style="display:flex; align-items:center; gap: 0.5rem; margin-bottom:0.5rem;">
                <a href="index.php?page=admin_courses" style="color:var(--dash-text-muted); font-size:0.9rem;">&larr; Kembali ke Kelas</a>
            </div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.25rem;">Kelola Bab Materi</h1>
            <p style="color: var(--dash-primary); font-weight:600; font-size:1.1rem;"><?php echo htmlspecialchars($course['title']); ?></p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 2rem;">
        <!-- Daftar Materi -->
        <div>
            <?php if (count($materials) === 0): ?>
                <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 3rem; text-align: center; border-radius: 16px;">
                    <h3 style="color: var(--dash-text-muted);">Belum ada bab materi di kelas ini.</h3>
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:1rem;">
                    <?php foreach ($materials as $index => $m): ?>
                    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 12px; padding: 1.5rem; display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.5rem;">
                                <div style="background:var(--dash-bg); width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; color:var(--dash-text-muted); font-size:0.85rem; border:1px solid var(--dash-border);">
                                    <?php echo $index + 1; ?>
                                </div>
                                <h3 style="margin:0; color:var(--dash-text); font-size:1.1rem;"><?php echo htmlspecialchars($m['title']); ?></h3>
                                <span style="background:rgba(245, 158, 11, 0.1); color:#f59e0b; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:bold;">
                                    <?php echo strtoupper($m['content_type']); ?>
                                </span>
                            </div>
                            
                            <div style="margin-left: 2.5rem;">
                                <div style="color:var(--dash-text-muted); font-size:0.85rem; margin-bottom:0.5rem;">
                                    <span style="font-weight:600; color:#10b981;">+<?php echo $m['xp_reward']; ?> XP</span> Reward
                                </div>
                                
                                <?php if ($m['unlock_keyword']): ?>
                                <div style="background:rgba(239, 68, 68, 0.05); border:1px dashed #ef4444; color:#ef4444; padding:0.5rem; border-radius:6px; font-size:0.8rem; display:inline-flex; align-items:center; gap:0.5rem;">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                    Terkunci. Clue Rahasia: <b><?php echo htmlspecialchars($m['unlock_keyword']); ?></b>
                                </div>
                                <?php else: ?>
                                <div style="color:#64748b; font-size:0.8rem; display:flex; align-items:center; gap:0.5rem;">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                                    Terbuka Publik
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div style="display:flex; gap: 8px;">
                            <a href="index.php?page=admin_modules_edit&id=<?php echo $m['id']; ?>" style="background:transparent; border:none; color:#f59e0b; cursor:pointer;" title="Edit">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>
                            <form method="POST" action="" style="margin:0;">
                                <input type="hidden" name="module_id" value="<?php echo $m['id']; ?>">
                                <button type="submit" name="action" value="delete_module" onclick="return confirm('Hapus bab materi ini?');" style="background:transparent; border:none; color:#ef4444; cursor:pointer;" title="Hapus">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Form Tambah -->
        <div>
            <div id="editor-wrapper" style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; position: sticky; top: 2rem;">
                <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--dash-text);">Tambah Bab Materi</h3>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_module">
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Judul Materi</label>
                        <input type="text" name="title" required placeholder="Misal: Pengenalan HTML" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    </div>
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">URL Thumbnail (Opsional)</label>
                        <input type="url" name="thumbnail" placeholder="Misal: https://contoh.com/gambar.jpg" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    </div>
                    
                    <div style="margin-bottom: 1.25rem; padding: 1.5rem; border: 2px dashed var(--dash-border); border-radius: 12px; text-align: center; background: rgba(0,0,0,0.02);">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600; color: var(--dash-text);">File Bahan Ajar (Opsional)</label>
                        <p style="font-size: 0.8rem; color: var(--dash-text-muted); margin-bottom: 1rem;">Upload file (PDF, ZIP, PPT, DOCX) agar siswa dapat mengunduhnya.</p>
                        <input type="file" name="attachment_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar" style="display: block; margin: 0 auto; color: var(--dash-text);">
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Tipe Konten</label>
                        <select name="content_type" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                            <option value="mixed">Teks & Video (Kombinasi)</option>
                            <option value="text">Hanya Teks / Artikel</option>
                            <option value="video">Hanya Video</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">URL Video YouTube (Opsional)</label>
                        <input type="text" name="video_url" placeholder="Masukkan ID atau URL Video (contoh: dQw4w9WgXcQ)" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    </div>
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Isi Materi Teks (Mendukung Markdown)</label>
                        <textarea name="content_text" id="content_text" rows="6" placeholder="Tulis penjelasan, kode, atau materi artikel di sini..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize: vertical;"></textarea>
                    </div>
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">XP Reward</label>
                        <input type="number" name="xp_reward" value="50" min="0" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    </div>
                    
                    <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(59, 130, 246, 0.05); border: 1px dashed var(--dash-primary); border-radius: 8px;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-primary);">Materi Hunting (Kata Kunci)</label>
                        <p style="font-size:0.75rem; color:var(--dash-text-muted); margin-bottom:0.75rem;">Kosongkan jika materi ini bebas diakses. Jika diisi, siswa harus menemukan kata kunci ini di bab sebelumnya.</p>
                        <input type="text" name="unlock_keyword" placeholder="Contoh: variabel" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-primary); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    </div>
                    
                    <button type="submit" style="width: 100%; background: var(--dash-primary); color: white; border: none; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer;">Simpan Materi</button>
                </form>
            </div>
        </div>
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
