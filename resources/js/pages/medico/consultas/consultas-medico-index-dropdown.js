// Dropdown “Ação” para Consultas (médico)
// Requer: window.mostrarModalConsulta, window.medicoAbrirModalConfirmar, window.medicoAbrirModalRejeitar

let openMenu = null;
const t = (p, fb='') => p.split('.').reduce((o,k)=>o?.[k], (window.I18N||{})) ?? fb;

function buildMenu(items) {
  const wrap = document.createElement('div');
  wrap.className = 'absolute z-[9999] w-44 bg-white border border-zinc-200 rounded-md shadow-lg overflow-hidden';
  wrap.style.top = '0px'; wrap.style.left = '0px';

  const ul = document.createElement('ul');
  ul.className = 'py-1 text-sm text-zinc-800';

  items.forEach(it => {
    if (it.type === 'divider') {
      const hr = document.createElement('div');
      hr.className = 'my-1 h-px bg-zinc-200';
      ul.appendChild(hr);
      return;
    }
    const li = document.createElement('li');
    const btn = document.createElement('button');
    btn.type = 'button';

    // base
    btn.className = 'block w-full text-left px-4 py-2 hover:bg-zinc-100';

    // reforçar cor para confirmar/rejeitar
    if (it.variant === 'ok') {
      btn.className =
        'block w-full text-left px-4 py-2 text-emerald-700 ' +
        'hover:bg-emerald-50 ';
    }
    if (it.variant === 'danger') {
      btn.className =
        'block w-full text-left px-4 py-2 text-red-600 ' +
        'hover:bg-red-50 ';
    }

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

  const payload       = JSON.parse(trigger.getAttribute('data-payload') || '{}');
  const estadoKey     = String(payload?.estado_key || '').toLowerCase(); // scheduled|pending_doctor|...
  const confirmUrl    = trigger.getAttribute('data-confirm-url') || '';
  const rejectUrl     = trigger.getAttribute('data-reject-url') || '';

  const items = [
    {
      label: t('actions.view','Ver'),
      onClick: () => window.mostrarModalConsulta?.(payload),
    },
  ];

  // Só quando estiver pendente (médico) mostramos confirmar/rejeitar
  if (estadoKey === 'pending_doctor') {
    items.push({ type: 'divider' });

    if (confirmUrl) {
      items.push({
        label: t('actions.confirm','Confirmar Consulta'),
        variant: 'ok',
        onClick: () => window.medicoAbrirModalConfirmar?.({
          action: confirmUrl,
          csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',
          when: payload?.data_consulta || '',
        }),
      });
    }
    if (rejectUrl) {
      items.push({
        label: t('actions.reject','Cancelar Consulta'),
        variant: 'danger',
        onClick: () => window.medicoAbrirModalRejeitar?.({
          action: rejectUrl,
          csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',
          when: payload?.data_consulta || '',
        }),
      });
    }
  }
  // Nota: não existe mais “Cancelar” para agendadas aqui.

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
