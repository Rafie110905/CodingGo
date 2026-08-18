<!-- Modal Profil User (dipicu saat avatar/nama di postingan atau komentar diklik) -->
    <div id="user-profile-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; padding:1rem;" onclick="if(event.target === this) closeUserProfileModal();">
        <div id="user-profile-modal-container" style="background:var(--dash-sidebar, #1e293b); border:1px solid var(--dash-border, #334155); border-radius:16px; width:100%; max-width:380px; position:relative; overflow:hidden;">
            <div style="max-height:85vh; overflow-y:auto; overflow-x:hidden; position:relative; width:100%;">
                <button onclick="closeUserProfileModal()" style="position:absolute; top:12px; right:12px; z-index:1000; background:rgba(0,0,0,0.4); border:none; color:#ffffff; cursor:pointer; padding:6px; border-radius:50%; width:28px; height:28px; display:flex; align-items:center; justify-content:center; transition:background 0.2s;" title="Tutup" onmouseover="this.style.background='rgba(0,0,0,0.7)'" onmouseout="this.style.background='rgba(0,0,0,0.4)'">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <div id="user-profile-modal-body" style="position:relative; min-height:100%;">
                    <div style="color:var(--dash-text-muted, #94a3b8); padding:3rem 0; text-align:center;">Memuat profil...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Broadcast (Reusable) -->
    <div id="global-broadcast-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center; padding:1rem; backdrop-filter: blur(4px);">
        <div style="background:var(--dash-bg, #ffffff); width:100%; max-width:420px; border-radius:24px; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); animation: modalZoomIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative;">
            <!-- Header background colored by type -->
            <div id="bc-header-bg" style="height: 120px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); position: absolute; top:0; left:0; right:0; z-index: 1;"></div>
            
            <!-- Decorative icon -->
            <div style="position: relative; z-index: 2; display: flex; justify-content: center; margin-top: 60px;">
                <div id="bc-icon-wrapper" style="width: 80px; height: 80px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border: 4px solid var(--dash-bg, #fff);">
                    <!-- icon injected via js -->
                </div>
            </div>

            <div style="padding: 1.5rem 2rem 2rem; position: relative; z-index: 2; text-align: center;">
                <h2 id="bc-title" style="margin: 0 0 1rem 0; color: var(--dash-text); font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em;">Title</h2>
                <div id="bc-message" style="color: var(--dash-text-muted); line-height: 1.6; font-size: 0.95rem; margin-bottom: 2rem;">
                    Message
                </div>
                <button id="bc-close-btn" style="width: 100%; background: var(--dash-primary); color: white; border: none; padding: 0.85rem; border-radius: 12px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 6px -1px rgba(59,130,246,0.4);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 8px -1px rgba(59,130,246,0.5)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(59,130,246,0.4)';">Mengerti</button>
            </div>
        </div>
    </div>

    <script>
    function showBroadcastModal(id, title, message, type, isPreview = false) {
        const modal = document.getElementById('global-broadcast-modal');
        const headerBg = document.getElementById('bc-header-bg');
        const iconWrapper = document.getElementById('bc-icon-wrapper');
        const titleEl = document.getElementById('bc-title');
        const messageEl = document.getElementById('bc-message');
        const closeBtn = document.getElementById('bc-close-btn');

        let bgStyle, iconHtml, btnColor, btnShadow;

        if (type === 'success') {
            bgStyle = 'linear-gradient(135deg, #10b981, #047857)'; // Emerald
            btnColor = '#10b981';
            btnShadow = 'rgba(16,185,129,0.4)';
            iconHtml = '<svg fill="none" viewBox="0 0 24 24" stroke="#10b981" width="40" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        } else if (type === 'warning') {
            bgStyle = 'linear-gradient(135deg, #f59e0b, #b45309)'; // Amber
            btnColor = '#f59e0b';
            btnShadow = 'rgba(245,158,11,0.4)';
            iconHtml = '<svg fill="none" viewBox="0 0 24 24" stroke="#f59e0b" width="40" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>';
        } else {
            bgStyle = 'linear-gradient(135deg, #3b82f6, #1d4ed8)'; // Blue
            btnColor = '#3b82f6';
            btnShadow = 'rgba(59,130,246,0.4)';
            iconHtml = '<svg fill="none" viewBox="0 0 24 24" stroke="#3b82f6" width="40" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        }

        headerBg.style.background = bgStyle;
        iconWrapper.innerHTML = iconHtml;
        titleEl.textContent = title;
        
        // Convert newlines to br
        messageEl.innerHTML = message.replace(/\n/g, '<br>');
        
        closeBtn.style.background = btnColor;
        closeBtn.style.boxShadow = `0 4px 6px -1px ${btnShadow}`;
        closeBtn.onmouseover = () => { closeBtn.style.transform = 'translateY(-2px)'; closeBtn.style.boxShadow = `0 6px 8px -1px ${btnShadow}`; };
        closeBtn.onmouseout = () => { closeBtn.style.transform = 'translateY(0)'; closeBtn.style.boxShadow = `0 4px 6px -1px ${btnShadow}`; };

        closeBtn.onclick = () => {
            modal.style.display = 'none';
            if (!isPreview && id) {
                fetch('api/broadcast_read.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({broadcast_id: id})
                }).catch(e => console.error(e));
            }
        };

        modal.style.display = 'flex';
    }
    </script>

    <?php
    // Fetch Global Broadcast for actual display
    if (isset($pdo) && isset($_SESSION['user_id'])) {
        $stmt_broadcast = $pdo->prepare("
            SELECT * FROM broadcasts 
            WHERE is_active = 1 
            AND (
                display_mode = 'always' 
                OR (display_mode = 'once' AND id NOT IN (SELECT broadcast_id FROM broadcast_views WHERE user_id = ?))
            )
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt_broadcast->execute([$_SESSION['user_id']]);
        $active_broadcast = $stmt_broadcast->fetch();
        
        if ($active_broadcast) {
            $b_id = $active_broadcast['id'];
            $b_title = json_encode($active_broadcast['title']);
            $b_message = json_encode($active_broadcast['message']);
            $b_type = json_encode($active_broadcast['type']);
            echo "<script>showBroadcastModal($b_id, $b_title, $b_message, $b_type);</script>";
        }
    }
    ?>

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

                    const avatarStyle = data.avatar_frame_css ? data.avatar_frame_css : 'border:4px solid var(--dash-sidebar, #1e293b);';
                    const avatarHtml = data.picture
                        ? `<div style="position:relative; width:80px; height:80px; flex-shrink:0;">
                               <img src="${data.picture}" alt="Foto profil" style="width:100%; height:100%; border-radius:50%; object-fit:cover; background:var(--dash-sidebar, #1e293b); ${avatarStyle}">
                           </div>`
                        : `<div style="width:80px; height:80px; border-radius:50%; background:${borderColor}; color:white; display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:bold; flex-shrink:0; ${avatarStyle}">${initial}</div>`;

                    const nameStyle = data.name_effect_css ? data.name_effect_css : 'color:#ffffff; text-shadow:0 1px 4px rgba(0,0,0,0.6);';
                    const nameHtml = `<h2 style="margin:0; font-size:1.2rem; line-height:1.25; overflow-wrap:anywhere; ${nameStyle}">${data.name}</h2>`;

                    const customStatusHtml = data.custom_status
                        ? `<div style="font-size:0.8rem; color:rgba(255,255,255,0.9); background:rgba(0,0,0,0.4); padding:4px 10px; border-radius:8px; margin-top:6px; display:inline-block; border:1px solid rgba(255,255,255,0.1);">
                               ${data.status_emoji ? data.status_emoji + ' ' : ''}${data.custom_status}
                           </div>`
                        : '';

                    const titleHtml = data.profile_title
                        ? `<div style="margin-top:4px;"><span style="display:inline-block; background:rgba(0,0,0,0.35); padding:3px 12px; border-radius:12px; font-size:0.75rem; font-weight:700; color:#ffffff;">${data.profile_title}</span></div>`
                        : '';

                    const bannerMediaHtml = isVideoBanner
                        ? `<video src="${bannerGifUrl}" autoplay loop muted playsinline style="width:100%; height:190px; object-fit:cover; display:block; pointer-events:none; filter:contrast(1.05) saturate(1.15);"></video>`
                        : `<img src="${bannerGifUrl}" alt="Banner animasi profil" style="width:100%; height:190px; object-fit:cover; display:block; pointer-events:none; filter:contrast(1.05) saturate(1.15);">`;

                    const bannerHtml = isRemoteBanner
                        ? `
                            <div style="position:relative; min-height:190px; width:100%; border-radius:16px 16px 0 0; overflow:hidden; background:linear-gradient(135deg, ${borderColor}, #1e293b);">
                                ${bannerMediaHtml}
                                <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.35) 55%, rgba(0,0,0,0.05) 100%); pointer-events:none;"></div>
                                <div style="position:absolute; left:20px; right:20px; bottom:16px; display:flex; align-items:center; gap:14px; box-sizing:border-box; pointer-events:none; z-index:2;">
                                    ${avatarHtml}
                                    <div style="min-width:0;">
                                        ${nameHtml}
                                        ${titleHtml}
                                        ${customStatusHtml}
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
                                    <div style="position:relative; display:flex; align-items:center; gap:14px; padding:16px 20px 18px 20px; width:100%; box-sizing:border-box; z-index:2;">
                                        ${avatarHtml}
                                        <div style="min-width:0;">
                                            ${nameHtml}
                                            ${titleHtml}
                                            ${customStatusHtml}
                                        </div>
                                    </div>
                                </div>`;
                        })();

                    const cardBorderStyle = data.card_border_css ? data.card_border_css : 'border:1px solid var(--dash-border, #334155); box-shadow:0 10px 25px rgba(0,0,0,0.2);';
                    const cursorStyle = data.cursor_effect_class ? data.cursor_effect_class : '';
                    const entranceAnimStyle = data.entrance_anim_class ? data.entrance_anim_class : 'animation: modalFadeIn 0.2s ease-out;';
                    
                    document.getElementById('user-profile-modal-container').style.cssText = `background:var(--dash-sidebar, #1e293b); border-radius:16px; width:100%; max-width:380px; position:relative; overflow:hidden; ${cardBorderStyle} ${cursorStyle} ${entranceAnimStyle}`;

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
                                ? `<img src="${b.icon_url}" alt="${b.name}" style="width:32px; height:32px; object-fit:contain; border-radius:6px;">`
                                : `<div style="width:32px; height:32px; border-radius:6px; background:#f59e0b; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:1rem;">B</div>`;
                            const tooltip = b.name + (b.description ? ' - ' + b.description : '');
                            const badgeEffectStyle = data.badge_effect_css ? data.badge_effect_css : '';
                            return `
                                <div title="${tooltip}" style="display:flex; align-items:center; justify-content:center; background:var(--dash-bg, #0f172a); border:1px solid var(--dash-border, #334155); border-radius:8px; padding:6px; cursor:pointer; transition:transform 0.2s, box-shadow 0.2s; ${badgeEffectStyle}" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                                    ${icon}
                                </div>`;
                        }).join('');
                    } else {
                        badgesHtml = '<div style="color:var(--dash-text-muted, #94a3b8); font-size:0.85rem; padding:1rem 0; width:100%;">Belum ada badge yang diraih.</div>';
                    }

                    const profileEffectOverlay = (() => {
                        if (!data.profile_effect_class) return '';
                        let eff = data.profile_effect_class;
                        if (/^https?:\/\//i.test(eff)) {
                            return `<div style="position:absolute; inset:0; pointer-events:none; z-index:1; border-radius:16px; overflow:hidden;"><img src="${eff}" style="width:100%; height:100%; object-fit:cover; opacity:0.7;"></div>`;
                        } else if (/\.(gif|jpg|jpeg|png|mp4)$/i.test(eff)) {
                            if (/\.mp4$/i.test(eff)) {
                                return `<div style="position:absolute; inset:0; pointer-events:none; z-index:1; border-radius:16px; overflow:hidden;"><video src="src/img/${eff}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover; opacity:0.7;"></video></div>`;
                            }
                            return `<div style="position:absolute; inset:0; pointer-events:none; z-index:1; border-radius:16px; overflow:hidden;"><img src="src/img/${eff}" style="width:100%; height:100%; object-fit:cover; opacity:0.7;"></div>`;
                        }
                        return `<div class="profile-effect-${eff}" style="position:absolute; inset:0; pointer-events:none; z-index:1; border-radius:16px; overflow:hidden;"></div>`;
                    })();

                    const cardBgOverlay = data.card_background_css 
                        ? `<div style="position:absolute; inset:0; z-index:0; pointer-events:none; border-radius:0 0 16px 16px; ${data.card_background_css}"></div>` 
                        : '';

                    body.innerHTML = `
                        ${profileEffectOverlay}
                        ${bannerHtml}
                        ${cardBgOverlay}
                        <div style="padding:1.5rem 2rem 2rem 2rem; text-align:center; position:relative; z-index:2;">
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
                                <div style="display:flex; flex-wrap:wrap; gap:8px;">
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

    <style>
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes modalZoomIn {
            0% { opacity: 0; transform: scale(0.5); }
            60% { opacity: 1; transform: scale(1.05); }
            100% { opacity: 1; transform: scale(1); }
        }
        @keyframes modalSlideUp {
            from { opacity: 0; transform: translateY(100px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes modal3DFlip {
            from { opacity: 0; transform: perspective(400px) rotateY(90deg); }
            to { opacity: 1; transform: perspective(400px) rotateY(0deg); }
        }
        
        /* Animasi Profile Effects */
        .profile-effect-matrix {
            background-image: 
                radial-gradient(circle, rgba(34, 197, 94, 0.4) 0%, transparent 70%),
                repeating-linear-gradient(0deg, rgba(34, 197, 94, 0.2) 0px, transparent 2px, transparent 4px);
            background-size: 100% 100%, 100% 4px;
            animation: matrix-scan 2s linear infinite;
            opacity: 0.8;
        }
        @keyframes matrix-scan {
            0% { background-position: 0 0, 0 0; }
            100% { background-position: 0 0, 0 100%; }
        }

        .profile-effect-snow {
            background-image: 
                radial-gradient(circle at 10px 10px, rgba(255,255,255,0.8) 0, transparent 3px),
                radial-gradient(circle at 40px 30px, rgba(255,255,255,0.8) 0, transparent 3px),
                radial-gradient(circle at 70px 60px, rgba(255,255,255,0.8) 0, transparent 3px),
                radial-gradient(circle at 20px 80px, rgba(255,255,255,0.8) 0, transparent 3px);
            background-size: 100px 100px;
            animation: snow-fall 4s linear infinite;
            opacity: 0.6;
        }
        @keyframes snow-fall {
            0% { background-position: 0 0, 0 0, 0 0, 0 0; }
            100% { background-position: 100px 100px, 50px 150px, -50px 200px, 0 100px; }
        }

        @keyframes rainbow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 69, 0, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255, 69, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 69, 0, 0); }
        }
    </style>

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
        // Time Tracking Script (Stats)
        setInterval(function() {
            // Only ping if tab is active (visible)
            if(document.visibilityState === 'visible') {
                const cid = window.currentCourseId ? window.currentCourseId : 0;
                fetch('api/track_time.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ course_id: cid })
                }).catch(err => console.error('Time tracking error:', err));
            }
        }, 10000);
    </script>
</body>
</html>