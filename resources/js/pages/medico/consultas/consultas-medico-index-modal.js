import '../../../../css/modal.css';

const T = {
  modals: {
    common: { close: 'Fechar', no: 'Não' },
    confirm: {
      title: 'Confirmar consulta',
      question: 'Tens a certeza que queres confirmar esta consulta?',
      yes: 'Sim, confirmar',
    },
    reject: {
      title: 'Rejeitar pedido',
      question: 'Tens a certeza que queres cancelar este pedido de consulta?',
      yes: 'Sim, cancelar',
    },
  },
};
const t = (p, fb='') => p.split('.').reduce((o,k)=>o?.[k], window.I18N||T) ?? fb;

function wireEscAndBackdrop(id){
  const root = document.getElementById(id);
  if (!root) return;
  function onKey(e){ if (e.key === 'Escape') { root.remove(); window.removeEventListener('keydown', onKey); } }
  window.addEventListener('keydown', onKey);
  root.addEventListener('click', (e) => { if (e.target === root) root.remove(); });
}

/* ===== Detalhe simples (mesmo do paciente/admin) ===== */
export function mostrarModalConsulta(props) {
  const rootId = 'modal-consulta-medico';
  document.getElementById(rootId)?.remove();

  const estadoKey = String(props.estado_key || '').toLowerCase();
  const estadoTxt = props.estado ?? (window.I18N?.status?.[estadoKey] || estadoKey || '—');

  const [dataStr, horaStr] = String(props.data_consulta || '').split(' ');

  const html = `
    <div id="${rootId}" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="mz-det-title">
      <div class="modal-content mz-card mz-card-compact">
        <button class="mz-x" aria-label="${t('modals.common.close','Fechar')}"
                onclick="document.getElementById('${rootId}')?.remove()">×</button>

        <h1 id="mz-det-title" class="mz-title-compact">Consulta</h1>

        <div class="mz-meta">
          <span class="mz-date">${dataStr || props.data_consulta || '—'}</span>
          ${horaStr ? `<span class="mz-dot">•</span><span class="mz-time">${horaStr}</span>` : ''}
          <span class="badge bg-emerald-500 mz-chip">${estadoTxt}</span>
        </div>

        <div class="mz-grid">
          <div class="mz-col">
            <div class="mz-label">Paciente</div>
            <div class="mz-value">${props.paciente_nome || '—'}</div>
            ${props.paciente_email && props.paciente_email!=='-' ? `<div class="mz-sub">${props.paciente_email}</div>` : ''}
          </div>
          <div class="mz-col">
            <div class="mz-label">Médico</div>
            <div class="mz-value">${props.medico_nome || '—'}</div>
            <div class="mz-sub">${props.especialidade_nome || '—'}</div>
          </div>
        </div>

        ${props.descricao && props.descricao!=='-' ? `
          <div class="mz-section">
            <div class="mz-label">Descrição do problema</div>
            <p class="mz-text" style="white-space:pre-wrap">${props.descricao}</p>
          </div>` : ''}

        <div class="mz-actions mt-2">
          <button class="mz-btn mz-btn--md mz-btn--inset"
                  onclick="document.getElementById('${rootId}')?.remove()">${t('modals.common.close','Fechar')}</button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', html);
  wireEscAndBackdrop(rootId);
}

/* ===== Modais de confirmação com pergunta e botões ===== */
function enviarPost(action, csrf) {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = action;
  form.style.display = 'none';
  const csrfInput = document.createElement('input');
  csrfInput.type = 'hidden'; csrfInput.name = '_token'; csrfInput.value = csrf;
  form.appendChild(csrfInput);
  document.body.appendChild(form);
  form.submit();
}

function basePrompt({ id, title, question, confirmLabel, confirmVariant, action, csrf, when }) {
  document.getElementById(id)?.remove();

  // ícone e texto no centro, estilo como a tua referência
  const html = `
    <div id="${id}" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="${id}-title">
      <div class="modal-content mz-card mz-card-sm">
        <button class="mz-x" aria-label="${t('modals.common.close','Fechar')}"
                onclick="document.getElementById('${id}')?.remove()">×</button>

        <div class="flex items-center justify-center mb-3">
          <div class="flex items-center justify-center w-16 h-16 rounded-full bg-slate-800 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
            </svg>
          </div>
        </div>

        <h2 id="${id}-title" class="sr-only">${title}</h2>
        <p class="mz-confirm-text text-center text-slate-900 text-lg">
          ${question}${when ? `<br><span class="font-semibold">${when}</span>` : ''}
        </p>

        <div class="mz-actions mt-4">
          <button id="${id}-yes"
                  class="inline-flex items-center justify-center px-4 py-2 font-bold rounded-xl text-white
                         ${confirmVariant==='ok'
                           ? 'bg-emerald-600 hover:bg-emerald-700 shadow-sm ring-1 ring-emerald-700/20'
                           : 'bg-red-600 hover:bg-red-700 shadow-sm ring-1 ring-red-700/20'}">
            ${confirmLabel}
          </button>

          <button class="mz-btn mz-btn--md mz-btn--inset"
                  onclick="document.getElementById('${id}')?.remove()">
            ${t('modals.common.no','Não')}
          </button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', html);

  document.getElementById(`${id}-yes`)?.addEventListener('click', () => enviarPost(action, csrf));
  wireEscAndBackdrop(id);
}

export function medicoAbrirModalConfirmar({ action, csrf, when }) {
  basePrompt({
    id: 'modal-confirmar-consulta',
    title: t('modals.confirm.title','Confirmar consulta'),
    question: t('modals.confirm.question','Tens a certeza que queres confirmar esta consulta?'),
    confirmLabel: t('modals.confirm.yes','Sim, confirmar'),
    confirmVariant: 'ok',
    action, csrf, when,
  });
}

export function medicoAbrirModalRejeitar({ action, csrf, when }) {
  basePrompt({
    id: 'modal-rejeitar-consulta',
    title: t('modals.reject.title','Rejeitar pedido'),
    question: t('modals.reject.question','Tens a certeza que queres cancelar este pedido de consulta?'),
    confirmLabel: t('modals.reject.yes','Sim, cancelar'),
    confirmVariant: 'danger',
    action, csrf, when,
  });
}

window.mostrarModalConsulta = mostrarModalConsulta;
window.medicoAbrirModalConfirmar = medicoAbrirModalConfirmar;
window.medicoAbrirModalRejeitar = medicoAbrirModalRejeitar;
