/**
 * PustakaGuard - PowerPoint Morph Page Transition Engine
 * 
 * Provides continuous, fluid PowerPoint-style Morph transitions between library pages:
 * - Gliding & morphing active navigation pill
 * - Direction-aware spatial morphing (forward vs backward)
 * - View Transitions API with CSS keyframe fallback
 * - Cascading staggered reveals for incoming content
 * - Full accessibility (WCAG AA & prefers-reduced-motion)
 * - Dynamic light/dark theme toggle with persistent storage
 * - Interactive pointer tracking glow
 * - Reinitialization of DataTables & Select2 on page swap
 */

(function () {
  'use strict';

  // Spatial page ranks to determine morph slide direction
  const MAIN_PAGE_RANKS = {
    'home': 0,
    'buku': 1,
    'bukutambah': 1,
    'bukuedit': 1,
    'penulis': 1,
    'penulistambah': 1,
    'penulisedit': 1,
    'penerbit': 1,
    'penerbittambah': 1,
    'penerbitedit': 1,
    'siswa': 2,
    'siswatambah': 2,
    'siswaedit': 2,
    'petugas': 3,
    'petugastambah': 3,
    'petugasedit': 3,
    'peminjaman': 4,
    'peminjamantambah': 4
  };

  const AUTH_FORM_RANKS = {
    'login': 0,
    'register': 1
  };

  const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Extract page rank from a URL for index.php
  function getPageIndex(urlStr) {
    try {
      const url = new URL(urlStr, window.location.origin);
      if (url.pathname.endsWith('login.php')) {
        const form = url.searchParams.get('form') || 'login';
        return AUTH_FORM_RANKS[form] ?? 0;
      }
      const page = url.searchParams.get('halaman') || 'home';
      return MAIN_PAGE_RANKS[page] ?? 0;
    } catch {
      return 0;
    }
  }

  // Determine direction between two URLs: 'forward' | 'backward'
  function getMorphDirection(fromUrl, toUrl) {
    const fromIndex = getPageIndex(fromUrl);
    const toIndex = getPageIndex(toUrl);
    return toIndex >= fromIndex ? 'forward' : 'backward';
  }

  // Create or retrieve top shimmer morph progress bar
  let progressBar = null;
  let progressTimer = null;

  function getProgressBar() {
    if (!progressBar) {
      progressBar = document.createElement('div');
      progressBar.className = 'morph-progress-bar';
      progressBar.setAttribute('aria-hidden', 'true');
      document.body.prepend(progressBar);
    }
    return progressBar;
  }

  function startProgress() {
    clearTimeout(progressTimer);
    progressTimer = setTimeout(() => {
      const bar = getProgressBar();
      bar.classList.remove('is-finishing');
      bar.classList.add('is-active');
    }, 50);
  }

  function endProgress() {
    clearTimeout(progressTimer);
    if (!progressBar) return;
    progressBar.classList.remove('is-active');
    progressBar.classList.add('is-finishing');
    setTimeout(() => {
      if (progressBar) progressBar.classList.remove('is-finishing');
    }, 380);
  }

  // =========================================================================
  // Morph Pill Indicator for .main-nav
  // =========================================================================

  function setupNavMorphPill(navContainer) {
    if (!navContainer) return null;

    let pill = navContainer.querySelector('.nav-morph-pill');
    if (!pill) {
      pill = document.createElement('span');
      pill.className = 'nav-morph-pill';
      pill.setAttribute('aria-hidden', 'true');
      navContainer.prepend(pill);
    }
    navContainer.classList.add('has-morph-pill');

    function updatePillPosition(targetElement, instant = false) {
      if (!targetElement || !pill) return;
      const navRect = navContainer.getBoundingClientRect();
      const targetRect = targetElement.getBoundingClientRect();

      const left = targetRect.left - navRect.left;
      const top = targetRect.top - navRect.top;
      const width = targetRect.width;
      const height = targetRect.height;

      if (instant || prefersReducedMotion()) {
        pill.style.transition = 'none';
      } else {
        pill.style.transition = '';
      }

      pill.style.transform = `translate3d(${left}px, ${top}px, 0)`;
      pill.style.width = `${width}px`;
      pill.style.height = `${height}px`;
      pill.style.opacity = '1';
    }

    const currentLink = navContainer.querySelector('a[aria-current="page"]');
    if (currentLink) {
      updatePillPosition(currentLink, true);
      requestAnimationFrame(() => updatePillPosition(currentLink));
    }

    // Interactive morph glide on hover
    navContainer.querySelectorAll('a').forEach((link) => {
      link.addEventListener('mouseenter', () => updatePillPosition(link));
      link.addEventListener('focus', () => updatePillPosition(link));
    });

    navContainer.addEventListener('mouseleave', () => {
      const active = navContainer.querySelector('a[aria-current="page"]');
      if (active) updatePillPosition(active);
    });

    window.addEventListener('resize', () => {
      const active = navContainer.querySelector('a[aria-current="page"]');
      if (active) updatePillPosition(active, true);
    });

    return {
      update: (target) => updatePillPosition(target || navContainer.querySelector('a[aria-current="page"]'))
    };
  }

  // =========================================================================
  // Library Plugins & Components Initializer (DataTables & Select2)
  // =========================================================================

  function initializeComponents(scope = document) {
    // Re-init password reveal
    if (typeof window.initPasswordReveal === 'function') {
      window.initPasswordReveal(scope);
    }

    // Form submission pending indicators
    scope.querySelectorAll('form').forEach((form) => {
      if (form.__hasPendingHandler) return;
      form.__hasPendingHandler = true;
      form.addEventListener('submit', (event) => {
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitBtn && !submitBtn.disabled) {
          // Allow confirm dialogues to cancel before disabling
          setTimeout(() => {
            if (!event.defaultPrevented) {
              submitBtn.setAttribute('data-original-text', submitBtn.tagName === 'INPUT' ? submitBtn.value : submitBtn.textContent);
              if (submitBtn.tagName === 'INPUT') {
                submitBtn.value = 'Memproses...';
              } else {
                submitBtn.textContent = 'Memproses...';
              }
              submitBtn.disabled = true;
            }
          }, 0);
        }
      });
    });

    // Reinitialize Select2 dropdowns if jQuery is loaded
    if (window.jQuery && typeof jQuery.fn.select2 === 'function') {
      const $selects = jQuery(scope).find('.konten-utama select, .auth-form select');
      if ($selects.length) {
        $selects.each(function() {
          const $el = jQuery(this);
          if (!$el.hasClass('select2-hidden-accessible')) {
            $el.select2({
              width: '100%',
              dropdownParent: $el.parent()
            });
          }
        });
      }
    }

    // Reinitialize DataTables if jQuery is loaded
    if (window.jQuery && typeof jQuery.fn.DataTable === 'function') {
      const $tables = jQuery(scope).find('.data-table');
      if ($tables.length) {
        $tables.each(function() {
          const $table = jQuery(this);
          if (!jQuery.fn.DataTable.isDataTable(this)) {
            $table.DataTable({
              language: {
                search: "Cari:",
                searchPlaceholder: "Ketik kata kunci...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang cocok",
                paginate: {
                  first: "«",
                  last: "»",
                  next: "›",
                  previous: "‹"
                }
              },
              pageLength: 10,
              columnDefs: [
                { orderable: false, targets: 'no-sort' }
              ]
            });
          }
        });
      }
    }
  }

  // =========================================================================
  // Page Swapper & Content Morph
  // =========================================================================

  let isNavigating = false;
  let activePillController = null;

  function applyPageSwap(newDoc, targetUrl, direction) {
    if (newDoc.title) {
      document.title = newDoc.title;
    }

    // 1. Swap main app content (index.php)
    const currentMain = document.querySelector('#main-content');
    const newMain = newDoc.querySelector('#main-content');
    if (currentMain && newMain) {
      // Destroy active DataTables before innerHTML replacement to avoid leaks
      if (window.jQuery && typeof jQuery.fn.DataTable === 'function') {
        jQuery(currentMain).find('.data-table').each(function() {
          if (jQuery.fn.DataTable.isDataTable(this)) {
            jQuery(this).DataTable().destroy(true);
          }
        });
      }

      currentMain.innerHTML = newMain.innerHTML;
      currentMain.className = newMain.className;
    }

    // 2. Swap auth card (login.php)
    const currentAuthCard = document.querySelector('.auth-card');
    const newAuthCard = newDoc.querySelector('.auth-card');
    if (currentAuthCard && newAuthCard) {
      currentAuthCard.innerHTML = newAuthCard.innerHTML;
    }

    // 3. Update main-nav active links
    const currentNav = document.querySelector('.main-nav');
    const newNav = newDoc.querySelector('.main-nav');
    if (currentNav && newNav) {
      const currentLinks = currentNav.querySelectorAll('a');
      const newLinks = newNav.querySelectorAll('a');
      newLinks.forEach((newLink, index) => {
        if (currentLinks[index]) {
          if (newLink.hasAttribute('aria-current')) {
            currentLinks[index].setAttribute('aria-current', 'page');
          } else {
            currentLinks[index].removeAttribute('aria-current');
          }
          currentLinks[index].href = newLink.href;
        }
      });

      if (activePillController) {
        activePillController.update();
      }
    }

    // 4. Update topbar account info if needed
    const currentAccount = document.querySelector('.account-label');
    const newAccount = newDoc.querySelector('.account-label');
    if (currentAccount && newAccount) {
      currentAccount.textContent = newAccount.textContent;
    }

    // Focus management for accessibility
    const heading = document.querySelector('#page-title, .konten-utama h2, .auth-card h1');
    if (heading) {
      heading.setAttribute('tabindex', '-1');
      heading.focus({ preventScroll: true });
    }

    // Initialize newly injected interactive elements
    initializeComponents(document);
  }

  async function morphNavigate(targetUrl, direction, isPopState = false) {
    if (isNavigating) return;
    isNavigating = true;
    startProgress();

    // Instant nav pill glide
    const nav = document.querySelector('.main-nav');
    if (nav) {
      const matchLink = Array.from(nav.querySelectorAll('a')).find((a) => {
        try {
          return new URL(a.href).search === new URL(targetUrl).search;
        } catch {
          return false;
        }
      });
      if (matchLink && activePillController) {
        nav.querySelectorAll('a').forEach((a) => a.removeAttribute('aria-current'));
        matchLink.setAttribute('aria-current', 'page');
        activePillController.update(matchLink);
      }
    }

    try {
      const response = await fetch(targetUrl, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      if (!response.ok) {
        window.location.href = targetUrl;
        return;
      }

      const html = await response.text();
      const parser = new DOMParser();
      const newDoc = parser.parseFromString(html, 'text/html');

      document.documentElement.setAttribute('data-morph-dir', direction);

      const targetContainer = document.querySelector('#main-content') || document.querySelector('.auth-card');

      if ('startViewTransition' in document && !prefersReducedMotion()) {
        const transition = document.startViewTransition(() => {
          applyPageSwap(newDoc, targetUrl, direction);
          if (!isPopState) {
            window.history.pushState({ url: targetUrl }, '', targetUrl);
          }
          window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        await transition.finished;
      } else {
        if (targetContainer && !prefersReducedMotion()) {
          targetContainer.classList.remove('morph-enter-forward', 'morph-enter-backward');
          targetContainer.classList.add(`morph-exit-${direction}`);

          await new Promise((resolve) => setTimeout(resolve, 180));

          applyPageSwap(newDoc, targetUrl, direction);
          if (!isPopState) {
            window.history.pushState({ url: targetUrl }, '', targetUrl);
          }
          window.scrollTo({ top: 0, behavior: 'smooth' });

          const newContainer = document.querySelector('#main-content') || document.querySelector('.auth-card');
          if (newContainer) {
            newContainer.classList.remove(`morph-exit-${direction}`);
            newContainer.classList.add(`morph-enter-${direction}`);
            setTimeout(() => {
              newContainer.classList.remove(`morph-enter-${direction}`);
            }, 500);
          }
        } else {
          applyPageSwap(newDoc, targetUrl, direction);
          if (!isPopState) {
            window.history.pushState({ url: targetUrl }, '', targetUrl);
          }
        }
      }
    } catch (err) {
      console.warn('Morph transition fallback to standard navigation:', err);
      window.location.href = targetUrl;
    } finally {
      endProgress();
      isNavigating = false;
    }
  }

  function handleLinkClick(event) {
    if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
      return;
    }

    const link = event.target.closest('a');
    if (!link || !link.href) return;

    // Ignore logout, file uploads, image popups, external links, anchor links
    const targetUrl = new URL(link.href, window.location.href);
    if (targetUrl.origin !== window.location.origin) return;
    if (targetUrl.pathname.endsWith('logout.php')) return;
    if (link.hasAttribute('download') || link.getAttribute('target') === '_blank') return;
    if (targetUrl.pathname.includes('/img/')) return;

    // Same page check
    const isSamePage = targetUrl.pathname === window.location.pathname &&
                       targetUrl.search === window.location.search;

    if (isSamePage) {
      if (targetUrl.hash) {
        const hashTarget = document.querySelector(targetUrl.hash);
        if (hashTarget) {
          event.preventDefault();
          hashTarget.scrollIntoView({ behavior: 'smooth' });
          window.history.pushState(null, '', targetUrl.href);
        }
      }
      return;
    }

    event.preventDefault();
    const direction = getMorphDirection(window.location.href, targetUrl.href);
    morphNavigate(targetUrl.href, direction, false);
  }

  window.addEventListener('popstate', () => {
    const direction = getMorphDirection(window.history.state?.url || window.location.href, window.location.href);
    morphNavigate(window.location.href, direction, true);
  });

  // =========================================================================
  // Theme Switcher System (Light / Dark Mode)
  // =========================================================================

  function initThemeToggle() {
    const themeToggle = document.querySelector('.theme-toggle');
    const themeText = document.querySelector('.theme-toggle-text');
    const storedTheme = localStorage.getItem('perpus_theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const initialTheme = storedTheme || (prefersDark ? 'dark' : 'light');

    const applyTheme = (theme) => {
      const nextTheme = theme === 'dark' ? 'dark' : 'light';
      document.body.setAttribute('data-theme', nextTheme);
      localStorage.setItem('perpus_theme', nextTheme);
      if (themeToggle) {
        const isDark = nextTheme === 'dark';
        themeToggle.setAttribute('aria-pressed', String(isDark));
        themeToggle.setAttribute('aria-label', isDark ? 'Beralih ke mode terang' : 'Beralih ke mode gelap');
      }
      if (themeText) {
        themeText.textContent = nextTheme === 'dark' ? 'Dark' : 'Light';
      }
    };

    applyTheme(initialTheme);

    if (themeToggle && !themeToggle.__hasToggleListener) {
      themeToggle.__hasToggleListener = true;
      themeToggle.addEventListener('click', () => {
        const currentTheme = document.body.getAttribute('data-theme') || 'light';
        const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(nextTheme);
      });
    }
  }

  // =========================================================================
  // Interactive Pointer Light Glow
  // =========================================================================

  function initPointerLight() {
    let ticking = false;
    window.addEventListener('pointermove', (event) => {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(() => {
        const x = (event.clientX / window.innerWidth) * 100;
        const y = (event.clientY / window.innerHeight) * 100;
        document.documentElement.style.setProperty('--page-x', `${x.toFixed(1)}%`);
        document.documentElement.style.setProperty('--page-y', `${y.toFixed(1)}%`);
        ticking = false;
      });
    }, { passive: true });
  }

  // =========================================================================
  // Initialization
  // =========================================================================

  function init() {
    document.addEventListener('click', handleLinkClick);

    const mainNav = document.querySelector('.main-nav');
    if (mainNav) {
      activePillController = setupNavMorphPill(mainNav);
    }

    initThemeToggle();
    initPointerLight();
    initializeComponents(document);

    const initialContent = document.querySelector('#main-content') || document.querySelector('.auth-card');
    if (initialContent && !prefersReducedMotion()) {
      initialContent.classList.add('morph-enter-forward');
      setTimeout(() => {
        initialContent.classList.remove('morph-enter-forward');
      }, 550);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

