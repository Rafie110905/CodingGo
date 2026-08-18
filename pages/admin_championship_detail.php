<?php
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'><h1 style='color:var(--dash-text);'>Akses Ditolak</h1></div>";
    exit();
}

require_once 'config/db.php';

$championship_id = $_GET['id'] ?? null;
if (!$championship_id) {
    header("Location: index.php?page=admin_championship");
    exit();
}

// Fetch Championship
$stmt = $pdo->prepare("SELECT * FROM championships WHERE id = ?");
$stmt->execute([$championship_id]);
$champ = $stmt->fetch();
if (!$champ) {
    echo "Turnamen tidak ditemukan.";
    exit();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_challenge') {
        $title = $_POST['title'];
        $description = $_POST['description']; // Markdown
        $correct_answer = $_POST['correct_answer'];
        $xp_reward = $_POST['xp_reward'];
        
        $stmt_add = $pdo->prepare("INSERT INTO championship_challenges (championship_id, title, description, correct_answer, xp_reward) VALUES (?, ?, ?, ?, ?)");
        $stmt_add->execute([$championship_id, $title, $description, $correct_answer, $xp_reward]);
        
        header("Location: index.php?page=admin_championship_detail&id=" . $championship_id . "&success=added");
        exit();
    } elseif ($action === 'edit_challenge') {
        $challenge_id = $_POST['challenge_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $correct_answer = $_POST['correct_answer'];
        $xp_reward = $_POST['xp_reward'];
        
        $stmt_edit = $pdo->prepare("UPDATE championship_challenges SET title=?, description=?, correct_answer=?, xp_reward=? WHERE id=? AND championship_id=?");
        $stmt_edit->execute([$title, $description, $correct_answer, $xp_reward, $challenge_id, $championship_id]);
        
        header("Location: index.php?page=admin_championship_detail&id=" . $championship_id . "&success=edited");
        exit();
    } elseif ($action === 'delete_challenge') {
        $challenge_id = $_POST['challenge_id'];
        
        $pdo->prepare("DELETE FROM championship_completed_challenges WHERE challenge_id=?")->execute([$challenge_id]);
        $pdo->prepare("DELETE FROM championship_challenges WHERE id=? AND championship_id=?")->execute([$challenge_id, $championship_id]);
        
        header("Location: index.php?page=admin_championship_detail&id=" . $championship_id . "&success=deleted");
        exit();
    }
}

// Fetch Challenges
$stmt_chal = $pdo->prepare("SELECT * FROM championship_challenges WHERE championship_id = ? ORDER BY created_at ASC");
$stmt_chal->execute([$championship_id]);
$challenges = $stmt_chal->fetchAll();

// Fetch Leaderboard
$stmt_lb = $pdo->prepare("
    SELECT u.name, cp.xp_earned, cp.joined_at 
    FROM championship_participants cp
    JOIN users u ON cp.user_id = u.id
    WHERE cp.championship_id = ?
    ORDER BY cp.xp_earned DESC, cp.joined_at ASC
    LIMIT 20
");
$stmt_lb->execute([$championship_id]);
$leaderboard = $stmt_lb->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <div style="display:flex; align-items:center; gap: 0.5rem; margin-bottom:0.5rem;">
                <a href="index.php?page=admin_championship" style="color:var(--dash-text-muted); font-size:0.9rem;">&larr; Kembali ke Daftar Turnamen</a>
            </div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.25rem;">Kelola Turnamen: <?php echo htmlspecialchars($champ['title']); ?></h1>
            <p style="color: var(--dash-text-muted);">Status: <strong style="color:var(--dash-primary);"><?php echo strtoupper($champ['status']); ?></strong> | Berakhir: <?php echo date('d M Y', strtotime($champ['end_date'])); ?></p>
        </div>
    </div>

    <div class="dash-grid-fixed-right" style="display: grid;  gap: 2rem;">
        
        <!-- Kolom Kiri: Challenges -->
        <div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <h2 style="font-size: 1.4rem; color: var(--dash-text); margin:0;">Daftar Challenge Khusus</h2>
                <button onclick="openAddModal()" class="btn btn-primary" style="background: #10b981; color:white; padding: 0.5rem 1rem; border:none; border-radius:8px; cursor:pointer; font-weight:600; font-size:0.9rem;">
                    + Tambah Challenge
                </button>
            </div>
            
            <?php if (count($challenges) === 0): ?>
                <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 3rem; text-align: center; border-radius: 16px;">
                    <h3 style="color: var(--dash-text-muted);">Belum ada Challenge di turnamen ini.</h3>
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:1rem;">
                    <?php foreach ($challenges as $index => $c): ?>
                    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 12px; padding: 1.5rem;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <div>
                                <h3 style="margin:0 0 0.5rem 0; color:var(--dash-text); font-size:1.2rem;">Challenge #<?php echo $index + 1; ?>: <?php echo htmlspecialchars($c['title']); ?></h3>
                                <div style="color: #10b981; font-weight: 600; font-size: 0.9rem; margin-bottom: 1rem;">+<?php echo $c['xp_reward']; ?> XP Reward</div>
                            </div>
                            <div style="display:flex; gap: 8px;">
                                <button onclick='editChallenge(<?php echo json_encode($c, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)' style="background:transparent; border:none; color:#f59e0b; cursor:pointer;" title="Edit">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <form method="POST" action="" style="margin:0;" onsubmit="return confirm('Hapus challenge ini?');">
                                    <input type="hidden" name="action" value="delete_challenge">
                                    <input type="hidden" name="challenge_id" value="<?php echo $c['id']; ?>">
                                    <button type="submit" style="background:transparent; border:none; color:#ef4444; cursor:pointer;" title="Hapus">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div style="background: rgba(0,0,0,0.02); padding: 1rem; border-radius: 8px; border: 1px solid var(--dash-border); font-size: 0.9rem; color: var(--dash-text-muted);">
                            <strong>Kunci Jawaban Singkat:</strong> <span style="font-family: monospace; color: var(--dash-text); background: var(--dash-bg); padding: 2px 6px; border-radius: 4px;"><?php echo htmlspecialchars($c['correct_answer']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Kolom Kanan: Leaderboard -->
        <div>
            <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 12px; padding: 1.5rem; position: sticky; top: 2rem;">
                <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--dash-text);">Peringkat Turnamen</h3>
                
                <?php if (count($leaderboard) === 0): ?>
                    <p style="color: var(--dash-text-muted); font-size: 0.9rem; text-align: center;">Belum ada peserta yang mengumpulkan XP.</p>
                <?php else: ?>
                    <div style="display:flex; flex-direction:column; gap:0.75rem;">
                        <?php foreach ($leaderboard as $idx => $lb): ?>
                            <div style="display:flex; align-items:center; gap: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--dash-border);">
                                <div style="font-weight:bold; color: <?php echo $idx === 0 ? '#f59e0b' : ($idx === 1 ? '#94a3b8' : ($idx === 2 ? '#b45309' : 'var(--dash-text-muted)')); ?>; font-size:1.1rem; width:20px; text-align:center;">
                                    <?php echo $idx + 1; ?>
                                </div>
                                <div style="flex:1;">
                                    <div style="color: var(--dash-text); font-weight: 600; font-size:0.9rem;"><?php echo htmlspecialchars($lb['name']); ?></div>
                                </div>
                                <div style="color: var(--dash-primary); font-weight: bold; font-size: 0.95rem;">
                                    <?php echo $lb['xp_earned']; ?> XP
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</div>

<!-- Modal Add Challenge -->
<div id="modal-add" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; overflow-y:auto; padding:2rem 0;">
    <div style="background:var(--dash-sidebar); width:90%; max-width:800px; border-radius:16px; padding:2rem; position:relative; margin:auto;">
        <h2 style="margin-top:0; color:var(--dash-text);">Buat Challenge Spesifik</h2>
        <button onclick="document.getElementById('modal-add').style.display='none';" style="position:absolute; top:1rem; right:1rem; background:transparent; border:none; font-size:1.5rem; color:var(--dash-text-muted); cursor:pointer;">&times;</button>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_challenge">
            
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Judul Challenge</label>
                <input type="text" name="title" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
            </div>
            
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Deskripsi & Soal (Mendukung Markdown)</label>
                <textarea name="description" id="desc_add" rows="6" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);"></textarea>
            </div>
            
            <div class="dash-grid-sidebar" style="display:grid;  gap:1.5rem; margin-bottom:1.5rem;">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Kunci Jawaban Singkat (Auto-Correct)</label>
                    <p style="font-size:0.75rem; color:var(--dash-text-muted); margin-bottom:0.5rem;">Sistem akan memvalidasi jawaban siswa secara persis (case-insensitive).</p>
                    <input type="text" name="correct_answer" required placeholder="Contoh: console.log('Hello World');" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text); font-family:monospace;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">XP Reward</label>
                    <p style="font-size:0.75rem; color:var(--dash-text-muted); margin-bottom:0.5rem;">Bonus XP jika benar.</p>
                    <input type="number" name="xp_reward" required value="200" min="1" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                </div>
            </div>
            
            <button type="submit" style="width:100%; padding:1rem; background:#10b981; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer;">Simpan Challenge</button>
        </form>
    </div>
</div>

<!-- Modal Edit Challenge -->
<div id="modal-edit" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; overflow-y:auto; padding:2rem 0;">
    <div style="background:var(--dash-sidebar); width:90%; max-width:800px; border-radius:16px; padding:2rem; position:relative; margin:auto;">
        <h2 style="margin-top:0; color:var(--dash-text);">Edit Challenge</h2>
        <button onclick="document.getElementById('modal-edit').style.display='none';" style="position:absolute; top:1rem; right:1rem; background:transparent; border:none; font-size:1.5rem; color:var(--dash-text-muted); cursor:pointer;">&times;</button>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="edit_challenge">
            <input type="hidden" name="challenge_id" id="edit_id">
            
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Judul Challenge</label>
                <input type="text" name="title" id="edit_title" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
            </div>
            
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Deskripsi & Soal (Mendukung Markdown)</label>
                <!-- EasyMDE will replace this -->
                <textarea name="description" id="desc_edit" rows="6" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);"></textarea>
            </div>
            
            <div class="dash-grid-sidebar" style="display:grid;  gap:1.5rem; margin-bottom:1.5rem;">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">Kunci Jawaban Singkat (Auto-Correct)</label>
                    <input type="text" name="correct_answer" id="edit_answer" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text); font-family:monospace;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--dash-text); font-size:0.9rem;">XP Reward</label>
                    <input type="number" name="xp_reward" id="edit_xp" required min="1" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--dash-border); background:var(--dash-bg); color:var(--dash-text);">
                </div>
            </div>
            
            <button type="submit" style="width:100%; padding:1rem; background:#f59e0b; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer;">Update Challenge</button>
        </form>
    </div>
</div>

<!-- EasyMDE -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>

<style>
    .editor-toolbar { border-color: var(--dash-border) !important; opacity: 0.9; }
    .editor-toolbar button { color: var(--dash-text) !important; }
    .editor-toolbar button.active, .editor-toolbar button:hover { background: var(--dash-sidebar) !important; }
    .CodeMirror { border-color: var(--dash-border) !important; background: var(--dash-bg) !important; color: var(--dash-text) !important; font-size: 1.05rem; }
</style>

<script>
    const mdeAdd = new EasyMDE({
        element: document.getElementById('desc_add'),
        spellChecker: false,
        toolbar: ["bold", "italic", "heading", "|", "code", "quote", "unordered-list", "ordered-list", "|", "link", "image", "|", "preview"]
    });

    const mdeEdit = new EasyMDE({
        element: document.getElementById('desc_edit'),
        spellChecker: false,
        toolbar: ["bold", "italic", "heading", "|", "code", "quote", "unordered-list", "ordered-list", "|", "link", "image", "|", "preview"]
    });

    function openAddModal() {
        document.getElementById('modal-add').style.display = 'flex';
        setTimeout(() => mdeAdd.codemirror.refresh(), 50);
    }

    function editChallenge(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_title').value = data.title;
        mdeEdit.value(data.description);
        document.getElementById('edit_answer').value = data.correct_answer;
        document.getElementById('edit_xp').value = data.xp_reward;
        
        document.getElementById('modal-edit').style.display = 'flex';
        // Refresh EasyMDE so it renders correctly inside the newly visible modal
        setTimeout(() => mdeEdit.codemirror.refresh(), 50);
    }
</script>
