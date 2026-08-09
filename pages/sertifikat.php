<?php
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];

// Sertifikat yang sudah diperoleh
$stmt = $pdo->prepare("SELECT cert.*, c.title as course_title, c.category, c.theme_color
                       FROM certificates cert
                       JOIN courses c ON cert.course_id = c.id
                       WHERE cert.user_id = ?
                       ORDER BY cert.issued_at DESC");
$stmt->execute([$user_id]);
$certificates = $stmt->fetchAll();

$owned_course_ids = array_column($certificates, 'course_id');

// Ambil kelas yang sedang diikuti (punya progress) tapi belum bersertifikat
$stmt_prog = $pdo->prepare("SELECT c.id, c.title, c.category, c.theme_color,
                            (SELECT COUNT(*) FROM materials WHERE course_id = c.id) as total_materi,
                            (SELECT COUNT(*) FROM user_progress up JOIN materials m ON up.material_id = m.id WHERE up.user_id = ? AND m.course_id = c.id AND up.status = 'completed') as materi_selesai
                            FROM courses c
                            WHERE c.id IN (SELECT DISTINCT m.course_id FROM materials m JOIN user_progress up ON up.material_id = m.id WHERE up.user_id = ?)
                            ORDER BY c.title ASC");
$stmt_prog->execute([$user_id, $user_id]);
$in_progress_all = $stmt_prog->fetchAll();

// Filter yang belum punya sertifikat
$in_progress = array_filter($in_progress_all, function ($c) use ($owned_course_ids) {
    return !in_array($c['id'], $owned_course_ids);
});
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Sertifikat</h1>
            <p style="color: var(--dash-text-muted);">Kumpulan sertifikat kompetensi yang sudah kamu dapatkan dari CodingGo.</p>
        </div>
    </div>

    <!-- Sertifikat Diperoleh -->
    <?php if (count($certificates) === 0): ?>
        <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 4rem; text-align: center; border-radius: 16px; margin-bottom: 2.5rem;">
            <div style="width: 64px; height: 64px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="32"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
            </div>
            <h3 style="color: var(--dash-text); margin-bottom: 0.5rem;">Belum Ada Sertifikat</h3>
            <p style="color: var(--dash-text-muted);">Selesaikan semua materi dan lulus ujian akhir sebuah kelas untuk mendapatkan sertifikat pertamamu.</p>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
            <?php foreach ($certificates as $c): ?>
            <?php $theme = $c['theme_color'] ?? '#4361ee'; ?>
            <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; overflow: hidden;">
                <div style="background: linear-gradient(135deg, <?php echo $theme; ?> 0%, #1e293b 120%); padding: 2rem 1.5rem; color: #fff; position: relative; min-height: 130px; display: flex; flex-direction: column; justify-content: space-between;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="26"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>
                    <div style="font-weight:700; font-size:0.95rem;"><?php echo htmlspecialchars($c['course_title']); ?></div>
                </div>
                <div style="padding: 1.25rem 1.5rem; display: flex; flex-direction: column; gap: 0.85rem;">
                    <span style="display:inline-block; width:fit-content; font-size:0.7rem; font-weight:700; padding:0.28rem 0.65rem; border-radius:999px; background:rgba(67,97,238,0.1); color:var(--dash-primary); text-transform:uppercase;"><?php echo htmlspecialchars($c['category']); ?></span>
                    <div style="font-size:0.8rem; color:var(--dash-text-muted);">Diterbitkan <?php echo date('d M Y', strtotime($c['issued_at'])); ?></div>
                    <div style="font-size:0.72rem; color:var(--dash-text-muted); font-family:monospace; background:var(--dash-bg); padding:0.4rem 0.6rem; border-radius:6px;">Kode: <?php echo htmlspecialchars($c['certificate_code']); ?></div>
                    <a href="index.php?page=certificate_view&code=<?php echo urlencode($c['certificate_code']); ?>" target="_blank" style="display:inline-flex; align-items:center; justify-content:center; gap:0.4rem; padding:0.65rem 1rem; border-radius:999px; font-weight:600; font-size:0.85rem; background: var(--dash-primary); color: #fff; text-decoration:none;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>
                        Lihat &amp; Cetak Sertifikat
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Sertifikat yang Sedang Diusahakan -->
    <?php if (count($in_progress) > 0): ?>
    <div class="section-header" style="margin-bottom: 1.25rem;">
        <h2>Sedang Dalam Progress</h2>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
        <?php foreach ($in_progress as $c): ?>
        <?php
            $pct = $c['total_materi'] > 0 ? round(($c['materi_selesai'] / $c['total_materi']) * 100) : 0;
            $theme = $c['theme_color'] ?? '#4361ee';
        ?>
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; overflow: hidden;">
            <div style="background: var(--dash-bg); padding: 2rem 1.5rem; color: var(--dash-text-muted); position: relative; min-height: 130px; display: flex; flex-direction: column; justify-content: space-between;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                <div style="font-weight:700; font-size:0.95rem; color: var(--dash-text);"><?php echo htmlspecialchars($c['title']); ?></div>
            </div>
            <div style="padding: 1.25rem 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                <span style="display:inline-block; width:fit-content; font-size:0.7rem; font-weight:700; padding:0.28rem 0.65rem; border-radius:999px; background:rgba(67,97,238,0.1); color: var(--dash-primary); text-transform:uppercase;"><?php echo htmlspecialchars($c['category']); ?></span>
                <div style="width:100%; background: var(--dash-border); border-radius: 999px; height: 8px; overflow: hidden;">
                    <div style="height:100%; border-radius:999px; background: <?php echo $theme; ?>; width: <?php echo $pct; ?>%;"></div>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.78rem; color:var(--dash-text-muted);">
                    <span><?php echo $pct; ?>% materi selesai</span>
                    <span><?php echo $c['materi_selesai']; ?>/<?php echo $c['total_materi']; ?> bab</span>
                </div>
                <a href="index.php?page=course_detail&id=<?php echo $c['id']; ?>" style="font-size:0.85rem; font-weight:600; color: var(--dash-primary); text-decoration:none;">Lanjutkan Belajar &rarr;</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div style="margin-top:2.5rem; background:rgba(67,97,238,0.06); border:1px solid rgba(67,97,238,0.15); border-radius:16px; padding:1.5rem;">
        <h4 style="margin:0 0 0.5rem 0; color: var(--dash-primary); font-size:0.95rem;">Cara Mendapatkan Sertifikat</h4>
        <p style="margin:0; font-size:0.85rem; color:var(--dash-text-muted); line-height:1.6;">Selesaikan <b>semua bab materi</b> pada sebuah kelas, lalu <b>lulus ujian akhir</b> kelas tersebut (nilai &ge; KKM). Sertifikat akan diterbitkan otomatis begitu kedua syarat itu terpenuhi.</p>
    </div>
</div>
