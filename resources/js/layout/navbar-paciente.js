// resources/js/layout/navbar-paciente.js
(function () {
  if (window.__navPacInited) return; window.__navPacInited = true;

  const rootNav = document.getElementById('patient-nav');
  const btn     = document.getElementById('servicos-btn');
  const menu    = document.getElementById('servicos-mega');
  if (!rootNav || !btn || !menu) return;

  const tabBtns = [...menu.querySelectorAll('.tab-btn')];
  const panels  = [...menu.querySelectorAll('.tab-panel')];

  // --- Tabs ---------------------------------------------------------------
  function setPanel(id) {
    tabBtns.forEach(b => {
      const on = b.dataset.tab === id;
      b.setAttribute('aria-selected', on ? 'true' : 'false');
      b.classList.toggle('bg-emerald-50', on);
      b.classList.toggle('text-emerald-700', on);
      b.classList.toggle('border-emerald-200/60', on);
      b.classList.toggle('bg-white', !on);
      b.classList.toggle('border-transparent', !on);
    });
    panels.forEach(p => p.classList.toggle('hidden', p.id !== id));
  }
  const defaultTab = () => (location.hash || '').toLowerCase().includes('apoio') ? 'tab-apoio' : 'tab-consulta';

  // --- Open/close ---------------------------------------------------------
  function open() {
    if (!menu.classList.contains('hidden')) return;
    menu.classList.remove('hidden');
    btn.setAttribute('aria-expanded', 'true');
    setPanel(defaultTab());

    // Foco inicial
    const focusable = menu.querySelector('a,button,[tabindex]:not([tabindex="-1"])');
    (focusable || menu).focus({ preventScroll: true });

    document.addEventListener('click', onDocClick, { capture: true });
    document.addEventListener('keydown', onKeydown);
    window.addEventListener('resize', onResize, { passive: true });
  }
  function close() {
    if (menu.classList.contains('hidden')) return;
    menu.classList.add('hidden');
    btn.setAttribute('aria-expanded', 'false');

    document.removeEventListener('click', onDocClick, { capture: true });
    document.removeEventListener('keydown', onKeydown);
    window.removeEventListener('resize', onResize);
  }
  function toggle() { menu.classList.contains('hidden') ? open() : close(); }

  // fecha ao clicar fora (qualquer sítio fora do menu ou do botão)
  function onDocClick(e) {
    if (menu.contains(e.target) || btn.contains(e.target)) return;
    close();
  }

  // Esc fecha; setas navegam entre tabs quando uma tab tem foco
  function onKeydown(e) {
    if (e.key === 'Escape') { e.preventDefault(); close(); btn.focus(); return; }

    const isTab = document.activeElement && document.activeElement.classList?.contains('tab-btn');
    if (!isTab) return;

    const idx = tabBtns.indexOf(document.activeElement);
    if (idx < 0) return;

    let next = idx;
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') next = (idx + 1) % tabBtns.length;
    if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   next = (idx - 1 + tabBtns.length) % tabBtns.length;
    if (e.key === 'Home') next = 0;
    if (e.key === 'End')  next = tabBtns.length - 1;

    if (next !== idx) {
      e.preventDefault();
      const b = tabBtns[next];
      setPanel(b.dataset.tab);
      b.focus();
    }
  }

  // em resize, não precisamos reposicionar (CSS já faz), mas garantimos que não sai da viewport
  function onResize() {
    // se, por algum motivo, o menu ficar maior que a viewport, apenas garantimos que continua visível
    // (o overflow-y já cuida do scroll interno).
  }

  // --- Listeners ----------------------------------------------------------
  btn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); toggle(); });
  menu.addEventListener('click', (e) => e.stopPropagation());
  tabBtns.forEach(b => b.addEventListener('click', () => setPanel(b.dataset.tab)));
  window.addEventListener('hashchange', () => { if (!menu.classList.contains('hidden')) setPanel(defaultTab()); });
})();
