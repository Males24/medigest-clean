import '../../../../css/modal.css';
const t = (p, fb='') => p.split('.').reduce((o,k)=>o?.[k], window.I18N||{}) ?? fb;

export function confirmarApagarMedico({ formId, nome }) {
  const rootId = 'modal-apagar-medico';
  const texto = t('modals.delete_doctor.question','Are you sure you want to delete the doctor “:name”?').replace(':name', nome);

  document.getElementById(rootId)?.remove();

  const html = `
    <div id="${rootId}" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="mz-medico-title">
      <div class="modal-content mz-card mz-card-sm">
        <button class="mz-x" aria-label="${t('modals.common.close','Close')}"
                onclick="document.getElementById('${rootId}')?.remove()">×</button>

        <div class="mz-icon-wrap"><div class="mz-icon" aria-hidden="true"></div></div>

        <h2 id="mz-medico-title" class="sr-only">${t('modals.delete_doctor.title','Confirm deletion')}</h2>
        <p class="mz-confirm-text">${texto}</p>

        <div class="mz-actions">
          <button id="mz-medico-yes" class="mz-btn mz-btn--md mz-btn--danger">
            ${t('modals.delete_doctor.yes','Yes, delete')}
          </button>
          <button class="mz-btn mz-btn--md mz-btn--inset"
                  onclick="document.getElementById('${rootId}')?.remove()">${t('modals.common.no','No')}</button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', html);

  document.getElementById('mz-medico-yes')?.addEventListener('click', () => {
    const form = document.getElementById(formId);
    if (form) form.submit();
  });

  wireEscAndBackdrop(rootId);
}

function delegateClicks() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-del-medico');
    if (!btn) return;
    e.preventDefault();
    confirmarApagarMedico({ formId: btn.getAttribute('data-form'), nome: btn.getAttribute('data-nome') || '' });
  }, { passive: false }); // precisa de preventDefault
}

function wireEscAndBackdrop(id){
  const root = document.getElementById(id);
  if (!root) return;
  function onKey(e){ if (e.key === 'Escape') { root.remove(); window.removeEventListener('keydown', onKey); } }
  window.addEventListener('keydown', onKey);
  root.addEventListener('click', (e) => { if (e.target === root) root.remove(); });
}
window.confirmarApagarMedico = confirmarApagarMedico;
delegateClicks();
