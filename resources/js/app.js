// Portfolio: Vesper entrance animations, mobile nav, project filters, project modal, contact form, scroll-reveal, monochrome parallax stars

document.addEventListener('DOMContentLoaded', () => {
    initVesperAppear();
    initParallaxStars();
    initMobileNav();
    initProjectFilters();
    initProjectModal();
    initContactForm();
    initScrollReveal();
});

/* ── Vesper entrance animation cleanup ───────────────────────── */
function initVesperAppear() {
    const items = document.querySelectorAll('.appear');

    items.forEach(el => {
        el.addEventListener('animationend', () => {
            el.classList.add('is-in');
        }, { once: true });
    });

    // Two-rAF fallback: if no animation started (e.g. prefers-reduced-motion or cached page), force final state
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            items.forEach(el => {
                const anims = el.getAnimations ? el.getAnimations() : [];
                const alive = anims.some(a => a.playState === 'running' || a.playState === 'finished');
                if (!alive) {
                    el.classList.add('is-in');
                }
            });
        });
    });
}

/* ── Mobile Navigation (Vesper overlay + R-03, R-32, R-39) ──────────── */
function initMobileNav() {
    const btn  = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    if (!btn || !menu) return;

    function open() {
        menu.classList.remove('hidden');
        document.body.classList.add('menu-open');
        btn.setAttribute('aria-expanded', 'true');
        btn.setAttribute('aria-label', 'Tutup menu navigasi');
        const first = menu.querySelector('a');
        if (first) first.focus();
    }
    function close() {
        document.body.classList.remove('menu-open');
        menu.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
        btn.setAttribute('aria-label', 'Buka menu navigasi');
    }

    btn.addEventListener('click', () => {
        document.body.classList.contains('menu-open') ? close() : open();
    });

    menu.querySelectorAll('a').forEach(a => a.addEventListener('click', close));

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && document.body.classList.contains('menu-open')) {
            close();
            btn.focus();
        }
    });

    const mq = window.matchMedia('(min-width: 768px)');
    mq.addEventListener('change', e => {
        if (e.matches && document.body.classList.contains('menu-open')) close();
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
        });

        if (moveFocus) {
            tab.focus();
        }

        updatePill(tab, true);

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

    updatePill(activeTab, false);

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            selectTab(tab);
        });
    });

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
    const modal = document.getElementById('project-modal');
    if (!modal) return;

    const get = id => document.getElementById(id);
    let lastFocus = null;

    function populateAndOpen(data) {
        lastFocus = document.activeElement;

        get('modal-project-title').textContent    = data.title || '';
        get('modal-project-tagline').textContent  = data.tagline || '';
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
            li.className = 'flex items-start gap-3 text-sm leading-relaxed text-[#d8d8d8]';
            li.innerHTML = `<span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-white/70 inline-block" aria-hidden="true"></span><span>${esc(f)}</span>`;
            featureList.appendChild(li);
        });

        const techWrap = get('modal-project-tech-stack');
        techWrap.innerHTML = '';
        (data.techStack || []).forEach(t => {
            const span = document.createElement('span');
            span.className = 'tech-chip';
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

    const submitBtn  = form.querySelector('button[type="submit"]');
    const btnText    = document.getElementById('submit-btn-text');
    const btnSpinner = document.getElementById('submit-btn-spinner');
    const statusBox  = document.getElementById('contact-form-status');

    function clearErrors() {
        form.querySelectorAll('[data-error-feedback]').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        form.querySelectorAll('input, textarea').forEach(el => {
            el.classList.remove('error');
        });
        if (statusBox) { statusBox.className = 'hidden'; statusBox.textContent = ''; }
    }

    function showStatus(msg, ok = true) {
        if (!statusBox) return;
        statusBox.className = ok
            ? 'mb-6 rounded-lg border border-emerald-500/30 bg-emerald-900/20 p-4 text-sm font-medium text-emerald-300'
            : 'mb-6 rounded-lg border border-red-500/30 bg-red-900/20 p-4 text-sm font-medium text-red-300';
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
                    if (input) input.classList.add('error');
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

/* ── Scroll Reveal ────────────────────────────────────────────────────── */
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

/* ── Monochrome Starlight Parallax Background ─────────────────────────── */
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
    let animationFrameId = null;

    let scrollY = window.scrollY || 0;
    let targetScrollY = scrollY;
    let mouseX = 0;
    let mouseY = 0;
    let targetMouseX = 0;
    let targetMouseY = 0;

    const STAR_COUNT = Math.min(220, Math.max(110, Math.floor(window.innerWidth / 6.5)));

    // Vesper monochrome starlight palette
    const STAR_COLORS = [
        { r: 255, g: 255, b: 255 },
        { r: 216, g: 216, b: 216 },
        { r: 200, g: 210, b: 230 },
        { r: 170, g: 185, b: 215 },
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
        let baseRadius = 0.6;
        let speedMultiplier = 0.1;
        let parallaxFactor = 0.04;

        if (rand > 0.84) {
            layer = 3;
            baseRadius = 1.4 + Math.random() * 0.7;
            speedMultiplier = 0.28 + Math.random() * 0.14;
            parallaxFactor = 0.28;
        } else if (rand > 0.45) {
            layer = 2;
            baseRadius = 0.9 + Math.random() * 0.45;
            speedMultiplier = 0.16 + Math.random() * 0.1;
            parallaxFactor = 0.12;
        } else {
            layer = 1;
            baseRadius = 0.45 + Math.random() * 0.4;
            speedMultiplier = 0.06 + Math.random() * 0.06;
            parallaxFactor = 0.04;
        }

        const color = STAR_COLORS[Math.floor(Math.random() * STAR_COLORS.length)];
        const angle = -Math.PI / 2 + (Math.random() - 0.45) * 0.6;
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
            baseAlpha: layer === 3 ? 0.65 : layer === 2 ? 0.42 : 0.22,
            twinklePhase: Math.random() * Math.PI * 2,
            twinkleSpeed: 0.012 + Math.random() * 0.025,
            twinkleAmp: 0.14 + Math.random() * 0.18,
            isSparkle: layer === 3 && Math.random() > 0.55,
        };
    }

    function initStars() {
        stars = [];
        for (let i = 0; i < STAR_COUNT; i++) {
            stars.push(createStar());
        }
    }

    window.addEventListener('scroll', () => {
        targetScrollY = window.scrollY || 0;
    }, { passive: true });

    window.addEventListener('mousemove', e => {
        targetMouseX = (e.clientX / width - 0.5) * 2;
        targetMouseY = (e.clientY / height - 0.5) * 2;
    }, { passive: true });

    function drawSparkleCross(cx, cy, radius, alpha, rgb) {
        ctx.save();
        ctx.strokeStyle = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${alpha * 0.6})`;
        ctx.lineWidth = 0.7;
        const arm = radius * 3.0;

        ctx.beginPath();
        ctx.moveTo(cx - arm, cy);
        ctx.lineTo(cx + arm, cy);
        ctx.moveTo(cx, cy - arm);
        ctx.lineTo(cx, cy + arm);
        ctx.stroke();
        ctx.restore();
    }

    function render() {
        scrollY += (targetScrollY - scrollY) * 0.07;
        mouseX += (targetMouseX - mouseX) * 0.05;
        mouseY += (targetMouseY - mouseY) * 0.05;

        ctx.clearRect(0, 0, width, height);

        for (let i = 0; i < stars.length; i++) {
            const s = stars[i];

            s.x += s.vx;
            s.y += s.vy;

            if (s.x < -20) s.x = width + 20;
            if (s.x > width + 20) s.x = -20;
            if (s.y < -20) s.y = height + 20;
            if (s.y > height + 20) s.y = -20;

            s.twinklePhase += s.twinkleSpeed;
            const currentAlpha = Math.max(0.08, Math.min(0.95, s.baseAlpha + Math.sin(s.twinklePhase) * s.twinkleAmp));

            const parallaxX = mouseX * 18 * s.parallaxFactor;
            const parallaxY = -(scrollY * s.parallaxFactor * 0.35) + (mouseY * 18 * s.parallaxFactor);

            let drawX = (s.x + parallaxX) % width;
            if (drawX < 0) drawX += width;
            let drawY = (s.y + parallaxY) % height;
            if (drawY < 0) drawY += height;

            const rgb = s.color;

            if (s.layer === 3) {
                const grad = ctx.createRadialGradient(drawX, drawY, 0, drawX, drawY, s.radius * 3.2);
                grad.addColorStop(0, `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${currentAlpha * 0.8})`);
                grad.addColorStop(0.35, `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${currentAlpha * 0.25})`);
                grad.addColorStop(1, `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0)`);

                ctx.fillStyle = grad;
                ctx.beginPath();
                ctx.arc(drawX, drawY, s.radius * 3.2, 0, Math.PI * 2);
                ctx.fill();

                if (s.isSparkle) {
                    drawSparkleCross(drawX, drawY, s.radius, currentAlpha, rgb);
                }
            } else if (s.layer === 2) {
                const grad = ctx.createRadialGradient(drawX, drawY, 0, drawX, drawY, s.radius * 2.0);
                grad.addColorStop(0, `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${currentAlpha * 0.75})`);
                grad.addColorStop(0.5, `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${currentAlpha * 0.16})`);
                grad.addColorStop(1, `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0)`);

                ctx.fillStyle = grad;
                ctx.beginPath();
                ctx.arc(drawX, drawY, s.radius * 2.0, 0, Math.PI * 2);
                ctx.fill();
            }

            ctx.fillStyle = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${currentAlpha})`;
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
