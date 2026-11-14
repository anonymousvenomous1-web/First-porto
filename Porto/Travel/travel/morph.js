/**
 * T-RavelGuard - Pseudo PowerPoint Morph Page Transition Engine
 * 
 * Provides continuous, fluid PowerPoint-style Morph transitions between page switches:
 * - Gliding & morphing active navigation pill
 * - Direction-aware spatial morphing (forward vs backward)
 * - Coordinated View Transitions API with CSS keyframe fallback
 * - Cascading staggered reveals for incoming content
 * - Graceful fallback to native browser navigation on error
 * - Full accessibility (WCAG AA & prefers-reduced-motion)
 */

(function () {
  'use strict';

  // Order of pages to determine spatial morph direction (slide left vs slide right)
  const MAIN_PAGE_RANKS = {
    'home': 0,
    'mobil': 1,
    'carter': 2,
    'kelola': 3
  };

  const AUTH_FORM_RANKS = {
    'login': 0,
    'register': 1,
    'register_driver': 2,
    'register_penumpang': 3
  };

  const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Extract page rank from a URL for index.php
  function getPageIndex(urlStr) {
    try {
      const url = new URL(urlStr, window.location.origin);
      if (url.pathname.endsWith('login.php')) {
        const form = url.searchParams.get('form') || 'login';
        const role = url.searchParams.get('role') || '';
        if (form === 'register') {
          if (role === 'driver') return AUTH_FORM_RANKS.register_driver;
          if (role === 'penumpang') return AUTH_FORM_RANKS.register_penumpang;
          return AUTH_FORM_RANKS.register;
        }
        return AUTH_FORM_RANKS.login;
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
    }, 60);
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
  // Morph Pill Indicator for .main-nav & .auth-role-switch
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
  // Page Swapper & Content Morph
  // =========================================================================

  let isNavigating = false;
  let activePillController = null;

  // Initialize interactive components within a scope
  function initializeComponents(scope = document) {
    // Re-bind data-pending-form handlers
    scope.querySelectorAll('[data-pending-form]').forEach((form) => {
      if (form.__hasPendingHandler) return;
      form.__hasPendingHandler = true;
      form.addEventListener('submit', (event) => {
        if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
          event.preventDefault();
          return;
        }
        const pending = form.querySelector('.form-pending');
        const submit = form.querySelector('button[type="submit"]');
        if (pending) pending.hidden = false;
        if (submit) submit.disabled = true;
        form.setAttribute('aria-busy', 'true');
      });
    });

    // Re-init password reveal toggles if helper exists
    if (typeof window.initPasswordReveal === 'function') {
      window.initPasswordReveal(scope);
    }

    // Fire custom page changed event
    window.dispatchEvent(new CustomEvent('travel:pagechanged', { detail: { scope } }));
  }

  // Performs DOM replacement from parsed new Document
  function applyPageSwap(newDoc, targetUrl, direction) {
    // Update document title
    if (newDoc.title) {
      document.title = newDoc.title;
    }

    // 1. Swap main app content (index.php)
    const currentMain = document.querySelector('#main-content');
    const newMain = newDoc.querySelector('#main-content');
    if (currentMain && newMain) {
      currentMain.innerHTML = newMain.innerHTML;
      currentMain.className = newMain.className;

      // Copy any updated attributes
      for (const attr of newMain.attributes) {
        currentMain.setAttribute(attr.name, attr.value);
      }
    }

    // 2. Swap auth card (login.php)
    const currentAuthCard = document.querySelector('.auth-card');
    const newAuthCard = newDoc.querySelector('.auth-card');
    if (currentAuthCard && newAuthCard) {
      currentAuthCard.innerHTML = newAuthCard.innerHTML;
    }

    // 3. Update main-nav active link and role label
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
          currentLinks[index].textContent = newLink.textContent;
          currentLinks[index].href = newLink.href;
        }
      });

      if (activePillController) {
        activePillController.update();
      }
    }

    // 4. Update auth-role-switch in login if present
    const currentRoleSwitch = document.querySelector('.auth-role-switch');
    const newRoleSwitch = newDoc.querySelector('.auth-role-switch');
    if (currentRoleSwitch && newRoleSwitch) {
      currentRoleSwitch.innerHTML = newRoleSwitch.innerHTML;
    }

    // 5. Update topbar account info if needed
    const currentAccount = document.querySelector('.account-label');
    const newAccount = newDoc.querySelector('.account-label');
    if (currentAccount && newAccount) {
      currentAccount.textContent = newAccount.textContent;
    }

    // Accessibility: move focus to page title or main container
    const headingToFocus = document.querySelector('#page-title, h1, .auth-card h1');
    if (headingToFocus) {
      headingToFocus.setAttribute('tabindex', '-1');
      headingToFocus.focus({ preventScroll: true });
    }

    // Initialize newly injected forms, buttons, and reveal helpers
    initializeComponents(document);
  }

  // Smooth PowerPoint Morph Navigation handler
  async function morphNavigate(targetUrl, direction, isPopState = false) {
    if (isNavigating) return;
    isNavigating = true;
    startProgress();

    // Visual feedback on nav pill immediately so it starts gliding
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
        // Fallback to normal navigation if server returns error
        window.location.href = targetUrl;
        return;
      }

      const html = await response.text();
      const parser = new DOMParser();
      const newDoc = parser.parseFromString(html, 'text/html');

      // Set morph direction attribute on <html> for CSS transitions
      document.documentElement.setAttribute('data-morph-dir', direction);

      const targetContainer = document.querySelector('#main-content') || document.querySelector('.auth-card');

      // Use native View Transitions API if supported and user has not reduced motion
      if ('startViewTransition' in document && !prefersReducedMotion()) {
        const transition = document.startViewTransition(() => {
          applyPageSwap(newDoc, targetUrl, direction);
          if (!isPopState) {
            window.history.pushState({ url: targetUrl }, '', targetUrl);
          }
          // Scroll smoothly to top or hash target
          const targetHash = new URL(targetUrl).hash;
          if (targetHash) {
            const el = document.querySelector(targetHash);
            if (el) el.scrollIntoView({ behavior: 'smooth' });
          } else {
            window.scrollTo({ top: 0, behavior: 'smooth' });
          }
        });

        await transition.finished;
      } else {
        // High-performance CSS keyframe morph fallback
        if (targetContainer && !prefersReducedMotion()) {
          targetContainer.classList.remove('morph-enter-forward', 'morph-enter-backward');
          targetContainer.classList.add(`morph-exit-${direction}`);

          await new Promise((resolve) => setTimeout(resolve, 200));

          applyPageSwap(newDoc, targetUrl, direction);
          if (!isPopState) {
            window.history.pushState({ url: targetUrl }, '', targetUrl);
          }

          const targetHash = new URL(targetUrl).hash;
          if (targetHash) {
            const el = document.querySelector(targetHash);
            if (el) el.scrollIntoView({ behavior: 'smooth' });
          } else {
            window.scrollTo({ top: 0, behavior: 'smooth' });
          }

          const newContainer = document.querySelector('#main-content') || document.querySelector('.auth-card');
          if (newContainer) {
            newContainer.classList.remove(`morph-exit-${direction}`);
            newContainer.classList.add(`morph-enter-${direction}`);
            setTimeout(() => {
              newContainer.classList.remove(`morph-enter-${direction}`);
            }, 520);
          }
        } else {
          // Instant swap for reduced-motion
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

  // Intercept navigation clicks
  function handleLinkClick(event) {
    // Ignore modified clicks (new tab, etc.)
    if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
      return;
    }

    const link = event.target.closest('a');
    if (!link || !link.href) return;

    // Ignore downloads, external links, anchor-only links, or logout
    const targetUrl = new URL(link.href, window.location.href);
    if (targetUrl.origin !== window.location.origin) return;
    if (targetUrl.pathname.endsWith('logout.php')) return;
    if (link.hasAttribute('download') || link.getAttribute('target') === '_blank') return;

    // Check if target is same page (excluding hash)
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

    // Eligible internal page switch: execute morph!
    event.preventDefault();
    const direction = getMorphDirection(window.location.href, targetUrl.href);
    morphNavigate(targetUrl.href, direction, false);
  }

  // Handle browser Back / Forward history buttons
  window.addEventListener('popstate', () => {
    const direction = getMorphDirection(window.history.state?.prevUrl || window.location.href, window.location.href);
    morphNavigate(window.location.href, direction, true);
  });

  // =========================================================================
  // Initialize Morph Engine
  // =========================================================================

  function init() {
    // Intercept clicks on links globally
    document.addEventListener('click', handleLinkClick);

    // Setup main navigation morph pill
    const mainNav = document.querySelector('.main-nav');
    if (mainNav) {
      activePillController = setupNavMorphPill(mainNav);
    }

    // Setup role choices or switch in login
    const authRoleSwitch = document.querySelector('.auth-role-switch');
    if (authRoleSwitch) {
      setupNavMorphPill(authRoleSwitch);
    }

    // Initial component setup
    initializeComponents(document);

    // Add entry animation on first page load for seamless PowerPoint entrance
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
