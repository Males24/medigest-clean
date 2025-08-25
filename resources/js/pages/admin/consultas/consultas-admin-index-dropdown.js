// Dropdown “Ação” para Consultas (admin)
// Agora: **apenas Ver** (sem cancelar)

let openMenu = null;
const t = (p, fb='') => p.split('.').reduce((o,k)=>o?.[k], (window.I18N||{})) ?? fb;

function buildMenu(items) {
  const wrap = document.createElement('div');
  wrap.className = 'absolute z-[9999] w-44 bg-white border border-zinc-200 rounded-md shadow-lg overflow-hidden';
  wrap.style.top = '0px'; wrap.style.left = '0px';

  const ul = document.createElement('ul');
  ul.className = 'py-1 text-sm text-zinc-800';

  items.forEach(it => {
    const li = document.createElement('li');
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'block w-full text-left px-4 py-2 hover:bg-zinc-100';
    if (it.primary) btn.className += ' text-medigest';
    btn.textContent = it.label;
    btn.addEventListener('click', (e) => { e.preventDefault(); it.onClick?.(); closeMenu(); });
    li.appendChild(btn);
    ul.appendChild(li);
  });

  wrap.appendChild(ul);
  return wrap;
}

function positionMenu(menu, trigger) {
  const r = trigger.getBoundingClientRect();
  const top = r.top + window.scrollY + r.height + 6;
  const left = r.left + window.scrollX + r.width - menu.offsetWidth;
  menu.style.top = `${top}px`;
  menu.style.left = `${Math.max(8, left)}px`;
}

function openDropdown(trigger) {
  closeMenu();

  const payload = JSON.parse(trigger.getAttribute('data-payload') || '{}');

  const items = [
    {
      label: t('actions.view','View'),
      primary: true,
      onClick: () => window.mostrarModalConsulta?.(payload),
    },
  ];

  const menu = buildMenu(items);
  document.body.appendChild(menu);
  positionMenu(menu, trigger);
  openMenu = menu;

  setTimeout(() => {
    document.addEventListener('click', onDocClick);
    window.addEventListener('keydown', onKey);
    window.addEventListener('resize', onResizeScroll, { passive: true });
    window.addEventListener('scroll', onResizeScroll, { passive: true });
  }, 0);
}

function closeMenu() {
  if (!openMenu) return;
  openMenu.remove(); openMenu = null;
  document.removeEventListener('click', onDocClick);
  window.removeEventListener('keydown', onKey);
  window.removeEventListener('resize', onResizeScroll);
  window.removeEventListener('scroll', onResizeScroll);
}

function onDocClick(e) {
  if (!openMenu) return;
  if (e.target.closest('.js-consulta-actions-btn')) return;
  if (!e.target.closest('.z-[9999]')) closeMenu();
}
function onKey(e){ if (e.key === 'Escape') closeMenu(); }
function onResizeScroll(){ closeMenu(); }

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.js-consulta-actions-btn');
  if (!btn) return;
  e.preventDefault();

  if (openMenu) {
    const again = btn !== (openMenu.__trigger || null);
    closeMenu();
    if (again) openDropdown(btn);
  } else {
    openDropdown(btn);
  }

  if (openMenu) openMenu.__trigger = btn;
}, { passive: false });
