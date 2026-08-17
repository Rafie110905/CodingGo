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
    $user = $stmt->fetch();
}
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
        max-width: 800px;
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

    <form method="POST" action="">
        <div style="margin-bottom: 2.5rem; padding: 2rem; background:linear-gradient(135deg, rgba(67, 97, 238, 0.05) 0%, rgba(67, 97, 238, 0.15) 100%); border-radius: 16px; border: 1px solid rgba(67, 97, 238, 0.2);">
            <h3 style="margin-top:0; color:var(--dash-primary); display:flex; align-items:center; gap:8px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                Perks & Gamifikasi
            </h3>
            <p style="font-size:0.85rem; color:var(--dash-text-muted); margin-bottom:1.5rem;">Selesaikan course dan dapatkan lebih banyak Badge untuk membuka fitur eksklusif ini! (Badge Anda saat ini: <strong><?php echo $user['total_badges']; ?></strong>)</p>
            
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

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                <button type="submit" style="padding: 0.75rem 2rem; background: var(--dash-primary); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                    Simpan Gamifikasi
                </button>
            </div>
        </div>
    </form>
</div>
