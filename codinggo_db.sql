-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 16, 2026 at 07:46 PM
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
-- Database: ``
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`id`, `name`, `description`, `icon_url`, `requirement_type`, `requirement_value`) VALUES
(1, 'Master of Microsoft Office Dasar', 'Lulus ujian Quiz: Microsoft Office Dasar', NULL, 'exam', 6),
(2, 'Master of Mengenal Perangkat & Internet', 'Lulus ujian Quiz: Mengenal Perangkat & Internet', NULL, 'exam', 3),
(3, 'Lulusan: Mengenal Perangkat & Internet', 'Berhasil menyelesaikan course Mengenal Perangkat & Internet', NULL, 'course', 4),
(4, 'Lulusan: Keamanan Digital Dasar', 'Berhasil menyelesaikan course Keamanan Digital Dasar', NULL, 'course', 5),
(5, 'Lulusan: Logika Dasar (Computational Thinking)', 'Berhasil menyelesaikan course Logika Dasar (Computational Thinking)', NULL, 'course', 6),
(6, 'Lulusan: Microsoft Office Dasar', 'Berhasil menyelesaikan course Microsoft Office Dasar', NULL, 'course', 7),
(7, 'Lulusan: Kreativitas Digital', 'Berhasil menyelesaikan course Kreativitas Digital', NULL, 'course', 8),
(8, 'Lulusan: Keamanan Siber Menengah', 'Berhasil menyelesaikan course Keamanan Siber Menengah', NULL, 'course', 9),
(9, 'Lulusan: Dasar Coding (Block-based ke Text-based)', 'Berhasil menyelesaikan course Dasar Coding (Block-based ke Text-based)', NULL, 'course', 10),
(10, 'Lulusan: Microsoft Office Menengah', 'Berhasil menyelesaikan course Microsoft Office Menengah', NULL, 'course', 11),
(11, 'Lulusan: Literasi Data & Privasi', 'Berhasil menyelesaikan course Literasi Data & Privasi', NULL, 'course', 12),
(12, 'Lulusan: Pengenalan Desain & Multimedia Dasar', 'Berhasil menyelesaikan course Pengenalan Desain & Multimedia Dasar', NULL, 'course', 13),
(13, 'Lulusan: Keamanan Siber Lanjutan', 'Berhasil menyelesaikan course Keamanan Siber Lanjutan', NULL, 'course', 14),
(14, 'Lulusan: Pemrograman Terapan', 'Berhasil menyelesaikan course Pemrograman Terapan', NULL, 'course', 15),
(15, 'Lulusan: Microsoft Office Profesional', 'Berhasil menyelesaikan course Microsoft Office Profesional', NULL, 'course', 16),
(16, 'Lulusan: Literasi AI & Etika Teknologi', 'Berhasil menyelesaikan course Literasi AI & Etika Teknologi', NULL, 'course', 17),
(17, 'Lulusan: Kesiapan Karier Digital', 'Berhasil menyelesaikan course Kesiapan Karier Digital', NULL, 'course', 18),
(18, 'Lulusan: Keamanan Siber untuk Profesional & Bisnis', 'Berhasil menyelesaikan course Keamanan Siber untuk Profesional & Bisnis', NULL, 'course', 19),
(19, 'Lulusan: Otomatisasi Kerja dengan Python Dasar', 'Berhasil menyelesaikan course Otomatisasi Kerja dengan Python Dasar', NULL, 'course', 20),
(20, 'Lulusan: Microsoft Office untuk Produktivitas Kerja', 'Berhasil menyelesaikan course Microsoft Office untuk Produktivitas Kerja', NULL, 'course', 21),
(21, 'Lulusan: Literasi AI & Produktivitas Kerja', 'Berhasil menyelesaikan course Literasi AI & Produktivitas Kerja', NULL, 'course', 22),
(22, 'Lulusan: Personal Branding & Karier Digital', 'Berhasil menyelesaikan course Personal Branding & Karier Digital', NULL, 'course', 23);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `broadcast_views`
--

CREATE TABLE `broadcast_views` (
  `id` int NOT NULL,
  `broadcast_id` int NOT NULL,
  `user_id` int NOT NULL,
  `viewed_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `certificate_code`, `user_id`, `course_id`, `issued_at`) VALUES
(1, 'CGO-9A87B1-73', 3, 7, '2026-08-10 05:27:11'),
(2, 'CGO-30EE73-43', 3, 4, '2026-08-10 05:40:54'),
(3, 'CGO-BC4A5B-41', 1, 4, '2026-08-13 07:33:30');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `championships`
--

INSERT INTO `championships` (`id`, `title`, `description`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(1, 'Challenge Mingguan CodingGo', 'Tantangan mingguan untuk melatih logika, literasi digital, dan kemampuan coding dasar.', '2026-08-15 13:11:33', '2026-08-29 13:11:33', 'active', '2026-08-15 13:11:33');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `championship_challenges`
--

INSERT INTO `championship_challenges` (`id`, `championship_id`, `title`, `description`, `correct_answer`, `xp_reward`, `created_at`) VALUES
(1, 1, 'Judul 1: Logika Boolean', '### Soal Challenge\n\nJika kondisi A benar dan B salah, maka hasil A && B adalah?\n\nJawaban singkat: false\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'false', 50, '2026-08-15 13:11:33'),
(2, 1, 'Judul 2: Penggunaan Variabel', '### Soal Challenge\n\nVariabel dalam program berfungsi untuk?\n\nJawaban singkat: menyimpan data\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'menyimpan data', 50, '2026-08-15 13:11:33'),
(3, 1, 'Judul 3: Struktur Kondisi', '### Soal Challenge\n\nFungsi utama if adalah?\n\nJawaban singkat: membuat keputusan berdasarkan kondisi\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'membuat keputusan berdasarkan kondisi', 50, '2026-08-15 13:11:33'),
(4, 1, 'Judul 4: Perulangan', '### Soal Challenge\n\nPerulangan digunakan agar kode?\n\nJawaban singkat: berjalan berulang sesuai pola\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'berjalan berulang sesuai pola', 50, '2026-08-15 13:11:33'),
(5, 1, 'Judul 5: Internet Aman', '### Soal Challenge\n\nSebelum klik tautan mencurigakan, yang paling tepat adalah?\n\nJawaban singkat: mencek sumbernya dan menanyakan ke orang dewasa\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'mencek sumbernya dan menanyakan ke orang dewasa', 50, '2026-08-15 13:11:33'),
(6, 1, 'Judul 6: Program Dasar', '### Soal Challenge\n\nBahasa pemrograman yang populer untuk web adalah?\n\nJawaban singkat: javascript\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'javascript', 50, '2026-08-15 13:11:33'),
(7, 1, 'Judul 7: Data Pribadi', '### Soal Challenge\n\nData paling sensitif yang tidak boleh dibagikan ke orang asing adalah?\n\nJawaban singkat: nomor telepon dan alamat rumah\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'nomor telepon dan alamat rumah', 50, '2026-08-15 13:11:33'),
(8, 1, 'Judul 8: Microsoft Excel', '### Soal Challenge\n\nFungsi utama rumus IF di Excel adalah?\n\nJawaban singkat: membuat keputusan berdasarkan syarat\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'membuat keputusan berdasarkan syarat', 50, '2026-08-15 13:11:33'),
(9, 1, 'Judul 9: Desain', '### Soal Challenge\n\nLayout yang rapi membuat desain terasa?\n\nJawaban singkat: lebih mudah dibaca dan enak dilihat\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'lebih mudah dibaca dan enak dilihat', 50, '2026-08-15 13:11:33'),
(10, 1, 'Judul 10: Keamanan Digital', '### Soal Challenge\n\n2FA adalah singkatan dari?\n\nJawaban singkat: two factor authentication\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'two factor authentication', 50, '2026-08-15 13:11:33'),
(11, 1, 'Judul 11: HTML', '### Soal Challenge\n\nTag yang digunakan untuk judul utama adalah?\n\nJawaban singkat: h1\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'h1', 50, '2026-08-15 13:11:33'),
(12, 1, 'Judul 12: Logika Matematika', '### Soal Challenge\n\nHasil dari 8 + 4 / 2 adalah?\n\nJawaban singkat: 10\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', '10', 50, '2026-08-15 13:11:33'),
(13, 1, 'Judul 13: Digital Footprint', '### Soal Challenge\n\nJejak digital adalah?\n\nJawaban singkat: rekam aktivitas kita di internet\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'rekam aktivitas kita di internet', 50, '2026-08-15 13:11:33'),
(14, 1, 'Judul 14: AI', '### Soal Challenge\n\nAI yang baik sebaiknya digunakan dengan?\n\nJawaban singkat: verifikasi dan etika\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'verifikasi dan etika', 50, '2026-08-15 13:11:33'),
(15, 1, 'Judul 15: Presentasi', '### Soal Challenge\n\nSlide yang efektif biasanya?\n\nJawaban singkat: ringkas, jelas, dan visual\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'ringkas, jelas, dan visual', 50, '2026-08-15 13:11:33'),
(16, 1, 'Judul 16: Internet & Data', '### Soal Challenge\n\nCookie pada website berfungsi untuk?\n\nJawaban singkat: mengingat preferensi pengguna\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'mengingat preferensi pengguna', 50, '2026-08-15 13:11:33'),
(17, 1, 'Judul 17: Simulasi', '### Soal Challenge\n\nUrutan instruksi yang benar disebut?\n\nJawaban singkat: sequencing\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'sequencing', 50, '2026-08-15 13:11:33'),
(18, 1, 'Judul 18: Cyber Safety', '### Soal Challenge\n\nJika menerima SMS menang hadiah tidak jelas, langkah paling aman adalah?\n\nJawaban singkat: mengabaikannya dan mengeceknya melalui kanal resmi\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'mengabaikannya dan mengeceknya melalui kanal resmi', 50, '2026-08-15 13:11:33'),
(19, 1, 'Judul 19: Programming Mindset', '### Soal Challenge\n\nCara terbaik belajar coding adalah?\n\nJawaban singkat: latihan rutin dan membangun proyek kecil\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'latihan rutin dan membangun proyek kecil', 50, '2026-08-15 13:11:33'),
(20, 1, 'Judul 20: Problem Solving', '### Soal Challenge\n\nSaat menemukan bug, langkah terbaik adalah?\n\nJawaban singkat: mencari penyebabnya dan mengujinya satu per satu\n\n> Tips: jawab singkat, jelas, dan tanpa spasi berlebih.', 'mencari penyebabnya dan mengujinya satu per satu', 50, '2026-08-15 13:11:33');

-- --------------------------------------------------------

--
-- Table structure for table `championship_completed_challenges`
--

CREATE TABLE `championship_completed_challenges` (
  `id` int NOT NULL,
  `challenge_id` int NOT NULL,
  `user_id` int NOT NULL,
  `completed_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(18, 'Kesiapan Karier Digital', 'SMA', 'Dasar CV digital, portofolio online, dan pengenalan dunia kerja IT.', 'https://uici.ac.id/wp-content/uploads/2024/01/IMG_2130.jpeg', '#8b5cf6', NULL, '2026-08-09 08:52:49'),
(19, 'Keamanan Siber untuk Profesional & Bisnis', 'Umum', 'Belajar cara menjaga akun digital, mengenali phishing, mengelola data sensitif, dan membangun keamanan siber di lingkungan kerja serta bisnis.', NULL, '#f59e0b', NULL, '2026-08-15 06:11:34'),
(20, 'Otomatisasi Kerja dengan Python Dasar', 'Umum', 'Pelajari cara menggunakan Python dasar untuk otomatisasi tugas harian, pengolahan data ringan, dan peningkatan produktivitas kerja.', NULL, '#f59e0b', NULL, '2026-08-15 06:11:34'),
(21, 'Microsoft Office untuk Produktivitas Kerja', 'Umum', 'Kuasai Word, Excel, dan PowerPoint untuk menulis dokumen, mengolah data, dan membuat presentasi yang efektif di pekerjaan sehari-hari.', NULL, '#f59e0b', NULL, '2026-08-15 06:11:34'),
(22, 'Literasi AI & Produktivitas Kerja', 'Umum', 'Pahami cara kerja AI, manfaatnya untuk produktivitas, serta apa yang harus diperhatikan agar penggunaan AI tetap etis dan aman.', NULL, '#f59e0b', NULL, '2026-08-15 06:11:34'),
(23, 'Personal Branding & Karier Digital', 'Umum', 'Bangun portofolio online, optimalkan LinkedIn, dan presentasikan diri secara digital agar lebih siap menghadapi dunia kerja modern.', NULL, '#f59e0b', NULL, '2026-08-15 06:11:34');

-- --------------------------------------------------------

--
-- Table structure for table `course_ratings`
--

CREATE TABLE `course_ratings` (
  `id` int NOT NULL,
  `course_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `review` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_ratings`
--

INSERT INTO `course_ratings` (`id`, `course_id`, `user_id`, `rating`, `review`, `created_at`, `updated_at`) VALUES
(2, 4, 1, 5, NULL, '2026-08-13 08:09:08', NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(17, 18, 'Quiz: Kesiapan Karier Digital', 'quiz', 70),
(18, 19, 'Ujian Akhir: Keamanan Siber untuk Profesional & Bisnis', 'quiz', 50),
(19, 20, 'Ujian Akhir: Otomatisasi Kerja dengan Python Dasar', 'quiz', 50),
(20, 21, 'Ujian Akhir: Microsoft Office untuk Produktivitas Kerja', 'quiz', 50),
(21, 22, 'Ujian Akhir: Literasi AI & Produktivitas Kerja', 'quiz', 50),
(22, 23, 'Ujian Akhir: Personal Branding & Karier Digital', 'quiz', 50);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(76, 17, 'multiple_choice', 'Sebelum melamar kerja di bidang IT, sebaiknya kita mempersiapkan?', 'CV dan portofolio yang relevan', 'Tidak perlu persiapan apapun', 'Hanya modal keberanian tanpa skill', 'Menunggu tanpa usaha', 'a', 20),
(77, 18, 'multiple_choice', 'Apa tujuan utama keamanan siber?', 'melindungi data dan sistem digital dari ancaman', 'membuat semua situs aman', 'menghapus semua file', 'mengganti perangkat komputer', 'a', 10),
(78, 18, 'multiple_choice', 'Tindakan paling aman saat menerima email mencurigakan adalah?', 'mengabaikan dan mengecek sumbernya dengan cara resmi', 'langsung mengklik tautannya', 'membalas dengan data pribadi', 'membagikan ke teman', 'a', 10),
(79, 18, 'multiple_choice', 'Apa fungsi autentikasi dua langkah?', 'menambah lapisan keamanan saat login', 'menghapus password', 'mengganti format file', 'membatasi koneksi internet', 'a', 10),
(80, 18, 'multiple_choice', 'Kenapa data bisnis perlu dilindungi?', 'karena data sensitif bisa merugikan organisasi jika bocor', 'karena data tidak bisa dipakai', 'karena semua data sama', 'karena bisnis tidak perlu data', 'a', 10),
(81, 18, 'multiple_choice', 'Yang paling tepat saat melihat link mencurigakan adalah?', 'menghindari klik dan mengecek domain asli', 'langsung membuka untuk membuktikannya', 'mengirim ke semua teman', 'membagikan di grup kerja', 'a', 10),
(82, 19, 'multiple_choice', 'Apa fungsi variabel dalam Python?', 'menyimpan data yang akan dipakai program', 'menghapus file', 'mencegah internet', 'membuat desain web', 'a', 10),
(83, 19, 'multiple_choice', 'Struktur if digunakan untuk?', 'membuat keputusan berdasarkan kondisi', 'mengulang kode tanpa batas', 'mengganti nama file', 'menghentikan program', 'a', 10),
(84, 19, 'multiple_choice', 'Apa manfaat loop dalam otomatisasi?', 'mengulang tugas tanpa menulis berulang', 'menghilangkan seluruh data', 'menambah password', 'mengatur tampilan layar', 'a', 10),
(85, 19, 'multiple_choice', 'Kapan Python cocok dipakai untuk pekerjaan?', 'ketika ada tugas berulang dan data yang perlu diproses', 'hanya saat menggambar', 'hanya saat menonton video', 'hanya saat membuat game', 'a', 10),
(86, 19, 'multiple_choice', 'List di Python berfungsi untuk?', 'menyimpan beberapa nilai dalam satu variabel', 'merubah warna layar', 'membuat email', 'membatasi internet', 'a', 10),
(87, 20, 'multiple_choice', 'Fungsi utama Microsoft Word adalah?', 'membuat dan mengedit dokumen', 'mengolah angka', 'membuat database server', 'mengedit foto', 'a', 10),
(88, 20, 'multiple_choice', 'Rumus Excel yang digunakan untuk menjumlahkan data adalah?', 'SUM', 'IF', 'AVERAGE', 'TEXT', 'a', 10),
(89, 20, 'multiple_choice', 'Fungsi utama PowerPoint adalah?', 'membuat presentasi visual', 'mengolah tabel', 'mengedit video', 'membuat email', 'a', 10),
(90, 20, 'multiple_choice', 'Manfaat menggunakan bullet point di Word adalah?', 'membuat informasi lebih mudah dibaca', 'menyembunyikan isi dokumen', 'mempercepat printer', 'menghapus semua halaman', 'a', 10),
(91, 20, 'multiple_choice', 'Mengapa tata letak slide harus sederhana?', 'agar audiens lebih mudah memahami pesan', 'karena semua slide harus penuh', 'karena ini aturan teknis', 'karena warna harus banyak', 'a', 10),
(92, 21, 'multiple_choice', 'AI paling sering digunakan untuk?', 'membantu proses analisis dan otomatisasi', 'mengganti semua manusia', 'menghapus semua data', 'membuat mesin listrik', 'a', 10),
(93, 21, 'multiple_choice', 'Apa yang harus dilakukan sebelum menerima hasil AI?', 'menganalisa dan memverifikasi kebenarannya', 'langsung mengirim tanpa cek', 'langsung percaya 100%', 'menghapus semua data lain', 'a', 10),
(94, 21, 'multiple_choice', 'Mengapa etika penting dalam penggunaan AI?', 'agar penggunaan AI tetap aman dan bertanggung jawab', 'karena AI tidak membutuhkan etika', 'karena semua AI selalu benar', 'karena AI tidak punya data', 'a', 10),
(95, 21, 'multiple_choice', 'AI dapat membantu pekerjaan seperti?', 'menulis ringkasan dan menganalisis data', 'menghentikan semua internet', 'menyusun jadwal tanpa logika', 'menghapus program', 'a', 10),
(96, 21, 'multiple_choice', 'Kebiasaan bijak menggunakan AI adalah?', 'menggunakannya sebagai alat bantu sambil tetap kritis', 'menggunakannya tanpa evaluasi', 'tidak pernah digunakan sama sekali', 'menghasilkan konten tanpa sumber', 'a', 10),
(97, 22, 'multiple_choice', 'Tujuan personal branding adalah?', 'membuat orang lebih mudah mengenali kemampuan dan nilai profesionalmu', 'menyembunyikan identitas', 'mengurangi semua portofolio', 'menghapus profil online', 'a', 10),
(98, 22, 'multiple_choice', 'Platform yang sering dipakai untuk profil profesional adalah?', 'LinkedIn', 'Game online', 'Aplikasi musik', 'File dokumen pribadi', 'a', 10),
(99, 22, 'multiple_choice', 'Keuntungan portofolio digital adalah?', 'menunjukkan bukti kemampuan secara nyata', 'menghapus semua pengalaman', 'mengganggu rekruter', 'membuat profil tampil rumit', 'a', 10),
(100, 22, 'multiple_choice', 'Komunikasi profesional yang baik biasanya?', 'jelas, sopan, dan terstruktur', 'panjang tanpa inti', 'menggunakan kata kasar', 'mengabaikan detail', 'a', 10),
(101, 22, 'multiple_choice', 'Konten profil digital yang baik adalah?', 'rapi, konsisten, dan relevan dengan tujuan karier', 'semua informasi tanpa filter', 'tidak perlu diperbarui', 'hanya foto tanpa deskripsi', 'a', 10);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_results`
--

INSERT INTO `exam_results` (`id`, `user_id`, `exam_id`, `score`, `passed`, `attempt_date`) VALUES
(3, 3, 6, 100, 1, '2026-08-10 05:27:11'),
(4, 3, 3, 100, 1, '2026-08-10 05:40:54'),
(5, 1, 3, 100, 1, '2026-08-13 07:33:30');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `user_id`, `title`, `content`, `upvotes`, `downvotes`, `is_solved`, `created_at`, `is_official`) VALUES
(1, 1, 'errotrdsgfgd', 'hhdf', 0, 0, 0, '2026-08-07 11:56:20', 0),
(2, 3, 'HIDUP JOKOWI', 'WE WOK THE TOK', 0, 0, 0, '2026-08-09 08:58:44', 0),
(3, 1, 'halo', 'halo semua', 0, 0, 0, '2026-08-13 17:29:34', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_votes`
--

CREATE TABLE `forum_votes` (
  `id` int NOT NULL,
  `target_type` varchar(20) NOT NULL,
  `target_id` int NOT NULL,
  `user_id` int NOT NULL,
  `vote_type` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_perks`
--

CREATE TABLE `gamification_perks` (
  `id` int NOT NULL,
  `type` enum('avatar_frame','name_effect','profile_effect','banner_gif','card_border','card_background','cursor_effect','badge_effect','entrance_anim') NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `required_badges` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gamification_perks`
--

INSERT INTO `gamification_perks` (`id`, `type`, `name`, `value`, `required_badges`, `created_at`) VALUES
(1, 'avatar_frame', 'Ring of Fire', 'box-shadow: 0 0 10px #ff4500, 0 0 20px #ff4500; border: 4px solid #ff4500;', 15, '2026-08-15 07:28:23'),
(2, 'avatar_frame', 'Hacker Matrix', 'border: 4px solid #00ff00; box-shadow: 0 0 15px #00ff00;', 15, '2026-08-15 07:28:23'),
(3, 'name_effect', 'Rainbow Gradient', 'background-image: linear-gradient(to left, violet, indigo, blue, green, yellow, orange, red); -webkit-background-clip: text; color: transparent; font-weight: 800;', 20, '2026-08-15 07:28:23'),
(4, 'name_effect', 'Gold VIP', 'color: #ffd700; text-shadow: 0 0 10px #ffd700; font-weight: 800;', 20, '2026-08-15 07:28:23'),
(5, 'profile_effect', 'Matrix Rain', 'matrix', 30, '2026-08-15 07:28:23'),
(6, 'profile_effect', 'Snowflakes', 'snow', 30, '2026-08-15 07:28:23'),
(7, 'banner_gif', 'Champion Banner', 'https://assets-v2.lottiefiles.com/a/618fc384-1184-11ee-94d3-7fa9529e93c3/OIgiq15Qro.mp4', 10, '2026-08-15 07:55:17'),
(8, 'banner_gif', 'Trophy Banner', 'https://assets-v2.lottiefiles.com/a/745fc364-117b-11ee-b7ec-9f18a8a356e0/8lgzK4zlmD.mp4', 10, '2026-08-15 07:55:17'),
(9, 'banner_gif', 'Winner Badge Banner', 'https://assets-v2.lottiefiles.com/a/ed5ae48c-117c-11ee-afee-879cb97bcc98/HdLCGkInQ3.mp4', 10, '2026-08-15 07:55:17'),
(10, 'banner_gif', 'Banner GIF', 'profile-banner-1.gif', 10, '2026-08-15 07:55:39'),
(11, 'banner_gif', 'Banner GIF', 'profile-banner-2.gif', 10, '2026-08-15 07:55:39'),
(12, 'banner_gif', 'Banner GIF', 'profile-banner-3.gif', 10, '2026-08-15 07:55:39'),
(14, 'profile_effect', 'Wining Particle Animatio', 'banner_1786782746.gif', 15, '2026-08-15 08:32:26'),
(15, 'card_border', 'blue gloww', 'border: 2px solid #0b57d0; box-shadow: 0 0 15px rgba(4, 79, 200, 0.4);', 15, '2026-08-15 08:50:15'),
(16, 'card_background', 'Grid Cyberpunk', 'background-image: linear-gradient(rgba(0, 255, 128, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 255, 128, 0.1) 1px, transparent 1px); background-size: 20px 20px; background-position: center; background-color: rgba(0,20,10,0.8);', 5, '2026-08-15 09:54:26'),
(17, 'card_background', 'Anime Night Sky', 'background-image: url(\'https://media.giphy.com/media/u01ioCe6G8URG/giphy.gif\'); background-size: cover; background-position: center; opacity: 0.8;', 10, '2026-08-15 09:54:26'),
(18, 'card_background', 'Sunset Vibes', 'background: linear-gradient(135deg, rgba(255,126,95,0.2) 0%, rgba(254,180,123,0.2) 100%);', 2, '2026-08-15 09:54:26'),
(19, 'cursor_effect', 'Pedang Diamond', 'cursor: url(\'https://cdn.custom-cursor.com/db/8626/32/minecraft-diamond-sword-pointer.png\'), auto;', 5, '2026-08-15 09:54:26'),
(20, 'cursor_effect', 'Neon Crosshair', 'cursor: crosshair;', 1, '2026-08-15 09:54:26'),
(21, 'cursor_effect', 'Bintang Emas', 'cursor: url(\'https://cdn.custom-cursor.com/db/9675/32/cute-gold-star-pointer.png\'), auto;', 8, '2026-08-15 09:54:26'),
(22, 'badge_effect', 'Legendary Gold Glow', 'border-color: #ffd700; box-shadow: 0 0 15px rgba(255, 215, 0, 0.8);', 15, '2026-08-15 09:54:26'),
(23, 'badge_effect', 'Neon Pink Glow', 'border-color: #ff00ff; box-shadow: 0 0 15px rgba(255, 0, 255, 0.8);', 10, '2026-08-15 09:54:26'),
(24, 'badge_effect', 'Aqua Blue Glow', 'border-color: #00ffff; box-shadow: 0 0 15px rgba(0, 255, 255, 0.8);', 5, '2026-08-15 09:54:26'),
(25, 'entrance_anim', 'Zoom In Bouncy', 'animation: modalZoomIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;', 10, '2026-08-15 09:54:26'),
(26, 'entrance_anim', '3D Flip', 'animation: modal3DFlip 0.6s ease-out forwards;', 20, '2026-08-15 09:54:26'),
(27, 'entrance_anim', 'Slide Up', 'animation: modalSlideUp 0.4s ease-out forwards;', 2, '2026-08-15 09:54:26'),
(29, 'card_background', 'tes', 'banner_1786907694.png', 15, '2026-08-16 19:14:54');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `course_id`, `title`, `thumbnail`, `content_type`, `content_text`, `video_url`, `xp_reward`, `order_index`, `unlock_keyword`, `attachment_file`) VALUES
(3, 4, 'Apa itu Komputer, Laptop, dan Tablet?', '', 'text', 'Komputer, laptop, dan tablet adalah alat elektronik yang bisa membantu kita belajar, bermain, dan berkreasi.\r\n\r\nKomputer biasanya diletakkan di meja dan terdiri dari beberapa bagian: layar (untuk melihat), keyboard (untuk mengetik), mouse (untuk menunjuk dan mengklik), dan CPU (otak dari komputer yang memproses semua perintah).\r\n\r\nLaptop adalah komputer yang lebih kecil dan bisa dibawa ke mana-mana karena semua bagiannya sudah menyatu jadi satu.\r\n\r\nTablet lebih tipis lagi dan biasanya dioperasikan dengan cara disentuh langsung di layarnya (touchscreen), tanpa keyboard atau mouse.\r\n\r\nKetiganya punya fungsi yang mirip yaitu membantu kita mengerjakan tugas, mencari informasi, dan berkomunikasi, hanya bentuk dan cara pakainya yang berbeda.', '', 30, 1, NULL, NULL),
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
(47, 18, 'Mengenal Dunia Kerja IT', NULL, 'text', 'Dunia kerja IT memiliki banyak peran (role) yang berbeda, masing-masing dengan fokus dan skill yang dibutuhkan.\n\nJunior Developer bertugas menulis dan membantu mengembangkan kode program untuk aplikasi atau website, biasanya di bawah bimbingan developer yang lebih senior. Skill utama: menguasai minimal satu bahasa pemrograman dan logika pemrograman dasar.\n\nData Analyst berfokus pada mengumpulkan, mengolah, dan menganalisis data untuk menghasilkan wawasan (insight) yang berguna bagi pengambilan keputusan. Skill utama: kemampuan mengolah data (misalnya Excel, SQL) dan berpikir analitis.\n\nMasih banyak peran IT lain seperti UI/UX Designer (merancang tampilan aplikasi), QA Tester (menguji kualitas aplikasi), dan Project Manager (mengelola jalannya proyek). Mengenal berbagai peran ini membantu kita menemukan bidang yang paling sesuai dengan minat dan kekuatan kita.', NULL, 50, 3, NULL, NULL),
(48, 19, 'Pengenalan Keamanan Siber', NULL, 'text', 'Keamanan siber adalah upaya melindungi data, akun, dan perangkat dari ancaman digital seperti phishing, malware, dan pencurian identitas. Di dunia kerja, keamanan data sangat penting karena informasi sensitif bisa merugikan organisasi jika jatuh ke tangan yang salah.', NULL, 40, 1, NULL, NULL),
(49, 19, 'Phishing dan Social Engineering', NULL, 'text', 'Phishing adalah teknik penipuan yang mengecoh pengguna untuk membuka link atau membagikan informasi pribadi. Social engineering memanfaatkan psikologi manusia untuk mendapatkan akses. Kunci pencegahan adalah skeptis terhadap pesan mendadak, mengecek sumber, dan tidak pernah membagikan password atau OTP.', NULL, 40, 2, NULL, NULL),
(50, 19, 'Keamanan Akun & Data Bisnis', NULL, 'text', 'Untuk keamanan akun, gunakan password unik, aktifkan autentikasi dua langkah, dan cadangkan data secara rutin. Di lingkungan bisnis, data pelanggan, keuangan, dan dokumen internal harus terlindungi dengan aturan akses yang jelas dan kebiasaan kerja yang aman.', NULL, 45, 3, NULL, NULL),
(51, 20, 'Pengenalan Python untuk Kerja', NULL, 'text', 'Python adalah bahasa pemrograman yang mudah dipelajari dan sering dipakai untuk otomatisasi tugas. Dengan Python, kamu bisa membuat skrip untuk mengolah data, meringkas file, atau menyederhanakan pekerjaan berulang.', NULL, 40, 1, NULL, NULL),
(52, 20, 'Variabel, List, dan Kondisi', NULL, 'text', 'Variabel menampung data, sedangkan list memungkinkan kita menyimpan beberapa data sekaligus. Struktur kondisi seperti if dan else membantu komputer mengambil keputusan berdasarkan aturan tertentu.', NULL, 40, 2, NULL, NULL),
(53, 20, 'Loop dan Automasi Tugas', NULL, 'text', 'Loop atau perulangan memungkinkan tugas dijalankan berulang tanpa menulis kode berulang. Ini sangat berguna saat mengolah berkas, membuat laporan, atau menjalankan tugas yang berulang setiap hari.', NULL, 45, 3, NULL, NULL),
(54, 21, 'Word untuk Dokumen Profesional', NULL, 'text', 'Microsoft Word membantu menulis proposal, surat, laporan, dan dokumen resmi. Fitur seperti heading, bullet, tabel, dan format paragraf membuat dokumen lebih rapi dan mudah dibaca.', NULL, 40, 1, NULL, NULL),
(55, 21, 'Excel untuk Data & Analisis', NULL, 'text', 'Excel digunakan untuk menghitung, mengelola data, dan membuat tabel. Fungsi dasar seperti SUM, AVERAGE, IF, dan format tabel akan sangat membantu pada pekerjaan kantor maupun tugas sekolah.', NULL, 40, 2, NULL, NULL),
(56, 21, 'PowerPoint untuk Presentasi', NULL, 'text', 'PowerPoint membantu menyampaikan ide dengan visual yang jelas. Slide yang baik berisi poin utama, desain yang tidak berlebihan, dan alur yang mudah dipahami audiens.', NULL, 45, 3, NULL, NULL),
(57, 22, 'Apa Itu AI?', NULL, 'text', 'AI atau kecerdasan buatan adalah teknologi yang mampu memproses pola data dan membantu manusia dalam tugas tertentu. AI dapat membantu menulis, menganalisis data, dan mengotomatiskan pekerjaan sederhana.', NULL, 40, 1, NULL, NULL),
(58, 22, 'AI untuk Produktivitas', NULL, 'text', 'AI dapat membantu menulis email, membuat ringkasan, menganalisis data, dan menyusun ide. Penggunaan AI yang benar akan mempercepat pekerjaan, tetapi tetap perlu cek hasilnya agar tetap akurat dan relevan.', NULL, 40, 2, NULL, NULL),
(59, 22, 'Etika AI & Verifikasi', NULL, 'text', 'Penggunaan AI harus memperhatikan etika, privasi, dan tanggung jawab. Jangan langsung percaya seluruh hasil AI; selalu cek fakta, hindari plagiarisme, dan gunakan AI sebagai alat bantu, bukan pengganti berpikir kritis.', NULL, 45, 3, NULL, NULL),
(60, 23, 'Dasar Personal Branding', NULL, 'text', 'Personal branding adalah cara seseorang menampilkan kemampuan, nilai, dan citra profesionalnya di dunia kerja. Branding yang kuat membuat calon perusahaan atau klien lebih mudah mengenali keunggulanmu.', NULL, 40, 1, NULL, NULL),
(61, 23, 'Profil Digital & LinkedIn', NULL, 'text', 'LinkedIn dan platform digital lain menjadi tempat untuk menampilkan pengalaman kerja, proyek, dan kemampuan. Profil yang rapi, jelas, dan konsisten akan meningkatkan kredibilitasmu di mata rekruter atau mitra bisnis.', NULL, 40, 2, NULL, NULL),
(62, 23, 'Portofolio dan Komunikasi Profesional', NULL, 'text', 'Portofolio membantu menunjukkan hasil kerja nyata. Selain itu, komunikasi profesional seperti email, presentasi, dan pesan kerja harus jelas, sopan, dan terstruktur agar hubungan kerja lebih efektif.', NULL, 45, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('app_name', 'CodingGo'),
('contact_email', 'support@codinggo.com'),
('enable_registration', '0'),
('enable_registration_google', '1'),
('enable_registration_manual', '1'),
('maintenance_mode', '1');

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
  `allowed_categories` varchar(255) DEFAULT NULL,
  `weekly_target` int DEFAULT '600',
  `banner_gif` varchar(255) DEFAULT NULL,
  `avatar_frame_id` int DEFAULT NULL,
  `name_effect_id` int DEFAULT NULL,
  `profile_effect_id` int DEFAULT NULL,
  `card_border_id` int DEFAULT NULL,
  `card_background_id` int DEFAULT NULL,
  `cursor_effect_id` int DEFAULT NULL,
  `badge_effect_id` int DEFAULT NULL,
  `entrance_anim_id` int DEFAULT NULL,
  `custom_status` varchar(100) DEFAULT NULL,
  `status_emoji` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `google_id`, `name`, `email`, `password`, `picture`, `birth_date`, `category`, `role`, `xp_points`, `streak_days`, `total_badges`, `profile_title`, `profile_color`, `last_login`, `created_at`, `allowed_categories`, `weekly_target`, `banner_gif`, `avatar_frame_id`, `name_effect_id`, `profile_effect_id`, `card_border_id`, `card_background_id`, `cursor_effect_id`, `badge_effect_id`, `entrance_anim_id`, `custom_status`, `status_emoji`) VALUES
(1, '116833722689297559750', 'kepo kamu', 'dedynurohim1@gmail.com', '$2y$10$I7gdle7KAi3Q0QAnIenC/u8OhmS87UAwehcOsEHm5V7PnTeh/Bu9G', 'https://lh3.googleusercontent.com/a/ACg8ocIzr8abXJSF_1FALUV5-K3Pq6eqQzFyMsq9mw6-zOd-FY6hrHA=s96-c', '2026-08-07', 'Umum', 'admin', 620, 0, 22, '', '#4361ee', '2026-08-17 02:11:06', '2026-08-06 18:12:17', NULL, 660, 'https://assets-v2.lottiefiles.com/a/ed5ae48c-117c-11ee-afee-879cb97bcc98/HdLCGkInQ3.mp4', NULL, NULL, 14, 15, 17, 21, 22, 26, '', ''),
(2, '105942729897026664092', 'Dedy Nurohim', 'dedynurohim01@gmail.com', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocLfmp_oKJZEhvS5waZz-oOwYbiRrY4K7ryGmDVDLRw5MxIzlQ=s96-c', '2000-08-16', 'Umum', 'user', 0, 0, 0, 'Novice Coder', '#4361ee', '2026-08-17 02:25:02', '2026-08-07 09:25:09', 'SD', 600, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, '114067548867849386620', 'Moh Rafie Nazar J', 'mohrafienazarjailani@gmail.com', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJ6Lx9xkvja0A62lamsxui__Axpbyk1jVvr3IsBFY_zY9e7eGlq=s96-c', '2005-09-11', 'Umum', 'user', 100, 0, 2, 'Novice Coder', '#4361ee', '2026-08-09 16:04:03', '2026-08-08 12:42:28', NULL, 600, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_badges`
--

CREATE TABLE `user_badges` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `badge_id` int NOT NULL,
  `earned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_badges`
--

INSERT INTO `user_badges` (`id`, `user_id`, `badge_id`, `earned_at`) VALUES
(1, 3, 1, '2026-08-10 05:27:11'),
(2, 3, 2, '2026-08-10 05:40:54'),
(4, 1, 1, '2026-08-15 07:15:11'),
(5, 1, 2, '2026-08-15 07:15:11'),
(6, 1, 3, '2026-08-15 07:15:11'),
(7, 1, 4, '2026-08-15 07:15:11'),
(8, 1, 5, '2026-08-15 07:15:11'),
(9, 1, 6, '2026-08-15 07:15:11'),
(10, 1, 7, '2026-08-15 07:15:11'),
(11, 1, 8, '2026-08-15 07:15:11'),
(12, 1, 9, '2026-08-15 07:15:11'),
(13, 1, 10, '2026-08-15 07:15:11'),
(14, 1, 11, '2026-08-15 07:15:11'),
(15, 1, 12, '2026-08-15 07:15:11'),
(16, 1, 13, '2026-08-15 07:15:11'),
(17, 1, 14, '2026-08-15 07:15:11'),
(18, 1, 15, '2026-08-15 07:15:11'),
(19, 1, 16, '2026-08-15 07:15:11'),
(20, 1, 17, '2026-08-15 07:15:11'),
(21, 1, 18, '2026-08-15 07:15:11'),
(22, 1, 19, '2026-08-15 07:15:11'),
(23, 1, 20, '2026-08-15 07:15:11'),
(24, 1, 21, '2026-08-15 07:15:11'),
(25, 1, 22, '2026-08-15 07:15:11');

-- --------------------------------------------------------

--
-- Table structure for table `user_learning_time`
--

CREATE TABLE `user_learning_time` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `log_date` date NOT NULL,
  `time_spent` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `course_id` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_learning_time`
--

INSERT INTO `user_learning_time` (`id`, `user_id`, `log_date`, `time_spent`, `created_at`, `course_id`) VALUES
(1, 2, '2026-08-04', 6000, '2026-08-10 14:27:19', 7),
(2, 2, '2026-08-05', 4620, '2026-08-10 14:27:19', 6),
(3, 2, '2026-08-06', 4440, '2026-08-10 14:27:19', 8),
(4, 2, '2026-08-07', 2700, '2026-08-10 14:27:19', 8),
(5, 2, '2026-08-08', 3540, '2026-08-10 14:27:19', 7),
(6, 2, '2026-08-09', 1620, '2026-08-10 14:27:19', 5),
(7, 2, '2026-08-10', 6780, '2026-08-10 14:27:19', 8),
(8, 1, '2026-08-04', 1320, '2026-08-10 14:27:19', 8),
(9, 1, '2026-08-05', 1080, '2026-08-10 14:27:19', 8),
(10, 1, '2026-08-06', 5520, '2026-08-10 14:27:19', 7),
(11, 1, '2026-08-07', 1980, '2026-08-10 14:27:19', 8),
(12, 1, '2026-08-08', 4620, '2026-08-10 14:27:19', 4),
(13, 1, '2026-08-09', 3600, '2026-08-10 14:27:19', 7),
(14, 1, '2026-08-10', 3840, '2026-08-10 14:27:19', 4),
(15, 3, '2026-08-04', 3180, '2026-08-10 14:27:19', 8),
(16, 3, '2026-08-05', 3480, '2026-08-10 14:27:19', 7),
(17, 3, '2026-08-06', 3660, '2026-08-10 14:27:19', 4),
(18, 3, '2026-08-07', 5880, '2026-08-10 14:27:19', 5),
(19, 3, '2026-08-08', 7080, '2026-08-10 14:27:19', 8),
(20, 3, '2026-08-09', 3840, '2026-08-10 14:27:19', 8),
(21, 3, '2026-08-10', 6660, '2026-08-10 14:27:19', 4),
(22, 1, '2026-08-11', 540, '2026-08-11 10:08:05', 6),
(23, 1, '2026-08-13', 1020, '2026-08-13 13:39:34', 4),
(24, 1, '2026-08-13', 3030, '2026-08-13 21:39:06', 0),
(25, 1, '2026-08-13', 30, '2026-08-13 22:15:25', 11),
(26, 1, '2026-08-13', 10, '2026-08-13 22:53:47', 7),
(27, 1, '2026-08-13', 320, '2026-08-13 23:01:31', 8),
(28, 1, '2026-08-14', 120, '2026-08-14 17:45:23', 0),
(29, 1, '2026-08-15', 3360, '2026-08-15 13:12:36', 0),
(30, 1, '2026-08-16', 440, '2026-08-17 02:11:48', 0),
(31, 2, '2026-08-16', 20, '2026-08-17 02:25:26', 0);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(9, 1, 3, 'completed', '2026-08-13 14:01:45'),
(10, 1, 4, 'completed', '2026-08-13 14:05:02'),
(11, 1, 5, 'completed', '2026-08-13 14:32:41'),
(12, 1, 18, 'started', NULL),
(13, 1, 19, 'started', NULL),
(14, 1, 21, 'started', NULL),
(15, 1, 6, 'completed', '2026-08-13 14:30:55'),
(16, 1, 7, 'started', NULL),
(17, 1, 12, 'completed', '2026-08-13 21:54:31'),
(18, 1, 13, 'completed', '2026-08-13 21:54:34'),
(19, 1, 14, 'completed', '2026-08-13 21:54:34'),
(20, 1, 15, 'completed', '2026-08-13 21:55:06'),
(21, 1, 16, 'completed', '2026-08-13 21:55:08'),
(22, 1, 17, 'completed', '2026-08-13 21:57:49'),
(23, 1, 39, 'completed', '2026-08-13 22:00:29'),
(24, 1, 40, 'completed', '2026-08-13 22:00:30'),
(25, 1, 41, 'completed', '2026-08-13 22:00:32'),
(26, 1, 24, 'completed', '2026-08-13 22:06:25'),
(27, 1, 25, 'completed', '2026-08-13 22:06:27'),
(28, 1, 26, 'completed', '2026-08-13 22:06:29');

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
-- Indexes for table `course_ratings`
--
ALTER TABLE `course_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_user_unique` (`course_id`,`user_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `forum_votes`
--
ALTER TABLE `forum_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_vote` (`target_type`,`target_id`,`user_id`);

--
-- Indexes for table `gamification_perks`
--
ALTER TABLE `gamification_perks`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_card_border` (`card_border_id`),
  ADD KEY `fk_user_card_bg` (`card_background_id`),
  ADD KEY `fk_user_cursor` (`cursor_effect_id`),
  ADD KEY `fk_user_badge_eff` (`badge_effect_id`),
  ADD KEY `fk_user_entrance` (`entrance_anim_id`);

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
  ADD UNIQUE KEY `idx_unique_time` (`user_id`,`log_date`,`course_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `championships`
--
ALTER TABLE `championships`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `championship_challenges`
--
ALTER TABLE `championship_challenges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `course_ratings`
--
ALTER TABLE `course_ratings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `exam_questions`
--
ALTER TABLE `exam_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_votes`
--
ALTER TABLE `forum_votes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gamification_perks`
--
ALTER TABLE `gamification_perks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_badges`
--
ALTER TABLE `user_badges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `user_learning_time`
--
ALTER TABLE `user_learning_time`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_badge_eff` FOREIGN KEY (`badge_effect_id`) REFERENCES `gamification_perks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_card_bg` FOREIGN KEY (`card_background_id`) REFERENCES `gamification_perks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_card_border` FOREIGN KEY (`card_border_id`) REFERENCES `gamification_perks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_cursor` FOREIGN KEY (`cursor_effect_id`) REFERENCES `gamification_perks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_entrance` FOREIGN KEY (`entrance_anim_id`) REFERENCES `gamification_perks` (`id`) ON DELETE SET NULL;

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
