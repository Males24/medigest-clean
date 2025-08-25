// resources/js/layout/navbar-paciente.js
(function () {
  if (window.__navPacInited) return; window.__navPacInited = true;

  const btn  = document.getElementById('servicos-btn');
  const menu = document.getElementById('servicos-mega');
  const wrap = document.getElementById('servicos-wrap');
  if (!btn || !menu || !wrap) return;

  const tabBtns   = Array.from(menu.querySelectorAll('.tab-btn'));
  const tabPanels = Array.from(menu.querySelectorAll('.tab-panel'));

  function setAria(btnEl, active) { btnEl.setAttribute('aria-selected', active ? 'true' : 'false'); }

  function setPanel(id) {
    tabBtns.forEach(b => {
      const active = b.dataset.tab === id;
      b.classList.toggle('bg-emerald-50', active);
      b.classList.toggle('text-emerald-700', active);
      b.classList.toggle('border-emerald-600', active);
      b.classList.toggle('border-transparent', !active);
      b.classList.toggle('bg-white', !active);
      setAria(b, active);
    });
    tabPanels.forEach(p => p.classList.toggle('hidden', p.id !== id));
  }

  function positionMenu() {
    const r = btn.getBoundingClientRect();
    menu.style.top  = `${r.bottom + 10}px`;
    menu.style.left = '50%';
  }

  function defaultTabFromHash() {
    const h = (location.hash || '').toLowerCase();
    if (h.includes('apoio'))    return 'tab-apoio';
    /* default */                 return 'tab-consulta';
  }

  function open() {
    if (!menu.classList.contains('hidden')) return;
    positionMenu();
    menu.classList.remove('hidden');
    btn.setAttribute('aria-expanded', 'true');
    setPanel(defaultTabFromHash());
    const active = tabBtns.find(b => b.getAttribute('aria-selected') === 'true');
    active && active.focus();
    document.addEventListener('click', onDocClick);
    document.addEventListener('keydown', onKeydown);
  }
  function close() {
    if (menu.classList.contains('hidden')) return;
    menu.classList.add('hidden');
    btn.setAttribute('aria-expanded', 'false');
    document.removeEventListener('click', onDocClick);
    document.removeEventListener('keydown', onKeydown);
  }
  function toggle() { menu.classList.contains('hidden') ? open() : close(); }
  function onDocClick(e) { if (!wrap.contains(e.target)) close(); }
  function onKeydown(e) {
    if (e.key === 'Escape') { close(); btn.focus(); return; }
    const idx = tabBtns.findIndex(t => t.getAttribute('aria-selected') === 'true');
    if (idx === -1) return;

    let n = idx;
    if (e.key === 'ArrowRight') n = (idx + 1) % tabBtns.length;
    if (e.key === 'ArrowLeft')  n = (idx - 1 + tabBtns.length) % tabBtns.length;
    if (e.key === 'Home')       n = 0;
    if (e.key === 'End')        n = tabBtns.length - 1;
    if (n !== idx) { e.preventDefault(); const nb = tabBtns[n]; setPanel(nb.dataset.tab); nb.focus(); }

    if ((e.key === 'Enter' || e.key === ' ') && document.activeElement?.classList.contains('tab-btn')) {
      e.preventDefault(); setPanel(document.activeElement.dataset.tab);
    }
  }

  btn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); toggle(); });
  menu.addEventListener('click', (e) => e.stopPropagation());
  tabBtns.forEach(b => b.addEventListener('click', () => setPanel(b.dataset.tab)));

  document.addEventListener('click', (e) => { if (!menu.classList.contains('hidden') && !wrap.contains(e.target)) close(); });
  window.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });

  ['resize','scroll'].forEach(ev => window.addEventListener(ev, () => { if (!menu.classList.contains('hidden')) positionMenu(); }, { passive:true }));
  window.addEventListener('hashchange', () => { if (!menu.classList.contains('hidden')) setPanel(defaultTabFromHash()); });
})();
