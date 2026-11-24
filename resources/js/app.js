// Portfolio: Theme, mobile nav, project filters, project modal, contact form, scroll-reveal, parallax stars

document.addEventListener('DOMContentLoaded', () => {
    initParallaxStars();
    initMobileNav();
    initProjectFilters();
    initProjectModal();
    initContactForm();
    initScrollReveal();
});

/* ── Mobile Navigation (R-03, R-32, R-39) ────────────────────────────── */
function initMobileNav() {
    const btn  = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    if (!btn || !menu) return;

    function open() {
        menu.classList.remove('hidden');
        btn.setAttribute('aria-expanded', 'true');
        const first = menu.querySelector('a');
        if (first) first.focus();
    }
    function close() {
        menu.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
        btn.focus();
    }

    btn.addEventListener('click', () => {
        btn.getAttribute('aria-expanded') === 'true' ? close() : open();
    });

    menu.querySelectorAll('a').forEach(a => a.addEventListener('click', close));

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !menu.classList.contains('hidden')) close();
    });
}

/* ── Project Category Filters (Morphing Pill & Responsive Tabs) ───────── */
function initProjectFilters() {
    const tablist    = document.getElementById('project-filter-tablist');
    const pill       = document.getElementById('project-filter-pill');
    const tabs       = document.querySelectorAll('.project-filter-tab');
    const cards      = document.querySelectorAll('.project-card');
    const emptyState = document.getElementById('projects-empty-state');
    if (!tablist || !tabs.length) return;

    let activeTab = Array.from(tabs).find(t => t.getAttribute('aria-selected') === 'true') || tabs[0];
    let morphTimeout = null;

    function updatePill(tab, animate = true) {
        if (!pill || !tab) return;

        if (!animate) {
            pill.style.transition = 'none';
        } else {
            pill.style.transition = '';
            pill.classList.add('is-morphing');
            clearTimeout(morphTimeout);
            morphTimeout = setTimeout(() => {
                pill.classList.remove('is-morphing');
            }, 380);
        }

        const x = tab.offsetLeft;
        const y = tab.offsetTop;
        const w = tab.offsetWidth;
        const h = tab.offsetHeight;

        pill.style.transform = `translate3d(${x}px, ${y}px, 0)`;
        pill.style.width = `${w}px`;
        pill.style.height = `${h}px`;
        pill.style.opacity = '1';

        if (!animate) {
            // Force browser reflow so position applies before CSS transition is restored
            void pill.offsetHeight;
            pill.style.transition = '';
        }
    }

    function selectTab(tab, moveFocus = false) {
        if (!tab) return;
        activeTab = tab;
        const cat = tab.dataset.category;

        tabs.forEach(t => {
            const isActive = t === tab;
            t.setAttribute('aria-selected', isActive ? 'true' : 'false');
            t.tabIndex = isActive ? 0 : -1;
            if (isActive) {
                t.style.color = '#0d0820';
                t.style.fontWeight = '700';
            } else {
                t.style.color = '#c0a8e8';
                t.style.fontWeight = '600';
            }
        });

        if (moveFocus) {
            tab.focus();
        }

        updatePill(tab, true);

        // Filter project cards with smooth visual transitions
        let visibleCount = 0;
        cards.forEach(card => {
            const match = cat === 'all' || card.dataset.category === cat;
            if (match) {
                visibleCount++;
                card.classList.remove('hidden');
                requestAnimationFrame(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0) scale(1)';
                });
            } else {
                card.style.opacity = '0';
                card.style.transform = 'translateY(8px) scale(0.97)';
                setTimeout(() => {
                    if (card.dataset.category !== cat && cat !== 'all') {
                        card.classList.add('hidden');
                    }
                }, 200);
            }
        });

        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
                requestAnimationFrame(() => {
                    emptyState.style.opacity = '1';
                });
            } else {
                emptyState.style.opacity = '0';
                setTimeout(() => {
                    emptyState.classList.add('hidden');
                }, 200);
            }
        }
    }

    // Initial positioning without animation jump
    updatePill(activeTab, false);

    // Click handler on tabs
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            selectTab(tab);
        });
    });

    // Keyboard navigation (WAI-ARIA tabs pattern: Left/Right/Home/End)
    const tabArray = Array.from(tabs);
    tablist.addEventListener('keydown', e => {
        const index = tabArray.indexOf(activeTab);
        if (index === -1) return;

        let nextIndex = null;
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
            e.preventDefault();
            nextIndex = (index + 1) % tabArray.length;
        } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
            e.preventDefault();
            nextIndex = (index - 1 + tabArray.length) % tabArray.length;
        } else if (e.key === 'Home') {
            e.preventDefault();
            nextIndex = 0;
        } else if (e.key === 'End') {
            e.preventDefault();
            nextIndex = tabArray.length - 1;
        }

        if (nextIndex !== null) {
            selectTab(tabArray[nextIndex], true);
        }
    });

    // Responsive adaptation: ResizeObserver on tablist container
    if (window.ResizeObserver) {
        const ro = new ResizeObserver(() => {
            updatePill(activeTab, false);
        });
        ro.observe(tablist);
    } else {
        window.addEventListener('resize', () => {
            updatePill(activeTab, false);
        });
    }

    // Window load and fonts load check to ensure exact dimensions
    window.addEventListener('load', () => {
        updatePill(activeTab, false);
    });
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(() => {
            updatePill(activeTab, false);
        });
    }
}

/* ── Project Modal (R-26, R-32, R-39) ─────────────────────────────────── */
function initProjectModal() {
    const modal    = document.getElementById('project-modal');
    if (!modal) return;

    const get = id => document.getElementById(id);
    let lastFocus = null;

    function populateAndOpen(data) {
        lastFocus = document.activeElement;

        get('modal-project-title').textContent    = data.title      || '';
        get('modal-project-tagline').textContent  = data.tagline    || '';
        get('modal-project-category').textContent = data.categoryLabel || data.category || '';

        get('modal-project-description').textContent = data.description || '';

        const archWrap = get('modal-project-architecture-wrapper');
        const archText = get('modal-project-architecture');
        if (data.architectureNotes?.trim()) {
            archText.textContent = data.architectureNotes;
            archWrap.classList.remove('hidden');
        } else {
            archWrap.classList.add('hidden');
        }

        const metWrap = get('modal-project-metrics-wrapper');
        const metText = get('modal-project-metrics');
        if (data.systemMetrics?.trim()) {
            metText.textContent = data.systemMetrics;
            metWrap.classList.remove('hidden');
        } else {
            metWrap.classList.add('hidden');
        }

        const featureList = get('modal-project-features');
        featureList.innerHTML = '';
        (data.keyFeatures || []).forEach(f => {
            const li = document.createElement('li');
            li.className = 'flex items-start gap-3 text-sm leading-relaxed text-purple-200';
            li.innerHTML = `<span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-400 inline-block" aria-hidden="true"></span><span>${esc(f)}</span>`;
            featureList.appendChild(li);
        });

        const techWrap = get('modal-project-tech-stack');
        techWrap.innerHTML = '';
        (data.techStack || []).forEach(t => {
            const span = document.createElement('span');
            span.className = 'inline-flex items-center rounded-lg border border-plum-600 bg-plum-800/60 px-3 py-1 font-mono text-xs text-plum-200';
            span.textContent = t;
            techWrap.appendChild(span);
        });

        const ghLink   = get('modal-project-github');
        const demoLink = get('modal-project-demo');

        if (data.githubUrl) {
            ghLink.href = data.githubUrl;
            ghLink.classList.remove('hidden');
            ghLink.setAttribute('aria-label', `View ${data.title} on GitHub`);
        } else {
            ghLink.classList.add('hidden');
        }
        if (data.demoUrl) {
            demoLink.href = data.demoUrl;
            demoLink.classList.remove('hidden');
            demoLink.setAttribute('aria-label', `Open live deployment for ${data.title}`);
        } else {
            demoLink.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        modal.querySelector('[data-modal-close]')?.focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        lastFocus?.focus();
    }

    document.querySelectorAll('[data-open-project-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            try {
                const data = JSON.parse(btn.closest('.project-card').dataset.projectJson);
                populateAndOpen(data);
            } catch (e) {
                console.error('Project modal parse error:', e);
            }
        });
    });

    modal.querySelectorAll('[data-modal-close]').forEach(b => b.addEventListener('click', closeModal));
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
}

/* ── Contact Form (R-26, R-27, R-39) ──────────────────────────────────── */
function initContactForm() {
    const form = document.getElementById('contact-form');
    if (!form) return;

    const submitBtn    = form.querySelector('button[type="submit"]');
    const btnText      = document.getElementById('submit-btn-text');
    const btnSpinner   = document.getElementById('submit-btn-spinner');
    const statusBox    = document.getElementById('contact-form-status');

    function clearErrors() {
        form.querySelectorAll('[data-error-feedback]').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        form.querySelectorAll('input, textarea').forEach(el => {
            el.classList.remove('border-red-500');
        });
        if (statusBox) { statusBox.className = 'hidden'; statusBox.textContent = ''; }
    }

    function showStatus(msg, ok = true) {
        if (!statusBox) return;
        statusBox.className = ok
            ? 'mb-6 rounded-xl border border-emerald-500/30 bg-emerald-900/20 p-4 text-sm font-medium text-emerald-300'
            : 'mb-6 rounded-xl border border-red-500/30 bg-red-900/20 p-4 text-sm font-medium text-red-300';
        statusBox.textContent = msg;
        statusBox.focus();
    }

    form.addEventListener('submit', async e => {
        e.preventDefault();
        clearErrors();

        if (submitBtn) submitBtn.disabled = true;
        if (btnText)    btnText.textContent = 'Mengirim pesan...';
        if (btnSpinner) btnSpinner.classList.remove('hidden');

        try {
            const res  = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const data = await res.json();

            if (res.ok && data.success) {
                form.reset();
                showStatus(data.message || 'Pesan Anda berhasil dikirim! Terima kasih, saya akan segera membalasnya.', true);
            } else if (res.status === 422 && data.errors) {
                Object.entries(data.errors).forEach(([field, msgs]) => {
                    const fb    = form.querySelector(`[data-error-feedback="${field}"]`);
                    const input = form.querySelector(`[name="${field}"]`);
                    if (fb) { fb.textContent = msgs[0]; fb.classList.remove('hidden'); }
                    if (input) input.classList.add('border-red-500');
                });
                showStatus('Mohon periksa kembali kolom isian yang ditandai merah di atas.', false);
            } else {
                showStatus('Terjadi kendala saat mengirim pesan. Silakan coba lagi atau hubungi via email langsung.', false);
            }
        } catch {
            form.submit();
        } finally {
            if (submitBtn) submitBtn.disabled = false;
            if (btnText)    btnText.textContent = 'Kirim Pesan';
            if (btnSpinner) btnSpinner.classList.add('hidden');
        }
    });
}

/* ── Scroll Reveal (MOTION 2 - R-19: purpose = guide attention on entry) ─ */
function initScrollReveal() {
    const items = document.querySelectorAll('.reveal');
    if (!items.length) return;

    const obs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    items.forEach(el => obs.observe(el));
}

function esc(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

/* ── Celestial Parallax Stars Background ("Ekstra Gebyar" & Dense Cosmos) ─ */
function initParallaxStars() {
    const canvas = document.getElementById('parallax-stars-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let width = 0;
    let height = 0;
    let dpr = 1;
    let stars = [];
    let shootingStars = [];
    let nextShootingStarTime = Date.now() + 1500;
    let animationFrameId = null;

    let scrollY = window.scrollY || 0;
    let targetScrollY = scrollY;
    let mouseX = 0;
    let mouseY = 0;
    let targetMouseX = 0;
    let targetMouseY = 0;

    // Dense celestial cosmos: ~480 on mobile up to 900 on widescreen
    const STAR_COUNT = Math.min(900, Math.max(480, Math.floor(window.innerWidth / 1.8)));

    const STAR_COLORS = [
        { r: 250, g: 225, b: 120 }, // Radiant Warm Gold
        { r: 247, g: 211, b: 64 },  // Luminous Amber Gold
        { r: 192, g: 132, b: 252 }, // Celestial Violet
        { r: 216, g: 180, b: 254 }, // Dreamy Lavender
        { r: 165, g: 180, b: 252 }, // Cyan / Periwinkle
        { r: 103, g: 232, b: 249 }, // Electric Aqua
        { r: 255, g: 255, b: 255 }, // Pure Diamond White
        { r: 244, g: 114, b: 182 }, // Rose Quartz Pink
    ];

    function resize() {
        dpr = Math.min(window.devicePixelRatio || 1, 2);
        width = window.innerWidth;
        height = window.innerHeight;

        canvas.width = Math.floor(width * dpr);
        canvas.height = Math.floor(height * dpr);
        canvas.style.width = `${width}px`;
        canvas.style.height = `${height}px`;

        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(dpr, dpr);
    }

    function createStar() {
        const rand = Math.random();
        let layer = 1;
        let baseRadius = 0.55;
        let speedMultiplier = 0.12;
        let parallaxFactor = 0.05;
        let sparkleType = 'none';

        if (rand > 0.94) {
            // Layer 4: Supernova / Diamond Glints (Ekstra Gebyar 8-point flare)
            layer = 4;
            baseRadius = 2.2 + Math.random() * 1.2;
            speedMultiplier = 0.42 + Math.random() * 0.25;
            parallaxFactor = 0.38;
            sparkleType = '8-point-diamond';
        } else if (rand > 0.80) {
            // Layer 3: Radiant Gem Stars (Brilliant 4-point sparkle flare)
            layer = 3;
            baseRadius = 1.5 + Math.random() * 0.9;
            speedMultiplier = 0.32 + Math.random() * 0.18;
            parallaxFactor = 0.28;
            sparkleType = Math.random() > 0.3 ? '4-point' : 'none';
        } else if (rand > 0.48) {
            // Layer 2: Glittering mid-ground stars
            layer = 2;
            baseRadius = 0.9 + Math.random() * 0.55;
            speedMultiplier = 0.18 + Math.random() * 0.12;
            parallaxFactor = 0.14;
            sparkleType = Math.random() > 0.7 ? '4-point' : 'none';
        } else {
            // Layer 1: Dense micro-stardust field
            layer = 1;
            baseRadius = 0.4 + Math.random() * 0.45;
            speedMultiplier = 0.07 + Math.random() * 0.07;
            parallaxFactor = 0.05;
            sparkleType = 'none';
        }

        const color = STAR_COLORS[Math.floor(Math.random() * STAR_COLORS.length)];
        const angle = -Math.PI / 2 + (Math.random() - 0.45) * 0.7;
        const speed = prefersReducedMotion ? 0 : speedMultiplier;

        return {
            x: Math.random() * width,
            y: Math.random() * height,
            radius: baseRadius,
            layer,
            color,
            vx: Math.cos(angle) * speed,
            vy: Math.sin(angle) * speed,
            parallaxFactor,
            sparkleType,
            baseAlpha: layer === 4 ? 0.9 : layer === 3 ? 0.8 : layer === 2 ? 0.55 : 0.35,
            twinklePhase: Math.random() * Math.PI * 2,
            twinkleSpeed: 0.02 + Math.random() * 0.055,
            twinkleAmp: 0.25 + Math.random() * 0.35,
            rotation: Math.random() * Math.PI,
            rotationSpeed: (Math.random() - 0.5) * 0.008,
        };
    }

    function initStars() {
        stars = [];
        for (let i = 0; i < STAR_COUNT; i++) {
            stars.push(createStar());
        }
    }

    function spawnShootingStar() {
        if (prefersReducedMotion) return;
        const startX = Math.random() * (width * 1.2) - width * 0.1;
        const startY = Math.random() * (height * 0.4);
        const angle = Math.PI / 4 + (Math.random() - 0.5) * 0.3;
        const speed = 7 + Math.random() * 6;
        const length = 100 + Math.random() * 120;
        const color = Math.random() > 0.4 ? { r: 247, g: 211, b: 64 } : { r: 192, g: 132, b: 252 };

        shootingStars.push({
            x: startX,
            y: startY,
            vx: Math.cos(angle) * speed,
            vy: Math.sin(angle) * speed,
            length,
            color,
            alpha: 1,
            fadeSpeed: 0.016 + Math.random() * 0.012,
        });
    }

    // Passive listeners for fluid parallax response
    window.addEventListener('scroll', () => {
        targetScrollY = window.scrollY || 0;
    }, { passive: true });

    window.addEventListener('mousemove', e => {
        targetMouseX = (e.clientX / width - 0.5) * 2;
        targetMouseY = (e.clientY / height - 0.5) * 2;
    }, { passive: true });

    function drawDiamondFlare(cx, cy, radius, alpha, rgb, isEightPoint, rotation) {
        ctx.save();
        ctx.translate(cx, cy);
        ctx.rotate(rotation);

        const primaryArm = radius * (isEightPoint ? 4.5 : 3.5);
        const secondaryArm = primaryArm * 0.55;

        // Radiant central diamond core
        const coreSize = radius * 1.2;
        ctx.fillStyle = `rgba(255, 255, 255, ${alpha})`;
        ctx.beginPath();
        ctx.moveTo(0, -coreSize);
        ctx.lineTo(coreSize * 0.6, 0);
        ctx.lineTo(0, coreSize);
        ctx.lineTo(-coreSize * 0.6, 0);
        ctx.closePath();
        ctx.fill();

        // Primary 4-point cross flare
        ctx.strokeStyle = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${alpha * 0.85})`;
        ctx.lineWidth = isEightPoint ? 1.0 : 0.8;
        ctx.beginPath();
        ctx.moveTo(-primaryArm, 0);
        ctx.lineTo(primaryArm, 0);
        ctx.moveTo(0, -primaryArm);
        ctx.lineTo(0, primaryArm);
        ctx.stroke();

        // Secondary diagonal flare for 8-point supernovas
        if (isEightPoint) {
            ctx.strokeStyle = `rgba(255, 255, 255, ${alpha * 0.6})`;
            ctx.lineWidth = 0.65;
            ctx.beginPath();
            ctx.moveTo(-secondaryArm, -secondaryArm);
            ctx.lineTo(secondaryArm, secondaryArm);
            ctx.moveTo(secondaryArm, -secondaryArm);
            ctx.lineTo(-secondaryArm, secondaryArm);
            ctx.stroke();
        }

        ctx.restore();
    }

    function render() {
        scrollY += (targetScrollY - scrollY) * 0.08;
        mouseX += (targetMouseX - mouseX) * 0.05;
        mouseY += (targetMouseY - mouseY) * 0.05;

        ctx.clearRect(0, 0, width, height);

        // Check shooting star spawn
        const now = Date.now();
        if (now > nextShootingStarTime) {
            spawnShootingStar();
            nextShootingStarTime = now + 3000 + Math.random() * 4000;
        }

        // Render shooting stars
        for (let j = shootingStars.length - 1; j >= 0; j--) {
            const ss = shootingStars[j];
            ss.x += ss.vx;
            ss.y += ss.vy;
            ss.alpha -= ss.fadeSpeed;

            if (ss.alpha <= 0 || ss.x > width + 100 || ss.y > height + 100) {
                shootingStars.splice(j, 1);
                continue;
            }

            const tailX = ss.x - (ss.vx / Math.hypot(ss.vx, ss.vy)) * ss.length;
            const tailY = ss.y - (ss.vy / Math.hypot(ss.vx, ss.vy)) * ss.length;

            const grad = ctx.createLinearGradient(tailX, tailY, ss.x, ss.y);
            grad.addColorStop(0, `rgba(${ss.color.r}, ${ss.color.g}, ${ss.color.b}, 0)`);
            grad.addColorStop(0.7, `rgba(${ss.color.r}, ${ss.color.g}, ${ss.color.b}, ${ss.alpha * 0.5})`);
            grad.addColorStop(1, `rgba(255, 255, 255, ${ss.alpha * 0.95})`);

            ctx.save();
            ctx.strokeStyle = grad;
            ctx.lineWidth = 1.8;
            ctx.lineCap = 'round';
            ctx.beginPath();
            ctx.moveTo(tailX, tailY);
            ctx.lineTo(ss.x, ss.y);
            ctx.stroke();

            // Head glow
            ctx.fillStyle = `rgba(255, 255, 255, ${ss.alpha})`;
            ctx.beginPath();
            ctx.arc(ss.x, ss.y, 2.2, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }

        // Render stars
        for (let i = 0; i < stars.length; i++) {
            const s = stars[i];

            // Drift
            s.x += s.vx;
            s.y += s.vy;
            s.rotation += s.rotationSpeed;

            // Wrap
            if (s.x < -20) s.x = width + 20;
            if (s.x > width + 20) s.x = -20;
            if (s.y < -20) s.y = height + 20;
            if (s.y > height + 20) s.y = -20;

            // Twinkle oscillation
            s.twinklePhase += s.twinkleSpeed;
            const currentAlpha = Math.max(0.12, Math.min(1, s.baseAlpha + Math.sin(s.twinklePhase) * s.twinkleAmp));

            // Parallax
            const parallaxX = mouseX * 22 * s.parallaxFactor;
            const parallaxY = -(scrollY * s.parallaxFactor * 0.38) + (mouseY * 22 * s.parallaxFactor);

            let drawX = (s.x + parallaxX) % width;
            if (drawX < 0) drawX += width;
            let drawY = (s.y + parallaxY) % height;
            if (drawY < 0) drawY += height;

            const rgb = s.color;

            // Soft dreamy glow halo
            if (s.layer >= 3) {
                const haloRadius = s.layer === 4 ? s.radius * 4.4 : s.radius * 3.4;
                const grad = ctx.createRadialGradient(drawX, drawY, 0, drawX, drawY, haloRadius);
                grad.addColorStop(0, `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${currentAlpha * 0.95})`);
                grad.addColorStop(0.35, `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${currentAlpha * 0.38})`);
                grad.addColorStop(1, `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0)`);

                ctx.fillStyle = grad;
                ctx.beginPath();
                ctx.arc(drawX, drawY, haloRadius, 0, Math.PI * 2);
                ctx.fill();

                if (s.sparkleType === '8-point-diamond') {
                    drawDiamondFlare(drawX, drawY, s.radius, currentAlpha, rgb, true, s.rotation);
                } else if (s.sparkleType === '4-point') {
                    drawDiamondFlare(drawX, drawY, s.radius, currentAlpha, rgb, false, s.rotation);
                }
            } else if (s.layer === 2) {
                const grad = ctx.createRadialGradient(drawX, drawY, 0, drawX, drawY, s.radius * 2.4);
                grad.addColorStop(0, `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${currentAlpha * 0.88})`);
                grad.addColorStop(0.5, `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${currentAlpha * 0.25})`);
                grad.addColorStop(1, `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0)`);

                ctx.fillStyle = grad;
                ctx.beginPath();
                ctx.arc(drawX, drawY, s.radius * 2.4, 0, Math.PI * 2);
                ctx.fill();

                if (s.sparkleType === '4-point') {
                    drawDiamondFlare(drawX, drawY, s.radius, currentAlpha, rgb, false, s.rotation);
                }
            }

            // Central radiant starlight core
            ctx.fillStyle = s.layer >= 3
                ? `rgba(255, 255, 255, ${currentAlpha})`
                : `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${currentAlpha})`;
            ctx.beginPath();
            ctx.arc(drawX, drawY, s.radius * 0.85, 0, Math.PI * 2);
            ctx.fill();
        }

        animationFrameId = requestAnimationFrame(render);
    }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            if (animationFrameId) cancelAnimationFrame(animationFrameId);
        } else {
            animationFrameId = requestAnimationFrame(render);
        }
    });

    window.addEventListener('resize', () => {
        resize();
    }, { passive: true });

    resize();
    initStars();
    animationFrameId = requestAnimationFrame(render);
}
