<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - CodingGo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    

    
    <style>
        .footer > .container:first-of-type {
            position: relative;
        }

        .footer-social {
            position: absolute;
            left: 50%;
            top: 0;
            transform: translateX(-50%);
        }
         @media (max-width: 640px) {
            .footer-social {
                position: static;
                transform: none;
                margin: 12px 0;
            }
        }
        
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --radius-full: 9999px;
            --dark-bg: #0f172a;
        }

        body.dark-mode {
            --bg-main: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-main); color: var(--text-main); }
        a { text-decoration: none; color: inherit; }
        button { cursor: pointer; border: none; font-family: inherit; }

        /* Navbar & Footer exact matches from index.php */
        .navbar { padding: 1.5rem 0; background: var(--bg-main); border-bottom: 1px solid var(--border); }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        .navbar .container { display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; gap: 0.75rem; font-weight: 700; font-size: 1.25rem; }
        .logo-icon { background: var(--primary); color: white; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 0.875rem; }
        .nav-actions { display: flex; align-items: center; gap: 1.5rem; }
        .btn { padding: 0.6rem 1.25rem; font-weight: 600; border-radius: var(--radius-full); transition: all 0.3s ease; font-size: 0.95rem; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-outline { background: transparent; color: var(--text-main); border: 1px solid var(--border); }

        .theme-toggle {
            display: flex; align-items: center; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 20px; padding: 4px 6px 4px 12px; gap: 8px; cursor: pointer;
        }
        .theme-text { font-size: 0.875rem; font-weight: 600; color: var(--primary); }
        .theme-icon-wrapper { background: var(--primary); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .theme-icon-wrapper svg { width: 14px; height: 14px; }
        body.dark-mode .theme-toggle { background: #1e293b; border-color: #334155; flex-direction: row-reverse; padding: 4px 12px 4px 6px; }
        body.dark-mode .theme-text { color: #e2e8f0; }
        
        /* Register Section */
        .register-container {
            display: flex;
            max-width: 1100px;
            margin: 3rem auto;
            background: var(--bg-card);
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid var(--border);
            min-height: 600px;
        }

        /* Left Side */
        .register-left {
            flex: 1;
            background: linear-gradient(145deg, #e0f2fe 0%, #f0f9ff 100%);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        body.dark-mode .register-left {
            background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
        }

        .badge-trust {
            background: white;
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: var(--radius-full);
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            width: fit-content;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        body.dark-mode .badge-trust { background: #0f172a; color: #38bdf8; }

        .register-left h1 {
            font-size: 2.5rem;
            color: var(--text-main);
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        .register-left p {
            color: var(--text-muted);
            font-size: 1rem;
            max-width: 50%;
            margin-bottom: 2rem;
        }

        .illustration {
            margin-top: 100px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 250px;
        }

        /* Right Side (Form) */
        .register-right {
            flex: 1;
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .lock-icon-top {
            width: 48px;
            height: 48px;
            background: #eff6ff;
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        body.dark-mode .lock-icon-top { background: rgba(59, 130, 246, 0.2); }

        .register-right h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .register-right p.subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .form-group {
            width: 100%;
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-wrapper svg {
            position: absolute;
            left: 1rem;
            width: 20px;
            height: 20px;
            color: var(--text-muted);
        }
        .input-wrapper input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: transparent;
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s ease;
        }
        .input-wrapper input:focus {
            border-color: var(--primary);
        }
        
        .toggle-password {
            position: absolute;
            right: 1rem;
            left: auto !important;
            cursor: pointer;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
        }
        .forgot-link {
            color: var(--primary);
            font-weight: 600;
        }

        .btn-full {
            width: 100%;
            padding: 0.875rem;
            border-radius: 12px;
            font-size: 1rem;
        }

        .divider {
            display: flex;
            align-items: center;
            width: 100%;
            margin: 1.5rem 0;
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 600;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
            margin: 0 1rem;
        }

        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.875rem;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: transparent;
            color: var(--text-main);
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.3s ease;
        }
        .btn-google:hover { background: rgba(0,0,0,0.02); }
        body.dark-mode .btn-google:hover { background: rgba(255,255,255,0.05); }

        .bottom-text {
            margin-top: 2rem;
            font-size: 0.95rem;
            color: var(--text-muted);
        }
        .bottom-text a {
            color: var(--primary);
            font-weight: 600;
        }
        
        /* Footer */
        .footer { background: var(--dark-bg); color: white; padding: 4rem 0 2rem 0; margin-top: auto; }
        .footer .container { display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 2rem; margin-bottom: 2rem; }
        .footer-logo { display: flex; align-items: center; gap: 0.75rem; font-weight: 700; font-size: 1.5rem; margin-bottom: 1rem; }
        .footer-logo .logo-icon { background: white; color: var(--primary); }
        .footer-desc { color: #94a3b8; max-width: 400px; font-size: 0.95rem; }
        .footer-bottom { display: flex; justify-content: space-between; align-items: center; color: #64748b; font-size: 0.875rem; }

        @media (max-width: 768px) {
            .register-container { flex-direction: column; }
            .register-left { padding: 2rem; }
            .register-right { padding: 2rem; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
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
                <a href="#" class="btn btn-outline" style="border:none; font-weight:600;">Masuk</a>
                <a href="#" class="btn btn-primary">Daftar</a>
            </div>
        </div>
    </nav>

    <!-- Main Register Content -->
    <div class="container">
        <div class="register-container">
            <!-- Left Side -->
            <div class="register-left">
                <div class="badge-trust">
                    <span style="color:#10b981;">●</span> Platform Pembelajaran Digital Terpercaya
                </div>
                <h1>Selamat Datang di<br><span style="color:var(--primary);">CodingGo</span> 👋</h1>
                <p>Masuk untuk melanjutkan perjalanan belajar coding yang aman dan efektif.</p>
                
                <div class="illustration">
                    <!-- Simple CSS/SVG representation of the laptop illustration -->
                    <div style="background-image: url('/src/img/programing.png'); background-size: cover; background-position: center; width:280px; height:180px; ">
                        <div style="padding:8px; display:flex;">
                            <!-- <div style="width:10px; height:10px; border-radius:50%; background:#ef4444;"></div>
                            <div style="width:10px; height:10px; border-radius:50%; background:#f59e0b;"></div>
                            <div style="width:10px; height:10px; border-radius:50%; background:#10b981;"></div> -->
                        </div>
                        <!-- <div style="padding:1rem; color:#94a3b8; font-family:monospace; font-size:0.75rem; line-height:1.6;">
                            <span style="color:#c678dd;">const</span> belajar = <span style="color:#61afef;">true</span>;<br>
                            <span style="color:#c678dd;">if</span> (coding) {<br>
                            &nbsp;&nbsp;<span style="color:#e06c75;">sukses()</span>;<br>
                            }
                        </div> -->
                    </div>
                    <!-- Badges -->
                    <div style="position:absolute; top:20px; right:10px; background:#f59e0b; color:white; padding:4px 12px; border-radius:8px; font-weight:bold; font-size:0.75rem; transform:rotate(5deg); box-shadow:0 4px 6px rgba(0,0,0,0.1);">JS</div>
                    <div style="position:absolute; bottom:40px; right:-10px; background:#3b82f6; color:white; padding:4px 12px; border-radius:8px; font-weight:bold; font-size:0.75rem; transform:rotate(-5deg); box-shadow:0 4px 6px rgba(0,0,0,0.1);">Python</div>
                    <div style="position:absolute; top:50px; left:10px; background:#ef4444; color:white; padding:4px 12px; border-radius:8px; font-weight:bold; font-size:0.75rem; transform:rotate(-10deg); box-shadow:0 4px 6px rgba(0,0,0,0.1);">HTML</div>
                </div>
            </div>

            <!-- Right Side (Form) -->
            <div class="register-right">
                <div class="lock-icon-top">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:24px; height:24px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
                <h2>Akun Baru <span style="color:var(--primary);">CodingGo</span></h2>
                <p class="subtitle">Isi data sebelum memulai pengalaman belajar</p>

                <form style="width: 100%;">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <div class="input-wrapper">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            <input type="text" placeholder="Masukkan Nama Lengkap kamu">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-wrapper">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <input type="email" placeholder="Masukkan email kamu">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            <input type="password" placeholder="Masukkan password kamu" id="password-input">
                            <svg class="toggle-password" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" style="width:16px; height:16px;"> Ingat saya
                        </label>
                        <a href="#" class="forgot-link">Lupa password?</a>
                    </div>

                    <button type="button" class="btn btn-primary btn-full">Daftar Sekarang</button>
                    
                    <div class="divider">ATAU</div>
                    
                    <button type="button" class="btn-google">
                        <svg viewBox="0 0 24 24" width="20" height="20" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        Daftar dengan Google
                    </button>
                </form>

                <div class="bottom-text">
                    Sudah punya akun? <a href="#">Masuk sekarang</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div>
                <div class="footer-logo">
                    <div class="logo-icon">CG</div>
                    CodingGo
                </div>
                <p class="footer-desc">Platform pembelajaran digital yang aman dan inovatif untuk membantu mengembangkan skill coding dan karir impianmu.</p>
            </div>
            
            <div class="footer-social">
                <a href="#" class="social-icon" aria-label="Facebook">
        <svg fill="currentColor" viewBox="0 0 24 24" width="20" height="20">
            <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z" />
        </svg>
    </a>
                <a href="#" class="social-icon">
                    <svg fill="currentColor" viewBox="0 0 24 24" width="20" height="20">
                        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                    </svg>
                </a>
                <a href="#" class="social-icon">
                    <svg fill="currentColor" viewBox="0 0 24 24" width="20" height="20">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                    </svg>
                </a>
                <a href="#" class="social-icon">
                    <svg fill="currentColor" viewBox="0 0 24 24" width="20" height="20">
                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                    </svg>
                </a>
            </div>
        </div>
        <div class="container footer-bottom">
    <div>&copy; <?php echo date('Y'); ?> CodingGo. All rights reserved.</div>
    <div class="footer-lang">
        <svg class="flag-icon" viewBox="0 0 3 2" width="16" height="16" style="border-radius:2px;flex-shrink:0">
            <rect width="3" height="1" y="0" fill="#e70011" />
            <rect width="3" height="1" y="1" fill="#ffffff" />
        </svg>
        Indonesia
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
</div>
    </footer>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const themeText = themeToggle.querySelector('.theme-text');
        const sunIcon = themeToggle.querySelector('.sun-icon');
        const moonIcon = themeToggle.querySelector('.moon-icon');

        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                themeText.textContent = 'Dark';
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            } else {
                themeText.textContent = 'Light';
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
            }
        });
    </script>
</body>
</html>
