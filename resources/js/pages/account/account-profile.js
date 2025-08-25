/* Password toggle + medidor de força (profile) */

function bindPasswordToggles(root = document) {
  const handler = (btn) => {
    const id = btn.getAttribute('data-target');
    const input = (root.getElementById?.(id)) || document.getElementById(id);
    if (!input) return;

    const showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    btn.setAttribute('aria-pressed', showing ? 'false' : 'true');

    // alterna os ícones
    const on  = btn.querySelector('[data-eye="on"]');
    const off = btn.querySelector('[data-eye="off"]');
    if (on && off) {
      on.classList.toggle('hidden', !showing);   // se estava a mostrar, volta a esconder
      off.classList.toggle('hidden', showing);
    }
    input.focus({ preventScroll: true });
  };

  // clique
  root.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-pwd-toggle], .toggle-password');
    if (!btn) return;
    e.preventDefault();
    handler(btn);
  });

  // teclado (espaço/enter)
  root.addEventListener('keydown', (e) => {
    const btn = e.target.closest('[data-pwd-toggle], .toggle-password');
    if (!btn) return;
    if (e.key === ' ' || e.key === 'Enter') {
      e.preventDefault();
      handler(btn);
    }
  });
}

/* Força da password — heurística simples com estado neutro quando vazio */
function bindStrengthMeter() {
  const input = document.getElementById('pwd-new');
  const bar   = document.getElementById('pwd-strength-bar');
  const txt   = document.getElementById('pwd-strength-text');
  if (!input || !bar || !txt) return;

  // prefixo i18n (ex.: "Força")
  const prefix = (txt.textContent.split(':')[0] || 'Força').trim();

  const colorsAll = ['bg-red-500','bg-orange-500','bg-yellow-500','bg-lime-500','bg-emerald-500','bg-gray-300'];

  const reset = () => {
    bar.classList.remove(...colorsAll);
    bar.classList.add('bg-gray-300');  // cor neutra
    bar.style.width = '0%';
    txt.textContent = `${prefix}: —`;
  };

  const set = (score) => {
    const widths = ['10%','35%','60%','85%','100%'];
    const colors = ['bg-red-500','bg-orange-500','bg-yellow-500','bg-lime-500','bg-emerald-500'];
    const labels = ['Fraca','Fraca','Média','Boa','Ótima'];

    bar.classList.remove(...colorsAll);
    bar.classList.add(colors[score]);
    bar.style.width = widths[score];
    txt.textContent = `${prefix}: ${labels[score]}`;
  };

  // estado inicial
  reset();

  input.addEventListener('input', () => {
    const v = input.value || '';
    if (!v.length) { reset(); return; }

    let score = 0;
    if (v.length >= 8)  score++;
    if (/[a-z]/.test(v) && /[A-Z]/.test(v)) score++;
    if (/\d/.test(v))   score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    if (v.length >= 12) score++;
    score = Math.min(score, 4);

    set(score);
  });
}

document.addEventListener('DOMContentLoaded', () => {
  bindPasswordToggles(document);
  bindStrengthMeter();
});
