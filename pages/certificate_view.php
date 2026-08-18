<?php
require_once 'config/db.php';

$code = $_GET['code'] ?? '';
$stmt = $pdo->prepare("SELECT cert.*, c.title as course_title, c.category, u.name as user_name
                       FROM certificates cert
                       JOIN courses c ON cert.course_id = c.id
                       JOIN users u ON cert.user_id = u.id
                       WHERE cert.certificate_code = ?");
$stmt->execute([$code]);
$cert = $stmt->fetch();

if ($cert) {
    // Format tanggal Indonesia
    $hari = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
    $bulan = ['January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April','May'=>'Mei','June'=>'Juni','July'=>'Juli','August'=>'Agustus','September'=>'September','October'=>'Oktober','November'=>'November','December'=>'Desember'];
    $tanggalFormatted = $hari[date('l', strtotime($cert['issued_at']))] . ', ' . date('d', strtotime($cert['issued_at'])) . ' ' . $bulan[date('F', strtotime($cert['issued_at']))] . ' ' . date('Y', strtotime($cert['issued_at']));

    $jenjangDisplay = 'Jenjang ' . ($cert['category'] ?? 'Umum');
}
?>
<div id="certificate-page-root" style="min-height: 80vh; display:flex; align-items:center; justify-content:center; padding: 2rem 1rem; background: var(--bg-main, #f8fafc);">
    <?php if (!$cert): ?>
        <div style="text-align:center;">
            <h2 style="color: var(--text-main, #0f172a);">Sertifikat tidak ditemukan</h2>
            <p style="color: var(--text-muted, #64748b);">Kode sertifikat tidak valid atau sudah tidak berlaku.</p>
        </div>
    <?php else: ?>
        <div class="cert-wrap">
            <div class="certificate" id="cert-box">

                <!-- NOMOR SERTIFIKAT -->
                <div class="cert-number">No. <?php echo htmlspecialchars($cert['certificate_code']); ?></div>

                <!-- NAMA -->
                <div class="recipient-name"><?php echo htmlspecialchars($cert['user_name']); ?></div>

                <!-- KELAS -->
                <div class="class-name"><?php echo htmlspecialchars($cert['course_title']); ?></div>

                <!-- JENJANG -->
                <div class="field-jenjang"><?php echo htmlspecialchars($jenjangDisplay); ?></div>

                <!-- TANGGAL -->
                <div class="field-tanggal"><?php echo $tanggalFormatted; ?></div>

            </div>
        </div>

        <div class="no-print" style="text-align:center; margin-top:1.5rem;">
            <button onclick="window.print()" style="background:#4361ee; color:#fff; border:none; padding:0.85rem 2rem; border-radius:999px; font-weight:600; cursor:pointer;">🖨️ Cetak / Simpan sebagai PDF</button>
        </div>

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap');

            /*
             * Font "The Youngest" dipakai untuk No. Sertifikat, Jenjang & Tanggal.
             * Sesuaikan path src di bawah dengan lokasi file font asli kamu.
             * Selama file font belum ada, otomatis fallback ke Georgia/serif.
             */
            @font-face {
                font-family: 'The Youngest';
                src: url('src/fonts/TheYoungest.woff2') format('woff2'),
                     url('src/fonts/TheYoungest.otf') format('opentype');
                font-display: swap;
            }

            .cert-wrap {
                position: relative;
                width: min(100%, 900px);
                margin: 0 auto;
                padding: 0.75rem;
            }

            /* ===== SERTIFIKAT DENGAN BACKGROUND GAMBAR (FRAME) ===== */
            /* Elemen statis (judul SERTIFIKAT, PENGHARGAAN, logo, "Diberikan
               Kepada :", deskripsi, garis ornamen, tanda tangan) sudah menyatu
               di dalam sertifikat-frame.png. Di sini hanya 5 teks dinamis yang
               ditaruh otomatis: No. Sertifikat, Nama, Kelas, Jenjang, Tanggal. */
            .certificate {
                position: relative;
                width: 100%;
                aspect-ratio: 1.414 / 1;
                background-image: url('src/img/sertifikat-frame.png');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                border-radius: 12px;
                box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
                overflow: hidden;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                /* PENTING: jadikan lebar .certificate sendiri sebagai acuan
                   skala (Container Query Width / cqw). Dengan ini teks di
                   dalamnya SELALU proporsional terhadap frame — baik saat di
                   layar sempit, layar lebar, maupun saat dicetak (di mana
                   lebar kotak berubah jadi seukuran kertas A4). Ini yang
                   memperbaiki bug "tulisan mengecil pas print": px tetap
                   tidak ikut membesar saat kotak membesar, cqw ikut. */
                container-type: inline-size;
            }

            /* ===== NOMOR SERTIFIKAT ===== */
            .cert-number {
                position: absolute;
                top: 30.8%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
                font-family: 'The Youngest', 'Georgia', 'Times New Roman', serif;
                font-size: 15px; /* fallback browser lama tanpa cqw */
                font-size: 1.667cqw;
                letter-spacing: 1px;
                color: #1a335f;
                text-align: center;
                white-space: nowrap;
                z-index: 2;
            }

            /* ===== NAMA ===== */
            .recipient-name {
                position: absolute;
                font-weight: bold;
                top: 43.3%;
                left: 50%;
                    /* outline sangat tipis */
            text-shadow:
                0.3px 0 #000,
                -0.3px 0 #000,
                0 0.3px #000,
                0 -0.3px #000;
                transform: translate(-50%, -50%);
                width: 92%;
                font-family: 'Pinyon Script', cursive;
                font-size: 50.9px; /* fallback browser lama tanpa cqw */
                font-size: 5.656cqw;
                color: #c9a227;
                line-height: 1.1;
                text-align: center;
                letter-spacing: 1px;
                word-break: break-word;
                z-index: 2;
            }

            /* ===== KELAS ===== */
            .class-name {
                font-weight: bold;
                position: absolute;
                top: 58.6%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 88%;
                font-family: 'Pinyon Script', cursive;
                font-size: 28px; /* fallback browser lama tanpa cqw */
                font-size: 3.111cqw;
                color: #16305c;
                text-align: center;
                letter-spacing: 0.5px;
                line-height: 1.1;
                z-index: 2;
            }

            /* ===== JENJANG ===== */
            .field-jenjang {
                position: absolute;
                top: 64.1%;
                left: 50.0%;
                transform: translate(-50%, -50%);
                font-family: 'The Youngest', 'Georgia', 'Times New Roman', serif;
                font-size: 15px; /* fallback browser lama tanpa cqw */
                font-size: 1.667cqw;
                color: #1a335f;
                white-space: nowrap;
                z-index: 2;
            }

            /* ===== TANGGAL ===== */
            .field-tanggal {
                position: absolute;
                top: 64%;
                left: 72%;
                transform: translate(-50%, -50%);
                font-family: 'The Youngest', 'Georgia', 'Times New Roman', serif;
                font-size: 15px; /* fallback browser lama tanpa cqw */
                font-size: 1.667cqw;
                color: #1a335f;
                white-space: nowrap;
                z-index: 2;
            }

            /* Catatan: breakpoint font-size manual (max-width 768px/480px)
               sudah tidak dipakai lagi — cqw di atas otomatis fluid mengikuti
               lebar kotak .certificate di SEMUA ukuran layar, jadi tidak
               perlu step manual lagi dan tidak akan konflik saat print. */

            /* ===== CETAK / PDF ===== */
            @page {
                size: A4 landscape;
                margin: 0;
            }

            @media print {
                * {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }

                body > *:not(#certificate-page-root) {
                    display: none !important;
                }

                .no-print { display: none !important; }

                html, body {
                    margin: 0 !important;
                    padding: 0 !important;
                    background: #fff !important;
                }

                #certificate-page-root {
                    display: block !important;
                    min-height: auto !important;
                    padding: 0 !important;
                    background: #fff !important;
                }

                .cert-wrap {
                    width: 100%;
                    max-width: none;
                    padding: 0;
                    margin: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .certificate {
                    width: 100vw;
                    height: 100vh;
                    max-width: 100%;
                    aspect-ratio: 1.414 / 1;
                    box-shadow: none;
                    border-radius: 0;
                    page-break-inside: avoid;
                    page-break-after: avoid;
                }
            }
        </style>
    <?php endif; ?>
</div>
