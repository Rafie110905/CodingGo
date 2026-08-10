#  CodingGo — Platform Belajar Coding yang Aman & Seru untuk Semua Kalangan

> Kompetisi Web Development — FTI Festival 2026 (PIXEL: Protection Information Exploration in the Digital Era)
> Subtema: **Platform Pembelajaran Digital yang Aman dan Inovatif**

---

##  Deskripsi Singkat

**CodingGo** adalah platform edukasi IT dasar yang bisa diakses **semua kalangan usia** — dari jenjang SD, SMP, SMA, hingga umum — untuk mengenal dunia teknologi digital secara bertahap. Materi mencakup literasi digital, logika komputer, dasar coding, hingga pengenalan Microsoft Office, dikemas dalam bentuk kursus interaktif berbasis web lengkap dengan sistem XP, badge, ujian, dan sertifikat.

Cakupan materi meliputi:
- Pengenalan dasar komputer & internet.
- Literasi digital & keamanan siber dasar.
- Logika komputasi (computational thinking).
- Dasar coding (quiz & code submission).
- Pengenalan Microsoft Office.

Yang membedakan CodingGo dari platform sejenis:
- **100% berbasis web** — cukup dibuka lewat browser, tidak butuh instalasi aplikasi atau perangkat fisik tambahan (robot, kit elektronik, dsb).
- **Materi berjenjang otomatis** — sistem mengunci akses materi berdasarkan kategori usia (SD/SMP/SMA/Umum), dihitung otomatis dari tanggal lahir pengguna saat mendaftar.
- **Login aman** — mendukung login manual maupun **Google Sign-In (OAuth)** yang diverifikasi langsung ke endpoint resmi Google.
- **Gamifikasi pembelajaran** — sistem XP, streak harian, badge pencapaian, leaderboard, dan sertifikat digital di setiap penyelesaian kursus.
- **Forum diskusi (community)** — peserta bisa saling tanya-jawab dan membantu satu sama lain di dalam platform.

##  Tim

| Nama | Peran | GitHub |
|---|---|---|
| Moh Rafiie Nazar J | Project Lead / Project Manager | Rafie110905 |
| Dedy Nurohim | Frontend Developer | dy-nm |
| Rian Renaldy | UI/UX Desainer | al-renaldy073 |

##  Teknologi (Tech Stack)

| Layer | Teknologi |
|---|---|
| Bahasa Utama | PHP (native, tanpa framework) |
| Frontend | HTML5 + CSS3 (custom, per halaman: `auth.css`, `dashboard.css`, `index.css`) |
| Database | MySQL (`codinggo_db`), dengan PDO sebagai lapisan koneksi |
| Autentikasi | Login manual (session PHP) + Google Identity Services (OAuth 2.0) |
| Server Lokal | XAMPP / Laragon / server PHP + MySQL sejenis |

##  Struktur Folder

```
CodingGo/
├── config/
│   └── db.php                # Konfigurasi koneksi database (PDO)
├── includes/
│   ├── auth_helpers.php       # Logika kategori usia & hak akses materi
│   ├── head.php / footer.php  # Komponen HTML yang dipakai berulang
│   ├── nav_public.php
│   ├── sidebar.php / topbar.php
│   └── materi_icons.php
├── pages/
│   ├── landing.php            # Halaman utama
│   ├── dashboard.php          # Dashboard peserta
│   ├── course_list.php / course_detail.php / course_learn.php / course_exam.php
│   ├── leaderboard.php / champions.php
│   ├── certificate_view.php / sertifikat.php
│   ├── community.php / community_post.php / community_edit.php
│   ├── user_exams.php / user_settings.php / setup_profile.php
│   └── admin_*.php            # Panel admin (kelola user, kursus, modul, soal ujian, pengaturan)
├── src/
│   ├── css/                   # Stylesheet per halaman
│   └── img/                   # Aset gambar
├── index.php                  # Entry point
├── login.php / register.php / logout.php
├── google_auth.php            # Handler login Google OAuth
├── codinggo_db.sql            # Skema database utama
└── database.sql               # Skema database (versi alternatif/setup)
```

##  Cara Menjalankan Website (Local Setup)

### Prasyarat
- PHP 8.x
- MySQL / MariaDB
- Web server lokal (disarankan **XAMPP** atau **Laragon**)

### Langkah instalasi

```bash
# 1. Clone repository
git clone https://github.com/username/CodingGo.git

# 2. Pindahkan folder project ke direktori server lokal
#    contoh untuk XAMPP: C:\xampp\htdocs\CodingGo

# 3. Buat database baru bernama "codinggo_db" lewat phpMyAdmin

# 4. Import skema database
#    phpMyAdmin -> pilih database "codinggo_db" -> Import -> pilih file codinggo_db.sql

# 5. Sesuaikan koneksi database jika perlu, di config/db.php
#    $host = 'localhost';
#    $dbname = 'codinggo_db';
#    $username = 'root';
#    $password = '';

# 6. Jalankan Apache & MySQL lewat XAMPP Control Panel

# 7. Buka di browser
http://localhost/CodingGo/
```

### Konfigurasi Login Google (opsional)

Untuk mengaktifkan fitur **Google Sign-In**, daftarkan **Client ID** di [Google Cloud Console](https://console.cloud.google.com/) lalu sesuaikan pada bagian script Google Identity Services di `login.php` / `register.php`.

##  Akun Demo (untuk Juri)

| Role | Email | Password |
|---|---|---|
| Admin | demo.admin@codinggo.id | Demo1234! |
| User (kategori Umum) | demo.user@codinggo.id | Demo1234! |

> Kredensial di atas hanya untuk keperluan demo/penilaian juri. Sesuaikan dengan akun yang benar-benar dibuat pada database kalian sebelum submit.

##  Fitur Keamanan yang Diimplementasikan

- **Prepared statement (PDO)** di seluruh query database — mencegah SQL Injection.
- **Password hashing** menggunakan `password_hash()` bawaan PHP saat pengguna mengganti password.
- **Verifikasi token Google OAuth** langsung ke endpoint resmi `oauth2.googleapis.com`, bukan validasi manual di sisi klien.
- **Kontrol akses berbasis kategori usia** — pengguna hanya bisa membuka materi sesuai jenjang (SD/SMP/SMA/Umum) yang dihitung otomatis dari tanggal lahir (`calculateAge()`), dengan opsi override manual oleh admin.
- **Role-based access control** — pemisahan hak akses `user` dan `admin` di seluruh halaman panel admin.
- **Session-based authentication** untuk menjaga status login pengguna.

##  Dokumentasi Pendukung

Struktur database lengkap (kursus, materi, ujian, badge, sertifikat, forum) dapat dilihat di `codinggo_db.sql` / `database.sql`.

##  Lisensi & Kredit

Proyek ini dibuat untuk keperluan Kompetisi Web Development FTI Festival 2026.

##  Kontribusi Tim

Panduan lengkap cara kolaborasi ada di [`CONTRIBUTING.md`](./CONTRIBUTING.md).
