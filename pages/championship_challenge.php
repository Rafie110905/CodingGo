<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';
$user_id = $_SESSION['user_id'];
$challenge_id = $_GET['id'] ?? null;

if (!$challenge_id) {
    header("Location: index.php?page=championship");
    exit();
}

// Fetch Challenge Info
$stmt_chal = $pdo->prepare("SELECT * FROM championship_challenges WHERE id = ?");
$stmt_chal->execute([$challenge_id]);
$challenge = $stmt_chal->fetch();

if (!$challenge) {
    echo "Challenge tidak ditemukan.";
    exit();
}

$championship_id = $challenge['championship_id'];

// Pastikan user sudah join turnamen ini
$stmt_check = $pdo->prepare("SELECT 1 FROM championship_participants WHERE championship_id = ? AND user_id = ?");
$stmt_check->execute([$championship_id, $user_id]);
if (!$stmt_check->fetch()) {
    header("Location: index.php?page=championship_detail&id=" . $championship_id);
    exit();
}

// Pastikan turnamen masih aktif
$stmt_champ = $pdo->prepare("SELECT status FROM championships WHERE id = ?");
$stmt_champ->execute([$championship_id]);
$champ = $stmt_champ->fetch();
$is_active = ($champ['status'] === 'active');

// Cek apakah challenge sudah diselesaikan
$stmt_comp = $pdo->prepare("SELECT 1 FROM championship_completed_challenges WHERE challenge_id = ? AND user_id = ?");
$stmt_comp->execute([$challenge_id, $user_id]);
$is_completed = $stmt_comp->fetch() !== false;

$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_completed && $is_active) {
    $user_answer = trim($_POST['answer'] ?? '');
    $correct_answer = trim($challenge['correct_answer']);
    
    // Auto-correct: case-insensitive check
    if (strtolower($user_answer) === strtolower($correct_answer)) {
        // JAWABAN BENAR!
        $xp_reward = $challenge['xp_reward'];
        
        $pdo->beginTransaction();
        try {
            // 1. Insert ke completed challenges
            $stmt_ins = $pdo->prepare("INSERT INTO championship_completed_challenges (challenge_id, user_id) VALUES (?, ?)");
            $stmt_ins->execute([$challenge_id, $user_id]);
            
            // 2. Tambah XP ke turnamen
            $stmt_upd_cp = $pdo->prepare("UPDATE championship_participants SET xp_earned = xp_earned + ? WHERE championship_id = ? AND user_id = ?");
            $stmt_upd_cp->execute([$xp_reward, $championship_id, $user_id]);
            
            // 3. Tambah XP ke global user xp
            $stmt_upd_user = $pdo->prepare("UPDATE users SET xp_points = xp_points + ? WHERE id = ?");
            $stmt_upd_user->execute([$xp_reward, $user_id]);
            
            $pdo->commit();
            
            $is_completed = true;
            $success_msg = 'Luar biasa! Jawaban Anda benar. Anda mendapatkan +' . $xp_reward . ' XP!';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = 'Terjadi kesalahan sistem saat memproses XP Anda.';
        }
    } else {
        // JAWABAN SALAH
        $error_msg = 'Oops! Jawaban Anda salah. Coba periksa kembali kode/teks Anda.';
    }
}

?>

<div class="container" style="padding: 2rem 0; min-height: 70vh;">
    <div style="display:flex; align-items:center; gap: 0.5rem; margin-bottom:2rem;">
        <a href="index.php?page=championship_detail&id=<?php echo $championship_id; ?>" style="color:var(--text-muted); font-size:0.9rem; text-decoration:none;">&larr; Kembali ke Dashboard Turnamen</a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
        
        <!-- Kolom Kiri: Soal -->
        <div style="background: var(--bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 1.5rem;">
                <h1 style="font-size: 2rem; color: var(--text); margin: 0;"><?php echo htmlspecialchars($challenge['title']); ?></h1>
                <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 6px 12px; border-radius: 20px; font-size: 0.9rem; font-weight: bold; border: 1px solid #f59e0b;">
                    +<?php echo $challenge['xp_reward']; ?> XP
                </span>
            </div>
            
            <hr style="border:0; border-top:1px solid var(--border-color); margin-bottom: 2rem;">
            
            <!-- Area Markdown -->
            <div id="raw-markdown-content" style="display:none;"><?php echo htmlspecialchars($challenge['description']); ?></div>
            <div id="rendered-markdown-content" class="markdown-body" style="background: transparent; color: var(--text); font-family: inherit;"></div>
        </div>
        
        <!-- Kolom Kanan: Form Jawaban -->
        <div>
            <div style="background: var(--bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; position: sticky; top: 2rem;">
                <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--text);">Kirim Jawaban Singkat</h3>
                
                <?php if ($success_msg): ?>
                    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; margin-bottom: 1.5rem; text-align: center;">
                        🎉 <?php echo $success_msg; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_msg): ?>
                    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; margin-bottom: 1.5rem; text-align: center;">
                        ❌ <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <?php if ($is_completed): ?>
                    <div style="text-align: center; padding: 1rem 0;">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">✅</div>
                        <h4 style="margin:0 0 0.5rem 0; color:var(--text);">Challenge Selesai!</h4>
                        <p style="color:var(--text-muted); font-size:0.9rem;">Anda telah memecahkan tantangan ini dan mendapatkan XP.</p>
                        <a href="index.php?page=championship_detail&id=<?php echo $championship_id; ?>" class="btn" style="display:block; margin-top:1.5rem; background: var(--bg-hover); color: var(--text); padding: 0.75rem; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid var(--border-color);">Kembali</a>
                    </div>
                <?php elseif (!$is_active): ?>
                    <div style="text-align: center; padding: 1rem 0;">
                        <h4 style="margin:0 0 0.5rem 0; color:#ef4444;">Turnamen Tidak Aktif</h4>
                        <p style="color:var(--text-muted); font-size:0.9rem;">Anda tidak dapat mengirimkan jawaban saat ini.</p>
                    </div>
                <?php else: ?>
                    <form method="POST" action="">
                        <label style="display:block; margin-bottom:0.5rem; color:var(--text-muted); font-size:0.9rem;">Masukkan kode atau jawaban persis (Auto-Correct):</label>
                        <input type="text" name="answer" required placeholder="Contoh: print('Hello')" autocomplete="off" style="width:100%; padding:1rem; border-radius:8px; border:2px solid var(--border-color); background:var(--bg); color:var(--text); font-family:monospace; font-size:1rem; margin-bottom:1rem; box-sizing:border-box;">
                        
                        <button type="submit" class="btn btn-primary" style="width:100%; background: var(--primary); color: white; border: none; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);">
                            Kirim Jawaban &rarr;
                        </button>
                        <p style="text-align:center; color:var(--text-muted); font-size:0.75rem; margin-top:0.75rem;">Jawaban Anda akan langsung dikoreksi oleh sistem.</p>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</div>

<!-- Library Markdown & Code Highlighting -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.2.0/github-markdown-dark.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css">
<script src="https://cdn.jsdelivr.net/npm/marked@4.3.0/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>

<style>
    /* Kostumisasi Markdown Body untuk menyatu dengan tema */
    .markdown-body { background: transparent !important; color: var(--text); font-family: inherit !important; font-size: 1rem; line-height: 1.8; }
    .markdown-body pre { background-color: #1e1e1e !important; border: 1px solid var(--border-color); border-radius: 8px; }
    .markdown-body pre, .markdown-body pre code { font-family: 'Fira Code', 'Consolas', monospace; color: #abb2bf !important; }
    .markdown-body p code, .markdown-body li code, .markdown-body h1 code, .markdown-body h2 code, .markdown-body h3 code { color: #d63384; background: rgba(214, 51, 132, 0.1); padding: 0.2em 0.4em; border-radius: 4px; font-size: 0.9em; font-family: 'Fira Code', 'Consolas', monospace; }
    .markdown-body a { color: #3b82f6; text-decoration: none; }
    .markdown-body a:hover { text-decoration: underline; }
    .markdown-body img { border-radius: 8px; max-width: 100%; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    /* Pastikan warna syntax highlighter highlight.js tetap tampil dan tidak tertimpa var(--text) */
    .markdown-body pre code span { color: inherit; }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const rawEl = document.getElementById('raw-markdown-content');
        if (rawEl) {
            const rawContent = rawEl.textContent || rawEl.innerText;
            
            marked.setOptions({
                highlight: function(code, lang) {
                    try {
                        const language = hljs.getLanguage(lang) ? lang : 'plaintext';
                        return hljs.highlight(code, { language }).value;
                    } catch(e) {
                        return code;
                    }
                },
                breaks: true,
                gfm: true
            });

            const htmlContent = marked.parse(rawContent);
            document.getElementById('rendered-markdown-content').innerHTML = htmlContent;
        }
    });
</script>
