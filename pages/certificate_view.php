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
?>
<div id="certificate-page-root" style="min-height: 80vh; display:flex; align-items:center; justify-content:center; padding: 2rem 1rem; background: var(--bg-main, #f8fafc);">
    <?php if (!$cert): ?>
        <div style="text-align:center;">
            <h2 style="color: var(--text-main, #0f172a);">Sertifikat tidak ditemukan</h2>
            <p style="color: var(--text-muted, #64748b);">Kode sertifikat tidak valid atau sudah tidak berlaku.</p>
        </div>
    <?php else: ?>
        <div class="cert-wrap">
            <div class="cert-box" id="cert-box">
                <div class="brand-mark">
                    <span>{CG}</span>
                </div>
                <div class="brand-text">CodingGo</div>

                <h1 class="cert-title">
                    SERTIFIKAT
                    <span>PENGHARGAAN</span>
                </h1>

                <div class="cert-subtitle">Diberikan Kepada :</div>
                <div class="cert-name"><?php echo htmlspecialchars($cert['user_name']); ?></div>

                <div class="gold-divider">
                    <span class="line-left"></span>
                    <span class="star">✦</span>
                    <span class="star">✦</span>
                    <span class="star">✦</span>
                    <span class="line-right"></span>
                </div>

                <p class="cert-body">
                    Atas keberhasilan menyelesaikan seluruh materi dan lulus ujian akhir pada kelas
                    <strong><?php echo htmlspecialchars($cert['course_title']); ?></strong>
                </p>

                <div class="signature-row">
                    <div class="sig-box">
                        <span>Lead Backend</span>
                        <strong>Rafi</strong>
                    </div>
                    <div class="sig-box">
                        <span>Frontend Developer</span>
                        <strong>Deddy Nurrohim</strong>
                    </div>
                    <div class="sig-box">
                        <span>UI/UX Designer</span>
                        <strong>Rian Renaldy</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="no-print" style="text-align:center; margin-top:1.5rem;">
            <button onclick="window.print()" style="background:#4361ee; color:#fff; border:none; padding:0.85rem 2rem; border-radius:999px; font-weight:600; cursor:pointer;">Cetak / Simpan sebagai PDF</button>
        </div>

        <style>
            .cert-wrap {
                position: relative;
                width: min(100%, 900px);
                margin: 0 auto;
                padding: 0.75rem;
            }

            .cert-box {
                position: relative;
                width: min(100%, 760px);
                min-height: 640px;
                margin: 0 auto;
                background: rgba(255,255,255,0.9);
                border: 4px solid #d7b057;
                box-shadow: inset 0 0 0 2px rgba(13, 52, 117, 0.18), 0 18px 40px rgba(15, 23, 42, 0.08);
                overflow: hidden;
                padding: 2.25rem 2.25rem 1.8rem;
                text-align: center;
                z-index: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .cert-box::before,
            .cert-box::after {
                content: "";
                position: absolute;
                inset: 0;
                pointer-events: none;
                z-index: 0;
            }

            .cert-box::before {
                background:
                    radial-gradient(circle at 17% 14%, rgba(0,0,0,0.04) 0 18%, transparent 18.5%),
                    radial-gradient(circle at 72% 18%, rgba(0,0,0,0.04) 0 17%, transparent 17.5%),
                    linear-gradient(135deg, rgba(33,92,178,0.12), rgba(98,149,255,0.04));
                opacity: 0.8;
            }

            .cert-box::after {
                inset: 10px;
                border: 1px solid rgba(33, 92, 178, 0.12);
            }

            .brand-mark,
            .brand-text,
            .cert-title,
            .cert-subtitle,
            .cert-name,
            .gold-divider,
            .cert-body,
            .signature-row {
                position: relative;
                z-index: 1;
            }

            .brand-mark {
                position: absolute;
                left: 2.4rem;
                top: 2.1rem;
                width: 70px;
                height: 70px;
                border-radius: 12px;
                background: linear-gradient(135deg, #3a7ae5, #2f6dca);
                box-shadow: 0 10px 20px rgba(37,99,235,0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
                color: #fff;
                font-size: 0.85rem;
                letter-spacing: -0.04em;
                border: 1px solid rgba(255,255,255,0.5);
            }

            .brand-text {
                position: absolute;
                left: 2.7rem;
                top: 6.15rem;
                font-size: 0.88rem;
                color: #1d4ed8;
                font-family: "Segoe UI", sans-serif;
                font-weight: 600;
                letter-spacing: 0.02em;
            }

            .cert-title {
                margin: 0;
                font-size: clamp(2rem, 2.8vw, 3.5rem);
                line-height: 1.05;
                letter-spacing: 0.06em;
                font-weight: 700;
                color: #1f5fa8;
                font-family: Georgia, "Times New Roman", serif;
                text-transform: uppercase;
                width: 100%;
            }

            .cert-title span {
                display: block;
                margin-top: 0.15rem;
                font-size: clamp(1.2rem, 1.9vw, 2.2rem);
                letter-spacing: 0.08em;
            }

            .cert-subtitle {
                margin-top: 1.4rem;
                font-size: clamp(1.05rem, 1.4vw, 1.7rem);
                color: rgba(15, 23, 42, 0.82);
                font-style: italic;
                font-family: Georgia, "Times New Roman", serif;
            }

            .cert-name {
                margin-top: 0.5rem;
                font-size: clamp(2rem, 2.8vw, 3rem);
                font-weight: 700;
                color: rgba(15,23,42,0.95);
                letter-spacing: 0.03em;
                font-family: Georgia, "Times New Roman", serif;
                padding-bottom: 0.2rem;
            }

            .gold-divider {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.7rem;
                margin: 1.3rem auto 1.4rem;
                width: min(100%, 420px);
            }

            .line-left,
            .line-right {
                flex: 1;
                height: 2px;
                background: linear-gradient(90deg, rgba(212,174,82,0.2), rgba(212,174,82,0.9), rgba(212,174,82,0.2));
            }

            .star {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 22px;
                height: 22px;
                color: #d4a74d;
                font-size: 1.1rem;
                line-height: 1;
            }

            .cert-body {
                max-width: 640px;
                margin: 0 auto;
                font-size: clamp(1rem, 1.35vw, 1.45rem);
                color: rgba(15, 23, 42, 0.82);
                line-height: 1.7;
                font-family: Georgia, "Times New Roman", serif;
            }

            .cert-body strong {
                display: block;
                margin-top: 0.2rem;
                color: rgba(15,23,42,1);
                font-size: 1.05em;
            }

            .signature-row {
                display: grid;
                grid-template-columns: repeat(3, minmax(120px, 1fr));
                gap: 1.2rem;
                width: min(100%, 560px);
                margin: 2rem auto 0;
            }

            .sig-box {
                width: 100%;
                text-align: center;
                color: rgba(15, 23, 42, 0.8);
                font-family: Georgia, "Times New Roman", serif;
                margin-bottom: 0.2rem;
            }

            .sig-box span {
                display: block;
                font-size: 0.95rem;
                margin-bottom: 0.1rem;
            }

            .sig-box strong {
                display: block;
                font-size: 1rem;
                color: rgba(15, 23, 42, 0.96);
                font-weight: 700;
            }

            @media (max-width: 768px) {
                .cert-box {
                    min-height: auto;
                    padding: 5.5rem 1.25rem 1.6rem;
                }

                .brand-mark {
                    width: 56px;
                    height: 56px;
                    left: 1.1rem;
                    top: 1.1rem;
                    font-size: 0.72rem;
                }

                .brand-text {
                    left: 1.2rem;
                    top: 4.9rem;
                    font-size: 0.72rem;
                }

                .signature-row {
                    grid-template-columns: repeat(3, minmax(100px, 1fr));
                    gap: 0.8rem;
                }
            }

            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            @media print {
                body > *:not(#certificate-page-root) {
                    display: none !important;
                }

                .no-print { display: none !important; }
                html, body {
                    margin: 0 !important;
                    padding: 0 !important;
                    background: #fff !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
                #certificate-page-root {
                    display: block !important;
                    min-height: auto !important;
                    padding: 0 !important;
                    background: #fff !important;
                }
                .cert-wrap {
                    width: 100%;
                    padding: 0;
                    margin: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                .cert-box {
                    width: 100%;
                    max-width: 760px;
                    min-height: 0;
                    margin: 0 auto;
                    box-shadow: none;
                    padding: 1.5rem 1.25rem 1.25rem;
                    aspect-ratio: 1.41 / 1;
                }
            }
        </style>
    <?php endif; ?>
</div>
