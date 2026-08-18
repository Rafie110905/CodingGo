# ✨ Fitur Unggulan Sistem

Aplikasi **CodingGo** tidak sekadar menyajikan materi pelajaran statis, tetapi dirancang secara khusus untuk meningkatkan *engagement* pengguna melalui elemen-elemen permainan (*gamification*) dan interaktivitas tingkat lanjut.

## 1. Gamification Engine (Mesin Gamifikasi)
- **XP Points & Levels**: *User* secara otomatis akan mendapatkan *Experience Points* dari setiap penyelesaian tugas, partisipasi materi, dan jawaban kuis yang benar.
- **Badges (Lencana)**: Sistem secara otomatis memberikan *Badge* pencapaian (contoh: "Pemula HTML", "Ahli Logika") berdasarkan aktivitas.
- **Daily Streak**: Memonitor dan menghitung hari-hari login beruntun pengguna untuk meningkatkan retensi.
- **Leaderboard**: Menampilkan peringkat siswa secara *real-time* berdasarkan perolehan XP mereka.

## 2. Kustomisasi Profil Dinamis (Dynamic Profile)
Sistem profil modular di mana pengguna dapat mengkustomisasi kartu nama mereka.
- Pengguna harus meraih sejumlah *Badge* tertentu (contoh: 10 Badge) untuk dapat membuka fitur kustomisasi (*Unlock*).
- **Banner GIF / Video**: Pengguna dapat memasang animasi `.gif` atau video `.mp4` pendek sebagai latar belakang *header* profil mereka.
- **Efek CSS Imersif**: Sistem memungkinkan injeksi *Class CSS* secara dinamis, seperti efek jatuhnya *salju*, efek layar *Matrix*, atau kilauan *Rainbow* yang melapisi keseluruhan antarmuka profil, semua diatur berdasarkan referensi di tabel `gamification_perks`.

## 3. Global Broadcast System (Sistem Pengumuman)
Panel pengumuman *pop-up* asinkron tingkat admin.
- Admin dapat mengirimkan pengumuman penting (*Maintenance*, *Pemberitahuan Lomba*) yang akan langsung mencegat *user* di layar mana pun mereka berada (*Dashboard*, *Leaderboard*, dll).
- **Mode Sekali Saja (Default)**: Jika pengguna mengeklik tombol "Mengerti", sistem menggunakan API (`Fetch AJAX`) ke `api/broadcast_read.php` untuk mencatat bahwa *user* tersebut sudah membacanya. Setelah itu, *pop-up* tidak akan muncul lagi di hari-hari berikutnya.
- **Mode Selalu Tampil (Emergency)**: Opsi paksa di mana *pop-up* akan *selalu* muncul setiap kali memuat halaman, sampai Admin mematikannya dari panel kontrol.
- **Desain Modern**: *Pop-up* ini memiliki elemen *Glassmorphism* (blur), bayangan lembut, warna tajam berdasarkan tipe pesan (Info/Sukses/Peringatan), dan animasi masuk 3D/Zoom yang elegan.

## 4. Theme Switcher (Mode Terang / Gelap)
- Menerapkan *Theming* via **CSS Variables**.
- Sistem *Dark / Light Mode* bersifat reaktif, dilengkapi persistensi penyimpanan berbasis `localStorage` di sisi klien (browser) untuk mengingat tema favorit mereka di kunjungan berikutnya.
