// resources/js/pages/horarios-admin-configurar.js
document.addEventListener('DOMContentLoaded', () => {
  const mount = document.querySelector('[data-page="horarios-admin-configurar"]');
  if (!mount) return;

  const all = document.getElementById('check-all');
  const checks = Array.from(document.querySelectorAll('.dia-check'));
  const btnSave = document.getElementById('btnSave');

  function syncAll() {
    const selectedCount = checks.filter(c => c.checked).length;

    // Estado "indeterminate" e check do "Selecionar todos"
    if (all) {
      all.indeterminate = selectedCount > 0 && selectedCount < checks.length;
      all.checked = selectedCount === checks.length;
    }

    // Botão "Guardar" só ativo quando há dias selecionados
    if (btnSave) btnSave.disabled = selectedCount === 0;
  }

  // “Selecionar todos”
  all?.addEventListener('change', () => {
    checks.forEach(c => (c.checked = all.checked));
    syncAll();
  });

  // Seleção individual
  checks.forEach(c => c.addEventListener('change', syncAll));

  // Estado inicial
  syncAll();
});
