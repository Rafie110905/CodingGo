-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 11, 2026 at 02:59 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `codinggo_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `icon_url` varchar(255) DEFAULT NULL,
  `requirement_type` enum('xp','course','exam','forum_upvotes') NOT NULL,
  `requirement_value` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`id`, `name`, `description`, `icon_url`, `requirement_type`, `requirement_value`) VALUES
(1, 'Master of Microsoft Office Dasar', 'Lulus ujian Quiz: Microsoft Office Dasar', NULL, 'exam', 6),
(2, 'Master of Mengenal Perangkat & Internet', 'Lulus ujian Quiz: Mengenal Perangkat & Internet', NULL, 'exam', 3);

-- --------------------------------------------------------

--
-- Table structure for table `broadcasts`
--

CREATE TABLE `broadcasts` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success') DEFAULT 'info',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `broadcast_views`
--

CREATE TABLE `broadcast_views` (
  `id` int NOT NULL,
  `broadcast_id` int NOT NULL,
  `user_id` int NOT NULL,
  `viewed_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int NOT NULL,
  `certificate_code` varchar(50) NOT NULL,
  `user_id` int NOT NULL,
  `course_id` int NOT NULL,
  `issued_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `certificate_code`, `user_id`, `course_id`, `issued_at`) VALUES
(1, 'CGO-9A87B1-73', 3, 7, '2026-08-10 05:27:11'),
(2, 'CGO-30EE73-43', 3, 4, '2026-08-10 05:40:54');

-- --------------------------------------------------------

--
-- Table structure for table `championships`
--

CREATE TABLE `championships` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT 'upcoming',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `championship_challenges`
--

CREATE TABLE `championship_challenges` (
  `id` int NOT NULL,
  `championship_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `correct_answer` varchar(255) DEFAULT NULL,
  `xp_reward` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `championship_completed_challenges`
--

CREATE TABLE `championship_completed_challenges` (
  `id` int NOT NULL,
  `challenge_id` int NOT NULL,
  `user_id` int NOT NULL,
  `completed_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `championship_participants`
--

CREATE TABLE `championship_participants` (
  `id` int NOT NULL,
  `championship_id` int NOT NULL,
  `user_id` int NOT NULL,
  `xp_earned` int DEFAULT '0',
  `joined_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int NOT NULL,
  `title` varchar(150) NOT NULL,
  `category` enum('SD','SMP','SMA','Umum') NOT NULL,
  `description` text,
  `thumbnail` varchar(255) DEFAULT NULL,
  `theme_color` varchar(10) DEFAULT '#4361ee',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `category`, `description`, `thumbnail`, `theme_color`, `created_by`, `created_at`) VALUES
(4, 'Mengenal Perangkat & Internet', 'SD', 'Mengenal komputer, laptop, tablet, dan cara internet bekerja secara sederhana.', 'https://kamus-ai.com/content/uploads/photos/2025/10/kamus_ai_c244c55db3c03f5c5db463d92e81c0a1.png', '#22c55e', NULL, '2026-08-09 08:52:49'),
(5, 'Keamanan Digital Dasar', 'SD', 'Mengenal password yang kuat dan pentingnya menjaga data pribadi saat online.', 'https://gnld.siberkreasi.id/wp-content/uploads/image-14.png', '#22c55e', NULL, '2026-08-09 08:52:49'),
(6, 'Logika Dasar (Computational Thinking)', 'SD', 'Belajar sequencing dan mengenali pola sederhana lewat puzzle bergambar, tanpa syntax.', 'https://www.kodingakademi.id/storage/blog/ChatGPT-Image-May-8-2025-11_14_39-AM-min.png', '#22c55e', NULL, '2026-08-09 08:52:49'),
(7, 'Microsoft Office Dasar', 'SD', 'Pengenalan Word, Excel, dan PowerPoint lewat misi dan aktivitas sederhana.', 'https://diengcyber.com/wp-content/uploads/2022/04/gambar-microsoft-office.jpg', '#22c55e', NULL, '2026-08-09 08:52:49'),
(8, 'Kreativitas Digital', 'SD', 'Membuat cerita digital sederhana dan menggambar dengan tools dasar berbasis web.', 'https://psea.unikama.ac.id/wp-content/uploads/sites/47/2025/08/result_2327605.jpg', '#22c55e', NULL, '2026-08-09 08:52:49'),
(9, 'Keamanan Siber Menengah', 'SMP', 'Mengenali phishing, penipuan online, dan pentingnya autentikasi dua langkah (2FA).', 'https://bif.telkomuniversity.ac.id/wp-content/uploads/2025/07/cyber-security.jpg', '#3b82f6', NULL, '2026-08-09 08:52:49'),
(10, 'Dasar Coding (Block-based ke Text-based)', 'SMP', 'Transisi dari Scratch-style ke pengenalan syntax dasar Python/JavaScript.', 'https://apps.enigmacamp.com/cms//uploads/Programmer_05d9633522.jpg', '#3b82f6', NULL, '2026-08-09 08:52:49'),
(11, 'Microsoft Office Menengah', 'SMP', 'Rumus Excel lanjutan (IF, VLOOKUP dasar), format dokumen profesional, dan animasi PowerPoint.', 'https://diengcyber.com/wp-content/uploads/2022/04/jenis-microsoft-office-dan-fungsi.jpg', '#3b82f6', NULL, '2026-08-09 08:52:49'),
(12, 'Literasi Data & Privasi', 'SMP', 'Memahami jejak digital, cara kerja cookies, dan hak atas data pribadi.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTvMcFh_nUd0Yjc_wkkUaDmOGro8BeZfoY27_53Ag3ihl4yIdCMOQpojEg&s=10', '#3b82f6', NULL, '2026-08-09 08:52:49'),
(13, 'Pengenalan Desain & Multimedia Dasar', 'SMP', 'Dasar editing gambar/video sederhana dan prinsip desain warna serta layout.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRKaWUFAA3kefqbpsRTSALqf090oAw3Kp6cHz8LYxU1bcMXjP3yDGHycouj&s=10', '#3b82f6', NULL, '2026-08-09 08:52:49'),
(14, 'Keamanan Siber Lanjutan', 'SMA', 'Konsep enkripsi dasar, social engineering, dan etika hacking (ethical hacking basics).', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRPqBiRp7Mzkgj3dnFue6NbWUKGKHBB4BHsLCpLhi9JWrpxz_jlnaIJx4w&s=10', '#8b5cf6', NULL, '2026-08-09 08:52:49'),
(15, 'Pemrograman Terapan', 'SMA', 'Membuat proyek nyata (mini web/app sederhana) menggunakan HTML/CSS/JS atau Python.', 'https://course-net.com/wp-content/uploads/2022/11/1-1024x538-1.jpeg', '#8b5cf6', NULL, '2026-08-09 08:52:49'),
(16, 'Microsoft Office Profesional', 'SMA', 'Analisis data dengan pivot table dan grafik di Excel, laporan akademik, dan presentasi formal.', 'https://diengcyber.com/wp-content/uploads/2022/04/jenis-microsoft-office-dan-fungsi.jpg', '#8b5cf6', NULL, '2026-08-09 08:52:49'),
(17, 'Literasi AI & Etika Teknologi', 'SMA', 'Cara kerja AI/machine learning sederhana, bias algoritma, dan penggunaan AI secara bertanggung jawab.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSunryFN-vloMBA07PzHirMEs8B7tQzH1MEzQDN046ek6xgbPBmotrx5Zc&s=10', '#8b5cf6', NULL, '2026-08-09 08:52:49'),
(18, 'Kesiapan Karier Digital', 'SMA', 'Dasar CV digital, portofolio online, dan pengenalan dunia kerja IT.', 'https://uici.ac.id/wp-content/uploads/2024/01/IMG_2130.jpeg', '#8b5cf6', NULL, '2026-08-09 08:52:49');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int NOT NULL,
  `course_id` int NOT NULL,
  `title` varchar(150) NOT NULL,
  `type` enum('quiz','challenge') NOT NULL,
  `min_score_passing` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `course_id`, `title`, `type`, `min_score_passing`) VALUES
(3, 4, 'Quiz: Mengenal Perangkat & Internet', 'quiz', 70),
(4, 5, 'Quiz: Keamanan Digital Dasar', 'quiz', 70),
(5, 6, 'Quiz: Logika Dasar (Computational Thinking)', 'quiz', 70),
(6, 7, 'Quiz: Microsoft Office Dasar', 'quiz', 70),
(7, 8, 'Quiz: Kreativitas Digital', 'quiz', 70),
(8, 9, 'Quiz: Keamanan Siber Menengah', 'quiz', 70),
(9, 10, 'Quiz: Dasar Coding (Block-based ke Text-based)', 'quiz', 70),
(10, 11, 'Quiz: Microsoft Office Menengah', 'quiz', 70),
(11, 12, 'Quiz: Literasi Data & Privasi', 'quiz', 70),
(12, 13, 'Quiz: Pengenalan Desain & Multimedia Dasar', 'quiz', 70),
(13, 14, 'Quiz: Keamanan Siber Lanjutan', 'quiz', 70),
(14, 15, 'Quiz: Pemrograman Terapan', 'quiz', 70),
(15, 16, 'Quiz: Microsoft Office Profesional', 'quiz', 70),
(16, 17, 'Quiz: Literasi AI & Etika Teknologi', 'quiz', 70),
(17, 18, 'Quiz: Kesiapan Karier Digital', 'quiz', 70);

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

CREATE TABLE `exam_questions` (
  `id` int NOT NULL,
  `exam_id` int NOT NULL,
  `question_type` enum('multiple_choice','code_submission') NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_answer` text NOT NULL,
  `points` int DEFAULT '10'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exam_questions`
--

INSERT INTO `exam_questions` (`id`, `exam_id`, `question_type`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `points`) VALUES
(2, 3, 'multiple_choice', 'Alat yang digunakan untuk mengetik pada komputer disebut?', 'Keyboard', 'Mouse', 'Speaker', 'Printer', 'a', 20),
(3, 3, 'multiple_choice', 'Internet menghubungkan komputer di seluruh dunia melalui?', 'Jaring laba-laba', 'Jaringan kabel dan sinyal', 'Surat pos', 'Telepon rumah saja', 'b', 20),
(4, 3, 'multiple_choice', 'Sebelum menggunakan komputer dalam waktu lama, sebaiknya kita?', 'Duduk sembarangan', 'Istirahatkan mata sesekali', 'Mematikan lampu ruangan', 'Tidak boleh berhenti sama sekali', 'b', 20),
(5, 3, 'multiple_choice', 'WiFi digunakan untuk?', 'Mencetak dokumen', 'Terhubung ke internet tanpa kabel', 'Mengisi daya baterai', 'Merekam suara', 'b', 20),
(6, 3, 'multiple_choice', 'Sebelum memakai gadget, sebaiknya kita?', 'Memakainya diam-diam', 'Minta izin orang tua atau guru', 'Membantingnya', 'Menyembunyikannya', 'b', 20),
(7, 4, 'multiple_choice', 'Password yang kuat sebaiknya berisi?', 'Nama sendiri saja', 'Angka 123456', 'Campuran huruf besar, kecil, dan angka', 'Tanggal lahir', 'c', 20),
(8, 4, 'multiple_choice', 'Jika orang asing di internet meminta alamat rumahmu, sebaiknya kamu?', 'Memberitahunya langsung', 'Tidak memberi tahu dan lapor orang tua', 'Mengirim foto rumah juga', 'Mengajaknya main ke rumah', 'b', 20),
(9, 4, 'multiple_choice', 'Data pribadi yang TIDAK boleh dibagikan ke orang asing online adalah?', 'Warna favorit', 'Nama, alamat rumah, dan nomor telepon', 'Judul film favorit', 'Nama hewan peliharaan di kartun', 'b', 20),
(10, 4, 'multiple_choice', 'Jika ada pesan aneh dari orang tak dikenal, sebaiknya kamu?', 'Membalas dengan marah', 'Mengklik semua link yang dikirim', 'Memberitahu orang tua atau guru', 'Diam saja tanpa lapor', 'c', 20),
(11, 4, 'multiple_choice', 'Password sebaiknya dirahasiakan kecuali kepada?', 'Teman sekelas', 'Orang tua atau wali', 'Orang asing di game online', 'Semua orang', 'b', 20),
(12, 5, 'multiple_choice', 'Sequencing artinya?', 'Menyusun langkah secara acak', 'Menyusun langkah secara berurutan', 'Menghapus langkah', 'Mengulang langkah tanpa aturan', 'b', 20),
(13, 5, 'multiple_choice', 'Urutan yang benar untuk menggosok gigi adalah?', 'Berkumur - ambil sikat - beri odol - sikat gigi', 'Ambil sikat - beri odol - sikat gigi - berkumur', 'Sikat gigi dulu baru ambil sikat', 'Berkumur saja tanpa menyikat', 'b', 20),
(14, 5, 'multiple_choice', 'Pola bentuk: Lingkaran, Segitiga, Lingkaran, Segitiga, ... bentuk berikutnya adalah?', 'Lingkaran', 'Segitiga', 'Kotak', 'Bintang', 'a', 20),
(15, 5, 'multiple_choice', 'Instruksi \"maju 2 langkah, belok kanan, maju 1 langkah\" adalah contoh dari?', 'Pola warna', 'Urutan instruksi', 'Password', 'Internet', 'b', 20),
(16, 5, 'multiple_choice', 'Computational thinking berguna untuk?', 'Bermain sembarangan', 'Memecahkan masalah secara terstruktur', 'Menghapus data', 'Membeli barang', 'b', 20),
(17, 6, 'multiple_choice', 'Aplikasi yang digunakan untuk menulis cerita adalah?', 'Excel', 'Word', 'PowerPoint', 'Kalkulator', 'b', 20),
(18, 6, 'multiple_choice', 'Kumpulan kotak yang tersusun baris dan kolom pada Excel disebut?', 'Slide', 'Sel (Cell)', 'Paragraf', 'Folder', 'b', 20),
(19, 6, 'multiple_choice', 'PowerPoint digunakan untuk membuat?', 'Tabel angka', 'Slide presentasi', 'Surat resmi', 'Musik', 'b', 20),
(20, 6, 'multiple_choice', 'Untuk membuat tulisan menjadi tebal di Word, kita gunakan tombol?', 'Italic', 'Bold', 'Underline', 'Delete', 'b', 20),
(21, 6, 'multiple_choice', 'Baris pertama pada tabel Excel biasanya digunakan untuk?', 'Judul kolom (header)', 'Warna latar', 'Musik', 'Gambar bergerak', 'a', 20),
(22, 7, 'multiple_choice', 'Cerita digital biasanya menggabungkan?', 'Hanya suara', 'Gambar dan teks', 'Hanya angka', 'Tidak ada apa-apa', 'b', 20),
(23, 7, 'multiple_choice', 'Tool menggambar dasar yang sering ada di aplikasi menggambar adalah?', 'Pensil dan warna', 'Kalkulator', 'Speaker', 'Printer 3D', 'a', 20),
(24, 7, 'multiple_choice', 'Setelah selesai membuat karya digital, sebaiknya kita?', 'Menghapusnya langsung', 'Menyimpannya', 'Membiarkannya tanpa disimpan', 'Membuang komputernya', 'b', 20),
(25, 7, 'multiple_choice', 'Kreativitas digital membantu kita untuk?', 'Mengekspresikan ide dengan teknologi', 'Merusak komputer', 'Menyembunyikan karya', 'Berhenti belajar', 'a', 20),
(26, 7, 'multiple_choice', 'Sebelum menghapus sebuah karya gambar, sebaiknya kita?', 'Yakin dulu supaya tidak menyesal', 'Langsung hapus tanpa berpikir', 'Bertanya ke teman saja', 'Tidak perlu berpikir', 'a', 20),
(27, 8, 'multiple_choice', 'Phishing adalah?', 'Teknik memancing korban memberi data pribadi lewat pesan palsu', 'Aplikasi edit foto', 'Jenis permainan online', 'Software antivirus', 'a', 20),
(28, 8, 'multiple_choice', 'Ciri umum pesan phishing adalah?', 'Bahasa formal tanpa kesalahan sama sekali', 'Mendesak dan berisi link mencurigakan', 'Selalu dari kontak yang sangat dikenal', 'Tidak pernah meminta data apapun', 'b', 20),
(29, 8, 'multiple_choice', '2FA (autentikasi dua langkah) berfungsi untuk?', 'Mempercepat koneksi internet', 'Menambah lapisan keamanan akun', 'Menghapus akun secara otomatis', 'Mengganti password tanpa kita tahu', 'b', 20),
(30, 8, 'multiple_choice', 'Jika menerima pesan menang giveaway yang tidak pernah diikuti, sebaiknya?', 'Langsung transfer biaya admin', 'Curiga dan tidak menanggapinya', 'Mengirim data KTP', 'Membagikannya ke semua teman', 'b', 20),
(31, 8, 'multiple_choice', 'Kode OTP pada 2FA sebaiknya?', 'Dibagikan ke siapa saja yang meminta', 'Dirahasiakan dan tidak dibagikan', 'Ditulis di media sosial', 'Dijual ke orang lain', 'b', 20),
(32, 9, 'multiple_choice', 'Scratch termasuk contoh pemrograman berbasis?', 'Blok (block-based)', 'Teks murni', 'Suara', 'Gambar 3D', 'a', 20),
(33, 9, 'multiple_choice', 'Variabel digunakan untuk?', 'Menyimpan nilai/data', 'Menghapus program', 'Mematikan komputer', 'Membuat suara', 'a', 20),
(34, 9, 'multiple_choice', 'Penulisan `nama = \"Andi\"` di Python artinya?', 'Menyimpan teks \"Andi\" ke variabel nama', 'Menghapus variabel nama', 'Membandingkan dua nilai', 'Membuat perulangan', 'a', 20),
(35, 9, 'multiple_choice', 'Struktur if digunakan untuk?', 'Mengulang kode selamanya', 'Membuat keputusan berdasarkan kondisi', 'Menyimpan gambar', 'Mematikan program', 'b', 20),
(36, 9, 'multiple_choice', 'Jika kondisi pada if bernilai benar (true), maka?', 'Blok kode di dalamnya akan dijalankan', 'Program otomatis berhenti', 'Semua variabel terhapus', 'Tidak terjadi apa-apa', 'a', 20),
(37, 10, 'multiple_choice', 'Rumus =IF(A1>=70,\"Lulus\",\"Tidak Lulus\") akan menghasilkan \"Lulus\" jika nilai di A1?', 'Kurang dari 70', 'Lebih dari atau sama dengan 70', 'Sama dengan 0', 'Kosong', 'b', 20),
(38, 10, 'multiple_choice', 'VLOOKUP digunakan untuk?', 'Mencari data berdasarkan nilai kunci di tabel lain', 'Menghapus data', 'Mewarnai sel', 'Membuat grafik saja', 'a', 20),
(39, 10, 'multiple_choice', 'Dokumen Word yang profesional biasanya memperhatikan?', 'Font yang acak-acakan', 'Margin dan heading yang rapi', 'Tanpa judul sama sekali', 'Semua huruf kapital', 'b', 20),
(40, 10, 'multiple_choice', 'Animasi pada PowerPoint sebaiknya digunakan untuk?', 'Membuat slide sulit dibaca', 'Mendukung penyampaian pesan tanpa berlebihan', 'Mengganti seluruh isi konten', 'Menutupi kesalahan konten', 'b', 20),
(41, 10, 'multiple_choice', 'Fungsi utama rumus IF di Excel adalah?', 'Membuat keputusan logis berdasarkan syarat', 'Menjumlahkan semua sel', 'Mengurutkan data', 'Mewarnai sel otomatis tanpa syarat', 'a', 20),
(42, 11, 'multiple_choice', 'Jejak digital adalah?', 'Sidik jari fisik', 'Rekam jejak aktivitas kita di internet', 'Nama aplikasi', 'Jenis virus komputer', 'b', 20),
(43, 11, 'multiple_choice', 'Cookies pada website berfungsi untuk?', 'Menghapus data pengguna', 'Mengingat preferensi/aktivitas pengunjung', 'Memperbaiki koneksi listrik', 'Mencetak dokumen', 'b', 20),
(44, 11, 'multiple_choice', 'Sebelum mendaftar aplikasi, sebaiknya kita membaca?', 'Kebijakan privasi', 'Resep makanan', 'Berita olahraga', 'Jadwal TV', 'a', 20),
(45, 11, 'multiple_choice', 'Salah satu hak atas data pribadi adalah?', 'Tidak boleh tahu data apa yang dikumpulkan', 'Berhak meminta data dihapus', 'Wajib membagikan semua data', 'Tidak punya hak apapun', 'b', 20),
(46, 11, 'multiple_choice', 'Postingan lama di media sosial yang sudah dihapus dari tampilan?', 'Selalu hilang total tanpa jejak', 'Bisa saja masih tersimpan di server/cache', 'Tidak pernah tersimpan sama sekali', 'Otomatis aman selamanya', 'b', 20),
(47, 12, 'multiple_choice', 'Memotong bagian gambar yang tidak diperlukan disebut?', 'Crop', 'Render', 'Export', 'Compile', 'a', 20),
(48, 12, 'multiple_choice', 'Menggabungkan beberapa klip video menjadi satu disebut?', 'Merging/menggabungkan klip', 'Menghapus video', 'Mengunci layar', 'Mematikan aplikasi', 'a', 20),
(49, 12, 'multiple_choice', 'Kombinasi warna yang serasi penting untuk?', 'Membuat desain enak dilihat', 'Mempercepat internet', 'Mengurangi ukuran file otomatis', 'Tidak berpengaruh apapun', 'a', 20),
(50, 12, 'multiple_choice', 'Layout dalam desain berkaitan dengan?', 'Tata letak elemen', 'Jenis huruf saja', 'Volume suara', 'Kecepatan animasi', 'a', 20),
(51, 12, 'multiple_choice', 'Resize pada gambar digunakan untuk?', 'Mengubah ukuran gambar', 'Mengubah warna gambar', 'Menghapus gambar', 'Menambah suara ke gambar', 'a', 20),
(52, 13, 'multiple_choice', 'Enkripsi berfungsi untuk?', 'Mengacak data agar tidak mudah dibaca pihak tak berwenang', 'Menghapus data secara permanen', 'Mempercepat koneksi internet', 'Menambah ukuran file saja', 'a', 20),
(53, 13, 'multiple_choice', 'Social engineering adalah teknik yang mengandalkan?', 'Kelemahan software semata', 'Manipulasi psikologis manusia', 'Kecepatan prosesor', 'Warna tampilan website', 'b', 20),
(54, 13, 'multiple_choice', 'Ethical hacker (white hat) bekerja dengan?', 'Izin resmi untuk menemukan celah keamanan', 'Tanpa izin dan merusak sistem', 'Menjual data curian', 'Menyebar virus sembarangan', 'a', 20),
(55, 13, 'multiple_choice', 'Bug bounty adalah program yang?', 'Memberi hadiah bagi penemu celah keamanan yang dilaporkan resmi', 'Menghukum semua peretas', 'Menjual bug ke pihak ketiga', 'Menghapus semua bug secara otomatis', 'a', 20),
(56, 13, 'multiple_choice', 'Contoh sederhana enkripsi klasik adalah?', 'Caesar Cipher', 'Microsoft Word', 'Browser Chrome', 'Speaker Bluetooth', 'a', 20),
(57, 14, 'multiple_choice', 'Langkah pertama sebelum membuat proyek nyata adalah?', 'Langsung menulis kode tanpa rencana', 'Merancang tujuan dan wireframe', 'Menghapus semua file', 'Mematikan komputer', 'b', 20),
(58, 14, 'multiple_choice', 'Tag HTML `<h1>` digunakan untuk?', 'Judul/heading utama', 'Gambar', 'Tabel', 'Video', 'a', 20),
(59, 14, 'multiple_choice', 'Proses menemukan dan memperbaiki kesalahan pada kode disebut?', 'Compiling', 'Debugging', 'Hosting', 'Formatting', 'b', 20),
(60, 14, 'multiple_choice', 'Python dan JavaScript adalah contoh?', 'Bahasa pemrograman', 'Jenis hardware', 'Aplikasi Office', 'Sistem operasi', 'a', 20),
(61, 14, 'multiple_choice', 'Setelah proyek selesai dibuat, sebaiknya kita?', 'Menguji dan menyempurnakannya', 'Langsung diabaikan', 'Menghapusnya', 'Tidak perlu dicoba lagi', 'a', 20),
(62, 15, 'multiple_choice', 'Pivot Table di Excel digunakan untuk?', 'Meringkas dan menganalisis data besar', 'Menghapus data', 'Mengedit gambar', 'Memutar video', 'a', 20),
(63, 15, 'multiple_choice', 'Grafik garis (line chart) paling cocok untuk menampilkan?', 'Perbandingan kategori tunggal', 'Tren data dari waktu ke waktu', 'Proporsi bagian dari keseluruhan', 'Data acak tanpa pola', 'b', 20),
(64, 15, 'multiple_choice', 'Laporan akademik/skripsi mini biasanya memiliki struktur seperti?', 'Pendahuluan, isi, kesimpulan', 'Hanya gambar tanpa teks', 'Tanpa judul dan tanpa struktur', 'Acak tanpa urutan', 'a', 20),
(65, 15, 'multiple_choice', 'Presentasi formal sebaiknya menggunakan?', 'Slide penuh teks kecil-kecil', 'Poin-poin ringkas dan visual pendukung', 'Tanpa slide sama sekali', 'Musik keras sepanjang presentasi', 'b', 20),
(66, 15, 'multiple_choice', 'Grafik lingkaran (pie chart) paling cocok untuk menunjukkan?', 'Proporsi/persentase bagian dari keseluruhan', 'Tren waktu', 'Data mentah tanpa ringkasan', 'Kecepatan internet', 'a', 20),
(67, 16, 'multiple_choice', 'Secara sederhana, machine learning bekerja dengan cara?', 'Belajar pola dari data untuk membuat prediksi', 'Menghafal semua jawaban secara manual', 'Tidak menggunakan data sama sekali', 'Hanya mengikuti perintah tetap tanpa belajar', 'a', 20),
(68, 16, 'multiple_choice', 'Bias algoritma dapat muncul karena?', 'Data pelatihan yang tidak representatif/seimbang', 'Komputer yang terlalu cepat', 'Warna antarmuka aplikasi', 'Ukuran layar pengguna', 'a', 20),
(69, 16, 'multiple_choice', 'Penggunaan AI yang bertanggung jawab mencakup?', 'Percaya semua hasil AI tanpa verifikasi', 'Memeriksa ulang informasi dan menghindari plagiarisme', 'Mengklaim hasil AI sebagai karya asli tanpa keterangan', 'Menggunakan AI untuk menyebarkan hoaks', 'b', 20),
(70, 16, 'multiple_choice', 'Dampak dari bias algoritma yang tidak ditangani adalah?', 'Hasil yang selalu adil untuk semua orang', 'Keputusan yang bisa merugikan kelompok tertentu secara tidak adil', 'Tidak ada dampak sama sekali', 'Meningkatkan privasi secara otomatis', 'b', 20),
(71, 16, 'multiple_choice', 'AI pada dasarnya belajar dari?', 'Data yang diberikan kepadanya', 'Imajinasi semata', 'Instruksi acak tanpa data', 'Tidak belajar apapun', 'a', 20),
(72, 17, 'multiple_choice', 'Komponen penting yang wajib ada dalam CV adalah?', 'Data diri, pendidikan, dan pengalaman/skill', 'Hanya foto tanpa keterangan', 'Daftar film favorit', 'Nomor rekening bank', 'a', 20),
(73, 17, 'multiple_choice', 'Portofolio online berguna untuk?', 'Menyembunyikan karya', 'Menampilkan hasil karya/proyek terbaik ke calon perekrut', 'Menghapus riwayat kerja', 'Menggantikan ijazah sepenuhnya', 'b', 20),
(74, 17, 'multiple_choice', 'Junior developer biasanya bertugas untuk?', 'Menulis dan membantu mengembangkan kode program', 'Hanya mengurus pemasaran', 'Mengelola gaji karyawan', 'Mendesain logo perusahaan saja', 'a', 20),
(75, 17, 'multiple_choice', 'Data analyst berfokus pada?', 'Menganalisis data untuk mendapatkan insight/wawasan', 'Memperbaiki hardware komputer', 'Mendesain pakaian', 'Mengelola dapur kantor', 'a', 20),
(76, 17, 'multiple_choice', 'Sebelum melamar kerja di bidang IT, sebaiknya kita mempersiapkan?', 'CV dan portofolio yang relevan', 'Tidak perlu persiapan apapun', 'Hanya modal keberanian tanpa skill', 'Menunggu tanpa usaha', 'a', 20);

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `exam_id` int NOT NULL,
  `score` int NOT NULL,
  `passed` tinyint(1) NOT NULL,
  `attempt_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exam_results`
--

INSERT INTO `exam_results` (`id`, `user_id`, `exam_id`, `score`, `passed`, `attempt_date`) VALUES
(3, 3, 6, 100, 1, '2026-08-10 05:27:11'),
(4, 3, 3, 100, 1, '2026-08-10 05:40:54');

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `upvotes` int DEFAULT '0',
  `downvotes` int DEFAULT '0',
  `is_solved` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_official` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `user_id`, `title`, `content`, `upvotes`, `downvotes`, `is_solved`, `created_at`, `is_official`) VALUES
(1, 1, 'errotrdsgfgd', 'hhdf', 0, 0, 0, '2026-08-07 11:56:20', 0),
(2, 3, 'HIDUP JOKOWI', 'WE WOK THE TOK', 0, 0, 0, '2026-08-09 08:58:44', 0);

-- --------------------------------------------------------

--
-- Table structure for table `forum_replies`
--

CREATE TABLE `forum_replies` (
  `id` int NOT NULL,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `upvotes` int DEFAULT '0',
  `is_accepted` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int NOT NULL,
  `course_id` int NOT NULL,
  `title` varchar(150) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `content_type` enum('video','text','mixed','interactive') NOT NULL DEFAULT 'mixed',
  `content_text` text,
  `video_url` varchar(255) DEFAULT NULL,
  `xp_reward` int DEFAULT '0',
  `order_index` int NOT NULL,
  `unlock_keyword` varchar(50) DEFAULT NULL,
  `attachment_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `course_id`, `title`, `thumbnail`, `content_type`, `content_text`, `video_url`, `xp_reward`, `order_index`, `unlock_keyword`, `attachment_file`) VALUES
(3, 4, 'Apa itu Komputer, Laptop, dan Tablet?', NULL, 'text', 'Komputer, laptop, dan tablet adalah alat elektronik yang bisa membantu kita belajar, bermain, dan berkreasi.\n\nKomputer biasanya diletakkan di meja dan terdiri dari beberapa bagian: layar (untuk melihat), keyboard (untuk mengetik), mouse (untuk menunjuk dan mengklik), dan CPU (otak dari komputer yang memproses semua perintah).\n\nLaptop adalah komputer yang lebih kecil dan bisa dibawa ke mana-mana karena semua bagiannya sudah menyatu jadi satu.\n\nTablet lebih tipis lagi dan biasanya dioperasikan dengan cara disentuh langsung di layarnya (touchscreen), tanpa keyboard atau mouse.\n\nKetiganya punya fungsi yang mirip yaitu membantu kita mengerjakan tugas, mencari informasi, dan berkomunikasi, hanya bentuk dan cara pakainya yang berbeda.', NULL, 30, 1, NULL, NULL),
(4, 4, 'Bagaimana Internet Bekerja?', NULL, 'text', 'Internet adalah jaringan raksasa yang menghubungkan jutaan komputer di seluruh dunia, sehingga kita bisa saling berbagi informasi dengan cepat.\n\nBayangkan internet seperti perpustakaan yang sangat besar. Setiap halaman website adalah satu buku, dan kita bisa membukanya kapan saja lewat aplikasi bernama browser (seperti Chrome atau Firefox).\n\nUntuk terhubung ke internet, perangkat kita butuh sinyal. Sinyal ini bisa datang lewat kabel, atau lewat WiFi (sinyal tanpa kabel yang biasa ada di rumah atau sekolah).\n\nSetiap kali kita membuka website, perangkat kita \"bertanya\" ke internet dan internet \"menjawab\" dengan mengirimkan halaman yang kita minta, semuanya terjadi hanya dalam hitungan detik!', NULL, 30, 2, NULL, NULL),
(5, 4, 'Menggunakan Perangkat dengan Baik', NULL, 'text', 'Menggunakan komputer, laptop, atau tablet itu menyenangkan, tapi kita juga perlu menggunakannya dengan cara yang sehat dan sopan.\n\nDuduk dengan posisi tegak dan jangan terlalu dekat dengan layar supaya mata dan punggung kita tetap sehat. Istirahatkan mata sesekali dengan melihat ke tempat yang jauh setiap 20-30 menit.\n\nSelalu minta izin orang tua atau guru sebelum menggunakan perangkat, dan gunakan sesuai waktu yang sudah disepakati bersama.\n\nJangan lupa untuk menjaga perangkat dengan baik, misalnya tidak makan atau minum di dekatnya, dan meletakkannya di tempat yang aman setelah selesai digunakan.', NULL, 30, 3, NULL, NULL),
(6, 5, 'Password yang Kuat', NULL, 'text', 'Password adalah kata sandi rahasia yang kita gunakan untuk masuk ke akun atau perangkat, supaya orang lain tidak bisa sembarangan membukanya.\n\nPassword yang kuat sebaiknya menggabungkan huruf besar, huruf kecil, dan angka, misalnya \"Kucing77Lucu\" lebih aman dibandingkan \"123456\" atau tanggal lahir kita.\n\nJangan pernah menggunakan nama sendiri atau tanggal lahir sebagai password karena mudah ditebak orang lain.\n\nPassword harus dirahasiakan dan hanya boleh diketahui oleh kita sendiri dan orang tua atau wali, jangan pernah memberitahukannya ke teman atau orang asing.', NULL, 30, 1, NULL, NULL),
(7, 5, 'Jangan Bagikan Data Pribadi', NULL, 'text', 'Data pribadi adalah informasi tentang diri kita, seperti nama lengkap, alamat rumah, nomor telepon, dan nama sekolah.\n\nSaat bermain game online atau chatting, kadang ada orang asing yang bertanya-tanya tentang data pribadi kita. Ini berbahaya karena data tersebut bisa disalahgunakan.\n\nJangan pernah memberikan alamat rumah, nomor telepon, atau informasi keluarga ke orang yang tidak kita kenal di internet, sekalipun mereka terlihat baik atau berjanji memberi hadiah.\n\nJika ragu, selalu tanyakan dulu ke orang tua atau guru sebelum menjawab pertanyaan semacam itu.', NULL, 30, 2, NULL, NULL),
(8, 5, 'Kalau Ada yang Mencurigakan', NULL, 'text', 'Kadang saat online kita bisa bertemu hal-hal yang aneh atau membuat tidak nyaman, misalnya pesan dari orang tak dikenal atau tautan (link) yang mencurigakan.\n\nJika ini terjadi, jangan klik sembarangan link yang dikirim orang asing, dan jangan membalas pesan dari orang yang tidak kita kenal.\n\nSegera beritahu orang tua atau guru jika kamu menemukan sesuatu yang mencurigakan atau membuatmu tidak nyaman saat menggunakan internet.\n\nMengadu dan bercerita ke orang dewasa yang kita percaya bukanlah hal yang memalukan, justru itu adalah langkah yang bijak dan berani.', NULL, 30, 3, NULL, NULL),
(9, 6, 'Apa itu Urutan (Sequencing)?', NULL, 'text', 'Sequencing artinya menyusun langkah-langkah secara berurutan agar suatu kegiatan bisa berhasil dilakukan dengan benar.\n\nContohnya, saat bangun tidur kita biasanya melakukan langkah berurutan: bangun, merapikan tempat tidur, mandi, lalu sarapan. Jika urutannya diacak, misalnya sarapan dulu baru bangun tidur, tentu jadi aneh dan tidak masuk akal!\n\nDi dunia komputer, urutan langkah ini disebut \"instruksi\", dan komputer akan mengikuti instruksi tersebut persis sesuai urutannya, satu per satu, tanpa melompat-lompat.\n\nBelajar menyusun urutan yang benar adalah dasar penting sebelum kita belajar membuat program komputer.', NULL, 30, 1, NULL, NULL),
(10, 6, 'Mengenal Pola', NULL, 'text', 'Pola adalah sesuatu yang berulang dengan aturan tertentu, misalnya warna atau bentuk yang muncul berulang-ulang.\n\nContoh pola sederhana: Lingkaran, Segitiga, Lingkaran, Segitiga. Jika kita perhatikan, bentuknya berganti-ganti secara teratur mengikuti aturan tertentu.\n\nMengenali pola sangat berguna untuk melatih otak kita berpikir logis, dan juga menjadi dasar penting dalam ilmu komputer, karena komputer sering diminta mengenali dan meneruskan pola dari data yang diberikan.\n\nCoba perhatikan pola di sekitarmu, misalnya pola pada ubin lantai, baju bercorak, atau susunan warna pada mainan!', NULL, 30, 2, NULL, NULL),
(11, 6, 'Latihan Puzzle Sederhana', NULL, 'text', 'Sekarang saatnya berlatih! Bayangkan ada sebuah karakter kecil yang harus berjalan menuju rumahnya dengan mengikuti instruksi yang kita berikan.\n\nContoh instruksi: \"maju 2 langkah, belok kanan, maju 1 langkah\". Karakter tersebut akan mengikuti instruksi itu persis satu per satu sampai selesai.\n\nJika instruksinya salah urutan atau ada langkah yang terlewat, karakter bisa saja tersesat dan tidak sampai ke tujuan. Ini mengajarkan kita untuk berpikir teliti dan runtut.\n\nCoba buat instruksi sendiri untuk membantu karaktermu mencapai tujuan, lalu periksa apakah urutannya sudah benar!', NULL, 30, 3, NULL, NULL),
(12, 7, 'Mengenal Microsoft Word', NULL, 'text', 'Microsoft Word adalah aplikasi yang digunakan untuk menulis, seperti membuat cerita pendek, surat, atau catatan.\n\nDi Word, kita bisa mengetik teks lalu mengatur tampilannya, misalnya membuat tulisan menjadi tebal (Bold), miring (Italic), atau bergaris bawah (Underline) supaya lebih menarik.\n\nKita juga bisa mengganti jenis dan ukuran huruf (font) sesuai keinginan, serta menambahkan gambar sederhana ke dalam dokumen.\n\nCoba buat sebuah cerita pendek tentang hewan kesukaanmu menggunakan Word, lalu buat judulnya tebal dan besar!', NULL, 30, 1, NULL, NULL),
(13, 7, 'Mengenal Microsoft Excel', NULL, 'text', 'Microsoft Excel adalah aplikasi untuk membuat tabel dan mengolah angka.\n\nDi Excel, area kerja terdiri dari kotak-kotak kecil yang disebut sel (cell), yang tersusun dalam baris dan kolom. Baris pertama biasanya digunakan untuk judul kolom (header), misalnya \"Nama\" dan \"Nilai\".\n\nKita bisa memasukkan angka atau teks ke dalam sel-sel tersebut untuk membuat tabel sederhana, misalnya daftar nilai ujian teman sekelas.\n\nExcel sangat berguna untuk merapikan data supaya lebih mudah dibaca dan dipahami.', NULL, 30, 2, NULL, NULL),
(14, 7, 'Mengenal Microsoft PowerPoint', NULL, 'text', 'Microsoft PowerPoint adalah aplikasi untuk membuat slide presentasi.\n\nSetiap halaman di PowerPoint disebut \"slide\", dan kita bisa mengisinya dengan judul, sedikit teks, serta gambar pendukung supaya lebih menarik.\n\nPresentasi yang baik biasanya tidak berisi terlalu banyak tulisan dalam satu slide, cukup poin-poin penting saja, ditambah gambar yang membantu menjelaskan.\n\nCoba buat 3 slide sederhana tentang hobimu: satu slide judul, satu slide gambar, dan satu slide penutup!', NULL, 30, 3, NULL, NULL),
(15, 8, 'Menulis Cerita Digital Sederhana', NULL, 'text', 'Cerita digital adalah cerita yang dibuat menggunakan komputer, biasanya menggabungkan gambar dan teks agar lebih hidup.\n\nUntuk membuat cerita digital, kita bisa mulai dengan menentukan tokoh utama, tempat kejadian, dan apa yang terjadi dalam cerita tersebut, sama seperti membuat cerita biasa.\n\nSetelah itu, kita bisa menambahkan gambar pendukung di setiap bagian cerita supaya pembaca lebih mudah membayangkan jalan ceritanya.\n\nCoba buat cerita digital pendek tentang petualangan seekor kucing, lengkap dengan 2-3 gambar pendukung!', NULL, 30, 1, NULL, NULL),
(16, 8, 'Menggambar dengan Tools Dasar', NULL, 'text', 'Ada banyak aplikasi menggambar sederhana berbasis web yang bisa kita gunakan untuk berkreasi, dengan tools dasar seperti pensil, kuas warna, dan penghapus.\n\nTool pensil digunakan untuk membuat garis atau bentuk, tool warna untuk mengisi warna pada gambar, dan tool hapus untuk menghapus bagian yang tidak diinginkan.\n\nKita bisa bereksperimen mengombinasikan berbagai warna dan bentuk untuk menciptakan gambar yang unik dan menarik.\n\nCoba gambar pemandangan favoritmu menggunakan minimal 3 warna berbeda!', NULL, 30, 2, NULL, NULL),
(17, 8, 'Menampilkan Karya', NULL, 'text', 'Setelah selesai membuat karya digital, baik itu cerita atau gambar, langkah penting berikutnya adalah menyimpannya supaya tidak hilang.\n\nSebelum menyimpan, pastikan kita sudah yakin dengan hasil karya kita, karena setelah disimpan, karya tersebut bisa kita lihat dan tunjukkan kapan saja.\n\nKita bisa menunjukkan karya digital kita kepada teman, guru, atau keluarga untuk mendapatkan pujian dan masukan yang membangun.\n\nBerbagi karya adalah cara yang menyenangkan untuk merayakan kreativitas kita dan belajar dari komentar orang lain!', NULL, 30, 3, NULL, NULL),
(18, 9, 'Mengenali Phishing', NULL, 'text', 'Phishing adalah teknik penipuan di mana pelaku berpura-pura menjadi pihak yang bisa dipercaya (seperti bank atau layanan resmi) untuk memancing korban memberikan data pribadi seperti password atau nomor kartu.\n\nCiri-ciri umum pesan phishing: menggunakan bahasa yang mendesak (\"akun Anda akan diblokir dalam 24 jam!\"), berisi tautan (link) yang mencurigakan atau alamat website yang mirip tapi tidak persis sama dengan aslinya, serta sering mengandung kesalahan penulisan.\n\nPhishing bisa datang lewat email, SMS, WhatsApp, atau bahkan media sosial.\n\nJika kamu menerima pesan yang mencurigakan seperti ini, jangan klik link-nya dan jangan berikan data apa pun. Verifikasi langsung ke pihak resmi melalui saluran komunikasi yang terpercaya.', NULL, 40, 1, NULL, NULL),
(19, 9, 'Penipuan Online Umum', NULL, 'text', 'Selain phishing, ada berbagai modus penipuan online lain yang perlu diwaspadai, seperti giveaway palsu (menang hadiah padahal tidak pernah ikut kontes), akun media sosial palsu yang meniru (clone) akun teman atau tokoh terkenal, dan permintaan transfer uang mendadak.\n\nModus umum: pelaku menghubungi korban mengaku sebagai teman/keluarga yang sedang butuh uang mendesak, atau menjanjikan hadiah besar dengan syarat membayar \"biaya admin\" terlebih dahulu.\n\nAturan emasnya: jangan pernah transfer uang atau membagikan data finansial ke orang yang identitasnya belum bisa dipastikan, walaupun terlihat meyakinkan.\n\nSelalu konfirmasi langsung (telepon atau bertemu) sebelum mempercayai permintaan uang secara online.', NULL, 40, 2, NULL, NULL),
(20, 9, 'Autentikasi Dua Langkah (2FA)', NULL, 'text', '2FA (Two-Factor Authentication atau autentikasi dua langkah) adalah lapisan keamanan tambahan selain password untuk melindungi akun kita.\n\nCara kerjanya: setelah memasukkan password, sistem akan meminta kode verifikasi kedua, biasanya berupa kode OTP (One-Time Password) yang dikirim lewat SMS, email, atau aplikasi authenticator.\n\nDengan 2FA, meskipun password kita bocor, akun tetap aman karena pelaku tidak punya akses ke kode OTP yang hanya dikirim ke perangkat kita.\n\nKode OTP bersifat sangat rahasia dan tidak boleh dibagikan ke siapa pun, termasuk pihak yang mengaku dari \"customer service\" resmi, karena layanan resmi tidak pernah meminta kode OTP pelanggannya.', NULL, 40, 3, NULL, NULL),
(21, 10, 'Dari Blok ke Teks', '', 'text', '```\r\nprint(\"hello word\")\r\n```\r\n\r\n\r\nSelama ini kamu mungkin sudah mengenal pemrograman berbasis blok seperti Scratch, di mana kita menyusun kode dengan cara menyeret dan menempelkan (drag and drop) balok-balok perintah.\r\n\r\nPemrograman berbasis teks (text-based) seperti Python atau JavaScript bekerja dengan cara yang mirip secara konsep, hanya saja instruksinya ditulis langsung sebagai teks/kode, bukan disusun sebagai balok visual.\r\n\r\nBelajar coding berbasis teks penting karena hampir semua aplikasi, website, dan software profesional dibangun menggunakan bahasa pemrograman berbasis teks.\r\n\r\nJangan khawatir, konsep dasar yang sudah kamu pelajari di Scratch (urutan, perulangan, kondisi) tetap berlaku sama persis di bahasa berbasis teks, kita hanya perlu belajar cara menuliskannya.', '', 40, 1, NULL, NULL),
(22, 10, 'Variabel dan Nilai', NULL, 'text', 'Variabel adalah tempat untuk menyimpan data atau nilai yang bisa kita gunakan dan ubah dalam program.\n\nContoh di Python: `nama = \"Andi\"` artinya kita membuat variabel bernama `nama` dan menyimpan teks \"Andi\" ke dalamnya. Kita juga bisa menyimpan angka, misalnya `umur = 13`.\n\nSetelah dibuat, variabel bisa kita gunakan berkali-kali dalam program, misalnya untuk ditampilkan atau dihitung. Nilainya juga bisa diubah kapan saja, misalnya `umur = 14` untuk memperbarui nilai umur.\n\nMenggunakan nama variabel yang jelas (seperti `nama` atau `skor`) akan membuat kode kita lebih mudah dibaca dan dipahami, baik oleh diri sendiri maupun orang lain.', NULL, 40, 2, NULL, NULL),
(23, 10, 'Percabangan Sederhana (if)', NULL, 'text', 'Percabangan (kondisi/if) digunakan untuk membuat program mengambil keputusan berdasarkan suatu syarat, mirip seperti kita berpikir \"jika hujan, bawa payung\".\n\nContoh sederhana: `jika nilai >= 70, maka tampilkan \"Lulus\"`. Program akan memeriksa apakah kondisi tersebut benar (true) atau salah (false), lalu menjalankan aksi yang sesuai.\n\nJika kondisinya benar, kode di dalam blok if akan dijalankan. Jika salah, program bisa melompat ke blok lain yang disebut `else` (jika tidak).\n\nPercabangan adalah salah satu konsep paling penting dalam pemrograman, karena hampir semua program perlu mengambil keputusan berdasarkan kondisi tertentu.', NULL, 40, 3, NULL, NULL),
(24, 11, 'Rumus Excel Lanjutan: IF', NULL, 'text', 'Rumus IF di Excel digunakan untuk membuat keputusan otomatis berdasarkan suatu syarat, mirip dengan konsep percabangan pada pemrograman.\n\nContoh: `=IF(A1>=70,\"Lulus\",\"Tidak Lulus\")` artinya jika nilai pada sel A1 lebih besar atau sama dengan 70, Excel akan menampilkan \"Lulus\", jika tidak maka akan menampilkan \"Tidak Lulus\".\n\nStruktur rumus IF adalah: `=IF(syarat, hasil_jika_benar, hasil_jika_salah)`. Kita bisa menggunakannya untuk berbagai keperluan, seperti menentukan status kelulusan, kategori nilai, atau diskon harga.\n\nRumus ini sangat membantu ketika kita punya banyak data dan perlu memproses keputusan yang sama secara otomatis untuk semuanya.', NULL, 40, 1, NULL, NULL),
(25, 11, 'Mengenal VLOOKUP Dasar', NULL, 'text', 'VLOOKUP adalah rumus Excel yang digunakan untuk mencari data pada tabel lain berdasarkan suatu nilai kunci (pencocokan).\n\nContoh penggunaan: jika kita punya tabel daftar nama siswa dengan nomor induknya di satu sheet, kita bisa menggunakan VLOOKUP untuk mencari nama siswa berdasarkan nomor induknya di sheet lain, tanpa perlu mencari manual satu per satu.\n\nSecara sederhana, VLOOKUP bekerja dengan cara: mencari suatu nilai di kolom pertama tabel, lalu mengembalikan nilai dari kolom lain pada baris yang sama.\n\nRumus ini sangat berguna untuk mengelola data dalam jumlah besar, seperti daftar nilai, inventaris barang, atau data karyawan.', NULL, 40, 2, NULL, NULL),
(26, 11, 'Format Dokumen Profesional & Animasi Slide', NULL, 'text', 'Dokumen Word yang profesional memperhatikan hal-hal seperti margin yang rapi, penggunaan heading (judul dan subjudul) yang konsisten, serta jenis dan ukuran huruf yang mudah dibaca.\n\nGunakan heading (Heading 1, Heading 2, dst) untuk menandai judul dan subjudul, ini juga membantu membuat daftar isi otomatis di Word.\n\nDi PowerPoint, animasi dan transisi bisa membuat presentasi lebih menarik, tapi sebaiknya digunakan secukupnya untuk mendukung penyampaian pesan, bukan untuk membuat slide berlebihan atau mengganggu perhatian audiens.\n\nPrinsip utamanya: desain yang baik adalah desain yang membantu audiens memahami isi, bukan desain yang \"ramai\" tapi membingungkan.', NULL, 40, 3, NULL, NULL),
(27, 12, 'Apa itu Jejak Digital?', NULL, 'text', 'Jejak digital (digital footprint) adalah semua rekam jejak aktivitas kita di internet, seperti postingan media sosial, komentar, \"like\", foto yang diunggah, hingga riwayat pencarian.\n\nJejak digital ini bisa bersifat aktif (yang sengaja kita buat, seperti postingan) maupun pasif (yang terekam otomatis, seperti alamat IP atau lokasi saat mengakses suatu website).\n\nPenting untuk diingat bahwa jejak digital bisa bertahan lama, bahkan setelah kita menghapus suatu postingan, kemungkinan masih ada salinannya yang tersimpan di tempat lain (seperti screenshot orang lain atau cache server).\n\nOleh karena itu, sebelum memposting sesuatu di internet, pikirkan dulu apakah kita nyaman jika hal tersebut dilihat banyak orang, bahkan di masa depan.', NULL, 40, 1, NULL, NULL),
(28, 12, 'Cara Kerja Cookies', NULL, 'text', 'Cookies adalah file kecil yang disimpan oleh website di perangkat kita untuk mengingat informasi tertentu, seperti preferensi bahasa, item di keranjang belanja, atau status login.\n\nBerkat cookies, saat kita membuka kembali sebuah website, kita tidak perlu login ulang atau mengatur preferensi dari awal.\n\nNamun, cookies juga bisa digunakan untuk \"tracking\" atau melacak aktivitas kita di berbagai website, misalnya untuk menampilkan iklan yang sesuai dengan hal yang pernah kita cari.\n\nKita bisa mengatur atau menghapus cookies lewat pengaturan browser, dan banyak website kini wajib menampilkan pemberitahuan penggunaan cookies sesuai aturan privasi data.', NULL, 40, 2, NULL, NULL),
(29, 12, 'Hak atas Data Pribadi', NULL, 'text', 'Sebagai pengguna internet, kita memiliki hak atas data pribadi kita, termasuk hak untuk mengetahui data apa saja yang dikumpulkan oleh sebuah aplikasi atau website.\n\nBeberapa hak penting: hak untuk mengakses data kita, hak untuk meminta data tersebut diperbaiki jika salah, dan hak untuk meminta data dihapus (right to be forgotten).\n\nSebelum mendaftar ke sebuah aplikasi atau layanan, sebaiknya kita membaca kebijakan privasi (privacy policy) untuk mengetahui bagaimana data kita akan digunakan dan dilindungi.\n\nMemahami hak-hak ini penting agar kita bisa lebih bijak dan kritis dalam membagikan data pribadi di dunia digital.', NULL, 40, 3, NULL, NULL),
(30, 13, 'Dasar Editing Gambar', NULL, 'text', 'Editing gambar sederhana melibatkan beberapa teknik dasar seperti crop (memotong bagian gambar yang tidak diperlukan), resize (mengubah ukuran gambar), dan menerapkan filter untuk mengubah tampilan warna atau suasana gambar.\n\nCrop berguna untuk memfokuskan perhatian pada objek utama dan membuang bagian gambar yang tidak penting.\n\nResize penting agar ukuran gambar sesuai kebutuhan, misalnya gambar untuk media sosial biasanya berukuran berbeda dengan gambar untuk dicetak.\n\nFilter bisa digunakan untuk memberi efek tertentu, seperti membuat gambar terlihat lebih cerah, hangat, atau bergaya hitam-putih (vintage).', NULL, 40, 1, NULL, NULL),
(31, 13, 'Dasar Editing Video Sederhana', NULL, 'text', 'Editing video dasar melibatkan proses memotong klip (trimming) untuk membuang bagian yang tidak diperlukan, menggabungkan beberapa klip video menjadi satu video utuh, dan menambahkan teks atau subtitle.\n\nTrimming membantu kita fokus hanya menampilkan bagian video yang penting dan menarik, membuang bagian yang membosankan atau tidak relevan.\n\nMenggabungkan klip (merging) memungkinkan kita menyusun beberapa potongan video menjadi satu cerita yang utuh dan mengalir.\n\nMenambahkan teks bisa membantu penonton memahami konteks video, misalnya judul, keterangan lokasi, atau dialog dalam bentuk subtitle.', NULL, 40, 2, NULL, NULL),
(32, 13, 'Prinsip Desain: Warna dan Layout', NULL, 'text', 'Warna dan layout (tata letak) adalah dua elemen penting dalam desain yang memengaruhi bagaimana sebuah karya terlihat dan dirasakan.\n\nKombinasi warna yang serasi membuat desain terlihat lebih enak dipandang. Sebagai contoh, warna-warna yang berdekatan pada roda warna (seperti biru dan hijau) cenderung terlihat harmonis, sedangkan warna yang berlawanan (seperti merah dan hijau) bisa menciptakan kontras yang mencolok.\n\nLayout berkaitan dengan bagaimana elemen-elemen (teks, gambar, tombol) disusun dalam suatu halaman atau desain, supaya terlihat rapi dan mudah dipahami alur bacanya.\n\nDesain yang baik biasanya memiliki keseimbangan antara ruang kosong (whitespace) dan elemen visual, sehingga tidak terlihat terlalu penuh atau berantakan.', NULL, 40, 3, NULL, NULL),
(33, 14, 'Konsep Enkripsi Dasar', NULL, 'text', 'Enkripsi adalah proses mengubah data asli (plaintext) menjadi bentuk acak yang tidak bisa dibaca (ciphertext), sehingga hanya pihak yang memiliki kunci yang tepat yang bisa membacanya kembali (dekripsi).\n\nContoh enkripsi klasik yang sederhana adalah Caesar Cipher, yaitu menggeser setiap huruf dalam pesan sejumlah posisi tertentu di alfabet. Misalnya dengan geseran 3, huruf \"A\" menjadi \"D\", \"B\" menjadi \"E\", dan seterusnya.\n\nDi dunia nyata, enkripsi modern jauh lebih kompleks dan digunakan di mana-mana, misalnya saat kita mengakses website dengan \"https\" (bukan \"http\"), data yang kita kirim dan terima sudah dienkripsi agar tidak bisa disadap orang lain.\n\nEnkripsi penting untuk melindungi kerahasiaan data seperti password, informasi kartu kredit, dan pesan pribadi saat dikirim melalui internet.', NULL, 50, 1, NULL, NULL),
(34, 14, 'Social Engineering', NULL, 'text', 'Social engineering adalah teknik manipulasi psikologis untuk membuat seseorang secara sukarela memberikan informasi rahasia atau akses ke sistem, tanpa perlu meretas sistem secara teknis.\n\nContoh teknik social engineering: pretexting (pelaku membuat skenario palsu, misalnya berpura-pura menjadi teknisi IT untuk meminta password), baiting (memberi \"umpan\" menarik seperti flashdisk gratis yang sebenarnya berisi malware), dan pura-pura menjadi atasan yang meminta data mendesak.\n\nSocial engineering berbahaya karena mengeksploitasi kepercayaan dan kebiasaan manusia, bukan celah teknis pada software, sehingga sulit dideteksi oleh sistem keamanan biasa.\n\nCara terbaik melawannya adalah selalu memverifikasi identitas orang yang meminta informasi sensitif, dan tidak mudah percaya pada permintaan yang terkesan mendesak atau tidak biasa.', NULL, 50, 2, NULL, NULL),
(35, 14, 'Etika Hacking (Ethical Hacking Basics)', NULL, 'text', 'Hacking secara umum berarti mencoba menemukan dan mengeksploitasi celah keamanan pada suatu sistem. Berdasarkan niatnya, hacker sering dibagi menjadi \"white hat\" (etis, bertujuan baik) dan \"black hat\" (tidak etis, merugikan pihak lain).\n\nEthical hacker (white hat) bekerja dengan izin resmi dari pemilik sistem untuk menemukan celah keamanan, lalu melaporkannya agar bisa diperbaiki, bukan untuk mencuri data atau merusak sistem.\n\nSalah satu bentuk kerja sama ini adalah program \"bug bounty\", di mana perusahaan memberikan hadiah kepada siapa saja yang berhasil menemukan dan melaporkan celah keamanan secara resmi dan bertanggung jawab.\n\nMenjadi ethical hacker membutuhkan pemahaman teknis yang kuat, sekaligus kode etik yang tegas: selalu meminta izin, tidak merugikan pihak lain, dan melaporkan temuan secara bertanggung jawab.', NULL, 50, 3, NULL, NULL),
(36, 15, 'Merancang Proyek Mini', NULL, 'text', 'Sebelum mulai menulis kode, langkah penting yang sering dilewatkan pemula adalah merancang proyek terlebih dahulu.\n\nMulailah dengan menentukan tujuan proyek: apa masalah yang ingin diselesaikan, dan siapa penggunanya? Misalnya, membuat aplikasi pencatat tugas sekolah sederhana.\n\nSetelah tujuan jelas, buat wireframe dasar, yaitu sketsa kasar tampilan aplikasi/website (bisa digambar di kertas atau aplikasi sederhana), untuk menentukan tata letak tombol, teks, dan fitur utama.\n\nMerancang terlebih dahulu membantu kita menghindari perubahan besar di tengah proses pengerjaan, dan membuat proses coding menjadi lebih terarah dan efisien.', NULL, 50, 1, NULL, NULL),
(37, 15, 'Membangun dengan HTML/CSS/JS atau Python', NULL, 'text', 'Setelah rancangan siap, saatnya membangun proyek. Untuk proyek berbasis web, kita menggunakan tiga bahasa utama: HTML untuk struktur konten (misalnya `<h1>` untuk judul, `<p>` untuk paragraf), CSS untuk mengatur tampilan (warna, ukuran, tata letak), dan JavaScript untuk membuat halaman menjadi interaktif (misalnya merespon tombol yang diklik).\n\nSebagai alternatif, kita juga bisa membangun proyek menggunakan Python, yang sering digunakan untuk membuat program sederhana seperti kalkulator, permainan tebak angka, atau pengolah data.\n\nMulailah dari fitur paling sederhana dan dasar terlebih dahulu, baru tambahkan fitur lain secara bertahap. Ini disebut pendekatan \"iteratif\", membangun sedikit demi sedikit sambil terus menguji hasilnya.\n\nJangan takut mencoba dan membuat kesalahan, karena itu adalah bagian normal dari proses belajar pemrograman.', NULL, 50, 2, NULL, NULL),
(38, 15, 'Menguji dan Menyempurnakan Proyek', NULL, 'text', 'Setelah proyek selesai dibangun, langkah penting selanjutnya adalah menguji dan menyempurnakannya (debugging dan iterasi).\n\nDebugging adalah proses menemukan dan memperbaiki kesalahan (bug) dalam kode, misalnya tombol yang tidak berfungsi atau tampilan yang berantakan. Kesalahan ini wajar terjadi, dan menemukannya adalah bagian penting dari belajar pemrograman.\n\nSetelah bug diperbaiki, mintalah feedback dari teman atau guru untuk mengetahui bagian mana yang masih bisa ditingkatkan, baik dari segi fungsi maupun tampilan.\n\nProses ini biasanya dilakukan berulang kali (iterasi): uji, temukan masalah, perbaiki, uji lagi, sampai hasilnya memuaskan dan sesuai dengan rancangan awal.', NULL, 50, 3, NULL, NULL),
(39, 16, 'Analisis Data dengan Pivot Table', NULL, 'text', 'Pivot Table adalah fitur Excel yang sangat berguna untuk meringkas dan menganalisis data dalam jumlah besar tanpa perlu menulis rumus yang rumit.\n\nDengan Pivot Table, kita bisa dengan cepat menghitung total, rata-rata, atau jumlah data berdasarkan kategori tertentu, misalnya total penjualan per bulan atau rata-rata nilai per kelas, hanya dengan menyeret (drag) kolom yang diinginkan.\n\nPivot Table sangat membantu ketika kita memiliki ratusan atau ribuan baris data mentah, dan ingin melihat gambaran besar (insight) dari data tersebut secara cepat dan interaktif.\n\nKemampuan ini sangat dibutuhkan dalam dunia kerja, terutama untuk peran yang berhubungan dengan analisis data dan pengambilan keputusan berbasis data.', NULL, 50, 1, NULL, NULL),
(40, 16, 'Grafik untuk Analisis Data', NULL, 'text', 'Memilih jenis grafik yang tepat sangat penting agar data yang kita sampaikan mudah dipahami oleh orang lain.\n\nGrafik garis (line chart) paling cocok digunakan untuk menampilkan tren data dari waktu ke waktu, misalnya perubahan suhu harian atau pertumbuhan pengguna per bulan.\n\nGrafik batang (bar chart) cocok untuk membandingkan nilai antar kategori, misalnya membandingkan nilai rata-rata antar kelas.\n\nGrafik lingkaran (pie chart) paling tepat digunakan untuk menunjukkan proporsi atau persentase bagian dari keseluruhan data, misalnya persentase jenis kendaraan yang digunakan siswa ke sekolah.', NULL, 50, 2, NULL, NULL),
(41, 16, 'Laporan Akademik & Presentasi Formal', NULL, 'text', 'Laporan akademik atau skripsi mini biasanya memiliki struktur yang baku, seperti: pendahuluan (latar belakang dan tujuan), isi/pembahasan, dan kesimpulan. Struktur ini membantu pembaca memahami alur berpikir penulisnya dengan jelas.\n\nDi Word, gunakan heading yang konsisten, penomoran halaman, dan daftar isi untuk membuat laporan terlihat rapi dan profesional.\n\nUntuk presentasi formal di PowerPoint, sebaiknya gunakan poin-poin ringkas (bukan paragraf panjang) yang didukung visual seperti grafik atau gambar, karena audiens akan lebih fokus mendengarkan penjelasan kita dibanding membaca teks panjang di slide.\n\nLatihan presentasi sebelum tampil di depan umum juga penting agar penyampaian materi lebih percaya diri dan terstruktur.', NULL, 50, 3, NULL, NULL),
(42, 17, 'Cara Kerja AI/Machine Learning Sederhana', NULL, 'text', 'Secara sederhana, machine learning (bagian dari AI) bekerja dengan cara mempelajari pola dari sejumlah besar data, lalu menggunakan pola tersebut untuk membuat prediksi atau keputusan pada data baru.\n\nProsesnya secara garis besar: pertama, model AI \"dilatih\" (training) menggunakan data yang sudah ada berikut jawaban yang benar, misalnya ribuan foto kucing dan anjing yang sudah diberi label. Model akan belajar mengenali pola pembeda antara keduanya.\n\nSetelah proses pelatihan selesai, model bisa digunakan untuk memprediksi data baru yang belum pernah dilihat sebelumnya, misalnya menentukan apakah sebuah foto baru berisi kucing atau anjing.\n\nSemakin banyak dan semakin baik kualitas data yang digunakan untuk melatih model, umumnya semakin akurat pula prediksi yang dihasilkan.', NULL, 50, 1, NULL, NULL),
(43, 17, 'Bias Algoritma', NULL, 'text', 'Bias algoritma terjadi ketika sebuah sistem AI menghasilkan keputusan yang tidak adil atau berat sebelah terhadap kelompok tertentu, biasanya karena data yang digunakan untuk melatihnya tidak seimbang atau tidak representatif.\n\nContoh: jika sebuah sistem rekrutmen kerja dilatih menggunakan data historis yang sebagian besar berasal dari satu kelompok tertentu, sistem tersebut berisiko \"belajar\" untuk lebih menyukai kandidat dari kelompok yang sama, meskipun tidak dimaksudkan secara sengaja.\n\nBias algoritma bisa berdampak nyata dan merugikan, misalnya dalam keputusan penerimaan kerja, pemberian pinjaman, atau bahkan penegakan hukum, karena keputusan yang dianggap \"otomatis dan objektif\" ternyata masih membawa ketidakadilan.\n\nMenyadari adanya potensi bias ini penting agar kita bisa lebih kritis terhadap hasil yang diberikan oleh sistem AI, bukan menerimanya begitu saja sebagai kebenaran mutlak.', NULL, 50, 2, NULL, NULL),
(44, 17, 'Menggunakan AI Secara Bertanggung Jawab', NULL, 'text', 'AI adalah alat yang sangat membantu, tapi perlu digunakan secara bijak dan bertanggung jawab.\n\nSelalu verifikasi ulang informasi yang diberikan oleh AI, karena AI bisa saja memberikan jawaban yang terdengar meyakinkan namun sebenarnya salah atau tidak akurat.\n\nHindari plagiarisme, yaitu mengklaim hasil karya AI sebagai karya asli buatan sendiri tanpa keterangan, terutama dalam konteks tugas sekolah atau karya ilmiah. Sebaiknya gunakan AI sebagai alat bantu untuk belajar, bukan pengganti proses berpikir kita sendiri.\n\nBersikap transparan tentang penggunaan AI (misalnya mencantumkan jika suatu bagian tulisan dibantu AI) adalah bagian dari etika digital yang penting untuk dijaga di era teknologi saat ini.', NULL, 50, 3, NULL, NULL),
(45, 18, 'Membuat CV Digital', NULL, 'text', 'CV (Curriculum Vitae) adalah dokumen ringkas yang berisi data diri, latar belakang pendidikan, pengalaman, dan keterampilan (skill) yang kita miliki, digunakan saat melamar pekerjaan atau magang.\n\nKomponen penting dalam CV: data diri (nama, kontak), riwayat pendidikan, pengalaman (organisasi, proyek, magang jika ada), dan daftar skill yang relevan dengan posisi yang dilamar.\n\nTips membuat CV yang baik: buat ringkas (idealnya 1 halaman), gunakan bahasa yang jelas dan jujur, serta sesuaikan isi CV dengan posisi yang dilamar, jangan menggunakan CV yang sama persis untuk semua lamaran.\n\nCV digital biasanya dibuat dalam format PDF agar tampilannya tetap rapi ketika dibuka di perangkat manapun.', NULL, 50, 1, NULL, NULL),
(46, 18, 'Membangun Portofolio Online', NULL, 'text', 'Portofolio online adalah kumpulan hasil karya atau proyek terbaik yang kita tampilkan secara digital, untuk menunjukkan kemampuan nyata kepada calon perekrut atau klien.\n\nBerbeda dengan CV yang berupa teks ringkas, portofolio menunjukkan bukti nyata dari kemampuan kita, misalnya contoh website yang pernah dibuat, desain grafis, tulisan, atau proyek coding lainnya.\n\nAda banyak platform yang bisa digunakan untuk membangun portofolio online, seperti GitHub (khusus untuk kode program), atau website portofolio pribadi yang berisi rangkuman proyek beserta penjelasannya.\n\nPortofolio yang baik menampilkan proyek-proyek terbaik saja (kualitas lebih penting dari kuantitas), disertai penjelasan singkat tentang peran kita dan hasil yang dicapai dalam setiap proyek.', NULL, 50, 2, NULL, NULL),
(47, 18, 'Mengenal Dunia Kerja IT', NULL, 'text', 'Dunia kerja IT memiliki banyak peran (role) yang berbeda, masing-masing dengan fokus dan skill yang dibutuhkan.\n\nJunior Developer bertugas menulis dan membantu mengembangkan kode program untuk aplikasi atau website, biasanya di bawah bimbingan developer yang lebih senior. Skill utama: menguasai minimal satu bahasa pemrograman dan logika pemrograman dasar.\n\nData Analyst berfokus pada mengumpulkan, mengolah, dan menganalisis data untuk menghasilkan wawasan (insight) yang berguna bagi pengambilan keputusan. Skill utama: kemampuan mengolah data (misalnya Excel, SQL) dan berpikir analitis.\n\nMasih banyak peran IT lain seperti UI/UX Designer (merancang tampilan aplikasi), QA Tester (menguji kualitas aplikasi), dan Project Manager (mengelola jalannya proyek). Mengenal berbagai peran ini membantu kita menemukan bidang yang paling sesuai dengan minat dan kekuatan kita.', NULL, 50, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('app_name', 'CodingGo'),
('contact_email', 'support@codinggo.com'),
('enable_registration', '0'),
('enable_registration_google', '1'),
('enable_registration_manual', '1'),
('maintenance_mode', '0');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `google_id` varchar(100) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `category` enum('SD','SMP','SMA','Umum') DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `xp_points` int DEFAULT '0',
  `streak_days` int DEFAULT '0',
  `total_badges` int DEFAULT '0',
  `profile_title` varchar(100) DEFAULT 'Novice Coder',
  `profile_color` varchar(20) DEFAULT '#4361ee',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `allowed_categories` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `google_id`, `name`, `email`, `password`, `picture`, `birth_date`, `category`, `role`, `xp_points`, `streak_days`, `total_badges`, `profile_title`, `profile_color`, `last_login`, `created_at`, `allowed_categories`) VALUES
(1, '116833722689297559750', 'kepo kamu', 'dedynurohim1@gmail.com', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocIzr8abXJSF_1FALUV5-K3Pq6eqQzFyMsq9mw6-zOd-FY6hrHA=s96-c', '2026-08-07', 'Umum', 'admin', 0, 0, 0, 'Novice Coder', '#4361ee', '2026-08-11 09:50:00', '2026-08-06 18:12:17', NULL),
(2, '105942729897026664092', 'Dedy Nurohim', 'dedynurohim01@gmail.com', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocLfmp_oKJZEhvS5waZz-oOwYbiRrY4K7ryGmDVDLRw5MxIzlQ=s96-c', '2000-08-16', 'Umum', 'user', 0, 0, 0, 'Novice Coder', '#4361ee', '2026-08-07 20:10:30', '2026-08-07 09:25:09', NULL),
(3, '114067548867849386620', 'Moh Rafie Nazar J', 'mohrafienazarjailani@gmail.com', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJ6Lx9xkvja0A62lamsxui__Axpbyk1jVvr3IsBFY_zY9e7eGlq=s96-c', '2005-09-11', 'Umum', 'user', 100, 0, 2, 'Novice Coder', '#4361ee', '2026-08-09 16:04:03', '2026-08-08 12:42:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_badges`
--

CREATE TABLE `user_badges` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `badge_id` int NOT NULL,
  `earned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_badges`
--

INSERT INTO `user_badges` (`id`, `user_id`, `badge_id`, `earned_at`) VALUES
(1, 3, 1, '2026-08-10 05:27:11'),
(2, 3, 2, '2026-08-10 05:40:54');

-- --------------------------------------------------------

--
-- Table structure for table `user_learning_time`
--

CREATE TABLE `user_learning_time` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `course_id` int DEFAULT '0',
  `log_date` date NOT NULL,
  `time_spent` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_learning_time`
--

INSERT INTO `user_learning_time` (`id`, `user_id`, `log_date`, `time_spent`, `created_at`) VALUES
(1, 2, '2026-08-04', 100, '2026-08-10 14:27:19'),
(2, 2, '2026-08-05', 77, '2026-08-10 14:27:19'),
(3, 2, '2026-08-06', 74, '2026-08-10 14:27:19'),
(4, 2, '2026-08-07', 45, '2026-08-10 14:27:19'),
(5, 2, '2026-08-08', 59, '2026-08-10 14:27:19'),
(6, 2, '2026-08-09', 27, '2026-08-10 14:27:19'),
(7, 2, '2026-08-10', 113, '2026-08-10 14:27:19'),
(8, 1, '2026-08-04', 22, '2026-08-10 14:27:19'),
(9, 1, '2026-08-05', 18, '2026-08-10 14:27:19'),
(10, 1, '2026-08-06', 92, '2026-08-10 14:27:19'),
(11, 1, '2026-08-07', 33, '2026-08-10 14:27:19'),
(12, 1, '2026-08-08', 77, '2026-08-10 14:27:19'),
(13, 1, '2026-08-09', 60, '2026-08-10 14:27:19'),
(14, 1, '2026-08-10', 64, '2026-08-10 14:27:19'),
(15, 3, '2026-08-04', 53, '2026-08-10 14:27:19'),
(16, 3, '2026-08-05', 58, '2026-08-10 14:27:19'),
(17, 3, '2026-08-06', 61, '2026-08-10 14:27:19'),
(18, 3, '2026-08-07', 98, '2026-08-10 14:27:19'),
(19, 3, '2026-08-08', 118, '2026-08-10 14:27:19'),
(20, 3, '2026-08-09', 64, '2026-08-10 14:27:19'),
(21, 3, '2026-08-10', 111, '2026-08-10 14:27:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `type` varchar(50) DEFAULT 'system',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link_url` varchar(255) DEFAULT '#',
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_notifications`
--

INSERT INTO `user_notifications` (`id`, `user_id`, `type`, `title`, `message`, `link_url`, `is_read`, `created_at`) VALUES
(1, 2, 'system', 'Selamat Datang!', 'Selamat datang di CodingGo. Ayo mulai petualangan coding-mu!', 'index.php?page=dashboard', 0, '2026-08-10 13:40:17'),
(2, 1, 'system', 'Selamat Datang!', 'Selamat datang di CodingGo. Ayo mulai petualangan coding-mu!', 'index.php?page=dashboard', 1, '2026-08-10 13:40:17'),
(3, 3, 'system', 'Selamat Datang!', 'Selamat datang di CodingGo. Ayo mulai petualangan coding-mu!', 'index.php?page=dashboard', 0, '2026-08-10 13:40:17'),
(4, 2, 'system', 'Selamat Datang!', 'Selamat datang di CodingGo. Ayo mulai petualangan coding-mu!', 'index.php?page=dashboard', 0, '2026-08-10 13:40:48'),
(5, 1, 'system', 'Selamat Datang!', 'Selamat datang di CodingGo. Ayo mulai petualangan coding-mu!', 'index.php?page=dashboard', 1, '2026-08-10 13:40:48'),
(6, 3, 'system', 'Selamat Datang!', 'Selamat datang di CodingGo. Ayo mulai petualangan coding-mu!', 'index.php?page=dashboard', 0, '2026-08-10 13:40:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `material_id` int NOT NULL,
  `status` enum('started','completed') DEFAULT 'started',
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_progress`
--

INSERT INTO `user_progress` (`id`, `user_id`, `material_id`, `status`, `completed_at`) VALUES
(3, 3, 3, 'completed', '2026-08-10 12:40:02'),
(4, 3, 12, 'completed', '2026-08-10 12:26:15'),
(5, 3, 13, 'completed', '2026-08-10 12:26:21'),
(6, 3, 14, 'completed', '2026-08-10 12:26:24'),
(7, 3, 4, 'completed', '2026-08-10 12:40:05'),
(8, 3, 5, 'completed', '2026-08-10 12:40:07'),
(9, 1, 3, 'started', NULL),
(10, 1, 4, 'started', NULL),
(11, 1, 5, 'started', NULL),
(12, 1, 18, 'started', NULL),
(13, 1, 19, 'started', NULL),
(14, 1, 21, 'started', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `broadcasts`
--
ALTER TABLE `broadcasts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `broadcast_views`
--
ALTER TABLE `broadcast_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `broadcast_id` (`broadcast_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_code` (`certificate_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `championships`
--
ALTER TABLE `championships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `championship_challenges`
--
ALTER TABLE `championship_challenges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `championship_id` (`championship_id`);

--
-- Indexes for table `championship_completed_challenges`
--
ALTER TABLE `championship_completed_challenges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `challenge_id` (`challenge_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `championship_participants`
--
ALTER TABLE `championship_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `championship_id` (`championship_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `badge_id` (`badge_id`);

--
-- Indexes for table `user_learning_time`
--
ALTER TABLE `user_learning_time`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`log_date`,`course_id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `material_id` (`material_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `broadcasts`
--
ALTER TABLE `broadcasts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `broadcast_views`
--
ALTER TABLE `broadcast_views`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `championships`
--
ALTER TABLE `championships`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `championship_challenges`
--
ALTER TABLE `championship_challenges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `championship_completed_challenges`
--
ALTER TABLE `championship_completed_challenges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `championship_participants`
--
ALTER TABLE `championship_participants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `exam_questions`
--
ALTER TABLE `exam_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_badges`
--
ALTER TABLE `user_badges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_learning_time`
--
ALTER TABLE `user_learning_time`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `broadcast_views`
--
ALTER TABLE `broadcast_views`
  ADD CONSTRAINT `broadcast_views_ibfk_1` FOREIGN KEY (`broadcast_id`) REFERENCES `broadcasts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `broadcast_views_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `championship_challenges`
--
ALTER TABLE `championship_challenges`
  ADD CONSTRAINT `championship_challenges_ibfk_1` FOREIGN KEY (`championship_id`) REFERENCES `championships` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `championship_completed_challenges`
--
ALTER TABLE `championship_completed_challenges`
  ADD CONSTRAINT `championship_completed_challenges_ibfk_1` FOREIGN KEY (`challenge_id`) REFERENCES `championship_challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `championship_completed_challenges_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `championship_participants`
--
ALTER TABLE `championship_participants`
  ADD CONSTRAINT `championship_participants_ibfk_1` FOREIGN KEY (`championship_id`) REFERENCES `championships` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `championship_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD CONSTRAINT `exam_questions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD CONSTRAINT `forum_replies_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD CONSTRAINT `user_badges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_badges_ibfk_2` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_learning_time`
--
ALTER TABLE `user_learning_time`
  ADD CONSTRAINT `user_learning_time_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `user_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
