// Dropdown “Ação” para Consultas (admin)
// Usa: botão com classe .js-consulta-actions-btn e data-* com payload/cancel
// Requer os métodos globais: window.mostrarModalConsulta, window.confirmarCancelamento

let openMenu = null;

function buildMenu(items) {
  const wrap = document.createElement('div');
  wrap.className =
    'absolute z-[9999] w-44 bg-white border border-zinc-200 rounded-md shadow-lg overflow-hidden';
  wrap.style.top = '0px';
  wrap.style.left = '0px';

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
    btn.className = 'block w-full text-left px-4 py-2 hover:bg-zinc-100';
    if (it.danger) btn.className += ' text-red-600 hover:bg-red-50';
    if (it.primary) btn.className += ' text-medigest'; // opcional, se tens essa cor

    btn.textContent = it.label;
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      it.onClick?.();
      closeMenu();
    });
    li.appendChild(btn);
    ul.appendChild(li);
  });

  wrap.appendChild(ul);
  return wrap;
}

function positionMenu(menu, trigger) {
  const r = trigger.getBoundingClientRect();
  const top = r.top + window.scrollY + r.height + 6;   // ligeiro offset
  const left = r.left + window.scrollX + r.width - menu.offsetWidth; // alinhado à direita
  menu.style.top = `${top}px`;
  menu.style.left = `${Math.max(8, left)}px`;
}

function openDropdown(trigger) {
  closeMenu();

  const payload = JSON.parse(trigger.getAttribute('data-payload') || '{}');
  const canCancel = trigger.getAttribute('data-has-cancel') === '1';
  const cancelUrl = trigger.getAttribute('data-cancel-url') || '';

  const items = [
    {
      label: 'Ver',
      primary: true,
      onClick: () => window.mostrarModalConsulta?.(payload),
    },
  ];

  if (canCancel && cancelUrl) {
    items.push({ type: 'divider' });
    items.push({
      label: 'Cancelar',
      danger: true,
      onClick: () =>
        window.confirmarCancelamento?.({
          action: cancelUrl,
          csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',
          mensagem: 'Tens a certeza que queres cancelar esta consulta?',
        }),
    });
  }

  const menu = buildMenu(items);
  document.body.appendChild(menu);
  positionMenu(menu, trigger);
  openMenu = menu;

  // fechar ao clicar fora
  setTimeout(() => {
    document.addEventListener('click', onDocClick);
    window.addEventListener('keydown', onKey);
    window.addEventListener('resize', onResizeScroll, { passive: true });
    window.addEventListener('scroll', onResizeScroll, { passive: true });
  }, 0);
}

function closeMenu() {
  if (!openMenu) return;
  openMenu.remove();
  openMenu = null;
  document.removeEventListener('click', onDocClick);
  window.removeEventListener('keydown', onKey);
  window.removeEventListener('resize', onResizeScroll);
  window.removeEventListener('scroll', onResizeScroll);
}

function onDocClick(e) {
  if (!openMenu) return;
  if (e.target.closest('.js-consulta-actions-btn')) return; // permite clicar no trigger
  if (!e.target.closest('.z-[9999]')) closeMenu();
}

function onKey(e) {
  if (e.key === 'Escape') closeMenu();
}

function onResizeScroll() {
  // Fechamos para evitar ficar mal posicionado
  closeMenu();
}

// Delegação: funciona mesmo em linhas renderizadas depois
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.js-consulta-actions-btn');
  if (!btn) return;
  e.preventDefault();

  // toggle
  if (openMenu) {
    closeMenu();
    // se clicaste noutro trigger, volta a abrir
    // se clicaste no mesmo, fica fechado
    const again = btn !== (openMenu.__trigger || null);
    if (again) openDropdown(btn);
  } else {
    openDropdown(btn);
  }

  if (openMenu) openMenu.__trigger = btn;
}, { passive: false });
