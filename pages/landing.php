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
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Berbasis Komunitas
                    </div>
                    <div class="hero-feature-item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Quiz & Test
                    </div>
                    <div class="hero-feature-item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        AI Review
                    </div>
                    <div class="hero-feature-item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Community
                    </div>
                </div>
            </div>

            <div class="hero-image">
                <div class="floating-badge">
                    <div class="badge-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    AI Review Aktif
                </div>
                <div class="floating-badge-2">
                    <div class="badge-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    HTML Berhasil
                </div>
                <div class="code-editor">
                    <div class="code-editor-header">
                        <div class="dot dot-red"></div>
                        <div class="dot dot-yellow"></div>
                        <div class="dot dot-green"></div>
                        <div class="code-editor-title">index.php - CodingGo</div>
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

                function getIcon($name)
                {
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
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="cta-text">
                        <h2>Siap Menjadi Programmer Profesional?</h2>
                        <p>Tingkatkan skill codingmu dan mulai karir suksesmu bersama CodingGo hari ini juga.</p>
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