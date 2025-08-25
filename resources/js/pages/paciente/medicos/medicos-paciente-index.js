document.addEventListener('DOMContentLoaded', () => {
  const q    = document.getElementById('q');
  const fEsp = document.getElementById('fEsp');
  const ord  = document.getElementById('ord');
  const grid = document.getElementById('grid');

  if (!grid) return;

  function apply() {
    const term = (q?.value || '').trim().toLowerCase();
    const esp  = (fEsp?.value || '').toLowerCase();
    const cards = [...grid.children];

    cards.forEach(c => {
      const name = c.dataset.name || '';
      const esps = c.dataset.esps || '';
      const okName = !term || name.includes(term);
      const okEsp  = !esp || esps.includes(esp);
      c.classList.toggle('hidden', !(okName && okEsp));
    });

    cards.sort((a, b) => {
      const A = a.dataset.name || '';
      const B = b.dataset.name || '';
      return (ord?.value === 'za') ? B.localeCompare(A) : A.localeCompare(B);
    }).forEach(c => grid.appendChild(c));
  }

  q?.addEventListener('input', apply);
  fEsp?.addEventListener('input', apply);
  ord?.addEventListener('input', apply);

  apply();
});
