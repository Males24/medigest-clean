// Dropdown "Ação" — Médicos (sem form oculto)
let medOpenMenu = null;

function buildMenu(items){ /* igual ao de cima */ 
  const wrap = document.createElement('div');
  wrap.className = 'absolute z-[9999] w-44 bg-white border border-zinc-200 rounded-md shadow-lg overflow-hidden';
  wrap.style.top='0px'; wrap.style.left='0px';
  const ul = document.createElement('ul'); ul.className = 'py-1 text-sm text-zinc-800';
  items.forEach(it => {
    if (it.type === 'divider'){ const hr = document.createElement('div'); hr.className='my-1 h-px bg-zinc-200'; ul.appendChild(hr); return; }
    const li = document.createElement('li');
    const btn = document.createElement('button');
    btn.type='button'; btn.className='block w-full text-left px-4 py-2 hover:bg-zinc-100';
    if (it.danger) btn.classList.add('text-red-600','hover:bg-red-50');
    btn.textContent = it.label;
    btn.addEventListener('click', (e)=>{ e.preventDefault(); it.onClick?.(); closeMenu(); });
    li.appendChild(btn); ul.appendChild(li);
  });
  wrap.appendChild(ul); return wrap;
}
function positionMenu(menu, trigger){
  const r = trigger.getBoundingClientRect();
  menu.style.top  = `${r.top + window.scrollY + r.height + 6}px`;
  menu.style.left = `${Math.max(8, r.left + window.scrollX + r.width - menu.offsetWidth)}px`;
}
function openDropdown(trigger){
  closeMenu();

  const editUrl    = trigger.getAttribute('data-edit-url')    || '';
  const destroyUrl = trigger.getAttribute('data-destroy-url') || '';
  const nome       = trigger.getAttribute('data-nome')        || 'este médico';
  const csrf       = document.querySelector('meta[name="csrf-token"]')?.content || '';

  const items = [
    { label: 'Editar', onClick: () => editUrl && (window.location.href = editUrl) },
    { type: 'divider' },
    {
      label: 'Apagar', danger: true,
      onClick: () => {
        if (window.confirmarApagarMedico) {
          window.confirmarApagarMedico({ action: destroyUrl, csrf, nome });
          return;
        }
        if (confirm(`Tens a certeza que queres apagar "${nome}"?`)) {
          const f = document.createElement('form');
          f.method='POST'; f.action=destroyUrl;
          f.innerHTML = `<input type="hidden" name="_token" value="${csrf}">
                         <input type="hidden" name="_method" value="DELETE">`;
          document.body.appendChild(f); f.submit();
        }
      }
    }
  ];

  const menu = buildMenu(items);
  document.body.appendChild(menu);
  positionMenu(menu, trigger);
  medOpenMenu = menu;

  setTimeout(() => {
    document.addEventListener('click', onDocClick);
    window.addEventListener('keydown', onKey);
    window.addEventListener('resize', onResizeScroll, { passive:true });
    window.addEventListener('scroll',  onResizeScroll, { passive:true });
  }, 0);
}
function closeMenu(){ if (!medOpenMenu) return; medOpenMenu.remove(); medOpenMenu=null;
  document.removeEventListener('click', onDocClick);
  window.removeEventListener('keydown', onKey);
  window.removeEventListener('resize', onResizeScroll);
  window.removeEventListener('scroll', onResizeScroll);
}
function onDocClick(e){ if (!medOpenMenu) return;
  if (e.target.closest('.js-medico-actions-btn')) return;
  if (!e.target.closest('.z-[9999]')) closeMenu();
}
function onKey(e){ if (e.key === 'Escape') closeMenu(); }
function onResizeScroll(){ closeMenu(); }

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.js-medico-actions-btn');
  if (!btn) return; e.preventDefault();
  if (medOpenMenu){ const reopen = medOpenMenu.__trigger !== btn; closeMenu(); if (reopen) openDropdown(btn); }
  else openDropdown(btn);
  if (medOpenMenu) medOpenMenu.__trigger = btn;
}, { passive:false });
