<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodingQa - Belajar Coding Lebih Mudah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="#" class="logo">
                <div class="logo-icon">CQ</div>
                CodingQa
            </a>
            
            <div class="nav-links">
                <a href="#">Beranda</a>
                <a href="#">Tentang</a>
                <a href="#">Fitur</a>
                <a href="#">Kisah Sukses</a>
            </div>
            
            <div class="nav-actions">
                <button id="theme-toggle" class="theme-toggle" aria-label="Toggle Dark Mode">
                    <span class="theme-text">Light</span>
                    <div class="theme-icon-wrapper">
                        <svg class="sun-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <svg class="moon-icon" style="display: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    </div>
                </button>
                <a href="#" class="login-link">Masuk</a>
                <a href="#" class="btn btn-primary">Daftar</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Belajar Coding Lebih <span class="highlight">Mudah, Aman,</span> dan Inovatif.</h1>
                <p>Pelajari HTML, CSS, JavaScript, Python dll, dari instruktur terbaik. Mulai perjalanan karir programming modern, aman, dan mudah dipahami.</p>
                
                <div class="hero-buttons">
                    <a href="#" class="btn btn-primary">
                        Mulai Gratis
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="#" class="btn btn-outline">
                        Lihat Kelas
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </div>
                
                <div class="hero-features">
                    <div class="hero-feature-item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        Berbasis Komunitas
                    </div>
                    <div class="hero-feature-item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        Quiz & Test
                    </div>
                    <div class="hero-feature-item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        AI Review
                    </div>
                    <div class="hero-feature-item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        Community
                    </div>
                </div>
            </div>
            
            <div class="hero-image">
                <div class="floating-badge">
                    <div class="badge-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    AI Review Aktif
                </div>
                <div class="floating-badge-2">
                    <div class="badge-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                    </div>
                    HTML Berhasil
                </div>
                <div class="code-editor">
                    <div class="code-editor-header">
                        <div class="dot dot-red"></div>
                        <div class="dot dot-yellow"></div>
                        <div class="dot dot-green"></div>
                        <div class="code-editor-title">index.php - CodingQa</div>
                    </div>
                    <div class="code-content">
                        <span class="token comment">&lt;!-- Platform Pembelajaran --&gt;</span><br>
                        <span class="token tag">&lt;div</span> <span class="token attr-name">class</span>="<span class="token string">student-profile</span>"<span class="token tag">&gt;</span><br>
                        &nbsp;&nbsp;<span class="token tag">&lt;h1&gt;</span>Hello, Future Programmer!<span class="token tag">&lt;/h1&gt;</span><br>
                        &nbsp;&nbsp;<span class="token tag">&lt;script&gt;</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="token keyword">const</span> user <span class="token keyword">=</span> {<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;name: <span class="token string">'Alex'</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;skills: [<span class="token string">'HTML'</span>, <span class="token string">'CSS'</span>, <span class="token string">'JS'</span>]<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;};<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="token function">console.log</span>(<span class="token string">"Ready to learn!"</span>);<br>
                        &nbsp;&nbsp;<span class="token tag">&lt;/script&gt;</span><br>
                        <span class="token tag">&lt;/div&gt;</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Keunggulan Section -->
    <section class="section bg-light">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Keunggulan</span>
                <h2>Platform Pembelajaran Digital yang Aman dan Inovatif</h2>
                <p>Teknologi AI canggih memberikan feedback dari code/tugas praktik untuk membantu mempercepat proses belajar coding mu.</p>
            </div>
            
            <div class="cards-grid">
                <?php
                $keunggulan = [
                    ['icon' => 'user-group', 'title' => 'Komunitas', 'desc' => 'Diskusi dan berbagi Ilmu dengan sesama developer/programmer.'],
                    ['icon' => 'clock', 'title' => 'Fleksibel', 'desc' => 'Belajar kapan saja, dari mana saja dan di mana saja sesuai jadwalmu.'],
                    ['icon' => 'academic-cap', 'title' => 'Instruktur Terbaik', 'desc' => 'Dibimbing oleh instruktur profesional dan berpengalaman di industrinya.'],
                    ['icon' => 'document-check', 'title' => 'Sertifikat', 'desc' => 'Dapatkan sertifikat kompetensi setelah menyelesaikan setiap kelas.'],
                    ['icon' => 'code-bracket', 'title' => 'Modul Praktik', 'desc' => 'Praktik langsung dengan berbagai studi kasus yang relevan di industri.'],
                    ['icon' => 'chat-bubble-left-right', 'title' => 'Konsultasi', 'desc' => 'Tanya jawab dengan instruktur dan mentor secara langsung 1 on 1.']
                ];
                
                function getIcon($name) {
                    $icons = [
                        'user-group' => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>',
                        'clock' => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                        'academic-cap' => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>',
                        'document-check' => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                        'code-bracket' => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>',
                        'chat-bubble-left-right' => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>'
                    ];
                    return $icons[$name] ?? $icons['user-group'];
                }
                
                foreach ($keunggulan as $item) {
                    echo '
                    <div class="card">
                        <div class="card-icon">
                            ' . getIcon($item['icon']) . '
                        </div>
                        <h3>' . $item['title'] . '</h3>
                        <p>' . $item['desc'] . '</p>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Fitur Unggulan Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Fitur Unggulan</span>
                <h2>Semua yang Dibutuhkan untuk Belajar Coding</h2>
                <p>Teknologi AI inovatif merangkum, dan menyajikan pre-skill untuk merekomendasikan path belajar coding mu.</p>
            </div>
            
            <div class="cards-grid">
                <?php
                $fitur = [
                    ['icon' => 'document-check', 'title' => 'Tugas & Evaluasi', 'desc' => 'Uji pemahamanmu dengan tugas, quiz & latihan evaluasi di akhir setiap bab praktik.'],
                    ['icon' => 'code-bracket', 'title' => 'Code Playground', 'desc' => 'Tulis kode langsung di browser tanpa perlu instalasi aplikasi lain.'],
                    ['icon' => 'chat-bubble-left-right', 'title' => 'Konsultasi Live', 'desc' => 'Tanya jawab dan konsultasi dengan mentor 1 on 1 via Live Chat/Video.'],
                    ['icon' => 'academic-cap', 'title' => 'Smart AI Review', 'desc' => 'Evaluasi code/tugas otomatis dan berikan feedback dalam detik.'],
                    ['icon' => 'user-group', 'title' => 'Karir & Portfolio', 'desc' => 'Bantu bangun portfolio dan raih kesempatan karir di industri IT.'],
                    ['icon' => 'clock', 'title' => 'Akses Selamanya', 'desc' => 'Akses materi dan update kelas seumur hidup dengan sekali bayar.']
                ];
                
                foreach ($fitur as $item) {
                    echo '
                    <div class="card">
                        <div class="card-icon">
                            ' . getIcon($item['icon']) . '
                        </div>
                        <h3>' . $item['title'] . '</h3>
                        <p>' . $item['desc'] . '</p>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-container">
                <div class="stat-item">
                    <div class="stat-icon">
                        <?php echo getIcon('code-bracket'); ?>
                    </div>
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Kelas Tersedia</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <?php echo getIcon('academic-cap'); ?>
                    </div>
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Instruktur Ahli</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <?php echo getIcon('user-group'); ?>
                    </div>
                    <div class="stat-number">10.000+</div>
                    <div class="stat-label">Pengguna Aktif</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <?php echo getIcon('document-check'); ?>
                    </div>
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Tingkat Kelulusan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-container">
                <div class="cta-content">
                    <div class="cta-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <div class="cta-text">
                        <h2>Siap Menjadi Programmer Profesional?</h2>
                        <p>Tingkatkan skill codingmu dan mulai karir suksesmu bersama CodingQa hari ini juga.</p>
                    </div>
                </div>
                <a href="#" class="btn btn-white" style="flex-shrink: 0;">
                    Mulai Belajar 
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div>
                <div class="footer-logo">
                    <div class="logo-icon">CQ</div>
                    CodingQa
                </div>
                <p class="footer-desc">Platform pembelajaran digital yang aman dan inovatif untuk membantu mengembangkan skill coding dan karir impianmu.</p>
            </div>
            
            <div class="footer-social">
                <a href="#" class="social-icon">
                    <svg fill="currentColor" viewBox="0 0 24 24" width="20" height="20"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                </a>
                <a href="#" class="social-icon">
                    <svg fill="currentColor" viewBox="0 0 24 24" width="20" height="20"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
                <a href="#" class="social-icon">
                    <svg fill="currentColor" viewBox="0 0 24 24" width="20" height="20"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
            </div>
        </div>
        <div class="container footer-bottom">
            <div>&copy; <?php echo date('Y'); ?> CodingQa. All rights reserved.</div>
            <div class="footer-lang">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Indonesia
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
