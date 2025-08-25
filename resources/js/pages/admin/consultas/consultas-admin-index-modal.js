import '../../../../css/modal.css';

const t = (p, fb='') => p.split('.').reduce((o,k)=>o?.[k], window.I18N||{}) ?? fb;

export function mostrarModalConsulta(props) {
  const rootId = 'modal-consulta';

  const estadoKey = String(props.estado_key || '').toLowerCase();
  const estadoTxt = props.estado ?? (estadoKey ? t(`status.${estadoKey}`, estadoKey) : '—');
  const statusColor = getStatusColor(estadoKey || props.estado || '');

  const [dataStr, horaStr] = String(props.data_consulta || '').split(' ');
  const showEmail = props.paciente_email && props.paciente_email !== '-';
  const showDesc  = props.descricao && props.descricao !== '-';

  const html = `
    <div id="${rootId}" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="mz-det-title">
      <div class="modal-content mz-card mz-card-compact">

        <button class="mz-x" aria-label="${t('modals.common.close','Close')}"
                onclick="document.getElementById('${rootId}')?.remove()">×</button>

        <h1 id="mz-det-title" class="mz-title-compact">${t('modals.consultation.title','Consultation')}</h1>

        <div class="mz-meta">
          <span class="mz-date">${dataStr || props.data_consulta || '—'}</span>
          ${horaStr ? `<span class="mz-dot">•</span><span class="mz-time">${horaStr}</span>` : ''}
          <span class="badge ${statusColor} mz-chip">${estadoTxt}</span>
        </div>

        <div class="mz-grid">
          <div class="mz-col">
            <div class="mz-label">${t('modals.consultation.patient','Patient')}</div>
            <div class="mz-value">${props.paciente_nome || '—'}</div>
            ${showEmail ? `<div class="mz-sub">${props.paciente_email}</div>` : ''}
          </div>
          <div class="mz-col">
            <div class="mz-label">${t('modals.consultation.doctor','Doctor')}</div>
            <div class="mz-value">${props.medico_nome || '—'}</div>
            <div class="mz-sub">${props.especialidade_nome || '—'}</div>
          </div>
        </div>

        ${showDesc ? `
          <div class="mz-section">
            <div class="mz-label">${t('modals.consultation.description','Description')}</div>
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

/* ===== Helpers ===== */
function getStatusColor(statusIn) {
  const s = String(statusIn).toLowerCase();
  if (s === 'confirmed' || s === 'confirmada' || s === 'confirmado') return 'bg-confirmado';
  if (s === 'scheduled' || s === 'agendada') return 'bg-emerald-500';
  if (s === 'pending' || s === 'pendente') return 'bg-pendente';
  if (s === 'pending_doctor' || s === 'pendente_medico') return 'bg-violet-500';
  if (s === 'canceled' || s === 'cancelada' || s === 'cancelado') return 'bg-cancelado';
  return 'bg-gray-500';
}

function wireEscAndBackdrop(id){
  const root = document.getElementById(id);
  if (!root) return;
  function onKey(e){ if (e.key === 'Escape') { root.remove(); window.removeEventListener('keydown', onKey); } }
  window.addEventListener('keydown', onKey);
  root.addEventListener('click', (e) => { if (e.target === root) root.remove(); });
}

window.mostrarModalConsulta = mostrarModalConsulta;
