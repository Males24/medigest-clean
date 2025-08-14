import '../../css/modal.css';

const t = (p, fb='') => p.split('.').reduce((o,k)=>o?.[k], window.I18N||{}) ?? fb;

export function mostrarModalConsulta(props) {
  const statusColor = getStatusColor(props.estado || '');
  const rootId = 'modal-consulta';

  const [dataStr, horaStr] = String(props.data_consulta || '').split(' ');
  const showEmail = props.paciente_email && props.paciente_email !== '-';
  const showDesc  = props.descricao && props.descricao !== '-';

  const html = `
    <div id="${rootId}" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="mz-det-title">
      <div class="modal-content mz-card mz-card-compact">

        <button class="mz-x" aria-label="${t('modals.common.close','Close')}"
                onclick="document.getElementById('${rootId}')?.remove()">×</button>

        <h1 id="mz-det-title" class="mz-title-compact">${t('modals.details.title','Consultation')}</h1>

        <div class="mz-meta">
          <span class="mz-date">${dataStr || props.data_consulta || '—'}</span>
          ${horaStr ? `<span class="mz-dot">•</span><span class="mz-time">${horaStr}</span>` : ''}
          <span class="badge ${statusColor} mz-chip">${props.estado || '—'}</span>
        </div>

        <div class="mz-grid">
          <div class="mz-col">
            <div class="mz-label">${t('modals.details.patient','Patient')}</div>
            <div class="mz-value">${props.paciente_nome || '—'}</div>
            ${showEmail ? `<div class="mz-sub">${props.paciente_email}</div>` : ''}
          </div>
          <div class="mz-col">
            <div class="mz-label">${t('modals.details.doctor','Doctor')}</div>
            <div class="mz-value">${props.medico_nome || '—'}</div>
            <div class="mz-sub">${props.especialidade_nome || '—'}</div>
          </div>
        </div>

        ${showDesc ? `
          <div class="mz-section">
            <div class="mz-label">${t('modals.details.description','Description')}</div>
            <p class="mz-text">${props.descricao}</p>
          </div>` : ''}

        <div class="mz-actions mt-2">
          <button class="mz-btn mz-btn--md mz-btn--inset"
                  onclick="document.getElementById('${rootId}')?.remove()">${t('modals.common.close','Close')}</button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', html);
  wireEscAndBackdrop(rootId);
}

export function confirmarCancelamento({ action, csrf, mensagem }) {
  const texto = mensagem || t('modals.cancel.question','Are you sure you want to cancel this consultation?');
  const rootId = 'modal-cancelar';

  const html = `
    <div id="${rootId}" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="mz-cancel-title">
      <div class="modal-content mz-card mz-card-sm">
        <button class="mz-x" aria-label="${t('modals.common.close','Close')}"
                onclick="document.getElementById('${rootId}')?.remove()">×</button>

        <div class="mz-icon-wrap"><div class="mz-icon" aria-hidden="true"></div></div>

        <h2 id="mz-cancel-title" class="sr-only">${t('modals.cancel.title','Confirm cancellation')}</h2>
        <p class="mz-confirm-text">${texto}</p>

        <div class="mz-actions">
          <button id="mz-confirm-yes" class="mz-btn mz-btn--md mz-btn--danger">${t('modals.cancel.confirm','Yes, cancel')}</button>
          <button class="mz-btn mz-btn--md mz-btn--inset"
                  onclick="document.getElementById('${rootId}')?.remove()">${t('modals.common.no','No')}</button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', html);

  document.getElementById('mz-confirm-yes')?.addEventListener('click', () => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;
    form.style.display = 'none';
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden'; csrfInput.name = '_token'; csrfInput.value = csrf;
    form.appendChild(csrfInput);
    document.body.appendChild(form);
    form.submit();
  });

  wireEscAndBackdrop(rootId);
}

function getStatusColor(status) {
  switch ((status || '').toLowerCase()) {
    case 'confirmada':
    case 'confirmado': return 'bg-confirmado';
    case 'pendente':
    case 'pendente_medico': return 'bg-pendente';
    case 'cancelada':
    case 'cancelado': return 'bg-cancelado';
    default: return 'bg-gray-500';
  }
}
function wireEscAndBackdrop(id){
  const root = document.getElementById(id);
  if (!root) return;
  function onKey(e){ if (e.key === 'Escape') { root.remove(); window.removeEventListener('keydown', onKey); } }
  window.addEventListener('keydown', onKey);
  root.addEventListener('click', (e) => { if (e.target === root) root.remove(); });
}
window.mostrarModalConsulta = mostrarModalConsulta;
window.confirmarCancelamento = confirmarCancelamento;
