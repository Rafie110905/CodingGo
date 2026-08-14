<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Update Target
$target_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_target'])) {
    $new_target_hours = (float)($_POST['weekly_target_hours'] ?? 10);
    $new_target = (int)($new_target_hours * 60);
    if ($new_target > 0) {
        $stmt_upd = $pdo->prepare("UPDATE users SET weekly_target = ? WHERE id = ?");
        $stmt_upd->execute([$new_target, $user_id]);
        $target_msg = "Target belajar berhasil diperbarui!";
    }
}

// Get user's current target
$stmt_target = $pdo->prepare("SELECT weekly_target FROM users WHERE id = ?");
$stmt_target->execute([$user_id]);
$current_target = $stmt_target->fetchColumn() ?: 600;

// Get total learning time
$stmt = $pdo->prepare("SELECT COALESCE(SUM(time_spent), 0) FROM user_learning_time WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_minutes = $stmt->fetchColumn();

// Get weekly learning time (last 7 days)
$stmt_week = $pdo->prepare("SELECT COALESCE(SUM(time_spent), 0) FROM user_learning_time WHERE user_id = ? AND log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$stmt_week->execute([$user_id]);
$weekly_minutes = (int)$stmt_week->fetchColumn();

$weekly_progress_percent = min(100, round(($weekly_minutes / max(1, $current_target)) * 100));
$stroke_offset = 283 - (283 * ($weekly_progress_percent / 100));

// Get XP
$stmt = $pdo->prepare("SELECT xp_points FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_xp = $stmt->fetchColumn();

// Get modules completed
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$modules_completed = $stmt->fetchColumn();

// Get Completed Modules List
$stmt_mod = $pdo->prepare("
    SELECT m.title as material_title, c.title as course_title, up.completed_at
    FROM user_progress up
    JOIN materials m ON up.material_id = m.id
    JOIN courses c ON m.course_id = c.id
    WHERE up.user_id = ? AND up.status = 'completed'
    ORDER BY up.completed_at DESC
    LIMIT 20
");
$stmt_mod->execute([$user_id]);
$completed_modules_list = $stmt_mod->fetchAll();

// Get Exam History
$stmt_exam = $pdo->prepare("
    SELECT er.score, er.passed, er.attempt_date, e.title as exam_title, c.title as course_title
    FROM exam_results er
    JOIN exams e ON er.exam_id = e.id
    JOIN courses c ON e.course_id = c.id
    WHERE er.user_id = ?
    ORDER BY er.attempt_date DESC
    LIMIT 20
");
$stmt_exam->execute([$user_id]);
$exam_history = $stmt_exam->fetchAll();

// Get past 7 days data for chart
$chart_labels = [];
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $display_date = date('d M', strtotime("-$i days"));
    $chart_labels[] = $display_date;
    
    $stmt = $pdo->prepare("SELECT time_spent FROM user_learning_time WHERE user_id = ? AND log_date = ?");
    $stmt->execute([$user_id, $date]);
    $spent = $stmt->fetchColumn();
    $chart_data[] = $spent ? (int)$spent : 0;
}
?>

<div class="dash-left" style="grid-column: 1 / -1;">
    <div class="section-header" style="margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--dash-text); margin-bottom: 0.25rem;">Statistik & Progress</h1>
            <p style="color: var(--dash-text-muted);">Pantau perkembangan dan dedikasi belajarmu di CodingGo.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Card 1: Time Spent -->
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); color: var(--dash-primary); display: flex; align-items: center; justify-content: center;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="28"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--dash-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Total Belajar</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dash-text); margin-top: 0.25rem;">
                    <?php 
                        if ($total_minutes >= 60) {
                            $hours = floor($total_minutes / 60);
                            $mins = $total_minutes % 60;
                            echo $hours . "j " . $mins . "m";
                        } else {
                            echo $total_minutes . " menit";
                        }
                    ?>
                </div>
            </div>
        </div>

        <!-- Card 2: Modules Completed -->
        <a href="index.php?page=my_achievements&tab=completed" style="text-decoration:none; color:inherit; display:block;">
            <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 20px -8px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="28"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                </div>
                <div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: var(--dash-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Modul Selesai</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--dash-text); margin-top: 0.25rem;"><?php echo number_format($modules_completed); ?> Modul</div>
                </div>
            </div>
        </a>

        <!-- Card 3: Total XP -->
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="28"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </div>
            <div>
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--dash-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Total XP</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dash-text); margin-top: 0.25rem;"><?php echo number_format($user_xp); ?> XP</div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <!-- Chart Section -->
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <h3 style="font-size: 1.2rem; color: var(--dash-text); margin-bottom: 1.5rem;">Progress Mingguan (7 Hari Terakhir)</h3>
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="progressChart"></canvas>
            </div>
        </div>

        <!-- Target Belajar Form -->
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
            <h3 style="font-size: 1.2rem; color: var(--dash-text); margin-bottom: 0.5rem;">Target Belajar Mingguan</h3>
            <p style="color: var(--dash-text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Atur berapa jam target kamu belajar dalam seminggu untuk menjaga motivasi.</p>
            
            <div style="display:flex; justify-content:center; margin-bottom:1.5rem; position:relative;">
                <svg viewBox="0 0 100 100" style="width:140px; height:140px;">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="var(--dash-border)" stroke-width="10" />
                    <circle cx="50" cy="50" r="45" fill="none" stroke="var(--dash-primary)" stroke-width="10" stroke-dasharray="283" stroke-dashoffset="<?php echo $stroke_offset; ?>" stroke-linecap="round" transform="rotate(-90 50 50)" />
                </svg>
                <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); text-align:center;">
                    <div style="font-size:1.75rem; font-weight:800; color:var(--dash-text);"><?php echo $weekly_progress_percent; ?>%</div>
                    <div style="font-size:0.6rem; color:var(--dash-text-muted); text-transform:uppercase;">Tercapai</div>
                </div>
            </div>
            
            <?php if ($target_msg): ?>
                <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.75rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($target_msg); ?>
                </div>
            <?php endif; ?>

            <form method="POST" style="margin-top: auto;">
                <label style="display: block; color: var(--dash-text); font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem;">Target (dalam jam):</label>
                <input type="number" name="weekly_target_hours" value="<?php echo htmlspecialchars($current_target / 60); ?>" min="1" step="0.5" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--dash-border); background: var(--dash-bg); color: var(--dash-text); font-family: inherit; margin-bottom: 1rem;">
                <button type="submit" name="update_target" value="1" style="width: 100%; background: #3b82f6; color: white; border: none; padding: 0.75rem; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Simpan Target
                </button>
            </form>
        </div>
    </div>

    <!-- History Sections -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; margin-bottom: 2rem;">

        <!-- Riwayat Hasil Ujian -->
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <h3 style="font-size: 1.2rem; color: var(--dash-text); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg fill="none" viewBox="0 0 24 24" stroke="#ef4444" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                Riwayat Hasil Ujian
            </h3>
            <?php if (empty($exam_history)): ?>
                <p style="color: var(--dash-text-muted); font-size: 0.95rem;">Kamu belum pernah mengikuti ujian apapun.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem; max-height: 400px; overflow-y: auto; padding-right: 0.5rem;">
                    <?php foreach ($exam_history as $exam): ?>
                    <div style="border-bottom: 1px solid var(--dash-border); padding-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.25rem;">
                            <div style="font-weight: 600; color: var(--dash-text); font-size: 1rem; flex: 1;"><?php echo htmlspecialchars($exam['exam_title']); ?></div>
                            <div style="font-weight: 700; font-size: 1.1rem; color: <?php echo $exam['passed'] ? '#10b981' : '#ef4444'; ?>; background: <?php echo $exam['passed'] ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)'; ?>; padding: 0.25rem 0.5rem; border-radius: 6px;">
                                <?php echo $exam['score']; ?>
                            </div>
                        </div>
                        <div style="color: var(--dash-primary); font-size: 0.85rem; font-weight: 500; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($exam['course_title']); ?></div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="color: var(--dash-text-muted); font-size: 0.75rem;"><?php echo date('d M Y, H:i', strtotime($exam['attempt_date'])); ?></div>
                            <div style="font-size: 0.75rem; font-weight: 600; color: <?php echo $exam['passed'] ? '#10b981' : '#ef4444'; ?>;">
                                <?php echo $exam['passed'] ? 'LULUS' : 'GAGAL'; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('progressChart').getContext('2d');
        const isDarkMode = document.body.classList.contains('dark-mode');
        
        // Setup Theme Colors
        const textColor = isDarkMode ? '#e2e8f0' : '#475569';
        const gridColor = isDarkMode ? '#334155' : '#e2e8f0';
        
        // Gradient for Bar Chart
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)'); // dash-primary
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.2)');

        const progressChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Waktu Belajar (Menit)',
                    data: <?php echo json_encode($chart_data); ?>,
                    backgroundColor: gradient,
                    borderColor: '#3b82f6',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    barPercentage: 0.6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#1e293b' : '#ffffff',
                        titleColor: isDarkMode ? '#f8fafc' : '#0f172a',
                        bodyColor: isDarkMode ? '#cbd5e1' : '#475569',
                        borderColor: gridColor,
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' Menit';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: gridColor,
                            drawBorder: false,
                        },
                        ticks: {
                            color: textColor,
                            font: { family: "'Inter', sans-serif", size: 12 },
                            stepSize: 30
                        },
                        title: {
                            display: true,
                            text: 'Menit',
                            color: textColor,
                            font: { family: "'Inter', sans-serif", size: 12 }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            color: textColor,
                            font: { family: "'Inter', sans-serif", size: 13 }
                        }
                    }
                },
                animation: {
                    y: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    }
                }
            }
        });
        
        // Listen for Theme changes to update chart colors dynamically
        const themeToggleBtn = document.getElementById('theme-toggle');
        if(themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                setTimeout(() => {
                    const dark = document.body.classList.contains('dark-mode');
                    const newText = dark ? '#e2e8f0' : '#475569';
                    const newGrid = dark ? '#334155' : '#e2e8f0';
                    const newTooltipBg = dark ? '#1e293b' : '#ffffff';
                    const newTooltipTitle = dark ? '#f8fafc' : '#0f172a';
                    const newTooltipBody = dark ? '#cbd5e1' : '#475569';
                    
                    progressChart.options.scales.x.ticks.color = newText;
                    progressChart.options.scales.y.ticks.color = newText;
                    progressChart.options.scales.y.title.color = newText;
                    progressChart.options.scales.y.grid.color = newGrid;
                    progressChart.options.plugins.tooltip.backgroundColor = newTooltipBg;
                    progressChart.options.plugins.tooltip.titleColor = newTooltipTitle;
                    progressChart.options.plugins.tooltip.bodyColor = newTooltipBody;
                    progressChart.options.plugins.tooltip.borderColor = newGrid;
                    
                    progressChart.update();
                }, 100);
            });
        }
    });
</script>
