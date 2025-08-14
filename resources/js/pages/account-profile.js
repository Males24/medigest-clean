// resources/js/pages/account-profile.js
document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-page="account-profile"]');
  if (!root) return;

  // Avatar preview/remover
  const img = document.getElementById('avatarPreview');
  const inp = document.getElementById('avatar');
  const chk = document.getElementById('remove_avatar');

  const AVATAR_CURRENT  = root.dataset.avatarCurrent || '';
  const AVATAR_FALLBACK = root.dataset.avatarFallback || '';

  inp?.addEventListener('change', (e) => {
    const [file] = e.target.files || [];
    if (!file) return;
    const url = URL.createObjectURL(file);
    if (img) img.src = url;
    if (chk) chk.checked = false; // se escolheu novo ficheiro, desmarca “remover”
  });

  chk?.addEventListener('change', (e) => {
    if (!img) return;
    if (e.target.checked) {
      img.src = AVATAR_FALLBACK; // mostra fallback imediato
      if (inp) inp.value = '';   // limpa eventual ficheiro selecionado
    } else {
      img.src = AVATAR_CURRENT;  // volta ao avatar atual
    }
  });

  // Mostrar/ocultar password
  function wireToggle(btn) {
    const targetId = btn.getAttribute('data-target');
    const input = document.getElementById(targetId);
    if (!input) return;

    const eyeOn  = btn.querySelector('[data-eye="on"]');
    const eyeOff = btn.querySelector('[data-eye="off"]');

    btn.addEventListener('click', () => {
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.setAttribute('aria-pressed', show ? 'true' : 'false');
      if (eyeOn && eyeOff) {
        eyeOn.classList.toggle('hidden', show);
        eyeOff.classList.toggle('hidden', !show);
      }
      input.focus({ preventScroll: true });
    });
  }

  document.querySelectorAll('.toggle-password').forEach(wireToggle);

  // Medidor de força da nova password
  const pwdNew   = document.getElementById('pwd-new');
  const bar      = document.getElementById('pwd-strength-bar');
  const label    = document.getElementById('pwd-strength-text');

  function scorePassword(str) {
    if (!str) return 0;
    let score = 0;

    // comprimento
    score += Math.min(10, Math.floor(str.length / 2)) * 5; // até 25

    // diversidade de caracteres (até 40)
    const sets = [
      /[a-z]/,  // minúsculas
      /[A-Z]/,  // maiúsculas
      /[0-9]/,  // números
      /[^A-Za-z0-9]/ // símbolos
    ];
    score += sets.reduce((acc, re) => acc + (re.test(str) ? 10 : 0), 0) * 1;

    // bónus por não ter sequências repetidas longas (pequeno)
    if (!/(.)\1{2,}/.test(str)) score += 10;

    return Math.max(0, Math.min(100, score));
  }

  function strengthLabel(val) {
    if (val >= 80) return { text: 'forte',  color: 'bg-emerald-500' };
    if (val >= 60) return { text: 'boa',    color: 'bg-lime-500'    };
    if (val >= 40) return { text: 'média',  color: 'bg-amber-500'   };
    if (val >= 20) return { text: 'fraca',  color: 'bg-orange-500'  };
    return           { text: 'muito fraca', color: 'bg-red-500'     };
  }

  function updateStrength() {
    if (!pwdNew || !bar || !label) return;
    const sc = scorePassword(pwdNew.value);
    const { text, color } = strengthLabel(sc);

    bar.style.width = `${sc}%`;
    bar.className = `h-1 rounded ${color}`;
    label.textContent = `Força: ${pwdNew.value ? text : '—'}`;
  }

  pwdNew?.addEventListener('input', updateStrength);
  updateStrength();
});
