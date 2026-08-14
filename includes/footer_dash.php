<!-- Modal Profil User (dipicu saat avatar/nama di postingan atau komentar diklik) -->
    <div id="user-profile-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; padding:1rem;" onclick="if(event.target === this) closeUserProfileModal();">
        <div style="background:var(--dash-sidebar, #1e293b); border:1px solid var(--dash-border, #334155); border-radius:16px; width:100%; max-width:380px; max-height:85vh; overflow-y:auto; position:relative;">
            <button onclick="closeUserProfileModal()" style="position:absolute; top:12px; right:12px; z-index:2; background:rgba(0,0,0,0.4); border:none; color:#ffffff; cursor:pointer; padding:6px; border-radius:50%; width:28px; height:28px; display:flex; align-items:center; justify-content:center;" title="Tutup">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <div id="user-profile-modal-body">
                <div style="color:var(--dash-text-muted, #94a3b8); padding:3rem 0; text-align:center;">Memuat profil...</div>
            </div>
        </div>
    </div>

    <script>
        // Daftar file GIF animasi banner yang tersedia untuk dipilih user (taruh semua filenya di src/img/)
        // User memilih sendiri banner mereka lewat halaman Pengaturan Profil setelah unlock 10 badge
        const BADGE_UNLOCK_THRESHOLD = 10;

        function closeUserProfileModal() {
            document.getElementById('user-profile-modal').style.display = 'none';
        }

        function showUserProfile(userId) {
            const modal = document.getElementById('user-profile-modal');
            const body = document.getElementById('user-profile-modal-body');
            body.innerHTML = '<div style="color:var(--dash-text-muted, #94a3b8); padding:3rem 0; text-align:center;">Memuat profil...</div>';
            modal.style.display = 'flex';

            fetch('user_profile_ajax.php?user_id=' + encodeURIComponent(userId))
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        body.innerHTML = '<div style="color:#ef4444; padding:3rem 0; text-align:center;">' + data.error + '</div>';
                        return;
                    }

                    const initial = data.name ? data.name.charAt(0).toUpperCase() : '?';
                    const borderColor = data.profile_color || '#3b82f6';
                    const hasUnlockedBanner = data.total_badges >= BADGE_UNLOCK_THRESHOLD;
                    const hasChosenBanner = hasUnlockedBanner && data.banner_gif;
                    const isRemoteBanner = hasChosenBanner && /^https?:\/\//i.test(data.banner_gif);
                    const bannerGifUrl = hasChosenBanner ? (isRemoteBanner ? data.banner_gif : ('src/img/' + data.banner_gif)) : null;
                    const isVideoBanner = isRemoteBanner && /\.mp4($|\?)/i.test(bannerGifUrl);

                    const avatarHtml = data.picture
                        ? `<img src="${data.picture}" alt="Foto profil" style="width:80px; height:80px; border-radius:50%; border:4px solid var(--dash-sidebar, #1e293b); object-fit:cover; background:var(--dash-sidebar, #1e293b); flex-shrink:0;">`
                        : `<div style="width:80px; height:80px; border-radius:50%; background:${borderColor}; color:white; display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:bold; border:4px solid var(--dash-sidebar, #1e293b); flex-shrink:0;">${initial}</div>`;

                    const titleHtml = data.profile_title
                        ? `<span style="display:inline-block; margin-top:4px; background:rgba(0,0,0,0.35); padding:3px 12px; border-radius:12px; font-size:0.75rem; font-weight:700; color:#ffffff;">${data.profile_title}</span>`
                        : '';

                    const bannerMediaHtml = isVideoBanner
                        ? `<video src="${bannerGifUrl}" autoplay loop muted playsinline style="width:100%; height:190px; object-fit:cover; display:block; pointer-events:none; filter:contrast(1.05) saturate(1.15);"></video>`
                        : `<img src="${bannerGifUrl}" alt="Banner animasi profil" style="width:100%; height:190px; object-fit:cover; display:block; pointer-events:none; filter:contrast(1.05) saturate(1.15);">`;

                    const bannerHtml = isRemoteBanner
                        ? `
                            <div style="position:relative; min-height:190px; width:100%; border-radius:16px 16px 0 0; overflow:hidden; background:linear-gradient(135deg, ${borderColor}, #1e293b);">
                                ${bannerMediaHtml}
                                <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.35) 55%, rgba(0,0,0,0.05) 100%); pointer-events:none;"></div>
                                <div style="position:absolute; left:20px; right:20px; bottom:16px; display:flex; align-items:center; gap:14px; box-sizing:border-box; pointer-events:none;">
                                    ${avatarHtml}
                                    <div style="min-width:0;">
                                        <h2 style="margin:0; color:#ffffff; font-size:1.2rem; line-height:1.25; text-shadow:0 1px 4px rgba(0,0,0,0.6); overflow-wrap:anywhere;">${data.name}</h2>
                                        ${titleHtml}
                                    </div>
                                </div>
                            </div>`
                        : (() => {
                            const bannerBg = bannerGifUrl
                                ? `url('${bannerGifUrl}') center/cover no-repeat, linear-gradient(135deg, ${borderColor}, #1e293b)`
                                : `linear-gradient(135deg, ${borderColor}33, #1e293b)`;
                            return `
                                <div style="position:relative; min-height:190px; width:100%; background:${bannerBg}; display:flex; align-items:flex-end; border-radius:16px 16px 0 0; overflow:hidden;">
                                    <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.35) 55%, rgba(0,0,0,0.05) 100%);"></div>
                                    <div style="position:relative; display:flex; align-items:center; gap:14px; padding:16px 20px 18px 20px; width:100%; box-sizing:border-box;">
                                        ${avatarHtml}
                                        <div style="min-width:0;">
                                            <h2 style="margin:0; color:#ffffff; font-size:1.2rem; line-height:1.25; text-shadow:0 1px 4px rgba(0,0,0,0.6); overflow-wrap:anywhere;">${data.name}</h2>
                                            ${titleHtml}
                                        </div>
                                    </div>
                                </div>`;
                        })();

                    const joinedDate = data.joined_at
                        ? new Date(data.joined_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                        : '-';

                    // Badge lock banner: progress menuju banner spesial
                    const remaining = Math.max(0, BADGE_UNLOCK_THRESHOLD - data.total_badges);
                    const progressPct = Math.min(100, Math.round((data.total_badges / BADGE_UNLOCK_THRESHOLD) * 100));
                    const bannerLockNoticeHtml = !hasUnlockedBanner
                        ? `<div style="margin:1.25rem 0; padding:0.9rem 1rem; background:rgba(245, 158, 11, 0.08); border:1px dashed #f59e0b; border-radius:10px; text-align:left;">
                                <div style="display:flex; align-items:center; gap:6px; font-size:0.8rem; font-weight:700; color:#d97706; margin-bottom:6px;">🔒 Banner Animasi Terkunci</div>
                                <div style="font-size:0.75rem; color:var(--dash-text-muted, #94a3b8); margin-bottom:8px;">Kumpulkan ${remaining} badge lagi untuk membuka banner profil animasi spesial.</div>
                                <div style="height:6px; background:var(--dash-bg, #0f172a); border-radius:99px; overflow:hidden;">
                                    <div style="height:100%; width:${progressPct}%; background:#f59e0b; border-radius:99px;"></div>
                                </div>
                                <div style="font-size:0.7rem; color:var(--dash-text-muted, #94a3b8); margin-top:4px; text-align:right;">${data.total_badges}/${BADGE_UNLOCK_THRESHOLD} badge</div>
                           </div>`
                        : (hasChosenBanner
                            ? `<div style="margin:1.25rem 0; padding:0.6rem 1rem; background:rgba(34, 197, 94, 0.08); border:1px solid rgba(34,197,94,0.3); border-radius:10px; text-align:left; font-size:0.78rem; font-weight:600; color:#22c55e; display:flex; align-items:center; gap:6px;">✨ Banner Animasi Aktif</div>`
                            : `<div style="margin:1.25rem 0; padding:0.6rem 1rem; background:rgba(59, 130, 246, 0.08); border:1px solid rgba(59,130,246,0.3); border-radius:10px; text-align:left; font-size:0.78rem; font-weight:600; color:#3b82f6;">🎉 Banner terbuka! Pilih banner favoritmu di halaman Pengaturan Profil.</div>`);

                    let badgesHtml;
                    if (data.badges && data.badges.length > 0) {
                        badgesHtml = data.badges.map(b => {
                            const icon = b.icon_url
                                ? `<img src="${b.icon_url}" alt="${b.name}" style="width:40px; height:40px; object-fit:contain;">`
                                : `<div style="width:40px; height:40px; border-radius:50%; background:#f59e0b; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold;">B</div>`;
                            return `
                                <div style="display:flex; align-items:center; gap:12px; background:var(--dash-bg, #0f172a); border:1px solid var(--dash-border, #334155); border-radius:10px; padding:10px 14px; text-align:left;">
                                    ${icon}
                                    <div>
                                        <div style="font-weight:600; color:var(--dash-text, #f1f5f9); font-size:0.9rem;">${b.name}</div>
                                        ${b.description ? `<div style="font-size:0.75rem; color:var(--dash-text-muted, #94a3b8);">${b.description}</div>` : ''}
                                    </div>
                                </div>`;
                        }).join('');
                    } else {
                        badgesHtml = '<div style="color:var(--dash-text-muted, #94a3b8); font-size:0.85rem; padding:1rem 0;">Belum ada badge yang diraih.</div>';
                    }

                    body.innerHTML = `
                        ${bannerHtml}
                        <div style="padding:1.5rem 2rem 2rem 2rem; text-align:center;">
                            <div style="display:flex; justify-content:center; gap:1.5rem; margin-bottom:1.25rem; padding-bottom:1rem; border-bottom:1px solid var(--dash-border, #334155);">
                                <div>
                                    <div style="font-weight:700; color:var(--dash-text, #f1f5f9); font-size:1.1rem;">${data.xp_points}</div>
                                    <div style="font-size:0.7rem; color:var(--dash-text-muted, #94a3b8);">XP</div>
                                </div>
                                <div>
                                    <div style="font-weight:700; color:var(--dash-text, #f1f5f9); font-size:1.1rem;">${data.streak_days}</div>
                                    <div style="font-size:0.7rem; color:var(--dash-text-muted, #94a3b8);">Streak</div>
                                </div>
                                <div>
                                    <div style="font-weight:700; color:var(--dash-text, #f1f5f9); font-size:1.1rem;">${data.total_badges}</div>
                                    <div style="font-size:0.7rem; color:var(--dash-text-muted, #94a3b8);">Badge</div>
                                </div>
                            </div>
                            ${bannerLockNoticeHtml}
                            <div style="text-align:left;">
                                <h4 style="color:var(--dash-text, #f1f5f9); font-size:0.9rem; margin-bottom:0.75rem;">🏆 Badge yang Diraih</h4>
                                <div style="display:flex; flex-direction:column; gap:8px;">
                                    ${badgesHtml}
                                </div>
                            </div>
                            <div style="margin-top:1.25rem; font-size:0.75rem; color:var(--dash-text-muted, #94a3b8);">Bergabung sejak ${joinedDate}</div>
                        </div>
                    `;
                })
                .catch(() => {
                    body.innerHTML = '<div style="color:#ef4444; padding:3rem 0; text-align:center;">Gagal memuat profil. Coba lagi.</div>';
                });
        }
    </script>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        if(themeToggle) {
            const themeText = themeToggle.querySelector('.theme-text');
            const sunIcon = themeToggle.querySelector('.sun-icon');
            const moonIcon = themeToggle.querySelector('.moon-icon');

            // Load saved theme
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark-mode');
                if(themeText) themeText.textContent = 'Dark';
                if(sunIcon) sunIcon.style.display = 'none';
                if(moonIcon) moonIcon.style.display = 'block';
            }

            themeToggle.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                const isDark = document.body.classList.contains('dark-mode');
                
                localStorage.setItem('theme', isDark ? 'dark' : 'light');

                if (isDark) {
                    if(themeText) themeText.textContent = 'Dark';
                    if(sunIcon) sunIcon.style.display = 'none';
                    if(moonIcon) moonIcon.style.display = 'block';
                } else {
                    if(themeText) themeText.textContent = 'Light';
                    if(sunIcon) sunIcon.style.display = 'block';
                    if(moonIcon) moonIcon.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>