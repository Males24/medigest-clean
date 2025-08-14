import '../../css/modal.css';
const t = (p, fb='') => p.split('.').reduce((o,k)=>o?.[k], window.I18N||{}) ?? fb;

export function confirmarApagarEspecialidade({ formId, nome }) {
  const rootId = 'modal-apagar-especialidade';
  const texto = t('modals.delete_specialty.question','Are you sure you want to delete the specialty “:name”?').replace(':name', nome);

  document.getElementById(rootId)?.remove();

  const html = `
    <div id="${rootId}" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="mz-espec-title">
      <div class="modal-content mz-card mz-card-sm">
        <button class="mz-x" aria-label="${t('modals.common.close','Close')}"
                onclick="document.getElementById('${rootId}')?.remove()">×</button>

        <div class="mz-icon-wrap"><div class="mz-icon" aria-hidden="true"></div></div>

        <h2 id="mz-espec-title" class="sr-only">${t('modals.delete_specialty.title','Confirm deletion')}</h2>
        <p class="mz-confirm-text">${texto}</p>

        <div class="mz-actions">
          <button id="mz-espec-yes" class="mz-btn mz-btn--md mz-btn--danger">${t('modals.delete_specialty.confirm','Yes, delete')}</button>
          <button class="mz-btn mz-btn--md mz-btn--inset"
                  onclick="document.getElementById('${rootId}')?.remove()">${t('modals.common.no','No')}</button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', html);

  document.getElementById('mz-espec-yes')?.addEventListener('click', () => {
    const form = document.getElementById(formId);
    if (form) form.submit();
  });

  wireEscAndBackdrop(rootId);
}

function delegateClicks() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-del-espec');
    if (!btn) return;
    e.preventDefault();
    confirmarApagarEspecialidade({ formId: btn.getAttribute('data-form'), nome: btn.getAttribute('data-nome') || '' });
  }, { passive: true });
}

function wireEscAndBackdrop(id){
  const root = document.getElementById(id);
  if (!root) return;
  function onKey(e){ if (e.key === 'Escape') { root.remove(); window.removeEventListener('keydown', onKey); } }
  window.addEventListener('keydown', onKey);
  root.addEventListener('click', (e) => { if (e.target === root) root.remove(); });
}
window.confirmarApagarEspecialidade = confirmarApagarEspecialidade;
delegateClicks();
