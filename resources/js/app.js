// Portfolio: Theme, mobile nav, project filters, project modal, contact form, scroll-reveal

document.addEventListener('DOMContentLoaded', () => {
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

/* ── Project Category Filters (R-26, R-27) ────────────────────────────── */
function initProjectFilters() {
    const tabs        = document.querySelectorAll('.project-filter-tab');
    const cards       = document.querySelectorAll('.project-card');
    const emptyState  = document.getElementById('projects-empty-state');
    if (!tabs.length) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const cat = tab.dataset.category;

            tabs.forEach(t => {
                const active = t === tab;
                t.setAttribute('aria-selected', active ? 'true' : 'false');
                if (active) {
                    t.classList.add('bg-gold-active', 'text-void', 'glow-gold-sm');
                    t.classList.remove('bg-plum-inactive', 'text-plum-light');
                } else {
                    t.classList.remove('bg-gold-active', 'text-void', 'glow-gold-sm');
                    t.classList.add('bg-plum-inactive', 'text-plum-light');
                }
            });

            let visible = 0;
            cards.forEach(card => {
                const show = cat === 'all' || card.dataset.category === cat;
                card.classList.toggle('hidden', !show);
                if (show) visible++;
            });

            if (emptyState) emptyState.classList.toggle('hidden', visible > 0);
        });
    });
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
        if (btnText)    btnText.textContent = 'Transmitting...';
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
                showStatus(data.message || 'Message sent. I will reply promptly.', true);
            } else if (res.status === 422 && data.errors) {
                Object.entries(data.errors).forEach(([field, msgs]) => {
                    const fb    = form.querySelector(`[data-error-feedback="${field}"]`);
                    const input = form.querySelector(`[name="${field}"]`);
                    if (fb) { fb.textContent = msgs[0]; fb.classList.remove('hidden'); }
                    if (input) input.classList.add('border-red-500');
                });
                showStatus('Please fix the highlighted fields above.', false);
            } else {
                showStatus('Something went wrong. Please try again or email directly.', false);
            }
        } catch {
            form.submit();
        } finally {
            if (submitBtn) submitBtn.disabled = false;
            if (btnText)    btnText.textContent = 'Send Message';
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
