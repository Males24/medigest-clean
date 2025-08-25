// Filtro + ordenação na página de médicos de uma especialidade
document.addEventListener('DOMContentLoaded', () => {
  const grid  = document.getElementById('grid');
  if (!grid) return;

  const cards = Array.from(grid.querySelectorAll('[data-name]'));
  const q     = document.getElementById('q');
  const order = document.getElementById('order');

  function apply() {
    const term = (q && q.value ? q.value : '').trim().toLowerCase();

    // filtro por nome
    cards.forEach(card => {
      const name = card.dataset.name || '';
      const hide = term && name.indexOf(term) === -1;
      card.classList.toggle('hidden', hide);
    });

    // ordenação (só nos visíveis)
    const visible = cards.filter(c => !c.classList.contains('hidden'));
    visible
      .sort((a, b) => {
        const na = a.dataset.name || '';
        const nb = b.dataset.name || '';
        const desc = order && order.value === 'za';
        return desc ? nb.localeCompare(na) : na.localeCompare(nb);
      })
      .forEach(c => grid.appendChild(c));
  }

  if (q)     q.addEventListener('input', apply);
  if (order) order.addEventListener('change', apply);

  // 1ª aplicação
  apply();
});
