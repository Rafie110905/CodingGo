# 🔒 Keamanan Aplikasi

Meskipun **CodingGo** menggunakan arsitektur Vanilla PHP dan menghindari tumpukan *framework* modern, aspek keamanan berlapis telah menjadi prioritas sejak desain awal.

## 1. SQL Injection Prevention
> [!IMPORTANT]
> **TIDAK ADA** satupun titik injeksi *query* SQL (*String Concatenation*) di dalam aplikasi ini.
> Semua operasi basis data (SELECT, INSERT, UPDATE, DELETE) yang melibatkan masukan (input) dari pihak eksternal, sepenuhnya dijalankan menggunakan metode **Prepared Statements** dan **Parameterized Queries** dari PDO.

## 2. XSS (Cross-Site Scripting) Protection
Setiap kali data diambil dari *database* dan dicetak/dimuat (*render*) ke elemen HTML untuk dibaca *user*, data tersebut wajib melewati fungsi `htmlspecialchars()`. Fungsi ini menjinakkan *tag* HTML, *script* JS, maupun karakter berbahaya agar tidak dieksekusi oleh *browser*.

## 3. Manajemen Kredensial
- Kata sandi *user* dan *admin* tidak pernah disimpan dalam bentuk teks biasa (Plain-Text).
- Kredensial disimpan dalam struktur *hash* kriptografi kuat (*salted*) menggunakan algoritma `BCRYPT`, dienkripsi oleh fungsi `password_hash()` dan diverifikasi melalui `password_verify()`.

## 4. Otorisasi Peran Berjenjang (Role Authorization)
Semua halaman diblokir secara bawaan jika tidak memiliki sesi.
- File di direktori administratif (seperti `admin_broadcast.php`, `admin_users.php`, dll) memiliki gerbang (*gate*) statis di baris pertamanya:
  ```php
  if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
      exit("Akses Ditolak");
  }
  ```
Ini memastikan eksploitasi URL (*Direct File Access*) sangat tidak mungkin dilakukan oleh *user* reguler.
