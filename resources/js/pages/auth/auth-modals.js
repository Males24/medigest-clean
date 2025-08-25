import '../../../css/modal.css';
import '../../../css/auth-modal-wave.css';

let lastActiveEl = null;

/* ===== Helpers ===== */
function getFocusable(root){
  return [...root.querySelectorAll(
    'a[href], area[href], input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [tabindex]:not([tabindex="-1"])'
  )].filter(el => (el.offsetParent !== null) || el.getClientRects().length);
}
function lockScroll(){ document.documentElement.classList.add('modal-open'); }
function unlockScroll(){ document.documentElement.classList.remove('modal-open'); }

/* ===== Open / Close ===== */
function openAuthModal(initialPane = 'login'){
  document.getElementById('modal-auth')?.remove();

  const tpl = document.getElementById('tpl-auth');
  if (!tpl) return;

  lastActiveEl = document.activeElement;

  const fragment = tpl.content.cloneNode(true);
  document.body.appendChild(fragment);
  lockScroll();

  const root = document.getElementById('modal-auth');
  wireEscBackdropAndTrap(root);
  enhanceInside(root);
  showPane(root, initialPane);
}

function closeAuthModal(){
  const root = document.getElementById('modal-auth');
  if (!root) return;
  root.remove();
  unlockScroll();
  if (lastActiveEl && document.contains(lastActiveEl)) lastActiveEl.focus();
}

/* ===== Focus trap / ESC / Backdrop ===== */
function wireEscBackdropAndTrap(root){
  const onKeydown = (e) => {
    if (e.key === 'Escape'){ e.preventDefault(); closeAuthModal(); return; }
    if (e.key !== 'Tab') return;
    const focusables = getFocusable(root);
    if (!focusables.length) return;
    const first = focusables[0], last = focusables[focusables.length - 1];
    if (e.shiftKey && document.activeElement === first){ e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last){ e.preventDefault(); first.focus(); }
  };
  window.addEventListener('keydown', onKeydown);
  root._onKeydown = onKeydown;

  root.addEventListener('click', (e) => {
    if (e.target === root) closeAuthModal();
    if (e.target.closest('[data-auth-close]')) closeAuthModal();
  });

  setTimeout(() => {
    const auto = root.querySelector('[autofocus], input, button, select, textarea');
    auto && auto.focus();
  }, 0);
}

/* ===== Pane switching ===== */
function showPane(root, name){
  root.querySelectorAll('[data-pane]').forEach(sec => {
    sec.hidden = (sec.getAttribute('data-pane') !== name);
  });

  root.querySelectorAll('[data-pane-target]').forEach(btn => {
    const active = btn.getAttribute('data-pane-target') === name;
    btn.setAttribute('aria-selected', active ? 'true' : 'false');
    btn.classList.toggle('bg-white', active);
    btn.classList.toggle('text-zinc-900', active);
    btn.classList.toggle('shadow', active);
    btn.classList.toggle('font-semibold', active);
    btn.classList.toggle('text-zinc-600', !active);
  });

  const first = root.querySelector(`[data-pane="${name}"] [autofocus], [data-pane="${name}"] input, [data-pane="${name}"] button`);
  first && first.focus({ preventScroll: true });
}

/* ===== Enhance: password toggle + AJAX login + tab clicks ===== */
function enhanceInside(root){
  const toggleBtn = (btn) => {
    const fieldId = btn.getAttribute('data-toggle-password');
    const input = root.querySelector(`#${CSS.escape(fieldId)}`);
    if (!input) return;

    const hasIcons  = btn.hasAttribute('data-icon-toggle');
    const currShown = hasIcons ? (btn.getAttribute('data-state') === 'shown') : (input.type === 'text');
    const nextShown = !currShown;

    try { input.type = nextShown ? 'text' : 'password'; } catch(_) {}

    const show = btn.getAttribute('data-label-show') || 'Mostrar';
    const hide = btn.getAttribute('data-label-hide') || 'Ocultar';
    btn.setAttribute('aria-pressed', nextShown ? 'true' : 'false');
    btn.setAttribute('aria-label', nextShown ? hide : show);
    if (hasIcons){ btn.setAttribute('data-state', nextShown ? 'shown' : 'hidden'); }
    input.focus({ preventScroll: true });
  };

  root.addEventListener('pointerdown', (e) => {
    const btn = e.target.closest('button[data-toggle-password]');
    if (!btn || !root.contains(btn)) return;
    e.preventDefault();
    toggleBtn(btn);
  }, { passive: false });

  root.addEventListener('keydown', (e) => {
    const btn = e.target.closest('button[data-toggle-password]');
    if (!btn) return;
    if (e.key === ' ' || e.key === 'Enter'){ e.preventDefault(); toggleBtn(btn); }
  });

  // Troca de pane pelas cápsulas e pelos links
  root.addEventListener('click', (e) => {
    const tabBtn = e.target.closest('[data-pane-target]');
    if (tabBtn){ showPane(root, tabBtn.getAttribute('data-pane-target')); return; }

    const switchBtn = e.target.closest('[data-auth-switch]');
    if (switchBtn){
      const to = switchBtn.getAttribute('data-auth-switch');
      showPane(root, to); // login/register/forgot dentro do mesmo modal
    }
  });

  // Login AJAX (sem reload)
  root.addEventListener('submit', async (e) => {
    const form = e.target.closest('form[data-auth-ajax="login"]');
    if (!form || !root.contains(form)) return;
    e.preventDefault();

    const box = root.querySelector('[data-auth-errors]');
    if (box){ box.classList.add('hidden'); box.innerHTML = ''; }
    form.querySelectorAll('[aria-invalid="true"]').forEach(el => {
      el.removeAttribute('aria-invalid');
      el.classList.remove('border-red-300','ring-red-500','focus:ring-red-500');
    });

    const btn = form.querySelector('button[type="submit"]');
    const btnOld = btn?.innerHTML;
    if (btn){ btn.disabled = true; btn.innerHTML = 'A entrar…'; }

    try{
      const fd = new FormData(form);
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: fd
      });

      if (res.ok){
        const data = await res.json().catch(() => ({}));
        if (data?.redirect_to) window.location.assign(data.redirect_to);
        return;
      }

      const payload = await res.json().catch(() => ({}));
      const msg = payload?.message || 'As credenciais estão incorretas.';
      const errors = payload?.errors || {};

      Object.keys(errors).forEach(name => {
        const el = form.querySelector(`[name="${CSS.escape(name)}"]`);
        if (el){
          el.setAttribute('aria-invalid', 'true');
          el.classList.add('border-red-300','ring-red-500','focus:ring-red-500');
        }
      });

      const box = root.querySelector('[data-auth-errors]');
      if (box){
        const list = Object.values(errors).flat();
        box.innerHTML = list.length
          ? `<ul class="list-disc ps-5 space-y-1">${list.map(li => `<li>${li}</li>`).join('')}</ul>`
          : msg;
        box.classList.remove('hidden');
      }
    } catch(err){
      console.error('Falha no login AJAX:', err);
      form.submit(); // fallback
    } finally {
      if (btn){ btn.disabled = false; btn.innerHTML = btnOld; }
    }
  });
}

/* ===== Delegação global ===== */
function handleClicks(e){
  const openBtn = e.target.closest('[data-auth-open]');
  if (openBtn){
    const pane = openBtn.getAttribute('data-auth-open') || 'login';
    openAuthModal(pane);
    return;
  }
}

export default function initAuthModals(){
  if (window.__authModalsBound) return;
  document.addEventListener('click', handleClicks);
  window.openAuthModal = openAuthModal;
  window.__authModalsBound = true;
}

if (typeof window !== 'undefined'){
  document.addEventListener('DOMContentLoaded', initAuthModals);
}
