import '../../css/modal.css';

/* ================= MODAL: VER DETALHES ================= */
export function mostrarModalConsulta(props) {
  const statusColor = getStatusColor(props.estado || '');
  const rootId = 'modal-consulta';

  const html = `
    <div id="${rootId}" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="mz-det-title">
      <div class="modal-content mz-card mz-card-md mz-card-centered">

        <button class="mz-x" aria-label="Fechar"
                onclick="document.getElementById('${rootId}')?.remove()">×</button>

        <div class="mz-head-centered">
          <h1 id="mz-det-title" class="text-3xl font-semibold tracking-tight text-gray-900">Detalhes da Consulta</h1>
        </div>

        <hr class="mz-hr">

        <dl class="mz-dl">
          <div class="mz-row">
            <dt>Data da consulta:</dt>
            <dd>${props.data_consulta}</dd>
          </div>
          <div class="mz-row">
            <dt>Nome do Paciente:</dt>
            <dd>${props.paciente_nome}</dd>
          </div>
          <div class="mz-row">
            <dt>Email do Paciente:</dt>
            <dd>${props.paciente_email}</dd>
          </div>
          <div class="mz-row">
            <dt>Descrição do Problema:</dt>
            <dd>${props.descricao}</dd>
          </div>
          <div class="mz-row">
            <dt>Nome do Médico:</dt>
            <dd>${props.medico_nome}</dd>
          </div>
          <div class="mz-row">
            <dt>Especialidade:</dt>
            <dd>${props.especialidade_nome}</dd>
          </div>
          <div class="mz-row">
            <dt>Estado da Consulta:</dt>
            <dd><span class="badge ${statusColor}">${props.estado}</span></dd>
          </div>
        </dl>

        <div class="mz-footer">
          <button class="mz-btn mz-btn--md mz-btn--close mz-btn-block"
                  onclick="document.getElementById('${rootId}')?.remove()">Fechar</button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', html);
  wireEscAndBackdrop(rootId);
}

/* ============ MODAL: CONFIRMAR CANCELAMENTO (igual estilo) ============ */
export function confirmarCancelamento({ action, csrf, mensagem }) {
  const texto = mensagem || 'Tens a certeza que queres cancelar esta consulta?';
  const rootId = 'modal-cancelar';

  const html = `
    <div id="${rootId}" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="mz-cancel-title">
      <div class="modal-content mz-card mz-card-sm">
        <button class="mz-x" aria-label="Fechar"
                onclick="document.getElementById('${rootId}')?.remove()">×</button>

        <div class="mz-icon-wrap"><div class="mz-icon" aria-hidden="true"></div></div>

        <h2 id="mz-cancel-title" class="sr-only">Confirmar cancelamento</h2>
        <p class="mz-confirm-text">${texto}</p>

        <div class="mz-actions">
          <button id="mz-confirm-yes" class="mz-btn mz-btn--md mz-btn--danger">Sim, cancelar</button>
          <button class="mz-btn mz-btn--md mz-btn--inset"
                  onclick="document.getElementById('${rootId}')?.remove()">Não</button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', html);

  // POST com CSRF (Laravel)
  document.getElementById('mz-confirm-yes')?.addEventListener('click', () => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;
    form.style.display = 'none';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrf;

    form.appendChild(csrfInput);
    document.body.appendChild(form);
    form.submit();
  });

  wireEscAndBackdrop(rootId);
}

/* ===== Helpers ===== */
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

/* Expor no window para os Blades */
window.mostrarModalConsulta = mostrarModalConsulta;
window.confirmarCancelamento = confirmarCancelamento;
