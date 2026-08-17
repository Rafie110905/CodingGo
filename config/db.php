<?php
$host = 'localhost';
$dbname = 'codinggo_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function ensureDatabaseBootstrap(PDO $pdo): void {
    try {
        $has_weekly_target = $pdo->query("SHOW COLUMNS FROM users LIKE 'weekly_target'")->fetch();
        if (!$has_weekly_target) {
            $pdo->exec("ALTER TABLE users ADD COLUMN weekly_target INT NOT NULL DEFAULT 600 AFTER profile_color");
        }
        
        $has_card_border = $pdo->query("SHOW COLUMNS FROM users LIKE 'card_border_id'")->fetch();
        if (!$has_card_border) {
            $pdo->exec("ALTER TABLE users ADD COLUMN card_border_id INT DEFAULT NULL AFTER profile_effect_id");
            $pdo->exec("ALTER TABLE users ADD CONSTRAINT fk_user_card_border FOREIGN KEY (card_border_id) REFERENCES gamification_perks(id) ON DELETE SET NULL");
        }

        $has_premium_perks = $pdo->query("SHOW COLUMNS FROM users LIKE 'card_background_id'")->fetch();
        if (!$has_premium_perks) {
            $pdo->exec("ALTER TABLE users 
                        ADD COLUMN card_background_id INT DEFAULT NULL AFTER card_border_id,
                        ADD COLUMN cursor_effect_id INT DEFAULT NULL AFTER card_background_id,
                        ADD COLUMN badge_effect_id INT DEFAULT NULL AFTER cursor_effect_id,
                        ADD COLUMN entrance_anim_id INT DEFAULT NULL AFTER badge_effect_id");
            
            $pdo->exec("ALTER TABLE users ADD CONSTRAINT fk_user_card_bg FOREIGN KEY (card_background_id) REFERENCES gamification_perks(id) ON DELETE SET NULL");
            $pdo->exec("ALTER TABLE users ADD CONSTRAINT fk_user_cursor FOREIGN KEY (cursor_effect_id) REFERENCES gamification_perks(id) ON DELETE SET NULL");
            $pdo->exec("ALTER TABLE users ADD CONSTRAINT fk_user_badge_eff FOREIGN KEY (badge_effect_id) REFERENCES gamification_perks(id) ON DELETE SET NULL");
            $pdo->exec("ALTER TABLE users ADD CONSTRAINT fk_user_entrance FOREIGN KEY (entrance_anim_id) REFERENCES gamification_perks(id) ON DELETE SET NULL");

            // Update enum
            $pdo->exec("ALTER TABLE gamification_perks MODIFY COLUMN type ENUM('avatar_frame', 'name_effect', 'profile_effect', 'banner_gif', 'card_border', 'card_background', 'cursor_effect', 'badge_effect', 'entrance_anim') NOT NULL");
        }
    } catch (PDOException $e) {
    }

    $champ_query = $pdo->query("SELECT id FROM championships WHERE status IN ('active', 'upcoming') ORDER BY start_date ASC LIMIT 1");
    $champ = $champ_query->fetch();
    $champ_id = $champ['id'] ?? null;

    if (!$champ_id) {
        $pdo->prepare("INSERT INTO championships (title, description, start_date, end_date, status) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 'active')")
            ->execute([
                'Challenge Mingguan CodingGo',
                'Tantangan mingguan untuk melatih logika, literasi digital, dan kemampuan coding dasar.'
            ]);
        $champ_id = (int)$pdo->lastInsertId();
    }

    $challenge_count = (int)$pdo->query("SELECT COUNT(*) FROM championship_challenges WHERE championship_id = " . (int)$champ_id)->fetchColumn();
    if ($challenge_count < 20) {
        $default_challenges = [
            ['Judul 1: Logika Boolean', 'Jika kondisi A benar dan B salah, maka hasil A && B adalah?', 'false', 50],
            ['Judul 2: Penggunaan Variabel', 'Variabel dalam program berfungsi untuk?', 'menyimpan data', 50],
            ['Judul 3: Struktur Kondisi', 'Fungsi utama if adalah?', 'membuat keputusan berdasarkan kondisi', 50],
            ['Judul 4: Perulangan', 'Perulangan digunakan agar kode?', 'berjalan berulang sesuai pola', 50],
            ['Judul 5: Internet Aman', 'Sebelum klik tautan mencurigakan, yang paling tepat adalah?', 'mencek sumbernya dan menanyakan ke orang dewasa', 50],
            ['Judul 6: Program Dasar', 'Bahasa pemrograman yang populer untuk web adalah?', 'javascript', 50],
            ['Judul 7: Data Pribadi', 'Data paling sensitif yang tidak boleh dibagikan ke orang asing adalah?', 'nomor telepon dan alamat rumah', 50],
            ['Judul 8: Microsoft Excel', 'Fungsi utama rumus IF di Excel adalah?', 'membuat keputusan berdasarkan syarat', 50],
            ['Judul 9: Desain', 'Layout yang rapi membuat desain terasa?', 'lebih mudah dibaca dan enak dilihat', 50],
            ['Judul 10: Keamanan Digital', '2FA adalah singkatan dari?', 'two factor authentication', 50],
            ['Judul 11: HTML', 'Tag yang digunakan untuk judul utama adalah?', 'h1', 50],
            ['Judul 12: Logika Matematika', 'Hasil dari 8 + 4 / 2 adalah?', '10', 50],
            ['Judul 13: Digital Footprint', 'Jejak digital adalah?', 'rekam aktivitas kita di internet', 50],
            ['Judul 14: AI', 'AI yang baik sebaiknya digunakan dengan?', 'verifikasi dan etika', 50],
            ['Judul 15: Presentasi', 'Slide yang efektif biasanya?', 'ringkas, jelas, dan visual', 50],
            ['Judul 16: Internet & Data', 'Cookie pada website berfungsi untuk?', 'mengingat preferensi pengguna', 50],
            ['Judul 17: Simulasi', 'Urutan instruksi yang benar disebut?', 'sequencing', 50],
            ['Judul 18: Cyber Safety', 'Jika menerima SMS menang hadiah tidak jelas, langkah paling aman adalah?', 'mengabaikannya dan mengeceknya melalui kanal resmi', 50],
            ['Judul 19: Programming Mindset', 'Cara terbaik belajar coding adalah?', 'latihan rutin dan membangun proyek kecil', 50],
            ['Judul 20: Problem Solving', 'Saat menemukan bug, langkah terbaik adalah?', 'mencari penyebabnya dan mengujinya satu per satu', 50],
        ];

        foreach ($default_challenges as $index => $challenge) {
            $exists = $pdo->prepare("SELECT id FROM championship_challenges WHERE championship_id = ? AND title = ?");
            $exists->execute([$champ_id, $challenge[0]]);
            if ($exists->fetch()) {
                continue;
            }

            $pdo->prepare("INSERT INTO championship_challenges (championship_id, title, description, correct_answer, xp_reward) VALUES (?, ?, ?, ?, ?)")
                ->execute([
                    $champ_id,
                    $challenge[0],
                    "### Soal Challenge\n\n" . $challenge[1] . "\n\nJawaban singkat: " . $challenge[2] . "\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.",
                    strtolower(trim($challenge[2])),
                    $challenge[3]
                ]);
        }
    }

    $umum_courses = [
        [
            'title' => 'Keamanan Siber untuk Profesional & Bisnis',
            'description' => 'Belajar cara menjaga akun digital, mengenali phishing, mengelola data sensitif, dan membangun keamanan siber di lingkungan kerja serta bisnis.',
            'materials' => [
                ['Pengenalan Keamanan Siber', "Keamanan siber adalah upaya melindungi data, akun, dan perangkat dari ancaman digital seperti phishing, malware, dan pencurian identitas. Di dunia kerja, keamanan data sangat penting karena informasi sensitif bisa merugikan organisasi jika jatuh ke tangan yang salah.", 40, 1],
                ['Phishing dan Social Engineering', "Phishing adalah teknik penipuan yang mengecoh pengguna untuk membuka link atau membagikan informasi pribadi. Social engineering memanfaatkan psikologi manusia untuk mendapatkan akses. Kunci pencegahan adalah skeptis terhadap pesan mendadak, mengecek sumber, dan tidak pernah membagikan password atau OTP.", 40, 2],
                ['Keamanan Akun & Data Bisnis', "Untuk keamanan akun, gunakan password unik, aktifkan autentikasi dua langkah, dan cadangkan data secara rutin. Di lingkungan bisnis, data pelanggan, keuangan, dan dokumen internal harus terlindungi dengan aturan akses yang jelas dan kebiasaan kerja yang aman.", 45, 3],
            ],
            'exam_title' => 'Ujian Akhir: Keamanan Siber untuk Profesional & Bisnis',
            'questions' => [
                ['multiple_choice', 'Apa tujuan utama keamanan siber?', 'melindungi data dan sistem digital dari ancaman', 'membuat semua situs aman', 'menghapus semua file', 'mengganti perangkat komputer', 'a', 10],
                ['multiple_choice', 'Tindakan paling aman saat menerima email mencurigakan adalah?', 'mengabaikan dan mengecek sumbernya dengan cara resmi', 'langsung mengklik tautannya', 'membalas dengan data pribadi', 'membagikan ke teman', 'a', 10],
                ['multiple_choice', 'Apa fungsi autentikasi dua langkah?', 'menambah lapisan keamanan saat login', 'menghapus password', 'mengganti format file', 'membatasi koneksi internet', 'a', 10],
                ['multiple_choice', 'Kenapa data bisnis perlu dilindungi?', 'karena data sensitif bisa merugikan organisasi jika bocor', 'karena data tidak bisa dipakai', 'karena semua data sama', 'karena bisnis tidak perlu data', 'a', 10],
                ['multiple_choice', 'Yang paling tepat saat melihat link mencurigakan adalah?', 'menghindari klik dan mengecek domain asli', 'langsung membuka untuk membuktikannya', 'mengirim ke semua teman', 'membagikan di grup kerja', 'a', 10],
            ]
        ],
        [
            'title' => 'Otomatisasi Kerja dengan Python Dasar',
            'description' => 'Pelajari cara menggunakan Python dasar untuk otomatisasi tugas harian, pengolahan data ringan, dan peningkatan produktivitas kerja.',
            'materials' => [
                ['Pengenalan Python untuk Kerja', "Python adalah bahasa pemrograman yang mudah dipelajari dan sering dipakai untuk otomatisasi tugas. Dengan Python, kamu bisa membuat skrip untuk mengolah data, meringkas file, atau menyederhanakan pekerjaan berulang.", 40, 1],
                ['Variabel, List, dan Kondisi', "Variabel menampung data, sedangkan list memungkinkan kita menyimpan beberapa data sekaligus. Struktur kondisi seperti if dan else membantu komputer mengambil keputusan berdasarkan aturan tertentu.", 40, 2],
                ['Loop dan Automasi Tugas', "Loop atau perulangan memungkinkan tugas dijalankan berulang tanpa menulis kode berulang. Ini sangat berguna saat mengolah berkas, membuat laporan, atau menjalankan tugas yang berulang setiap hari.", 45, 3],
            ],
            'exam_title' => 'Ujian Akhir: Otomatisasi Kerja dengan Python Dasar',
            'questions' => [
                ['multiple_choice', 'Apa fungsi variabel dalam Python?', 'menyimpan data yang akan dipakai program', 'menghapus file', 'mencegah internet', 'membuat desain web', 'a', 10],
                ['multiple_choice', 'Struktur if digunakan untuk?', 'membuat keputusan berdasarkan kondisi', 'mengulang kode tanpa batas', 'mengganti nama file', 'menghentikan program', 'a', 10],
                ['multiple_choice', 'Apa manfaat loop dalam otomatisasi?', 'mengulang tugas tanpa menulis berulang', 'menghilangkan seluruh data', 'menambah password', 'mengatur tampilan layar', 'a', 10],
                ['multiple_choice', 'Kapan Python cocok dipakai untuk pekerjaan?', 'ketika ada tugas berulang dan data yang perlu diproses', 'hanya saat menggambar', 'hanya saat menonton video', 'hanya saat membuat game', 'a', 10],
                ['multiple_choice', 'List di Python berfungsi untuk?', 'menyimpan beberapa nilai dalam satu variabel', 'merubah warna layar', 'membuat email', 'membatasi internet', 'a', 10],
            ]
        ],
        [
            'title' => 'Microsoft Office untuk Produktivitas Kerja',
            'description' => 'Kuasai Word, Excel, dan PowerPoint untuk menulis dokumen, mengolah data, dan membuat presentasi yang efektif di pekerjaan sehari-hari.',
            'materials' => [
                ['Word untuk Dokumen Profesional', "Microsoft Word membantu menulis proposal, surat, laporan, dan dokumen resmi. Fitur seperti heading, bullet, tabel, dan format paragraf membuat dokumen lebih rapi dan mudah dibaca.", 40, 1],
                ['Excel untuk Data & Analisis', "Excel digunakan untuk menghitung, mengelola data, dan membuat tabel. Fungsi dasar seperti SUM, AVERAGE, IF, dan format tabel akan sangat membantu pada pekerjaan kantor maupun tugas sekolah.", 40, 2],
                ['PowerPoint untuk Presentasi', "PowerPoint membantu menyampaikan ide dengan visual yang jelas. Slide yang baik berisi poin utama, desain yang tidak berlebihan, dan alur yang mudah dipahami audiens.", 45, 3],
            ],
            'exam_title' => 'Ujian Akhir: Microsoft Office untuk Produktivitas Kerja',
            'questions' => [
                ['multiple_choice', 'Fungsi utama Microsoft Word adalah?', 'membuat dan mengedit dokumen', 'mengolah angka', 'membuat database server', 'mengedit foto', 'a', 10],
                ['multiple_choice', 'Rumus Excel yang digunakan untuk menjumlahkan data adalah?', 'SUM', 'IF', 'AVERAGE', 'TEXT', 'a', 10],
                ['multiple_choice', 'Fungsi utama PowerPoint adalah?', 'membuat presentasi visual', 'mengolah tabel', 'mengedit video', 'membuat email', 'a', 10],
                ['multiple_choice', 'Manfaat menggunakan bullet point di Word adalah?', 'membuat informasi lebih mudah dibaca', 'menyembunyikan isi dokumen', 'mempercepat printer', 'menghapus semua halaman', 'a', 10],
                ['multiple_choice', 'Mengapa tata letak slide harus sederhana?', 'agar audiens lebih mudah memahami pesan', 'karena semua slide harus penuh', 'karena ini aturan teknis', 'karena warna harus banyak', 'a', 10],
            ]
        ],
        [
            'title' => 'Literasi AI & Produktivitas Kerja',
            'description' => 'Pahami cara kerja AI, manfaatnya untuk produktivitas, serta apa yang harus diperhatikan agar penggunaan AI tetap etis dan aman.',
            'materials' => [
                ['Apa Itu AI?', "AI atau kecerdasan buatan adalah teknologi yang mampu memproses pola data dan membantu manusia dalam tugas tertentu. AI dapat membantu menulis, menganalisis data, dan mengotomatiskan pekerjaan sederhana.", 40, 1],
                ['AI untuk Produktivitas', "AI dapat membantu menulis email, membuat ringkasan, menganalisis data, dan menyusun ide. Penggunaan AI yang benar akan mempercepat pekerjaan, tetapi tetap perlu cek hasilnya agar tetap akurat dan relevan.", 40, 2],
                ['Etika AI & Verifikasi', "Penggunaan AI harus memperhatikan etika, privasi, dan tanggung jawab. Jangan langsung percaya seluruh hasil AI; selalu cek fakta, hindari plagiarisme, dan gunakan AI sebagai alat bantu, bukan pengganti berpikir kritis.", 45, 3],
            ],
            'exam_title' => 'Ujian Akhir: Literasi AI & Produktivitas Kerja',
            'questions' => [
                ['multiple_choice', 'AI paling sering digunakan untuk?', 'membantu proses analisis dan otomatisasi', 'mengganti semua manusia', 'menghapus semua data', 'membuat mesin listrik', 'a', 10],
                ['multiple_choice', 'Apa yang harus dilakukan sebelum menerima hasil AI?', 'menganalisa dan memverifikasi kebenarannya', 'langsung mengirim tanpa cek', 'langsung percaya 100%', 'menghapus semua data lain', 'a', 10],
                ['multiple_choice', 'Mengapa etika penting dalam penggunaan AI?', 'agar penggunaan AI tetap aman dan bertanggung jawab', 'karena AI tidak membutuhkan etika', 'karena semua AI selalu benar', 'karena AI tidak punya data', 'a', 10],
                ['multiple_choice', 'AI dapat membantu pekerjaan seperti?', 'menulis ringkasan dan menganalisis data', 'menghentikan semua internet', 'menyusun jadwal tanpa logika', 'menghapus program', 'a', 10],
                ['multiple_choice', 'Kebiasaan bijak menggunakan AI adalah?', 'menggunakannya sebagai alat bantu sambil tetap kritis', 'menggunakannya tanpa evaluasi', 'tidak pernah digunakan sama sekali', 'menghasilkan konten tanpa sumber', 'a', 10],
            ]
        ],
        [
            'title' => 'Personal Branding & Karier Digital',
            'description' => 'Bangun portofolio online, optimalkan LinkedIn, dan presentasikan diri secara digital agar lebih siap menghadapi dunia kerja modern.',
            'materials' => [
                ['Dasar Personal Branding', "Personal branding adalah cara seseorang menampilkan kemampuan, nilai, dan citra profesionalnya di dunia kerja. Branding yang kuat membuat calon perusahaan atau klien lebih mudah mengenali keunggulanmu.", 40, 1],
                ['Profil Digital & LinkedIn', "LinkedIn dan platform digital lain menjadi tempat untuk menampilkan pengalaman kerja, proyek, dan kemampuan. Profil yang rapi, jelas, dan konsisten akan meningkatkan kredibilitasmu di mata rekruter atau mitra bisnis.", 40, 2],
                ['Portofolio dan Komunikasi Profesional', "Portofolio membantu menunjukkan hasil kerja nyata. Selain itu, komunikasi profesional seperti email, presentasi, dan pesan kerja harus jelas, sopan, dan terstruktur agar hubungan kerja lebih efektif.", 45, 3],
            ],
            'exam_title' => 'Ujian Akhir: Personal Branding & Karier Digital',
            'questions' => [
                ['multiple_choice', 'Tujuan personal branding adalah?', 'membuat orang lebih mudah mengenali kemampuan dan nilai profesionalmu', 'menyembunyikan identitas', 'mengurangi semua portofolio', 'menghapus profil online', 'a', 10],
                ['multiple_choice', 'Platform yang sering dipakai untuk profil profesional adalah?', 'LinkedIn', 'Game online', 'Aplikasi musik', 'File dokumen pribadi', 'a', 10],
                ['multiple_choice', 'Keuntungan portofolio digital adalah?', 'menunjukkan bukti kemampuan secara nyata', 'menghapus semua pengalaman', 'mengganggu rekruter', 'membuat profil tampil rumit', 'a', 10],
                ['multiple_choice', 'Komunikasi profesional yang baik biasanya?', 'jelas, sopan, dan terstruktur', 'panjang tanpa inti', 'menggunakan kata kasar', 'mengabaikan detail', 'a', 10],
                ['multiple_choice', 'Konten profil digital yang baik adalah?', 'rapi, konsisten, dan relevan dengan tujuan karier', 'semua informasi tanpa filter', 'tidak perlu diperbarui', 'hanya foto tanpa deskripsi', 'a', 10],
            ]
        ],
    ];

    $existing_umum = $pdo->query("SELECT id, title FROM courses WHERE category = 'Umum' ORDER BY id ASC")->fetchAll();
    $existing_titles = [];
    foreach ($existing_umum as $course_row) {
        $existing_titles[$course_row['title']] = (int)$course_row['id'];
    }

    foreach ($umum_courses as $umum_course) {
        $course_title = $umum_course['title'];
        $course_id = (int)($existing_titles[$course_title] ?? 0);

        if (!$course_id) {
            $pdo->prepare("INSERT INTO courses (title, category, description, theme_color, created_by) VALUES (?, 'Umum', ?, '#f59e0b', NULL)")
                ->execute([$course_title, $umum_course['description']]);
            $course_id = (int)$pdo->lastInsertId();
        } else {
            $pdo->prepare("UPDATE courses SET description = ?, theme_color = '#f59e0b' WHERE id = ?")
                ->execute([$umum_course['description'], $course_id]);
        }

        $material_count = (int)$pdo->query("SELECT COUNT(*) FROM materials WHERE course_id = " . $course_id)->fetchColumn();
        if ($material_count < count($umum_course['materials'])) {
            foreach ($umum_course['materials'] as $idx => $material) {
                $exists = $pdo->prepare("SELECT id FROM materials WHERE course_id = ? AND title = ?");
                $exists->execute([$course_id, $material[0]]);
                if ($exists->fetch()) {
                    continue;
                }

                $pdo->prepare("INSERT INTO materials (course_id, title, content_type, content_text, xp_reward, order_index) VALUES (?, ?, 'text', ?, ?, ?)")
                    ->execute([$course_id, $material[0], $material[1], $material[2], $idx + 1]);
            }
        }

        $exam = $pdo->query("SELECT id, min_score_passing FROM exams WHERE course_id = " . $course_id . " ORDER BY id ASC LIMIT 1")->fetch();
        $exam_id = (int)($exam['id'] ?? 0);
        if (!$exam_id) {
            $pdo->prepare("INSERT INTO exams (course_id, title, type, min_score_passing) VALUES (?, ?, 'quiz', 70)")
                ->execute([$course_id, $umum_course['exam_title']]);
            $exam_id = (int)$pdo->lastInsertId();
        } else {
            $pdo->prepare("UPDATE exams SET min_score_passing = 70 WHERE id = ?")
                ->execute([$exam_id]);
        }

        $question_count = (int)$pdo->query("SELECT COUNT(*) FROM exam_questions WHERE exam_id = " . $exam_id)->fetchColumn();
        if ($question_count < count($umum_course['questions'])) {
            foreach ($umum_course['questions'] as $question) {
                $exists = $pdo->prepare("SELECT id FROM exam_questions WHERE exam_id = ? AND question_text = ?");
                $exists->execute([$exam_id, $question[1]]);
                if ($exists->fetch()) {
                    continue;
                }

                $pdo->prepare("INSERT INTO exam_questions (exam_id, question_type, question_text, option_a, option_b, option_c, option_d, correct_answer, points) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")
                    ->execute([
                        $exam_id,
                        $question[0],
                        $question[1],
                        $question[2],
                        $question[3],
                        $question[4],
                        $question[5],
                        $question[6],
                        $question[7],
                    ]);
            }
        }
    }

    $pdo->query("UPDATE exams e
        JOIN (
            SELECT exam_id, COALESCE(SUM(points), 0) AS total_points
            FROM exam_questions
            GROUP BY exam_id
        ) q ON q.exam_id = e.id
        SET e.min_score_passing = CASE
            WHEN q.total_points >= 70 THEN 70
            ELSE q.total_points
        END
        WHERE e.min_score_passing != CASE
            WHEN q.total_points >= 70 THEN 70
            ELSE q.total_points
        END");
}

ensureDatabaseBootstrap($pdo);
