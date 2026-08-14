<?php
$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'User');
$user_class = 'Learner';
if ($_SESSION['user_role'] === 'admin') {
    $user_class = 'Administrator';
}

$user_picture = isset($_SESSION['user_picture']) && $_SESSION['user_picture'] ? htmlspecialchars($_SESSION['user_picture']) : 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%2394a3b8"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';

// Fetch Notifications and update role
$unread_notifications_count = 0;
$notifications = [];
if (isset($_SESSION['user_id'])) {
    // Fetch actual user access for profile role display
    $stmt_u = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt_u->execute([$_SESSION['user_id']]);
    $u_data = $stmt_u->fetch();
    
    if ($u_data && $_SESSION['user_role'] !== 'admin') {
        $allowed = getUserAllowedCategories($u_data);
        if (!empty($allowed)) {
            $user_class = end($allowed); // Get highest level allowed (e.g. if SD, SMP -> SMP)
        } else {
            $user_class = 'Akses Terbatas';
        }
    }

    $stmt_notif = $pdo->prepare("SELECT * FROM user_notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt_notif->execute([$_SESSION['user_id']]);
    $notifications = $stmt_notif->fetchAll();
    
    $stmt_unread = $pdo->prepare("SELECT COUNT(*) FROM user_notifications WHERE user_id = ? AND is_read = 0");
    $stmt_unread->execute([$_SESSION['user_id']]);
    $unread_notifications_count = $stmt_unread->fetchColumn();
}
?>
<header class="dash-topbar">
    <div class="dash-search-box">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" style="color:var(--dash-text-muted);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        <input type="text" placeholder="Cari materi, kelas, atau topik...">
    </div>
    
    <div class="dash-topbar-right">
        <div class="notif-dropdown-wrapper" style="position:relative; display:flex; align-items:center;">
            <button class="notif-btn" style="background:transparent; border:none; position:relative; color:var(--dash-text-muted); cursor:pointer;" onclick="toggleNotifDropdown(event)">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                <?php if($unread_notifications_count > 0): ?>
                    <span class="notif-badge" style="position:absolute; top:0; right:2px; width:8px; height:8px; background:var(--dash-warning); border-radius:50%; border:2px solid var(--dash-bg);"></span>
                <?php endif; ?>
            </button>
            
            <div id="notif-dropdown" style="display:none; position:absolute; right:0; top:calc(100% + 10px); background:var(--dash-sidebar); border:1px solid var(--dash-border); border-radius:12px; width:300px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); z-index:100; overflow:hidden;">
                <div style="padding:1rem; border-bottom:1px solid var(--dash-border); font-weight:600; color:var(--dash-text); display:flex; justify-content:space-between; align-items:center;">
                    Notifikasi
                </div>
                <div style="max-height:350px; overflow-y:auto;">
                    <?php if(count($notifications) > 0): ?>
                        <?php foreach($notifications as $notif): ?>
                            <a href="<?php echo htmlspecialchars($notif['link_url'] ?? '#'); ?>" style="display:block; padding:1rem; border-bottom:1px solid var(--dash-border); text-decoration:none; transition:background 0.2s; background:<?php echo $notif['is_read'] ? 'transparent' : 'rgba(59, 130, 246, 0.05)'; ?>;" onmouseover="this.style.background='var(--dash-bg)'" onmouseout="this.style.background='<?php echo $notif['is_read'] ? 'transparent' : 'rgba(59, 130, 246, 0.05)'; ?>'">
                                <div style="font-size:0.9rem; font-weight:600; color:var(--dash-text); margin-bottom:4px;"><?php echo htmlspecialchars($notif['title']); ?></div>
                                <div style="font-size:0.8rem; color:var(--dash-text-muted); line-height:1.4;"><?php echo htmlspecialchars($notif['message']); ?></div>
                                <div style="font-size:0.7rem; color:var(--dash-primary); margin-top:6px;"><?php echo date('d M Y, H:i', strtotime($notif['created_at'])); ?></div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding:2rem 1rem; text-align:center; color:var(--dash-text-muted); font-size:0.9rem;">
                            Tidak ada notifikasi saat ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <button id="theme-toggle" style="background:transparent; border:none; color:var(--dash-text-muted); cursor:pointer; display:flex; align-items:center;">
            <svg class="sun-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            <svg class="moon-icon" style="display:none;" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        </button>

        <div class="profile-dropdown-wrapper" style="position:relative; display:flex; align-items:center;">
            <div class="dash-profile" style="cursor:pointer; transition:background 0.2s;" onclick="document.getElementById('profile-dropdown').classList.toggle('show')">
                <img src="<?php echo $user_picture; ?>" alt="Profile" referrerpolicy="no-referrer">
                <div class="dash-profile-info">
                    <span class="dash-profile-name" style="color:var(--dash-text);"><?php echo explode(' ', $user_name)[0]; ?></span>
                    <span class="dash-profile-role" style="color:var(--dash-text-muted);"><?php echo $user_class; ?></span>
                </div>
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" style="color:var(--dash-text-muted); margin-left:4px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </div>
            
            <div id="profile-dropdown" style="display:none; position:absolute; right:0; top:calc(100% + 10px); background:var(--dash-sidebar); border:1px solid var(--dash-border); border-radius:12px; min-width:200px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); padding:0.5rem; z-index:100;">
                <a href="index.php?page=user_settings" style="display:flex; align-items:center; gap:10px; padding:0.75rem 1rem; color:var(--dash-text); text-decoration:none; border-radius:8px; font-size:0.9rem; font-weight:500; transition:background 0.2s;" onmouseover="this.style.background='var(--dash-bg-hover)'" onmouseout="this.style.background='transparent'">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" style="color:var(--dash-text-muted);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Pengaturan Profil
                </a>
                <div style="height:1px; background:var(--dash-border); margin:0.5rem 0;"></div>
                <a href="logout.php" style="display:flex; align-items:center; gap:10px; padding:0.75rem 1rem; color:#ef4444; text-decoration:none; border-radius:8px; font-size:0.9rem; font-weight:500; transition:background 0.2s;" onmouseover="this.style.background='rgba(239, 68, 68, 0.1)'" onmouseout="this.style.background='transparent'">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Keluar / Logout
                </a>
            </div>
        </div>
        
        <script>
            // Close dropdown if clicked outside (Profile)
            document.addEventListener('click', function(event) {
                var isClickInsideProfile = document.querySelector('.profile-dropdown-wrapper').contains(event.target);
                if (!isClickInsideProfile) {
                    var pDropdown = document.getElementById('profile-dropdown');
                    if (pDropdown && pDropdown.classList.contains('show')) {
                        pDropdown.classList.remove('show');
                        pDropdown.style.display = 'none';
                    }
                }
                
                var isClickInsideNotif = document.querySelector('.notif-dropdown-wrapper').contains(event.target);
                if (!isClickInsideNotif) {
                    var nDropdown = document.getElementById('notif-dropdown');
                    if (nDropdown && nDropdown.classList.contains('show')) {
                        nDropdown.classList.remove('show');
                        nDropdown.style.display = 'none';
                    }
                }
            });
            
            // Override toggle logic for Profile
            const profWrapper = document.querySelector('.profile-dropdown-wrapper .dash-profile');
            if (profWrapper) {
                profWrapper.onclick = function(e) {
                    e.stopPropagation();
                    // close notif if open
                    var nDropdown = document.getElementById('notif-dropdown');
                    if(nDropdown) { nDropdown.style.display = 'none'; nDropdown.classList.remove('show'); }

                    var dropdown = document.getElementById('profile-dropdown');
                    if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                        dropdown.style.display = 'block';
                        dropdown.classList.add('show');
                    } else {
                        dropdown.style.display = 'none';
                        dropdown.classList.remove('show');
                    }
                };
            }
            
            // Toggle Logic for Notifications
            function toggleNotifDropdown(e) {
                e.stopPropagation();
                // close profile if open
                var pDropdown = document.getElementById('profile-dropdown');
                if(pDropdown) { pDropdown.style.display = 'none'; pDropdown.classList.remove('show'); }
                
                var dropdown = document.getElementById('notif-dropdown');
                if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                    dropdown.style.display = 'block';
                    dropdown.classList.add('show');
                    
                    // Mark as read via AJAX
                    var badge = document.querySelector('.notif-badge');
                    if(badge) {
                        fetch('api/read_notifications.php')
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                badge.style.display = 'none'; // hide red dot immediately
                            }
                        });
                    }
                } else {
                    dropdown.style.display = 'none';
                    dropdown.classList.remove('show');
                }
            }
        </script>
    </div>
</header>
