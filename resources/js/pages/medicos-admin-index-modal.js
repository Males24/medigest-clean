import '../../css/modal.css';

/**
 * Abre o modal de confirmação para apagar médico.
 * Aceita { formId, nome } e submete o form quando confirmar.
 */
export function confirmarApagarMedico({ formId, nome }) {
  const rootId = 'modal-apagar-medico';
  const texto = `Tens a certeza que queres apagar o médico “${nome}”?`;

  // Evita múltiplos modais abertos
  document.getElementById(rootId)?.remove();

  const html = `
    <div id="${rootId}" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="mz-medico-title">
      <div class="modal-content mz-card mz-card-sm">
        <button class="mz-x" aria-label="Fechar"
                onclick="document.getElementById('${rootId}')?.remove()">×</button>

        <div class="mz-icon-wrap"><div class="mz-icon" aria-hidden="true"></div></div>

        <h2 id="mz-medico-title" class="sr-only">Confirmar remoção do médico</h2>
        <p class="mz-confirm-text">${texto}</p>

        <div class="mz-actions">
          <button id="mz-medico-yes" class="mz-btn mz-btn--md mz-btn--danger">Sim, apagar</button>
          <button class="mz-btn mz-btn--md mz-btn--inset"
                  onclick="document.getElementById('${rootId}')?.remove()">Não</button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', html);

  // Submete o form ao confirmar
  document.getElementById('mz-medico-yes')?.addEventListener('click', () => {
    const form = document.getElementById(formId);
    if (form) form.submit();
  });

  wireEscAndBackdrop(rootId);
}

/* ---------- Delegação de eventos (funciona para conteúdo dinâmico) ---------- */
function delegateClicks() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-del-medico');
    if (!btn) return;

    e.preventDefault();

    const formId = btn.getAttribute('data-form');
    const nome   = btn.getAttribute('data-nome') || '';

    confirmarApagarMedico({ formId, nome });
  }, { passive: true });
}

/* ---------- Helpers ---------- */
function wireEscAndBackdrop(id){
  const root = document.getElementById(id);
  if (!root) return;
  function onKey(e){ if (e.key === 'Escape') { root.remove(); window.removeEventListener('keydown', onKey); } }
  window.addEventListener('keydown', onKey);
  root.addEventListener('click', (e) => { if (e.target === root) root.remove(); });
}

/* Disponível globalmente (se precisares) */
window.confirmarApagarMedico = confirmarApagarMedico;

/* Arranque imediato */
delegateClicks();
