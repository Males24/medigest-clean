document.addEventListener('DOMContentLoaded', () => {
  const q    = document.getElementById('q');
  const chk  = document.getElementById('chkComMedicos');
  const grid = document.getElementById('grid');
  if (!grid) return;

  const cards = [...grid.querySelectorAll('a.sp-card')];

  function apply() {
    const term = (q?.value || '').trim().toLowerCase();
    const needDocs = !!chk?.checked;

    cards.forEach(c => {
      const name = c.dataset.name || '';
      const has  = Number(c.dataset.hasdocs || 0) === 1;
      const okName = !term || name.includes(term);
      const okDocs = !needDocs || has;
      c.classList.toggle('hidden', !(okName && okDocs));
    });
  }

  q?.addEventListener('input', apply);
  chk?.addEventListener('change', apply);

  apply();
});
