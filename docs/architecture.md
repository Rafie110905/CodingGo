# 🏛 Arsitektur Sistem

## 1. Konsep Utama
CodingGo beroperasi menggunakan pendekatan **Native/Vanilla Stack** yang monolitik (*Monolithic Architecture*). Hal ini menjamin performa *rendering* halaman yang sangat cepat, *footprint* server yang ringan, dan kemudahan proses integrasi/deployment di ekosistem *shared-hosting* konvensional tanpa kebergantungan *Composer* atau *Node Modules*.

## 2. Diagram Alur (Request Flow)
Semua *request* ke halaman difokuskan ke `index.php` melalui *query parameter* `?page=`.

```mermaid
graph TD
    Client[Browser] -->|GET / POST| Index[index.php]
    Index -->|Auth Check| Includes[includes/ (header, sidebar)]
    Index -->|Route Match| Pages[pages/ (dashboard, admin_users)]
    Pages -->|Database Query| Config[config/db.php]
    Client -->|AJAX Fetch| API[api/ (broadcast_read.php)]
    API -->|Database Query| Config
```

## 3. Pola Desain (Design Patterns)
- **Front Controller Pattern (Mini)**: `index.php` bertindak sebagai pengontrol pusat yang mencegat semua *request* untuk memvalidasi sesi, memuat konfigurasi, dan menentukan tata letak (*layout*) sebelum halaman dimuat.
- **PRG (Post/Redirect/Get)**: Digunakan di seluruh *form* pengiriman (seperti *management broadcast*) untuk mencegah duplikasi pengiriman data (*form resubmission*) saat *refresh* halaman.

## 4. Struktur Direktori

Kode diatur dengan memisahkan *routing*, komponen UI, dan layanan API internal.

```text
CodingGo/
├── index.php                 # Core Router & Main Entry Point
├── login.php / register.php  # Public Authentication Gateway
├── config/
│   └── db.php                # PDO Connection & Schema Bootstrapper
├── includes/
│   ├── head.php              # Document <head>, SEO Meta, Fonts
│   ├── sidebar.php           # Global Navigation Menu
│   ├── topbar.php            # Contextual Header & Theme Toggler
│   └── footer_dash.php       # Modals (Broadcast/Profile) & Global Scripts
├── pages/
│   ├── dashboard.php         # Main User Interface
│   ├── admin_broadcast.php   # Administrative Tools
│   ├── statistics.php        # Gamification Analytics & Leaderboard
│   └── ... 
├── api/
│   ├── broadcast_read.php    # AJAX Handler: Broadcast acknowledgment
│   └── track_time.php        # AJAX Handler: User time tracking metric
└── docs/                     # Sistem Dokumentasi Proyek
```
