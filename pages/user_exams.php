<?php
require_once 'config/db.php';

// Ambil semua ujian yang tersedia beserta info kelasnya
$stmt = $pdo->query("SELECT e.*, c.title as course_title, c.category, c.thumbnail 
                     FROM exams e 
                     JOIN courses c ON e.course_id = c.id 
                     ORDER BY e.id DESC");
$exams = $stmt->fetchAll();
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.5rem;">Soal & Ujian</h1>
            <p style="color: var(--dash-text-muted);">Uji kemampuan Anda dan dapatkan skor terbaik!</p>
        </div>
    </div>

    <?php if (count($exams) === 0): ?>
        <div style="background: var(--dash-sidebar); border: 1px dashed var(--dash-border); padding: 4rem; text-align: center; border-radius: 16px;">
            <div style="width: 64px; height: 64px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="32"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
            </div>
            <h3 style="color: var(--dash-text); margin-bottom: 0.5rem;">Belum Ada Ujian</h3>
            <p style="color: var(--dash-text-muted);">Saat ini belum ada soal ujian yang tersedia dari sistem.</p>
        </div>
    <?php else: ?>
        <div class="courses-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php foreach ($exams as $e): ?>
            <a href="index.php?page=course_exam&id=<?php echo $e['id']; ?>" style="text-decoration:none; color:inherit; display:block;">
                <div class="course-card" style="transition: transform 0.2s, box-shadow 0.2s; height: 100%; display: flex; flex-direction: column; border-radius:16px; overflow:hidden; background:var(--dash-sidebar); border:1px solid var(--dash-border);">
                    <?php 
                        $gradients = [
                            'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)',
                            'linear-gradient(135deg, #0f766e 0%, #14b8a6 100%)',
                            'linear-gradient(135deg, #4338ca 0%, #6366f1 100%)',
                            'linear-gradient(135deg, #b45309 0%, #f59e0b 100%)',
                            'linear-gradient(135deg, #be123c 0%, #e11d48 100%)'
                        ];
                        $bg = $gradients[array_rand($gradients)];
                        if (!empty($e['thumbnail'])) {
                            $bg = 'url(' . htmlspecialchars($e['thumbnail']) . ') center/cover';
                        }
                    ?>
                    <div class="course-img" style="background: <?php echo $bg; ?>; height: 120px; display:flex; align-items:center; justify-content:center; color:white; font-size:2rem; font-weight:bold; position:relative;">
                        <?php if(empty($e['thumbnail'])) echo substr(htmlspecialchars($e['course_title']), 0, 1); ?>
                        <div style="position:absolute; bottom:10px; right:10px; background:rgba(0,0,0,0.6); color:white; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:600; backdrop-filter:blur(4px);">
                            <?php echo strtoupper($e['type']); ?>
                        </div>
                    </div>
                    <div class="course-body" style="padding:1.5rem; flex:1; display:flex; flex-direction:column;">
                        <span style="font-size:0.75rem; font-weight:700; color:var(--dash-primary); text-transform:uppercase; letter-spacing:1px; margin-bottom:0.5rem;"><?php echo htmlspecialchars($e['course_title']); ?></span>
                        <div class="course-title" style="margin-bottom:1rem; font-size:1.2rem; line-height:1.4; color:var(--dash-text); font-weight:700;"><?php echo htmlspecialchars($e['title']); ?></div>
                        
                        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1.5rem; color:var(--dash-text-muted); font-size:0.9rem;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            KKM: <?php echo $e['min_score_passing']; ?>
                        </div>
                        
                        <div style="margin-top:auto;">
                            <button style="width:100%; background:rgba(59,130,246,0.1); color:#3b82f6; border:1px solid rgba(59,130,246,0.2); padding:0.75rem; border-radius:8px; font-weight:600; font-size:0.9rem; cursor:pointer; transition:all 0.2s;">
                                Mulai Ujian &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.course-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px -8px rgba(0,0,0,0.15);
}
.course-card:hover button {
    background: #3b82f6 !important;
    color: white !important;
}
</style>
