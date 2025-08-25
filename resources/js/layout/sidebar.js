// resources/js/layout/sidebar.js
document.addEventListener('DOMContentLoaded', () => {
  const header   = document.querySelector('header');
  const sidebar  = document.getElementById('layout-sidebar');
  const scrim    = document.getElementById('sidebar-scrim');
  const toggle   = document.getElementById('sidebar-toggle');
  if (!sidebar || !toggle || !scrim) return;

  // actualiza --header-h para o overlay mobile
  const setHeaderVar = () => {
    const h = header?.offsetHeight || 64;
    document.documentElement.style.setProperty('--header-h', `${h}px`);
  };
  setHeaderVar();
  window.addEventListener('resize', setHeaderVar);

  const mqLg = window.matchMedia('(min-width: 1024px)');
  const isDesktop = () => mqLg.matches;

  // estado inicial de colapso (desktop)
  const savedCollapsed = localStorage.getItem('sidebar:collapsed') === '1';
  if (savedCollapsed) sidebar.classList.add('is-collapsed');

  const collapseDesktop = (toCollapsed) => {
    if (toCollapsed) {
      sidebar.classList.add('is-collapsed');
      localStorage.setItem('sidebar:collapsed', '1');
    } else {
      sidebar.classList.remove('is-collapsed');
      localStorage.setItem('sidebar:collapsed', '0');
    }
  };

  // abrir/fechar mobile overlay
  const openMobile = () => { document.body.classList.add('sidebar-open');  toggle.setAttribute('aria-expanded','true');  };
  const closeMobile = () => { document.body.classList.remove('sidebar-open'); toggle.setAttribute('aria-expanded','false'); };

  // clique no botão
  toggle.addEventListener('click', (e) => {
    e.preventDefault();
    if (isDesktop()) {
      collapseDesktop(!sidebar.classList.contains('is-collapsed'));
    } else {
      if (document.body.classList.contains('sidebar-open')) closeMobile(); else openMobile();
    }
  });

  // scrim fecha mobile e repõe colapso guardado
  scrim.addEventListener('click', () => {
    closeMobile();
    const wasCollapsed = localStorage.getItem('sidebar:collapsed') === '1';
    if (wasCollapsed) sidebar.classList.add('is-collapsed');
  });

  // Esc fecha mobile
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMobile(); });

  // quando muda para desktop, garante que o overlay fecha
  const syncMode = () => { if (isDesktop()) closeMobile(); };
  mqLg.addEventListener?.('change', syncMode);
  syncMode();
});
