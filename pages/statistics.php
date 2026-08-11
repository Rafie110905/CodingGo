<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get total learning time
$stmt = $pdo->prepare("SELECT COALESCE(SUM(time_spent), 0) FROM user_learning_time WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_minutes = $stmt->fetchColumn();

// Get XP
$stmt = $pdo->prepare("SELECT xp_points FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_xp = $stmt->fetchColumn();

// Get modules completed
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$modules_completed = $stmt->fetchColumn();

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
        <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="28"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
            </div>
            <div>
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--dash-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Modul Selesai</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dash-text); margin-top: 0.25rem;"><?php echo number_format($modules_completed); ?> Modul</div>
            </div>
        </div>

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

    <!-- Chart Section -->
    <div style="background: var(--dash-sidebar); border: 1px solid var(--dash-border); border-radius: 16px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <h3 style="font-size: 1.2rem; color: var(--dash-text); margin-bottom: 1.5rem;">Progress Mingguan (7 Hari Terakhir)</h3>
        <div style="position: relative; height: 400px; width: 100%;">
            <canvas id="progressChart"></canvas>
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
