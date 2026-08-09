<?php
require_once 'config/db.php';

// Ambil data user terbaru dari DB
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id'] ?? 0]);
$user_db = $stmt->fetch();

if (!$user_db) {
    // Fallback jika tidak ada di DB (harus login lagi)
    header('Location: login.php');
    exit();
}

// Redirect jika belum punya tanggal lahir
if (empty($user_db['birth_date'])) {
    header('Location: index.php?page=setup_profile');
    exit();
}

$short_name = explode(' ', htmlspecialchars($user_db['name']))[0];
$xp = $user_db['xp_points'];
$streak = $user_db['streak_days'];
$badges = $user_db['total_badges'];

// Hitung Kelas Aktif (distinct course_id dari materi yang pernah diakses)
$stmt_ac = $pdo->prepare("SELECT COUNT(DISTINCT m.course_id) as total FROM user_progress up JOIN materials m ON up.material_id = m.id WHERE up.user_id = ?");
$stmt_ac->execute([$_SESSION['user_id']]);
$active_courses = $stmt_ac->fetch()['total'] ?? 0;

// Hitung Materi Selesai
$stmt_mc = $pdo->prepare("SELECT COUNT(*) as total FROM user_progress WHERE user_id = ? AND status = 'completed'");
$stmt_mc->execute([$_SESSION['user_id']]);
$completed_materials = $stmt_mc->fetch()['total'] ?? 0;
?>

<!-- Dashboard Main Grid -->
    <!-- Dashboard Main Grid -->
    <div class="dash-left">
        <div class="dash-welcome">
            <h1>Selamat datang kembali, <?php echo $short_name; ?>! 👋</h1>
            <p>Terus belajar dan tingkatkan skill coding-mu hari ini.</p>
        </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="width:36px; height:36px; background:#eff6ff; color:#3b82f6; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </div>
            </div>
            <div class="stat-val"><?php echo $active_courses; ?></div>
            <div class="stat-label">Kelas Berjalan</div>
        </div>
        <div class="stat-card">
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="width:36px; height:36px; background:#f0fdf4; color:#22c55e; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <div class="stat-val"><?php echo $completed_materials; ?></div>
            <div class="stat-label">Materi Selesai</div>
        </div>
        <!-- XP Points -->
        <div class="stat-card" style="border-top:4px solid var(--dash-warning);">
            <div style="width:40px; height:40px; background:rgba(247, 37, 133, 0.1); color:var(--dash-warning); border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:0.5rem;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
            </div>
            <div class="stat-val"><?php echo number_format($xp); ?></div>
            <div class="stat-label">XP Points <span style="color:var(--dash-text-muted); font-size:0.7rem; margin-left:4px;">Level <?php echo floor($xp / 100) + 1; ?></span></div>
        </div>

        <!-- Badges -->
        <div class="stat-card" style="border-top:4px solid #f59e0b;">
            <div style="width:40px; height:40px; background:rgba(245, 158, 11, 0.1); color:#f59e0b; border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:0.5rem;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
            </div>
            <div class="stat-val"><?php echo number_format($badges); ?></div>
            <div class="stat-label">Badges <span style="color:var(--dash-text-muted); font-size:0.7rem; margin-left:4px;">Koleksi</span></div>
        </div>
    </div> <!-- End stats-grid -->

    <!-- Lanjutkan Belajar -->
    <div class="section-header">
                <h2>Lanjutkan Belajar</h2>
                <a href="#">Lihat Semua</a>
            </div>
            <div class="courses-grid" style="grid-template-columns: repeat(2, 1fr);">
                <div class="course-card">
                    <div class="course-img" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">HTML</div>
                    <div class="course-body">
                        <span class="course-tag">Dasar</span>
                        <div class="course-title">HTML Fundamental</div>
                        <div class="course-desc">Pelajari dasar-dasar HTML untuk membuat struktur web.</div>
                        <div class="progress-wrap"><div class="progress-bar" style="width: 65%;"></div></div>
                        <div class="progress-text">65%</div>
                    </div>
                </div>
                <div class="course-card">
                    <div class="course-img" style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);">CSS</div>
                    <div class="course-body">
                        <span class="course-tag">Dasar</span>
                        <div class="course-title">CSS Styling</div>
                        <div class="course-desc">Membuat tampilan web yang menarik dengan CSS.</div>
                        <div class="progress-wrap"><div class="progress-bar" style="width: 40%;"></div></div>
                        <div class="progress-text">40%</div>
                    </div>
                </div>
            </div>

            <!-- Rekomendasi Untukmu -->
            <div class="section-header">
                <h2>Rekomendasi Untukmu</h2>
                <a href="#">Lihat Semua</a>
            </div>
            <div class="courses-grid" style="grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                <div class="course-card">
                    <div class="course-img" style="height:90px; font-size:1.5rem; background: linear-gradient(135deg, #b45309 0%, #f59e0b 100%);">JS</div>
                    <div class="course-body">
                        <div class="course-title" style="font-size:0.85rem;">JavaScript DOM</div>
                        <div class="course-desc" style="font-size:0.75rem; margin-bottom:0.5rem;">Menengah</div>
                        <div style="font-size:0.75rem; color:#eab308; font-weight:600;">⭐ 4.8 (120)</div>
                    </div>
                </div>
                <div class="course-card">
                    <div class="course-img" style="height:90px; font-size:1.5rem; background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);">React</div>
                    <div class="course-body">
                        <div class="course-title" style="font-size:0.85rem;">React untuk Pemula</div>
                        <div class="course-desc" style="font-size:0.75rem; margin-bottom:0.5rem;">Menengah</div>
                        <div style="font-size:0.75rem; color:#eab308; font-weight:600;">⭐ 4.9 (98)</div>
                    </div>
                </div>
                <div class="course-card">
                    <div class="course-img" style="height:90px; font-size:1.5rem; background: linear-gradient(135deg, #166534 0%, #22c55e 100%);">Node</div>
                    <div class="course-body">
                        <div class="course-title" style="font-size:0.85rem;">Node.js Dasar</div>
                        <div class="course-desc" style="font-size:0.75rem; margin-bottom:0.5rem;">Menengah</div>
                        <div style="font-size:0.75rem; color:#eab308; font-weight:600;">⭐ 4.7 (76)</div>
                    </div>
                </div>
                <div class="course-card">
                    <div class="course-img" style="height:90px; font-size:1.5rem; background: linear-gradient(135deg, #be123c 0%, #e11d48 100%);">Git</div>
                    <div class="course-body">
                        <div class="course-title" style="font-size:0.85rem;">Git Version Control</div>
                        <div class="course-desc" style="font-size:0.75rem; margin-bottom:0.5rem;">Dasar</div>
                        <div style="font-size:0.75rem; color:#eab308; font-weight:600;">⭐ 4.9 (210)</div>
                    </div>
                </div>
            </div>
            
            <div style="background:linear-gradient(90deg, #4361ee 0%, #3a0ca3 100%); border-radius:16px; padding:1.5rem 2rem; display:flex; justify-content:space-between; align-items:center; color:white;">
                <div style="display:flex; align-items:center; gap:1rem;">
                    <div style="width:48px; height:48px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                    </div>
                    <div>
                        <h3 style="margin-bottom:0.25rem;">Ikuti Challenge Mingguan</h3>
                        <p style="font-size:0.85rem; color:rgba(255,255,255,0.8);">Selesaikan challenge dan dapatkan XP ekstra!</p>
                    </div>
                </div>
                <button style="background:white; color:#3a0ca3; padding:0.75rem 1.5rem; border-radius:8px; border:none; font-weight:600; cursor:pointer;">Lihat Challenge &rarr;</button>
            </div>
        </div> <!-- End dash-left -->

        <div class="dash-right">
            <!-- Progress Mingguan -->
            <div class="widget-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <h3 class="widget-title" style="margin:0;">Progress Mingguan</h3>
                    <span style="font-size:0.75rem; color:var(--dash-text-muted); background:var(--dash-bg); padding:4px 8px; border-radius:4px;">Minggu Ini &or;</span>
                </div>
                
                <div style="display:flex; justify-content:center; margin-bottom:1.5rem; position:relative;">
                    <svg viewBox="0 0 100 100" style="width:140px; height:140px;">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="var(--dash-border)" stroke-width="10" />
                        <circle cx="50" cy="50" r="45" fill="none" stroke="var(--dash-primary)" stroke-width="10" stroke-dasharray="283" stroke-dashoffset="73" stroke-linecap="round" transform="rotate(-90 50 50)" />
                    </svg>
                    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); text-align:center;">
                        <div style="font-size:1.75rem; font-weight:800; color:var(--dash-text);">74%</div>
                        <div style="font-size:0.6rem; color:var(--dash-text-muted); text-transform:uppercase;">Target Mingguan</div>
                    </div>
                </div>
                
                <div style="text-align:center; font-size:0.85rem; color:var(--dash-text-muted); margin-bottom:1.5rem;">
                    Target: 10 jam belajar<br>
                    Waktu tersisa: 2 jam 30 menit
                </div>
                <button style="width:100%; background:rgba(67, 97, 238, 0.1); color:var(--dash-primary); border:none; padding:0.75rem; border-radius:8px; font-weight:600; cursor:pointer;">Lihat Progress Detail &rarr;</button>
            </div>

            <!-- Jadwal Mendatang -->
            <div class="widget-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <h3 class="widget-title" style="margin:0;">Jadwal Mendatang</h3>
                    <a href="#" style="font-size:0.75rem; color:var(--dash-primary); text-decoration:none; font-weight:500;">Lihat Semua</a>
                </div>
                
                <div style="display:flex; flex-direction:column; gap:1rem;">
                    <div style="display:flex; gap:12px; align-items:flex-start; padding:12px; border:1px solid var(--dash-border); border-radius:12px;">
                        <div style="background:#eff6ff; color:#3b82f6; padding:8px; border-radius:8px;"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
                        <div>
                            <div style="font-weight:600; font-size:0.85rem; color:var(--dash-text); margin-bottom:4px;">Live Class: CSS Grid Layout</div>
                            <div style="font-size:0.75rem; color:var(--dash-text-muted);">Hari ini, 19:00 WIB</div>
                        </div>
                    </div>
                    
                    <div style="display:flex; gap:12px; align-items:flex-start; padding:12px; border:1px solid var(--dash-border); border-radius:12px;">
                        <div style="background:#f0fdf4; color:#22c55e; padding:8px; border-radius:8px;"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg></div>
                        <div>
                            <div style="font-weight:600; font-size:0.85rem; color:var(--dash-text); margin-bottom:4px;">Challenge: Flexbox Master</div>
                            <div style="font-size:0.75rem; color:var(--dash-text-muted);">Besok, 10:00 WIB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- End dash-right -->
