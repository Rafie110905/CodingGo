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
<div style="min-height: 80vh; display:flex; align-items:center; justify-content:center; padding: 2rem 1rem; background: var(--bg-main, #f8fafc);">
    <?php if (!$cert): ?>
        <div style="text-align:center;">
            <h2 style="color: var(--text-main, #0f172a);">Sertifikat tidak ditemukan</h2>
            <p style="color: var(--text-muted, #64748b);">Kode sertifikat tidak valid atau sudah tidak berlaku.</p>
        </div>
    <?php else: ?>
        <div id="cert-box" style="width: 100%; max-width: 850px; aspect-ratio: 1.41 / 1; background: #fff; border: 12px solid #4361ee; border-radius: 12px; padding: 3rem 4rem; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: 0 20px 40px rgba(0,0,0,0.1); position: relative;">
            <div style="position:absolute; inset:14px; border: 2px solid rgba(67,97,238,0.3); border-radius:6px; pointer-events:none;"></div>

            <div style="font-size:0.8rem; letter-spacing:0.2em; color:#4361ee; font-weight:700; text-transform:uppercase; margin-bottom:1rem;">CodingGo &middot; Sertifikat Kompetensi</div>
            <h1 style="font-size:2.5rem; color:#0f172a; margin:0 0 0.5rem 0; font-family: Georgia, serif;">Sertifikat Penyelesaian</h1>
            <p style="color:#64748b; margin-bottom:2rem;">Dengan bangga diberikan kepada</p>

            <div style="font-size:2rem; font-weight:800; color:#0f172a; margin-bottom:2rem; border-bottom: 2px solid #4361ee; padding-bottom: 0.75rem; display:inline-block;">
                <?php echo htmlspecialchars($cert['user_name']); ?>
            </div>

            <p style="color:#64748b; max-width:500px; line-height:1.7; margin-bottom:2rem;">
                Atas keberhasilan menyelesaikan seluruh materi dan lulus ujian akhir pada kelas
                <br><b style="color:#0f172a;"><?php echo htmlspecialchars($cert['course_title']); ?></b>
                <br>Jenjang <?php echo htmlspecialchars($cert['category']); ?>
            </p>

            <div style="display:flex; justify-content:space-between; width:100%; max-width:500px; margin-top:1rem; font-size:0.8rem; color:#64748b;">
                <div>
                    <div>Tanggal Terbit</div>
                    <div style="font-weight:700; color:#0f172a;"><?php echo date('d F Y', strtotime($cert['issued_at'])); ?></div>
                </div>
                <div>
                    <div>Kode Verifikasi</div>
                    <div style="font-weight:700; color:#0f172a; font-family:monospace;"><?php echo htmlspecialchars($cert['certificate_code']); ?></div>
                </div>
            </div>
        </div>

        <div class="no-print" style="text-align:center; margin-top:1.5rem;">
            <button onclick="window.print()" style="background:#4361ee; color:#fff; border:none; padding:0.85rem 2rem; border-radius:999px; font-weight:600; cursor:pointer;">Cetak / Simpan sebagai PDF</button>
        </div>

        <style>
            @media print {
                .no-print { display: none !important; }
                body { background: #fff !important; }
            }
        </style>
    <?php endif; ?>
</div>
