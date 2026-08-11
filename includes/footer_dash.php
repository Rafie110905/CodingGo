<?php
// Cek Broadcast yang belum dilihat
if (isset($pdo) && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $stmt_bc = $pdo->prepare("SELECT * FROM broadcasts WHERE is_active = 1 AND id NOT IN (SELECT broadcast_id FROM broadcast_views WHERE user_id = ?) ORDER BY created_at DESC LIMIT 1");
    $stmt_bc->execute([$uid]);
    $active_broadcast = $stmt_bc->fetch();
}
?>
<?php if (!empty($active_broadcast)): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: <?php echo json_encode($active_broadcast['title']); ?>,
            html: <?php echo json_encode(nl2br(htmlspecialchars($active_broadcast['message']))); ?>,
            icon: <?php echo json_encode($active_broadcast['type']); ?>,
            confirmButtonText: 'Tutup & Mengerti',
            confirmButtonColor: 'var(--primary, #3b82f6)',
            allowOutsideClick: false,
            backdrop: `rgba(0,0,0,0.5)`
        }).then((result) => {
            if (result.isConfirmed) {
                // Tandai sebagai terbaca
                fetch('api/broadcast_read.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ broadcast_id: <?php echo $active_broadcast['id']; ?> })
                }).catch(err => console.error('Error marking broadcast as read:', err));
            }
        });
    });
</script>
<?php endif; ?>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        if(themeToggle) {
            const themeText = themeToggle.querySelector('.theme-text');
            const sunIcon = themeToggle.querySelector('.sun-icon');
            const moonIcon = themeToggle.querySelector('.moon-icon');

            // Load saved theme
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark-mode');
                if(themeText) themeText.textContent = 'Dark';
                if(sunIcon) sunIcon.style.display = 'none';
                if(moonIcon) moonIcon.style.display = 'block';
            }

            themeToggle.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                const isDark = document.body.classList.contains('dark-mode');
                
                localStorage.setItem('theme', isDark ? 'dark' : 'light');

                if (isDark) {
                    if(themeText) themeText.textContent = 'Dark';
                    if(sunIcon) sunIcon.style.display = 'none';
                    if(moonIcon) moonIcon.style.display = 'block';
                } else {
                    if(themeText) themeText.textContent = 'Light';
                    if(sunIcon) sunIcon.style.display = 'block';
                    if(moonIcon) moonIcon.style.display = 'none';
                }
            });
        }

        // Time Tracking Script (Stats)
        setInterval(function() {
            // Only ping if tab is active (visible)
            if(document.visibilityState === 'visible') {
                fetch('api/track_time.php')
                .catch(err => console.error('Time tracking error:', err));
            }
        }, 60000);
    </script>
</body>
</html>
