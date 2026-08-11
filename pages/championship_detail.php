<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';
$user_id = $_SESSION['user_id'];
$championship_id = $_GET['id'] ?? null;

if (!$championship_id) {
    header("Location: index.php?page=championship");
    exit();
}

// Cek apakah user tergabung
$stmt_check = $pdo->prepare("SELECT xp_earned FROM championship_participants WHERE championship_id = ? AND user_id = ?");
$stmt_check->execute([$championship_id, $user_id]);
$participant = $stmt_check->fetch();
if (!$participant) {
    header("Location: index.php?page=championship");
    exit();
}

// Data turnamen
$stmt_champ = $pdo->prepare("SELECT * FROM championships WHERE id = ?");
$stmt_champ->execute([$championship_id]);
$champ = $stmt_champ->fetch();

// Daftar Challenge
$stmt_chal = $pdo->prepare("SELECT * FROM championship_challenges WHERE championship_id = ? ORDER BY created_at ASC");
$stmt_chal->execute([$championship_id]);
$challenges = $stmt_chal->fetchAll();

// Cek challenge yang sudah diselesaikan
$completed_chal = [];
$stmt_comp = $pdo->prepare("SELECT challenge_id FROM championship_completed_challenges WHERE user_id = ?");
$stmt_comp->execute([$user_id]);
while ($r = $stmt_comp->fetch()) {
    $completed_chal[] = $r['challenge_id'];
}

// Leaderboard
$stmt_lb = $pdo->prepare("
    SELECT u.name, cp.xp_earned, cp.user_id 
    FROM championship_participants cp
    JOIN users u ON cp.user_id = u.id
    WHERE cp.championship_id = ?
    ORDER BY cp.xp_earned DESC, cp.joined_at ASC
    LIMIT 20
");
$stmt_lb->execute([$championship_id]);
$leaderboard = $stmt_lb->fetchAll();
?>

<div class="container" style="padding: 2rem 0; min-height: 70vh;">
    <div style="display:flex; align-items:center; gap: 0.5rem; margin-bottom:1.5rem;">
        <a href="index.php?page=championship" style="color:var(--text-muted); font-size:0.9rem; text-decoration:none;">&larr; Kembali ke Daftar Turnamen</a>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2.2rem; color: var(--text); margin: 0 0 0.5rem 0;"><?php echo htmlspecialchars($champ['title']); ?></h1>
            <p style="color: var(--text-muted); margin:0;">Berlangsung hingga: <?php echo date('d M Y', strtotime($champ['end_date'])); ?></p>
        </div>
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; padding: 1rem 1.5rem; border-radius: 12px; text-align: center;">
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 4px;">XP TURNAMEN ANDA</div>
            <div style="font-size: 1.8rem; font-weight: bold; color: #10b981;"><?php echo $participant['xp_earned']; ?></div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
        
        <!-- Kolom Kiri: Challenges -->
        <div>
            <h2 style="font-size: 1.5rem; color: var(--text); margin-top:0; margin-bottom: 1.5rem;">Tantangan Khusus (Challenges)</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Selesaikan soal atau tantangan di bawah ini untuk mendapatkan bonus XP yang besar secara instan!</p>
            
            <?php if (count($challenges) === 0): ?>
                <div style="background: var(--bg); border: 1px dashed var(--border-color); padding: 3rem; text-align: center; border-radius: 16px;">
                    <h3 style="color: var(--text-muted);">Belum ada tantangan untuk turnamen ini.</h3>
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:1.5rem;">
                    <?php foreach ($challenges as $index => $c): ?>
                    <?php $is_completed = in_array($c['id'], $completed_chal); ?>
                    <div style="background: var(--bg); border: 1px solid <?php echo $is_completed ? '#10b981' : 'var(--border-color)'; ?>; border-radius: 16px; padding: 1.5rem; display:flex; justify-content:space-between; align-items:center; transition: transform 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                        <div>
                            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                                <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">+<?php echo $c['xp_reward']; ?> XP</span>
                                <?php if($is_completed): ?>
                                    <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display:flex; align-items:center; gap:4px;">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Selesai
                                    </span>
                                <?php endif; ?>
                            </div>
                            <h3 style="margin:0; color:var(--text); font-size:1.2rem;"><?php echo htmlspecialchars($c['title']); ?></h3>
                        </div>
                        
                        <?php if($is_completed): ?>
                            <a href="index.php?page=championship_challenge&id=<?php echo $c['id']; ?>" class="btn" style="background:var(--bg-hover); color:var(--text); border:1px solid var(--border-color); padding:0.75rem 1.25rem; border-radius:8px; text-decoration:none; font-weight:600;">
                                Lihat Soal
                            </a>
                        <?php else: ?>
                            <a href="index.php?page=championship_challenge&id=<?php echo $c['id']; ?>" class="btn btn-primary" style="background:var(--primary); color:white; border:none; padding:0.75rem 1.25rem; border-radius:8px; text-decoration:none; font-weight:600; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);">
                                Kerjakan &rarr;
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Kolom Kanan: Leaderboard -->
        <div>
            <div style="background: var(--bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; position: sticky; top: 2rem;">
                <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--text); display:flex; align-items:center; gap:8px;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="#f59e0b" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    Leaderboard Turnamen
                </h3>
                
                <?php if (count($leaderboard) === 0): ?>
                    <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center;">Belum ada klasemen.</p>
                <?php else: ?>
                    <div style="display:flex; flex-direction:column; gap:0.75rem;">
                        <?php foreach ($leaderboard as $idx => $lb): ?>
                            <?php $is_me = ($lb['user_id'] == $user_id); ?>
                            <div style="display:flex; align-items:center; gap: 1rem; padding: 0.75rem; border-radius: 8px; background: <?php echo $is_me ? 'rgba(59, 130, 246, 0.05)' : 'transparent'; ?>; border: 1px solid <?php echo $is_me ? 'var(--primary)' : 'transparent'; ?>;">
                                <div style="font-weight:bold; color: <?php echo $idx === 0 ? '#f59e0b' : ($idx === 1 ? '#94a3b8' : ($idx === 2 ? '#b45309' : 'var(--text-muted)')); ?>; font-size:1.2rem; width:24px; text-align:center;">
                                    <?php echo $idx + 1; ?>
                                </div>
                                <div style="flex:1;">
                                    <div style="color: var(--text); font-weight: 600; font-size:0.95rem;">
                                        <?php echo htmlspecialchars($lb['name']); ?>
                                        <?php if($is_me) echo '<span style="color:var(--primary); font-size:0.75rem; margin-left:4px;">(Anda)</span>'; ?>
                                    </div>
                                </div>
                                <div style="color: var(--primary); font-weight: bold; font-size: 1rem;">
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
