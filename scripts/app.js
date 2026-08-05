/**
 * WikiDocs — Modern UI App Script
 * Desktop: sidebar collapses as drawer
 * Mobile: sidebar opens as overlay
 */

(function () {
  'use strict';

  const themeToggle = document.getElementById('theme-toggle');
  const html = document.documentElement;
  const savedTheme = localStorage.getItem('wikidocs-theme');
  if (savedTheme) { html.setAttribute('data-theme', savedTheme); }

  if (themeToggle) {
    themeToggle.addEventListener('click', function () {
      const current = html.getAttribute('data-theme');
      const next = current === 'dark' ? 'light' : 'dark';
      html.setAttribute('data-theme', next);
      localStorage.setItem('wikidocs-theme', next);
    });
  }

  const langBtn = document.getElementById('lang-switcher-btn');
  const langDropdown = document.getElementById('lang-dropdown');
  if (langBtn && langDropdown) {
    langBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      langDropdown.classList.toggle('active');
    });
    document.addEventListener('click', function (e) {
      if (!langBtn.contains(e.target) && !langDropdown.contains(e.target)) {
        langDropdown.classList.remove('active');
      }
    });
  }

  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  const menuBtn = document.getElementById('mobile-menu-btn');

  function isDesktop() { return window.innerWidth > 1024; }

  if (menuBtn && sidebar) {
    menuBtn.addEventListener('click', function () {
      if (isDesktop()) {
        sidebar.classList.toggle('collapsed');
      } else {
        if (sidebar.classList.contains('mobile-open')) { closeSidebar(); }
        else { openSidebar(); }
      }
    });
    if (overlay) {
      overlay.addEventListener('click', closeSidebar);
    }
    window.addEventListener('resize', function () {
      if (isDesktop()) closeSidebar();
    });
  }

  function openSidebar() {
    sidebar.classList.add('mobile-open');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  document.addEventListener('click', function (e) {
    const trigger = e.target.closest('.modal-trigger');
    if (trigger) { openModal(trigger.getAttribute('data-modal')); return; }
    const closeBtn = e.target.closest('.modal-close');
    if (closeBtn) { closeModal(closeBtn.getAttribute('data-modal')); return; }
    if (e.target.classList.contains('modal-overlay') && e.target.classList.contains('active')) {
      closeModal(e.target.id);
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.active').forEach(function (m) { closeModal(m.id); });
      if (sidebar && sidebar.classList.contains('mobile-open')) closeSidebar();
      if (sidebar && isDesktop() && sidebar.classList.contains('collapsed')) {
        sidebar.classList.remove('collapsed');
      }
      if (langDropdown) langDropdown.classList.remove('active');
    }
  });

  function openModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.add('active');
    document.body.style.overflow = 'hidden';
    setTimeout(function () {
      const inp = m.querySelector('input:not([type="hidden"])');
      if (inp) inp.focus();
    }, 200);
  }

  function closeModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.remove('active');
    if (!document.querySelector('.modal-overlay.active')) document.body.style.overflow = '';
  }

  window.WD = { openModal: openModal, closeModal: closeModal };

  window.showToast = function (msg, type) {
    type = type || 'info';
    const c = document.getElementById('toast-container');
    if (!c) return;
    const t = document.createElement('div');
    t.className = 'toast toast-' + type;
    t.innerHTML = msg;
    c.appendChild(t);
    setTimeout(function () { if (t.parentNode) t.remove(); }, 4000);
  };

  const btt = document.getElementById('back-to-top');
  if (btt) {
    btt.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
    window.addEventListener('scroll', function () {
      btt.classList.toggle('visible', window.scrollY > 300);
    });
  }

  const activeNav = document.querySelector('.nav-item.active');
  if (activeNav) {
    setTimeout(function () { activeNav.scrollIntoView({ block: 'center', behavior: 'smooth' }); }, 300);
  }

  function isTouchDevice() {
    try { document.createEvent('TouchEvent'); return true; }
    catch (e) { return false; }
  }
  if (isTouchDevice()) {
    const nav = document.getElementById('sidebar-nav');
    if (nav) { nav.style.overflowY = 'auto'; nav.style.WebkitOverflowScrolling = 'touch'; }
  }
})();