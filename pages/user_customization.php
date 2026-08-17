<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Ambil data perks
$stmt_perks = $pdo->query("SELECT * FROM gamification_perks ORDER BY required_badges ASC");
$all_perks = $stmt_perks->fetchAll(PDO::FETCH_ASSOC);
$grouped_perks = [
    'avatar_frame' => [], 
    'name_effect' => [], 
    'profile_effect' => [], 
    'banner_gif' => [], 
    'card_border' => [],
    'card_background' => [],
    'cursor_effect' => [],
    'badge_effect' => [],
    'entrance_anim' => []
];
foreach ($all_perks as $p) {
    if(isset($grouped_perks[$p['type']])) {
        $grouped_perks[$p['type']][] = $p;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total = $user['total_badges'];
    $is_admin = ($_SESSION['user_role'] === 'admin');

    if (isset($_POST['profile_color']) && ($total >= 3 || $is_admin)) {
        $pdo->prepare("UPDATE users SET profile_color=? WHERE id=?")->execute([$_POST['profile_color'], $user_id]);
    }
    if (isset($_POST['profile_title']) && ($total >= 5 || $is_admin)) {
        $pdo->prepare("UPDATE users SET profile_title=? WHERE id=?")->execute([trim($_POST['profile_title']), $user_id]);
    }
    if (isset($_POST['custom_status']) && ($total >= 25 || $is_admin)) {
        $pdo->prepare("UPDATE users SET custom_status=?, status_emoji=? WHERE id=?")->execute([
            trim($_POST['custom_status']),
            trim($_POST['status_emoji'] ?? ''),
            $user_id
        ]);
    }

    // Verify and update Perks (Frames, Effects)
    $perk_columns = [
        'avatar_frame_id', 'name_effect_id', 'profile_effect_id', 
        'card_border_id', 'card_background_id', 'cursor_effect_id', 
        'badge_effect_id', 'entrance_anim_id'
    ];
    foreach ($perk_columns as $perk_col) {
        if (isset($_POST[$perk_col])) {
            $p_id = $_POST[$perk_col] === '' ? null : (int)$_POST[$perk_col];
            if ($p_id !== null) {
                // Check if user is eligible
                $stmt_p = $pdo->prepare("SELECT required_badges FROM gamification_perks WHERE id = ?");
                $stmt_p->execute([$p_id]);
                $req = $stmt_p->fetchColumn();
                if ($req !== false && ($total >= $req || $is_admin)) {
                    $pdo->prepare("UPDATE users SET $perk_col=? WHERE id=?")->execute([$p_id, $user_id]);
                }
            } else {
                $pdo->prepare("UPDATE users SET $perk_col=NULL WHERE id=?")->execute([$user_id]);
            }
        }
    }

    // Banner
    if (isset($_POST['banner_gif'])) {
        $b_gif = $_POST['banner_gif'];
        if ($b_gif === '') {
            $pdo->prepare("UPDATE users SET banner_gif=NULL WHERE id=?")->execute([$user_id]);
        } else {
            // Check if banner exists and user has enough badges
            $stmt_b = $pdo->prepare("SELECT required_badges FROM gamification_perks WHERE type='banner_gif' AND value=?");
            $stmt_b->execute([$b_gif]);
            $req = $stmt_b->fetchColumn();
            if ($req !== false && ($total >= $req || $is_admin)) {
                $pdo->prepare("UPDATE users SET banner_gif=? WHERE id=?")->execute([$b_gif, $user_id]);
            }
        }
    }
    
    $success_msg = "Pengaturan Kustomisasi berhasil disimpan!";
    
    // Reload user data
    $stmt->execute([$user_id]);

    $success_msg = "Pengaturan Kustomisasi berhasil disimpan!";
    
    // Reload user data
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}

$perks_json = json_encode($all_perks);
$user_json = json_encode([
    'name' => $_SESSION['user_name'],
    'picture' => $user['picture'],
    'xp_points' => $user['xp_points'],
    'streak_days' => $user['streak_days'],
    'total_badges' => $user['total_badges'],
    'joined_at' => $user['created_at']
]);
?>


<style>
    .main-content {
        display: block !important;
        background: var(--dash-bg) !important;
        min-height: auto !important;
        height: max-content !important;
        flex: 0 0 auto !important;
    }
    .dash-left {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 2rem 4rem 2rem;
    }
</style>

<div class="dash-left">
    <div class="section-header" style="margin-bottom: 2rem; flex-shrink: 0;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Kustomisasi Profil</h1>
            <p style="color: var(--dash-text-muted);">Sesuaikan tampilan kartu profil Gamifikasi Anda.</p>
        </div>
    </div>

    <?php if ($success_msg): ?>
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600; flex-shrink: 0;">
            <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>
    

    <?php if ($error_msg): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600; flex-shrink: 0;">
            <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap; padding-bottom: 3rem;">
        <!-- Form Kiri -->
        <div style="flex: 1; min-width: 300px;">
            <form method="POST" action="">

        <div style="margin-bottom: 2.5rem; padding: 2rem; background:linear-gradient(135deg, rgba(67, 97, 238, 0.05) 0%, rgba(67, 97, 238, 0.15) 100%); border-radius: 16px; border: 1px solid rgba(67, 97, 238, 0.2);">
            <h3 style="margin-top:0; color:var(--dash-primary); display:flex; align-items:center; gap:8px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                Perks & Gamifikasi
            </h3>
            <p style="font-size:0.85rem; color:var(--dash-text-muted); margin-bottom:1.5rem;">Selesaikan course dan dapatkan lebih banyak Badge untuk membuka fitur eksklusif ini! (Badge Anda saat ini: <strong><?php echo $user['total_badges']; ?></strong>)</p>
            
            <div class="tabs" style="display:flex; gap:10px; margin-bottom:1.5rem; border-bottom:1px solid rgba(67, 97, 238, 0.2); padding-bottom:10px; flex-wrap:wrap;">
                <button type="button" class="tab-btn active" onclick="switchTab('tab1', this)" style="padding:8px 16px; border:none; background:var(--dash-primary); color:white; border-radius:8px; cursor:pointer;">Dasar & Banner</button>
                <button type="button" class="tab-btn" onclick="switchTab('tab2', this)" style="padding:8px 16px; border:none; background:transparent; color:var(--dash-text); border-radius:8px; cursor:pointer; transition: 0.3s;">Profil & Bingkai</button>
                <button type="button" class="tab-btn" onclick="switchTab('tab3', this)" style="padding:8px 16px; border:none; background:transparent; color:var(--dash-text); border-radius:8px; cursor:pointer; transition: 0.3s;">Kartu & Animasi</button>
            </div>
            
            <div id="tab1" class="tab-content" style="display:block;">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="<?php echo ($user['total_badges'] < 3 && $_SESSION['user_role'] !== 'admin') ? 'opacity:0.5; pointer-events:none;' : ''; ?>">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                        Warna Border Profil
                        <?php if($user['total_badges'] < 3 && $_SESSION['user_role'] !== 'admin'): ?> <span style="color:#ef4444; font-size:0.7rem;">(Butuh 3 Badge)</span> <?php else: ?> <span style="color:#10b981; font-size:0.7rem;">(Unlocked!)</span> <?php endif; ?>
                    </label>
                    <input type="color" name="profile_color" value="<?php echo htmlspecialchars($user['profile_color'] ?? '#4361ee'); ?>" style="width: 100%; height:45px; padding: 0.25rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); cursor:pointer;">
                </div>
                
                <div style="<?php echo ($user['total_badges'] < 5 && $_SESSION['user_role'] !== 'admin') ? 'opacity:0.5; pointer-events:none;' : ''; ?>">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                        Gelar Profil Khusus
                        <?php if($user['total_badges'] < 5 && $_SESSION['user_role'] !== 'admin'): ?> <span style="color:#ef4444; font-size:0.7rem;">(Butuh 5 Badge)</span> <?php else: ?> <span style="color:#10b981; font-size:0.7rem;">(Unlocked!)</span> <?php endif; ?>
                    </label>
                    <input type="text" name="profile_title" value="<?php echo htmlspecialchars($user['profile_title'] ?? 'Novice Coder'); ?>" placeholder="Contoh: Algo Master" style="width: 100%; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                </div>
            </div>

            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px dashed rgba(67, 97, 238, 0.3);">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                    Banner Animasi Profil
                </label>
                <p style="font-size:0.8rem; color:var(--dash-text-muted); margin-bottom:1rem;">Pilih banner animasi yang akan tampil di kartu profilmu saat dilihat orang lain.</p>

                <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1rem;">
                    <!-- Tanpa Banner -->
                    <label style="cursor:pointer; display:block;">
                        <input type="radio" name="banner_gif" value="" <?php echo empty($user['banner_gif']) ? 'checked' : ''; ?> style="position:absolute; opacity:0;" onchange="document.querySelectorAll('.banner-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                        <div class="banner-option" style="border: 2px solid <?php echo empty($user['banner_gif']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius: 10px; overflow:hidden; background:var(--dash-bg); height:85px; display:flex; align-items:center; justify-content:center; color:var(--dash-text-muted); font-size:0.7rem;">
                            Tanpa Banner
                        </div>
                    </label>

                    <?php foreach ($grouped_perks['banner_gif'] as $p): 
                        $val = $p['value'];
                        $unlocked = ($user['total_badges'] >= $p['required_badges'] || $_SESSION['user_role'] === 'admin');
                    ?>
                        <label style="cursor:<?php echo $unlocked ? 'pointer' : 'not-allowed'; ?>; <?php echo $unlocked ? '' : 'opacity:0.5;'; ?> display:block;">
                            <input type="radio" name="banner_gif" value="<?php echo htmlspecialchars($val); ?>" <?php echo ($user['banner_gif'] === $val) ? 'checked' : ''; ?> <?php echo $unlocked ? '' : 'disabled'; ?> style="position:absolute; opacity:0;" onchange="document.querySelectorAll('.banner-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                            <div class="banner-option" style="border: 2px solid <?php echo ($user['banner_gif'] === $val) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius: 10px; overflow:hidden; background:var(--dash-sidebar); position:relative;">
                                <?php if(!$unlocked): ?>
                                    <div style="position:absolute; inset:0; background:rgba(0,0,0,0.6); display:flex; align-items:center; justify-content:center; color:#fff; font-size:0.7rem; font-weight:bold; z-index:10; text-align:center;">Bth <?php echo $p['required_badges']; ?> Badge</div>
                                <?php endif; ?>
                                
                                <?php if (str_ends_with($val, '.mp4')): ?>
                                    <video src="<?php echo htmlspecialchars($val); ?>" autoplay loop muted playsinline style="width:100%; height:60px; object-fit:cover; display:block; filter:contrast(1.05) saturate(1.15); pointer-events:none;"></video>
                                <?php elseif (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')): ?>
                                    <img src="<?php echo htmlspecialchars($val); ?>" alt="Banner" style="width:100%; height:60px; object-fit:cover; display:block; pointer-events:none;">
                                <?php else: ?>
                                    <img src="src/img/<?php echo htmlspecialchars($val); ?>" alt="Banner" style="width:100%; height:60px; object-fit:cover; display:block; pointer-events:none;">
                                <?php endif; ?>
                                <div style="text-align:center; font-size:0.7rem; padding:4px; color:var(--dash-text); background:rgba(0,0,0,0.2);"><?php echo htmlspecialchars($p['name']); ?></div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            </div> <!-- End Tab 1 -->
            
            <div id="tab2" class="tab-content" style="display:none;">
            <!-- Bingkai Avatar -->
            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px dashed rgba(67, 97, 238, 0.3);">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                    Bingkai Avatar (Avatar Frame)
                </label>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <label style="cursor:pointer;">
                        <input type="radio" name="avatar_frame_id" value="" <?php echo empty($user['avatar_frame_id']) ? 'checked' : ''; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                        <div class="perk-option" style="padding:10px; border:2px solid <?php echo empty($user['avatar_frame_id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">Tanpa Bingkai</div>
                    </label>
                    <?php foreach($grouped_perks['avatar_frame'] as $p): 
                        $unlocked = ($user['total_badges'] >= $p['required_badges'] || $_SESSION['user_role'] === 'admin');
                    ?>
                        <label style="cursor:<?php echo $unlocked ? 'pointer' : 'not-allowed'; ?>; <?php echo $unlocked ? '' : 'opacity:0.5;'; ?>">
                            <input type="radio" name="avatar_frame_id" value="<?php echo $p['id']; ?>" <?php echo ($user['avatar_frame_id'] == $p['id']) ? 'checked' : ''; ?> <?php echo $unlocked ? '' : 'disabled'; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                            <div class="perk-option" style="padding:10px; border:2px solid <?php echo ($user['avatar_frame_id'] == $p['id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">
                                <?php echo htmlspecialchars($p['name']); ?> 
                                <?php if(!$unlocked): ?><span style="color:#ef4444; font-size:0.7rem;">(Bth <?php echo $p['required_badges']; ?> Badge)</span><?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Efek Nama -->
            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px dashed rgba(67, 97, 238, 0.3);">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                    Efek Nama (Name Effect)
                </label>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <label style="cursor:pointer;">
                        <input type="radio" name="name_effect_id" value="" <?php echo empty($user['name_effect_id']) ? 'checked' : ''; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                        <div class="perk-option" style="padding:10px; border:2px solid <?php echo empty($user['name_effect_id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">Polos</div>
                    </label>
                    <?php foreach($grouped_perks['name_effect'] as $p): 
                        $unlocked = ($user['total_badges'] >= $p['required_badges'] || $_SESSION['user_role'] === 'admin');
                    ?>
                        <label style="cursor:<?php echo $unlocked ? 'pointer' : 'not-allowed'; ?>; <?php echo $unlocked ? '' : 'opacity:0.5;'; ?>">
                            <input type="radio" name="name_effect_id" value="<?php echo $p['id']; ?>" <?php echo ($user['name_effect_id'] == $p['id']) ? 'checked' : ''; ?> <?php echo $unlocked ? '' : 'disabled'; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                            <div class="perk-option" style="padding:10px; border:2px solid <?php echo ($user['name_effect_id'] == $p['id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">
                                <?php echo htmlspecialchars($p['name']); ?> 
                                <?php if(!$unlocked): ?><span style="color:#ef4444; font-size:0.7rem;">(Bth <?php echo $p['required_badges']; ?> Badge)</span><?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Custom Status -->
            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px dashed rgba(67, 97, 238, 0.3); <?php echo ($user['total_badges'] < 25 && $_SESSION['user_role'] !== 'admin') ? 'opacity:0.5; pointer-events:none;' : ''; ?>">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                    Custom Status & Emoji
                    <?php if($user['total_badges'] < 25 && $_SESSION['user_role'] !== 'admin'): ?> <span style="color:#ef4444; font-size:0.7rem;">(Butuh 25 Badge)</span> <?php else: ?> <span style="color:#10b981; font-size:0.7rem;">(Unlocked!)</span> <?php endif; ?>
                </label>
                <div style="display:flex; gap:10px;">
                    <input type="text" name="status_emoji" value="<?php echo htmlspecialchars($user['status_emoji'] ?? ''); ?>" placeholder="🚀" maxlength="2" style="width: 60px; text-align:center; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                    <input type="text" name="custom_status" value="<?php echo htmlspecialchars($user['custom_status'] ?? ''); ?>" placeholder="Sedang ngoding santai..." maxlength="80" style="flex:1; padding: 0.75rem; border: 1px solid var(--dash-border); border-radius: 8px; background: var(--dash-bg); color: var(--dash-text); font-family: inherit;">
                </div>
            </div>

            <!-- Profile Effect -->
            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px dashed rgba(67, 97, 238, 0.3);">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                    Efek Layar Profil (Profile Effect)
                </label>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <label style="cursor:pointer;">
                        <input type="radio" name="profile_effect_id" value="" <?php echo empty($user['profile_effect_id']) ? 'checked' : ''; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                        <div class="perk-option" style="padding:10px; border:2px solid <?php echo empty($user['profile_effect_id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">Tanpa Efek</div>
                    </label>
                    <?php foreach($grouped_perks['profile_effect'] as $p): 
                        $unlocked = ($user['total_badges'] >= $p['required_badges'] || $_SESSION['user_role'] === 'admin');
                    ?>
                        <label style="cursor:<?php echo $unlocked ? 'pointer' : 'not-allowed'; ?>; <?php echo $unlocked ? '' : 'opacity:0.5;'; ?>">
                            <input type="radio" name="profile_effect_id" value="<?php echo $p['id']; ?>" <?php echo ($user['profile_effect_id'] == $p['id']) ? 'checked' : ''; ?> <?php echo $unlocked ? '' : 'disabled'; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                            <div class="perk-option" style="padding:10px; border:2px solid <?php echo ($user['profile_effect_id'] == $p['id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">
                                <?php echo htmlspecialchars($p['name']); ?> 
                                <?php if(!$unlocked): ?><span style="color:#ef4444; font-size:0.7rem;">(Bth <?php echo $p['required_badges']; ?> Badge)</span><?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            </div> <!-- End Tab 2 -->
            
            <div id="tab3" class="tab-content" style="display:none;">
            <!-- Card Border -->
            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px dashed rgba(67, 97, 238, 0.3);">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                    Border Kartu Profil (Card Border)
                </label>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <label style="cursor:pointer;">
                        <input type="radio" name="card_border_id" value="" <?php echo empty($user['card_border_id']) ? 'checked' : ''; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                        <div class="perk-option" style="padding:10px; border:2px solid <?php echo empty($user['card_border_id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">Tanpa Border Khusus</div>
                    </label>
                    <?php foreach($grouped_perks['card_border'] as $p): 
                        $unlocked = ($user['total_badges'] >= $p['required_badges'] || $_SESSION['user_role'] === 'admin');
                    ?>
                        <label style="cursor:<?php echo $unlocked ? 'pointer' : 'not-allowed'; ?>; <?php echo $unlocked ? '' : 'opacity:0.5;'; ?>">
                            <input type="radio" name="card_border_id" value="<?php echo $p['id']; ?>" <?php echo ($user['card_border_id'] == $p['id']) ? 'checked' : ''; ?> <?php echo $unlocked ? '' : 'disabled'; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                            <div class="perk-option" style="padding:10px; border:2px solid <?php echo ($user['card_border_id'] == $p['id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">
                                <?php echo htmlspecialchars($p['name']); ?> 
                                <?php if(!$unlocked): ?><span style="color:#ef4444; font-size:0.7rem;">(Bth <?php echo $p['required_badges']; ?> Badge)</span><?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 1. Tema Latar Kartu -->
            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px dashed rgba(67, 97, 238, 0.3);">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                    Tema Latar Kartu (Card Background)
                </label>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <label style="cursor:pointer;">
                        <input type="radio" name="card_background_id" value="" <?php echo empty($user['card_background_id']) ? 'checked' : ''; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                        <div class="perk-option" style="padding:10px; border:2px solid <?php echo empty($user['card_background_id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">Polos (Tema Dasar)</div>
                    </label>
                    <?php foreach($grouped_perks['card_background'] as $p): 
                        $unlocked = ($user['total_badges'] >= $p['required_badges'] || $_SESSION['user_role'] === 'admin');
                    ?>
                        <label style="cursor:<?php echo $unlocked ? 'pointer' : 'not-allowed'; ?>; <?php echo $unlocked ? '' : 'opacity:0.5;'; ?>">
                            <input type="radio" name="card_background_id" value="<?php echo $p['id']; ?>" <?php echo ($user['card_background_id'] == $p['id']) ? 'checked' : ''; ?> <?php echo $unlocked ? '' : 'disabled'; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                            <div class="perk-option" style="padding:10px; border:2px solid <?php echo ($user['card_background_id'] == $p['id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">
                                <?php echo htmlspecialchars($p['name']); ?> 
                                <?php if(!$unlocked): ?><span style="color:#ef4444; font-size:0.7rem;">(Bth <?php echo $p['required_badges']; ?> Badge)</span><?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 2. Efek Partikel Kursor -->
            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px dashed rgba(67, 97, 238, 0.3);">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                    Partikel Kursor (Cursor Trails)
                </label>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <label style="cursor:pointer;">
                        <input type="radio" name="cursor_effect_id" value="" <?php echo empty($user['cursor_effect_id']) ? 'checked' : ''; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                        <div class="perk-option" style="padding:10px; border:2px solid <?php echo empty($user['cursor_effect_id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">Tanpa Efek</div>
                    </label>
                    <?php foreach($grouped_perks['cursor_effect'] as $p): 
                        $unlocked = ($user['total_badges'] >= $p['required_badges'] || $_SESSION['user_role'] === 'admin');
                    ?>
                        <label style="cursor:<?php echo $unlocked ? 'pointer' : 'not-allowed'; ?>; <?php echo $unlocked ? '' : 'opacity:0.5;'; ?>">
                            <input type="radio" name="cursor_effect_id" value="<?php echo $p['id']; ?>" <?php echo ($user['cursor_effect_id'] == $p['id']) ? 'checked' : ''; ?> <?php echo $unlocked ? '' : 'disabled'; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                            <div class="perk-option" style="padding:10px; border:2px solid <?php echo ($user['cursor_effect_id'] == $p['id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">
                                <?php echo htmlspecialchars($p['name']); ?> 
                                <?php if(!$unlocked): ?><span style="color:#ef4444; font-size:0.7rem;">(Bth <?php echo $p['required_badges']; ?> Badge)</span><?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 3. Glow & Animasi Badge -->
            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px dashed rgba(67, 97, 238, 0.3);">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                    Animasi & Gaya Badge (Badge Effect)
                </label>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <label style="cursor:pointer;">
                        <input type="radio" name="badge_effect_id" value="" <?php echo empty($user['badge_effect_id']) ? 'checked' : ''; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                        <div class="perk-option" style="padding:10px; border:2px solid <?php echo empty($user['badge_effect_id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">Standar</div>
                    </label>
                    <?php foreach($grouped_perks['badge_effect'] as $p): 
                        $unlocked = ($user['total_badges'] >= $p['required_badges'] || $_SESSION['user_role'] === 'admin');
                    ?>
                        <label style="cursor:<?php echo $unlocked ? 'pointer' : 'not-allowed'; ?>; <?php echo $unlocked ? '' : 'opacity:0.5;'; ?>">
                            <input type="radio" name="badge_effect_id" value="<?php echo $p['id']; ?>" <?php echo ($user['badge_effect_id'] == $p['id']) ? 'checked' : ''; ?> <?php echo $unlocked ? '' : 'disabled'; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                            <div class="perk-option" style="padding:10px; border:2px solid <?php echo ($user['badge_effect_id'] == $p['id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">
                                <?php echo htmlspecialchars($p['name']); ?> 
                                <?php if(!$unlocked): ?><span style="color:#ef4444; font-size:0.7rem;">(Bth <?php echo $p['required_badges']; ?> Badge)</span><?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 4. Animasi Masuk Profil -->
            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px dashed rgba(67, 97, 238, 0.3);">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 600; color: var(--dash-text);">
                    Animasi Masuk Profil (Entrance Animation)
                </label>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <label style="cursor:pointer;">
                        <input type="radio" name="entrance_anim_id" value="" <?php echo empty($user['entrance_anim_id']) ? 'checked' : ''; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                        <div class="perk-option" style="padding:10px; border:2px solid <?php echo empty($user['entrance_anim_id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">Tampil Langsung</div>
                    </label>
                    <?php foreach($grouped_perks['entrance_anim'] as $p): 
                        $unlocked = ($user['total_badges'] >= $p['required_badges'] || $_SESSION['user_role'] === 'admin');
                    ?>
                        <label style="cursor:<?php echo $unlocked ? 'pointer' : 'not-allowed'; ?>; <?php echo $unlocked ? '' : 'opacity:0.5;'; ?>">
                            <input type="radio" name="entrance_anim_id" value="<?php echo $p['id']; ?>" <?php echo ($user['entrance_anim_id'] == $p['id']) ? 'checked' : ''; ?> <?php echo $unlocked ? '' : 'disabled'; ?> style="position:absolute; opacity:0;" onchange="this.parentElement.parentElement.querySelectorAll('.perk-option').forEach(el => el.style.borderColor = 'var(--dash-border)'); this.nextElementSibling.style.borderColor = 'var(--dash-primary)';">
                            <div class="perk-option" style="padding:10px; border:2px solid <?php echo ($user['entrance_anim_id'] == $p['id']) ? 'var(--dash-primary)' : 'var(--dash-border)'; ?>; border-radius:10px; background:var(--dash-bg); font-size:0.8rem;">
                                <?php echo htmlspecialchars($p['name']); ?> 
                                <?php if(!$unlocked): ?><span style="color:#ef4444; font-size:0.7rem;">(Bth <?php echo $p['required_badges']; ?> Badge)</span><?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            </div> <!-- End Tab 3 -->
            
            <script>
                function switchTab(tabId, btn) {
                    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
                    document.querySelectorAll('.tab-btn').forEach(el => {
                        el.style.background = 'transparent';
                        el.style.color = 'var(--dash-text)';
                        el.classList.remove('active');
                    });
                    document.getElementById(tabId).style.display = 'block';
                    btn.style.background = 'var(--dash-primary)';
                    btn.style.color = 'white';
                    btn.classList.add('active');
                }
            </script>

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                <button type="submit" style="padding: 0.75rem 2rem; background: var(--dash-primary); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                    Simpan Gamifikasi
                </button>
            </div>
        </div>
    </form>
        </div> <!-- End Form Kiri -->

        <!-- Live Preview Kanan -->
        <div style="width: 380px; position: sticky; top: 2rem; flex-shrink: 0; max-height: calc(100vh - 4rem); overflow-y: auto; overflow-x: hidden;" class="preview-sticky">
            <h3 style="margin-top:0; color:var(--dash-text); font-size:1.1rem; margin-bottom:1rem; display:flex; align-items:center; gap:8px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Live Preview
            </h3>
            
            <!-- Tempat Preview Profil -->
            <div id="preview-modal-container" style="background:var(--dash-sidebar, #1e293b); border:1px solid var(--dash-border, #334155); border-radius:16px; width:100%; position:relative; overflow:hidden; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
                <div id="preview-modal-body" style="position:relative; min-height:100%;">
                    <div style="color:var(--dash-text-muted, #94a3b8); padding:3rem 0; text-align:center;">Memuat preview...</div>
                </div>
            </div>
            
        </div>
    </div> <!-- End Flex Container -->
</div> <!-- End dash-left -->
    
    <script>
        
        const perksData = <?php echo $perks_json; ?>;
        const userData = <?php echo $user_json; ?>;
        const BADGE_UNLOCK_THRESHOLD = 10;
        
        function getSelectedPerk(name) {
            let el = document.querySelector(`input[name="${name}"]:checked`);
            if(!el && document.querySelector(`input[name="${name}"]`)) el = document.querySelector(`input[name="${name}"]`);
            if(!el || !el.value) return null;
            return perksData.find(p => p.id == el.value || p.value === el.value);
        }
        
        function safeCSS(val) {
            return val ? val.replace(/"/g, "'") : '';
        }
        
        function updateLivePreview() {
            const profileColorInp = document.querySelector('input[name="profile_color"]');
            const profileColor = profileColorInp ? profileColorInp.value : '';
            
            const profileTitleInp = document.querySelector('input[name="profile_title"]');
            const profileTitle = profileTitleInp ? profileTitleInp.value : '';
            
            const customStatusInp = document.querySelector('input[name="custom_status"]');
            const customStatus = customStatusInp ? customStatusInp.value : '';
            const statusEmojiInp = document.querySelector('input[name="status_emoji"]');
            const statusEmoji = statusEmojiInp ? statusEmojiInp.value : '';
            
            const avatarFrame = getSelectedPerk('avatar_frame_id');
            const nameEffect = getSelectedPerk('name_effect_id');
            const profileEffect = getSelectedPerk('profile_effect_id');
            const cardBorder = getSelectedPerk('card_border_id');
            const cardBg = getSelectedPerk('card_background_id');
            const cursorEffect = getSelectedPerk('cursor_effect_id');
            const badgeEffect = getSelectedPerk('badge_effect_id');
            const entranceAnim = getSelectedPerk('entrance_anim_id');
            const bannerGif = getSelectedPerk('banner_gif');
            
            const initial = userData.name ? userData.name.charAt(0).toUpperCase() : '?';
            const borderColor = profileColor || '#3b82f6';
            const hasUnlockedBanner = userData.total_badges >= BADGE_UNLOCK_THRESHOLD;
            
            let bannerGifUrl = null;
            let isRemoteBanner = false;
            let isVideoBanner = false;
            if (hasUnlockedBanner && bannerGif) {
                isRemoteBanner = /^https?:\/\//i.test(bannerGif.value);
                bannerGifUrl = isRemoteBanner ? bannerGif.value : ('src/img/' + bannerGif.value);
                isVideoBanner = isRemoteBanner && /\.mp4($|\?)/i.test(bannerGifUrl);
            }
            
            const avatarStyle = avatarFrame ? safeCSS(avatarFrame.value) : 'border:4px solid var(--dash-sidebar, #1e293b);';
            const avatarHtml = userData.picture
                ? `<div style="position:relative; width:80px; height:80px; flex-shrink:0;"><img src="${userData.picture}" alt="Foto profil" style="width:100%; height:100%; border-radius:50%; object-fit:cover; background:var(--dash-sidebar, #1e293b); ${avatarStyle}"></div>`
                : `<div style="width:80px; height:80px; border-radius:50%; background:${borderColor}; color:white; display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:bold; flex-shrink:0; ${avatarStyle}">${initial}</div>`;
                
            const nameStyle = nameEffect ? safeCSS(nameEffect.value) : 'color:#ffffff; text-shadow:0 1px 4px rgba(0,0,0,0.6);';
            const nameHtml = `<h2 style="margin:0; font-size:1.2rem; line-height:1.25; overflow-wrap:anywhere; ${nameStyle}">${userData.name}</h2>`;
            
            const customStatusHtml = customStatus
                ? `<div style="font-size:0.8rem; color:rgba(255,255,255,0.9); background:rgba(0,0,0,0.4); padding:4px 10px; border-radius:8px; margin-top:6px; display:inline-block; border:1px solid rgba(255,255,255,0.1);">${statusEmoji ? statusEmoji + ' ' : ''}${customStatus}</div>`
                : '';
                
            const titleHtml = profileTitle
                ? `<div style="margin-top:4px;"><span style="display:inline-block; background:rgba(0,0,0,0.35); padding:3px 12px; border-radius:12px; font-size:0.75rem; font-weight:700; color:#ffffff;">${profileTitle}</span></div>`
                : '';
                
            const bannerMediaHtml = isVideoBanner
                ? `<video src="${bannerGifUrl}" autoplay loop muted playsinline style="width:100%; height:190px; object-fit:cover; display:block; pointer-events:none; filter:contrast(1.05) saturate(1.15);"></video>`
                : `<img src="${bannerGifUrl}" style="width:100%; height:190px; object-fit:cover; display:block; pointer-events:none; filter:contrast(1.05) saturate(1.15);">`;
                
            const bannerHtml = isRemoteBanner
                ? `<div style="position:relative; min-height:190px; width:100%; border-radius:16px 16px 0 0; overflow:hidden; background:linear-gradient(135deg, ${borderColor}, #1e293b);">${bannerMediaHtml}<div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.35) 55%, rgba(0,0,0,0.05) 100%); pointer-events:none;"></div><div style="position:absolute; left:20px; right:20px; bottom:16px; display:flex; align-items:center; gap:14px; box-sizing:border-box; pointer-events:none; z-index:2;">${avatarHtml}<div style="min-width:0;">${nameHtml}${titleHtml}${customStatusHtml}</div></div></div>`
                : (() => {
                    const bannerBg = bannerGifUrl ? `url('${bannerGifUrl}') center/cover no-repeat, linear-gradient(135deg, ${borderColor}, #1e293b)` : `linear-gradient(135deg, ${borderColor}33, #1e293b)`;
                    return `<div style="position:relative; min-height:190px; width:100%; background:${bannerBg}; display:flex; align-items:flex-end; border-radius:16px 16px 0 0; overflow:hidden;"><div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.35) 55%, rgba(0,0,0,0.05) 100%);"></div><div style="position:relative; display:flex; align-items:center; gap:14px; padding:16px 20px 18px 20px; width:100%; box-sizing:border-box; z-index:2;">${avatarHtml}<div style="min-width:0;">${nameHtml}${titleHtml}${customStatusHtml}</div></div></div>`;
                })();
                
            const cardBorderStyle = cardBorder ? safeCSS(cardBorder.value) : 'border:1px solid var(--dash-border, #334155); box-shadow:0 10px 25px rgba(0,0,0,0.2);';
            const cursorStyle = cursorEffect ? safeCSS(cursorEffect.value) : '';
            const entranceAnimStyle = entranceAnim ? safeCSS(entranceAnim.value) : '';
            
            const container = document.getElementById('preview-modal-container');
            container.style.cssText = `background:var(--dash-sidebar, #1e293b); border-radius:16px; width:100%; position:relative; overflow:hidden; ${cardBorderStyle} ${cursorStyle}`;
            if(entranceAnimStyle) {
                container.style.animation = 'none';
                container.offsetHeight; // trigger reflow
                container.style.cssText += ` ${entranceAnimStyle}`;
            }

            const badgeEffectStyle = badgeEffect ? safeCSS(badgeEffect.value) : '';
            const badgesHtml = `<div title="Preview Badge" style="display:flex; align-items:center; justify-content:center; background:var(--dash-bg, #0f172a); border:1px solid var(--dash-border, #334155); border-radius:8px; padding:6px; cursor:pointer; transition:transform 0.2s, box-shadow 0.2s; ${badgeEffectStyle}"><div style="width:32px; height:32px; border-radius:6px; background:#f59e0b; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:1rem;">B</div></div>`;
            
            const profileEffectOverlay = (() => {
                if (!profileEffect) return '';
                let eff = profileEffect.value;
                if (/^https?:\/\//i.test(eff)) return `<div style="position:absolute; inset:0; pointer-events:none; z-index:1; border-radius:16px; overflow:hidden;"><img src="${eff}" style="width:100%; height:100%; object-fit:cover; opacity:0.7;"></div>`;
                if (/\.(gif|jpg|jpeg|png|mp4)$/i.test(eff)) {
                    if (/\.mp4$/i.test(eff)) return `<div style="position:absolute; inset:0; pointer-events:none; z-index:1; border-radius:16px; overflow:hidden;"><video src="src/img/${eff}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover; opacity:0.7;"></video></div>`;
                    return `<div style="position:absolute; inset:0; pointer-events:none; z-index:1; border-radius:16px; overflow:hidden;"><img src="src/img/${eff}" style="width:100%; height:100%; object-fit:cover; opacity:0.7;"></div>`;
                }
                return `<div class="profile-effect-${eff}" style="position:absolute; inset:0; pointer-events:none; z-index:1; border-radius:16px; overflow:hidden;"></div>`;
            })();
            
            const cardBgOverlay = cardBg ? `<div style="position:absolute; inset:0; z-index:0; pointer-events:none; border-radius:0 0 16px 16px; ${safeCSS(cardBg.value)}"></div>` : '';
            
            document.getElementById('preview-modal-body').innerHTML = `
                ${profileEffectOverlay}
                ${bannerHtml}
                ${cardBgOverlay}
                <div style="padding:1.5rem 2rem 2rem 2rem; text-align:center; position:relative; z-index:2;">
                    <div style="display:flex; justify-content:center; gap:1.5rem; margin-bottom:1.25rem; padding-bottom:1rem; border-bottom:1px solid var(--dash-border, #334155);">
                        <div><div style="font-weight:700; color:var(--dash-text, #f1f5f9); font-size:1.1rem;">${userData.xp_points}</div><div style="font-size:0.7rem; color:var(--dash-text-muted, #94a3b8);">XP</div></div>
                        <div><div style="font-weight:700; color:var(--dash-text, #f1f5f9); font-size:1.1rem;">${userData.streak_days}</div><div style="font-size:0.7rem; color:var(--dash-text-muted, #94a3b8);">Streak</div></div>
                        <div><div style="font-weight:700; color:var(--dash-text, #f1f5f9); font-size:1.1rem;">${userData.total_badges}</div><div style="font-size:0.7rem; color:var(--dash-text-muted, #94a3b8);">Badge</div></div>
                    </div>
                    <div style="text-align:left;">
                        <h4 style="color:var(--dash-text, #f1f5f9); font-size:0.9rem; margin-bottom:0.75rem;">🏆 Badge yang Diraih</h4>
                        <div style="display:flex; flex-wrap:wrap; gap:8px;">
                            ${badgesHtml}
                        </div>
                    </div>
                    <div style="margin-top:1.25rem; font-size:0.75rem; color:var(--dash-text-muted, #94a3b8);">Ini adalah Live Preview</div>
                </div>
            `;
        }
        
        // Listeners
        document.querySelectorAll('input[type="radio"], input[type="color"], input[type="text"], input[type="number"]').forEach(el => {
            el.addEventListener('input', updateLivePreview);
            el.addEventListener('change', updateLivePreview);
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            updateLivePreview();
        });
    </script>
