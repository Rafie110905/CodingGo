<?php
/**
 * materi_icons.php
 * Mendeteksi ikon/logo yang cocok secara otomatis berdasarkan judul materi.
 * Contoh: judul "Microsoft Word" -> logo resmi Word, "Belajar HTML" -> logo HTML5, dst.
 * Jika tidak ada kecocokan, akan pakai ikon default (emoji) dengan warna gradient.
 *
 * Sumber logo brand: Simple Icons (cdn.simpleicons.org) - ikon resmi berwarna, gratis dipakai.
 */

if (!function_exists('detectMateriIcon')) {

    function detectMateriIcon(string $title): array
    {
        $t = strtolower($title);

        // Urutan penting: pola paling spesifik dicek lebih dulu.
        $map = [
            // --- Microsoft Office ---
            '/microsoft\s*word|\bms\.?\s*word\b|\bword\b/'            => ['brand', 'microsoftword', '2B579A'],
            '/microsoft\s*excel|\bms\.?\s*excel\b|\bexcel\b|spreadsheet/' => ['brand', 'microsoftexcel', '217346'],
            '/microsoft\s*power\s*point|\bms\.?\s*powerpoint\b|\bpowerpoint\b/' => ['brand', 'microsoftpowerpoint', 'B7472A'],
            '/microsoft\s*access|\bms\.?\s*access\b/'                 => ['brand', 'microsoftaccess', 'A4373A'],
            '/microsoft\s*outlook|\boutlook\b/'                       => ['brand', 'microsoftoutlook', '0078D4'],
            '/microsoft\s*teams/'                                     => ['brand', 'microsoftteams', '6264A7'],
            '/microsoft\s*365|\bms\.?\s*office\b|\bmicrosoft\s*office\b/' => ['brand', 'microsoftoffice', 'D83B01'],
            '/\bmicrosoft\b/'                                         => ['brand', 'microsoft', '5E5E5E'],

            // --- Google Workspace ---
            '/google\s*docs?/'      => ['brand', 'googledocs', '4285F4'],
            '/google\s*sheets?/'    => ['brand', 'googlesheets', '34A853'],
            '/google\s*slides?/'    => ['brand', 'googleslides', 'FBBC04'],
            '/google\s*drive/'      => ['brand', 'googledrive', '4285F4'],
            '/google\s*classroom/'  => ['brand', 'googleclassroom', '4285F4'],
            '/canva/'                => ['brand', 'canva', '00C4CC'],

            // --- Bahasa & Teknologi Pemrograman ---
            '/\bhtml5?\b/'                 => ['brand', 'html5', 'E34F26'],
            '/\bcss3?\b/'                  => ['brand', 'css3', '1572B6'],
            '/javascript|\bjs\b/'         => ['brand', 'javascript', 'F7DF1E'],
            '/typescript|\bts\b/'         => ['brand', 'typescript', '3178C6'],
            '/\bpython\b/'                 => ['brand', 'python', '3776AB'],
            '/\bphp\b/'                    => ['brand', 'php', '777BB4'],
            '/\bjava\b(?!\s*script)/'     => ['brand', 'openjdk', 'ED8B00'],
            '/c\+\+/'                       => ['brand', 'cplusplus', '00599C'],
            '/c#|c\s*sharp/'               => ['brand', 'csharp', '239120'],
            '/\bmysql\b/'                   => ['brand', 'mysql', '4479A1'],
            '/\bsql\b|basis\s*data|database/' => ['brand', 'mysql', '4479A1'],
            '/\bgit\b(?!hub)/'              => ['brand', 'git', 'F05032'],
            '/github/'                       => ['brand', 'github', '181717'],
            '/visual\s*studio\s*code|vscode/' => ['brand', 'visualstudiocode', '007ACC'],
            '/bootstrap/'                     => ['brand', 'bootstrap', '7952B3'],
            '/tailwind/'                      => ['brand', 'tailwindcss', '06B6D4'],
            '/\breact\b/'                    => ['brand', 'react', '61DAFB'],
            '/node\s*\.?js|nodejs/'          => ['brand', 'nodedotjs', '5FA04E'],
            '/wordpress/'                      => ['brand', 'wordpress', '21759B'],
            '/figma/'                          => ['brand', 'figma', 'F24E1E'],
            '/photoshop/'                      => ['brand', 'adobephotoshop', '31A8FF'],
            '/illustrator/'                    => ['brand', 'adobeillustrator', 'FF9A00'],
            '/scratch/'                        => ['brand', 'scratch', '4D97FF'],
            '/\blinux\b/'                     => ['brand', 'linux', 'FCC624'],
            '/android/'                        => ['brand', 'android', '3DDC84'],

            // --- Kategori umum tanpa logo brand resmi ---
            '/algoritma|logika|flowchart/'   => ['emoji', '🧩', '8b5cf6'],
            '/jaringan|internet|networking/' => ['emoji', '🌐', '0ea5e9'],
            '/keamanan|security|siber|cyber/' => ['emoji', '🛡️', 'ef4444'],
            '/quiz|kuis|ujian|exam/'          => ['emoji', '📝', 'f59e0b'],
            '/game|permainan/'                 => ['emoji', '🎮', 'ec4899'],
        ];

        foreach ($map as $pattern => $icon) {
            if (preg_match($pattern, $t)) {
                return $icon;
            }
        }

        // Fallback default: buku terbuka
        return ['emoji', '📘', '3b82f6'];
    }

    /**
     * Render HTML kotak ikon bulat/rounded untuk sebuah materi.
     * $size dalam px.
     */
    function renderMateriIcon(string $title, int $size = 48, string $radius = '12px'): string
    {
        [$type, $value, $hex] = detectMateriIcon($title);

        if ($type === 'brand') {
            $imgSize = (int) round($size * 0.6);
            $src = "https://cdn.simpleicons.org/{$value}/{$hex}";
            return '<div style="width:' . $size . 'px; height:' . $size . 'px; border-radius:' . $radius . '; background:#' . $hex . '14; border:1px solid #' . $hex . '33; display:flex; align-items:center; justify-content:center; flex-shrink:0;">'
                . '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($title) . '" width="' . $imgSize . '" height="' . $imgSize . '" loading="lazy" onerror="this.style.display=\'none\'">'
                . '</div>';
        }

        // emoji fallback
        $fontSize = (int) round($size * 0.45);
        return '<div style="width:' . $size . 'px; height:' . $size . 'px; border-radius:' . $radius . '; background:#' . $hex . '1a; border:1px solid #' . $hex . '33; display:flex; align-items:center; justify-content:center; font-size:' . $fontSize . 'px; flex-shrink:0;">'
            . $value
            . '</div>';
    }

    /**
     * Render badge ikon untuk ditaruh di atas banner/gradient berwarna (mis. kartu kelas).
     * Selalu pakai kartu putih/translucent supaya logo tetap kontras di background apapun.
     */
    function renderCourseBadge(string $title, int $size = 72): string
    {
        [$type, $value] = detectMateriIcon($title);

        if ($type === 'brand') {
            $imgSize = (int) round($size * 0.55);
            // Untuk badge di atas warna, pakai versi putih logo biar konsisten & kontras
            $src = "https://cdn.simpleicons.org/{$value}/ffffff";
            return '<div style="width:' . $size . 'px; height:' . $size . 'px; border-radius:18px; background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.35); backdrop-filter:blur(2px); display:flex; align-items:center; justify-content:center; flex-shrink:0;">'
                . '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($title) . '" width="' . $imgSize . '" height="' . $imgSize . '" loading="lazy">'
                . '</div>';
        }

        $fontSize = (int) round($size * 0.5);
        return '<div style="width:' . $size . 'px; height:' . $size . 'px; border-radius:18px; background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.35); backdrop-filter:blur(2px); display:flex; align-items:center; justify-content:center; font-size:' . $fontSize . 'px; flex-shrink:0;">'
            . $value
            . '</div>';
    }
}
