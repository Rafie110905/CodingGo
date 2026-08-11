<?php
// Proteksi ketat: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='padding: 3rem; text-align:center;'><h1 style='color:var(--dash-text);'>Akses Ditolak</h1></div>";
    exit();
}

require_once 'config/db.php';

// Handle Add/Delete Badge
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $name = $_POST['name'];
        $desc = $_POST['description'];
        $icon = $_POST['icon_url'];
        $req_type = $_POST['requirement_type'];
        $req_val = $_POST['requirement_value'];
        
        $stmt = $pdo->prepare("INSERT INTO badges (name, description, icon_url, requirement_type, requirement_value) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $icon, $req_type, $req_val]);
        header("Location: index.php?page=admin_badges");
        exit();
    } elseif ($action === 'edit') {
        $id = $_POST['badge_id'];
        $name = $_POST['name'];
        $desc = $_POST['description'];
        $icon = $_POST['icon_url'];
        $req_type = $_POST['requirement_type'];
        $req_val = $_POST['requirement_value'];
        
        $stmt = $pdo->prepare("UPDATE badges SET name=?, description=?, icon_url=?, requirement_type=?, requirement_value=? WHERE id=?");
        $stmt->execute([$name, $desc, $icon, $req_type, $req_val, $id]);
        header("Location: index.php?page=admin_badges");
        exit();
    } elseif ($action === 'delete') {
        $id = $_POST['badge_id'];
        $stmt = $pdo->prepare("DELETE FROM badges WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: index.php?page=admin_badges");
        exit();
    }
}

// Get all badges
$stmt = $pdo->query("SELECT * FROM badges ORDER BY requirement_type, requirement_value");
$badges = $stmt->fetchAll();

// Get courses for dropdown
$stmt_c = $pdo->query("SELECT id, title FROM courses ORDER BY title");
$courses_list = $stmt_c->fetchAll();

// Get exams for dropdown
$stmt_e = $pdo->query("SELECT id, title FROM exams ORDER BY title");
$exams_list = $stmt_e->fetchAll();

// Handle Edit Mode
$edit_badge = null;
if (isset($_GET['edit_id'])) {
    $stmt_edit = $pdo->prepare("SELECT * FROM badges WHERE id = ?");
    $stmt_edit->execute([$_GET['edit_id']]);
    $edit_badge = $stmt_edit->fetch();
}
?>

<div class="dash-left" style="grid-column: 1 / -1; display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
    <!-- Daftar Badges -->
    <div>
        <div class="section-header" style="margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Kelola Badges</h1>
                <p style="color: var(--dash-text-muted);">Atur *reward* sistem gamifikasi Anda. Tambahkan badge baru dengan kriteria khusus.</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">
            <?php if (count($badges) === 0): ?>
                <div style="grid-column: 1/-1; background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 3rem; text-align: center; border-radius: 16px;">
                    <h3 style="color: var(--dash-text-muted);">Belum ada Badge yang dibuat.</h3>
                </div>
            <?php endif; ?>

            <?php foreach ($badges as $b): ?>
            <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; display:flex; flex-direction:column; align-items:center; text-align:center; position:relative;">
                <div style="position:absolute; top:10px; right:10px; display:flex; gap:8px; margin:0;">
                    <a href="index.php?page=admin_badges&edit_id=<?php echo $b['id']; ?>" style="color:#3b82f6; cursor:pointer; text-decoration:none;" title="Edit Badge">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </a>
                    <form method="POST" action="" style="margin:0;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="badge_id" value="<?php echo $b['id']; ?>">
                        <button type="submit" onclick="return confirm('Hapus badge ini?');" style="background:transparent; border:none; color:#ef4444; cursor:pointer;" title="Hapus Badge">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </form>
                </div>

                <?php if(!empty($b['icon_url'])): ?>
                    <img src="<?php echo htmlspecialchars($b['icon_url']); ?>" alt="Badge Icon" style="width:80px; height:80px; object-fit:contain; margin-bottom:1rem;">
                <?php else: ?>
                    <div style="width:80px; height:80px; border-radius:50%; background:#f59e0b; color:white; display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="40"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                    </div>
                <?php endif; ?>
                
                <h3 style="margin: 0 0 0.5rem 0; color: var(--dash-text); font-size:1.1rem;"><?php echo htmlspecialchars($b['name']); ?></h3>
                <p style="color: var(--dash-text-muted); font-size: 0.85rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($b['description']); ?></p>
                
                <div style="background: rgba(99, 102, 241, 0.1); color: #6366f1; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; width:100%;">
                    Syarat: <?php echo strtoupper($b['requirement_type']); ?> = <?php echo $b['requirement_value']; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Form Tambah/Edit Badge -->
    <div>
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; position: sticky; top: 2rem;">
            <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--dash-text);">
                <?php echo $edit_badge ? 'Edit Badge' : 'Buat Badge Baru'; ?>
            </h3>
            
            <?php if($edit_badge): ?>
                <div style="margin-bottom: 1rem; text-align:right;">
                    <a href="index.php?page=admin_badges" style="font-size:0.85rem; color:var(--dash-text-muted); text-decoration:none;">Batal Edit &times;</a>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="action" value="<?php echo $edit_badge ? 'edit' : 'add'; ?>">
                <?php if($edit_badge): ?>
                    <input type="hidden" name="badge_id" value="<?php echo $edit_badge['id']; ?>">
                <?php endif; ?>
                
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Nama Badge</label>
                    <input type="text" name="name" required placeholder="Misal: HTML Master" value="<?php echo htmlspecialchars($edit_badge['name'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                </div>
                
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Kriteria Buka (Tipe)</label>
                    <select name="requirement_type" id="req_type" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;" onchange="updateReqInput()">
                        <option value="xp" <?php echo ($edit_badge['requirement_type'] ?? '') === 'xp' ? 'selected' : ''; ?>>XP Point</option>
                        <option value="course" <?php echo ($edit_badge['requirement_type'] ?? '') === 'course' ? 'selected' : ''; ?>>Selesaikan Course</option>
                        <option value="exam" <?php echo ($edit_badge['requirement_type'] ?? '') === 'exam' ? 'selected' : ''; ?>>Lulus Exam</option>
                        <option value="forum_upvotes" <?php echo ($edit_badge['requirement_type'] ?? '') === 'forum_upvotes' ? 'selected' : ''; ?>>Total Upvote Forum</option>
                    </select>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Nilai Syarat</label>
                    
                    <?php 
                        $req_val = $edit_badge['requirement_value'] ?? ''; 
                        $req_type = $edit_badge['requirement_type'] ?? 'xp';
                    ?>

                    <!-- Input untuk Angka (XP / Upvote) -->
                    <input type="number" name="requirement_value" id="input_number" required placeholder="Misal: 100" value="<?php echo ($req_type === 'xp' || $req_type === 'forum_upvotes') ? $req_val : ''; ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    
                    <!-- Select untuk Course -->
                    <select name="requirement_value_course" id="input_course" style="display:none; width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                        <?php foreach($courses_list as $cl): ?>
                            <option value="<?php echo $cl['id']; ?>" <?php echo ($req_type === 'course' && $req_val == $cl['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cl['title']); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Select untuk Exam -->
                    <select name="requirement_value_exam" id="input_exam" style="display:none; width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                        <?php foreach($exams_list as $el): ?>
                            <option value="<?php echo $el['id']; ?>" <?php echo ($req_type === 'exam' && $req_val == $el['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($el['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <p style="font-size:0.75rem; color:var(--dash-text-muted); margin-top:4px;">Tentukan batas minimum poin atau pilih kelas/ujian spesifik.</p>
                </div>
                
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">Deskripsi</label>
                    <textarea name="description" required rows="3" placeholder="Deskripsi untuk dipamerkan..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($edit_badge['description'] ?? ''); ?></textarea>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">URL Ikon (Opsional)</label>
                    <div style="display: flex; gap: 1rem; align-items: flex-start;">
                        <div id="icon_preview_container" style="flex-shrink: 0; width: 60px; height: 60px; border-radius: 8px; border: 1px solid var(--dash-border); background: var(--dash-bg); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <?php if(!empty($edit_badge['icon_url'])): ?>
                                <img id="icon_preview_img" src="<?php echo htmlspecialchars($edit_badge['icon_url']); ?>" style="width: 100%; height: 100%; object-fit: contain;">
                            <?php else: ?>
                                <img id="icon_preview_img" src="" style="width: 100%; height: 100%; object-fit: contain; display: none;">
                                <svg id="icon_preview_default" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" width="30"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                            <?php endif; ?>
                        </div>
                        <input type="url" name="icon_url" id="icon_url_input" placeholder="https://contoh.com/badge.png" value="<?php echo htmlspecialchars($edit_badge['icon_url'] ?? ''); ?>" oninput="updateIconPreview()" style="flex-grow: 1; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    </div>
                </div>
                
                <button type="submit" style="width: 100%; background: var(--dash-primary); color: white; border: none; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer;">
                    <?php echo $edit_badge ? 'Simpan Perubahan' : 'Buat Badge'; ?>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function updateReqInput() {
    const type = document.getElementById('req_type').value;
    const inputNumber = document.getElementById('input_number');
    const inputCourse = document.getElementById('input_course');
    const inputExam = document.getElementById('input_exam');
    
    // Reset names and disable required temporarily
    inputNumber.name = ''; inputNumber.required = false; inputNumber.style.display = 'none';
    inputCourse.name = ''; inputCourse.required = false; inputCourse.style.display = 'none';
    inputExam.name = ''; inputExam.required = false; inputExam.style.display = 'none';

    if (type === 'xp' || type === 'forum_upvotes') {
        inputNumber.name = 'requirement_value';
        inputNumber.required = true;
        inputNumber.style.display = 'block';
    } else if (type === 'course') {
        inputCourse.name = 'requirement_value';
        inputCourse.required = true;
        inputCourse.style.display = 'block';
    } else if (type === 'exam') {
        inputExam.name = 'requirement_value';
        inputExam.required = true;
        inputExam.style.display = 'block';
    }
}

// Inisialisasi tampilan pertama kali
updateReqInput();

function updateIconPreview() {
    const input = document.getElementById('icon_url_input').value;
    const img = document.getElementById('icon_preview_img');
    const def = document.getElementById('icon_preview_default');
    
    if (input.trim() !== '') {
        img.src = input;
        img.style.display = 'block';
        if(def) def.style.display = 'none';
        
        // Handle error jika gambar gagal dimuat
        img.onerror = function() {
            img.style.display = 'none';
            if(def) def.style.display = 'block';
        };
    } else {
        img.style.display = 'none';
        if(def) def.style.display = 'block';
    }
}
</script>
