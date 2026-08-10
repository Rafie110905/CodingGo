#  CodingGo — Platform Belajar Coding yang Aman & Seru untuk semua kalangan

> Kompetisi Web Development — FTI Festival 2026 (PIXEL: Protection Information Exploration in the Digital Era)
> Subtema: Platform Pembelajaran Digital yang Aman dan Inovatif

---

##  Deskripsi Singkat

CodingGo adalah platform edukasi IT dasar untuk semua kalangan usia yang ingin mengenal dunia teknologi digital sejak dini — mencakup literasi digital, dasar-dasar logika komputer, dan pengenalan coding sederhana — semuanya dikemas dalam bentuk game interaktif berbasis web, tanpa memerlukan perangkat atau hardware tambahan apa pun.

Cakupan materi meliputi:
- Pengenalan dasar komputer & internet (apa itu perangkat digital, cara kerja internet secara sederhana).
- Literasi digital & keamanan siber dasar (mengenali informasi palsu, memahami pentingnya menjaga data pribadi).
- Logika komputasi (computational thinking) lewat puzzle sederhana.
- Pengenalan coding dasar berbasis blok (opsional, sebagai materi lanjutan).
- Pengenalan Microsoft Office

Yang membedakan Kodinara dari platform sejenis:
- 100% berbasis web — cukup dibuka lewat browser di laptop/tablet sekolah, tidak butuh instalasi aplikasi atau perangkat fisik (robot, kit elektronik, dsb).
- Aman untuk anak — pendaftaran dilakukan lewat akun orang tua (parental gate), tanpa iklan pihak ketiga, dan data anak tidak dibagikan ke pihak luar.
- Validasi usia otomatis — sistem memverifikasi bahwa pengguna benar-benar berada di rentang usia SD (6–12 tahun) berdasarkan tanggal lahir yang diinput orang tua.
- Materi bertahap — dimulai dari literasi IT paling dasar sebelum masuk ke logika coding, cocok untuk anak yang benar-benar baru mengenal teknologi.

##  Tim

| Nama | Peran | GitHub |
|---|---|---|
| Moh Rafiie Nazar J | Project Lead / Project Manager | @username |
| Dedy Nurohim | Frontend Developer | @username |
| Rian Renaldy | UI/UX Desainer | al-renaldy073 |

##  Teknologi (Tech Stack)

| Layer | Teknologi |
|---|---|
| Frontend | React.js + Tailwind CSS |
| Backend | Node.js + Express.js |
| Database | MongoDB |
| Autentikasi | JWT + bcrypt (hashing password) |
| Deployment | Vercel (frontend) / Railway atau Render (backend) |
| Lainnya | Zod/Joi (validasi form), Docker (opsional untuk sandbox run-code) |

##  Struktur Folder

\```
kodinara/
├── client/                 # Frontend (React)
│   ├── src/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── assets/
│   │   └── App.jsx
│   └── package.json
├── server/                  # Backend (Node/Express)
│   ├── src/
│   │   ├── controllers/
│   │   ├── models/
│   │   ├── routes/
│   │   ├── middlewares/
│   │   └── index.js
│   └── package.json
├── docs/                    # Wireframe, flowchart, dokumentasi API
├── .env.example
├── .gitignore
└── README.md
\```

##  Cara Menjalankan Website (Local Setup)



Aplikasi akan berjalan di:

### Variabel Environment (.env)



##  Akun Demo (untuk Juri)

| Role | Email | Password |
|---|---|---|
| Orang Tua | demo.ortu@kodinara.id | Demo1234! |
| Anak (Profil) | dibuat otomatis setelah login orang tua | — |
| Admin | demo.admin@kodinara.id | Demo1234! |

##  Fitur Keamanan yang Diimplementasikan

- Hashing password dengan bcrypt.
- Validasi input di sisi client dan server.
- Rate limiting pada endpoint login.
- Role-based access control (orang tua, anak, admin).
- Sandbox terisolasi untuk eksekusi kode buatan anak.
- Tidak menyimpan data sensitif anak tanpa persetujuan orang tua.

##  Dokumentasi Pendukung

Lihat folder /docs untuk wireframe, user flow, dan dokumentasi API.

##  Lisensi & Kredit

Proyek ini dibuat untuk keperluan Kompetisi Web Development FTI Festival 2026.

## 🤝 Kontribusi Tim

Panduan lengkap cara kolaborasi ada di CONTRIBUTING.md.
