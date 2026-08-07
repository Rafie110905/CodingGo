<nav class="navbar">
    <div class="container">
        <a href="index.php" class="logo">
            <div class="logo-icon">CG</div>
            CodingGo
        </a>
        
        <div class="nav-actions">
            <button id="theme-toggle" class="theme-toggle" aria-label="Toggle Dark Mode">
                <span class="theme-text">Light</span>
                <div class="theme-icon-wrapper">
                    <svg class="sun-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <svg class="moon-icon" style="display: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                </div>
            </button>
            <?php if(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                <a href="index.php?page=dashboard" class="btn btn-primary" style="font-weight:600; display:flex; align-items:center; gap:8px;">
                    Dashboard
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline" style="border:none; font-weight:600;">Masuk</a>
                <a href="register.php" class="btn btn-primary">Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
