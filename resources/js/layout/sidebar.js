// resources/js/layout/sidebar.js
document.addEventListener('DOMContentLoaded', () => {
  const header   = document.querySelector('header');
  const toggleBtn= document.getElementById('sidebar-toggle');
  const scrim    = document.getElementById('sidebar-scrim');
  const mqLg     = window.matchMedia('(min-width: 1024px)');
  if (!toggleBtn || !scrim) return;

  // manter o sidebar alinhado ao header (CSS usa --header-h)
  const setHeaderVar = () => {
    const h = header?.offsetHeight || 64;
    document.documentElement.style.setProperty('--header-h', `${h}px`);
  };
  setHeaderVar();
  window.addEventListener('resize', setHeaderVar);

  const open  = () => { document.body.classList.add('sidebar-open');  toggleBtn.setAttribute('aria-expanded','true');  };
  const close = () => { document.body.classList.remove('sidebar-open'); toggleBtn.setAttribute('aria-expanded','false'); };

  toggleBtn.addEventListener('click', (e)=>{ e.preventDefault(); document.body.classList.contains('sidebar-open') ? close() : open(); });
  scrim.addEventListener('click', close);
  document.addEventListener('keydown', (e)=>{ if (e.key === 'Escape') close(); });

  // em desktop, fecha (fica visível por CSS sem overlay)
  const sync = () => { if (mqLg.matches) close(); };
  sync();
  mqLg.addEventListener?.('change', sync);
});
