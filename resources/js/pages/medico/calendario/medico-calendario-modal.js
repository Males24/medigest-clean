// resources/js/pages/medico/calendario/medico-calendario-modal.js
import '../../../../css/modal.css';

/* ========= helpers ========= */
const t = (p, fb = '') => p.split('.').reduce((o, k) => o?.[k], window.I18N || {}) ?? fb;

function statusColor(statusIn) {
  const s = String(statusIn || '').toLowerCase();
  if (['confirmed','confirmada','confirmado'].includes(s)) return 'bg-confirmado';
  if (['scheduled','agendada'].includes(s))               return 'bg-emerald-500';
  if (['pending','pendente'].includes(s))                 return 'bg-pendente';
  if (['pending_doctor','pendente_medico'].includes(s))   return 'bg-violet-500';
  if (['canceled','cancelada','cancelado'].includes(s))   return 'bg-cancelado';
  return 'bg-gray-500';
}

function fmtDate(d, locale='pt-PT'){ try{ return new Date(d).toLocaleDateString(locale,{day:'2-digit',month:'2-digit',year:'numeric'});}catch{return String(d||'');} }
function fmtTime(d, locale='pt-PT'){ try{ return new Date(d).toLocaleTimeString(locale,{hour:'2-digit',minute:'2-digit',hour12:false});}catch{return '';} }
function diffMinutes(a,b){ if(!a||!b) return null; const ms=(new Date(b)-new Date(a)); return Math.round(ms/60000); }

function parseFromLabel(s){
  // "dd/mm/aaaa HH:MM–HH:MM" ou "dd/mm/aaaa HH:MM"
  const m = String(s||'').match(/^(\d{2}\/\d{2}\/\d{4})(?:\s+(\d{2}:\d{2})(?:\s*[-–]\s*(\d{2}:\d{2}))?)?$/);
  if (!m) return { date: '—', time: '—', minutes: null };
  const date = m[1];
  const ini  = m[2] || '';
  const fim  = m[3] || '';
  let minutes = null;
  if (ini && fim){
    const [h1,mi1] = ini.split(':').map(Number);
    const [h2,mi2] = fim.split(':').map(Number);
    minutes = (h2*60+mi2) - (h1*60+mi1);
  }
  return { date, time: ini ? (fim ? `${ini}–${fim}` : ini) : '—', minutes };
}

function closeById(id){
  const el = document.getElementById(id);
  if (!el) return;
  el.remove();
}

/* ========= modal ========= */
export function showCalendarEventModal(input){
  const rootId = 'mz-calendario-modal';

  const appLang = (window.__APP_LOCALE || document.documentElement.lang || 'pt').split('-')[0];
  const locale  = appLang === 'pt' ? 'pt-PT' : (appLang === 'es' ? 'es-ES' : 'en-US');

  // aceita payload já normalizado
  const xp = input || {};

  // 1) Data/hora: preferir start/end se existirem, senão extrair de data_consulta
  let dStr = '—', tempo = '—', durMin = null;
  if (xp.start || xp.end){
    const start = xp.start ? new Date(xp.start) : null;
    const end   = xp.end   ? new Date(xp.end)   : null;
    dStr = start ? fmtDate(start, locale) : '—';
    const tIni = start ? fmtTime(start, locale) : '';
    const tFim = end   ? fmtTime(end,   locale) : '';
    tempo  = tIni ? (tFim ? `${tIni}–${tFim}` : tIni) : (tFim || '—');
    durMin = diffMinutes(start, end);
  } else if (xp.data_consulta) {
    const p = parseFromLabel(xp.data_consulta);
    dStr    = p.date;
    tempo   = p.time;
    durMin  = p.minutes;
  }

  // 2) Estado/cores
  const estadoKey  = (xp.estado_key || xp.estado || xp.status || '').toString().toLowerCase();
  const estadoTxt  = xp.estado || (estadoKey ? t(`status.${estadoKey}`, estadoKey) : '—');
  const estadoCls  = statusColor(estadoKey || xp.estado || '');

  // 3) Paciente / Médico
  const pacienteNome  = xp.paciente_nome || xp.paciente || xp.patient || '—';
  const pacienteEmail = xp.paciente_email || '';
  const medicoNome    = xp.medico_nome || window.__MEDICO_LOGADO?.nome || '—';
  const medicoEmail   = xp.medico_email || window.__MEDICO_LOGADO?.email || '';
  const especialidade = xp.especialidade_nome || xp.especialidade || window.__MEDICO_LOGADO?.especialidade_nome || '—';
  const tipo          = xp.tipo || xp.consulta_tipo || '—';
  const descricao     = xp.descricao || xp.motivo || '';

  // limpa modal antigo se existir
  closeById(rootId);

  const showTime = tempo && tempo !== '—';

  const html = `
  <div id="${rootId}" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="mz-cal-title">
    <div class="modal-content mz-card mz-card-compact">
      <button class="mz-x" aria-label="${t('modals.common.close','Fechar')}" onclick="document.getElementById('${rootId}')?.remove()">×</button>

      <h1 id="mz-cal-title" class="mz-title-compact">${t('modals.consultation.title','Consulta')}</h1>

      <div class="mz-meta">
        <span class="mz-date">${dStr}</span>
        ${showTime ? `<span class="mz-dot">•</span><span class="mz-time">${tempo}</span>` : ''}
        <span class="badge ${estadoCls} mz-chip">${estadoTxt}</span>
      </div>

      <!-- Bloco Paciente / Médico -->
      <div class="mz-grid">
        <div class="mz-col">
          <div class="mz-label">${t('modals.consultation.patient','Paciente')}</div>
          <div class="mz-value">${escapeHtml(pacienteNome)}</div>
          ${pacienteEmail ? `<div class="mz-sub">${escapeHtml(pacienteEmail)}</div>` : ''}
        </div>

        <div class="mz-col">
          <div class="mz-label">${t('modals.consultation.doctor','Médico')}</div>
          <div class="mz-value">${escapeHtml(medicoNome)}</div>
          <div class="mz-sub">${escapeHtml(especialidade)}</div>
          ${medicoEmail ? `<div class="mz-sub">${escapeHtml(medicoEmail)}</div>` : ''}
        </div>
      </div>

      <!-- Detalhes adicionais -->
      <div class="mz-grid" style="margin-top:12px;">
        <div class="mz-col">
          <div class="mz-label">${t('common.type','Tipo')}</div>
          <div class="mz-value">${escapeHtml(String(tipo))}</div>
        </div>
        <div class="mz-col">
          <div class="mz-label">${t('modals.consultation.time','Hora')}</div>
          <div class="mz-value">${showTime ? tempo : '—'}</div>
          ${durMin ? `<div class="mz-sub">${durMin} min</div>` : ''}
        </div>
      </div>

      ${descricao ? `
        <div class="mz-section">
          <div class="mz-label">${t('modals.consultation.description','Descrição')}</div>
          <p class="mz-text">${escapeHtml(descricao)}</p>
        </div>
      ` : ''}

      <div class="mz-actions" style="margin-top:14px;">
        <button class="mz-btn mz-btn--md mz-btn--close" onclick="document.getElementById('${rootId}')?.remove()">
          ${t('modals.common.close','Fechar')}
        </button>
      </div>
    </div>
  </div>`;

  document.body.insertAdjacentHTML('beforeend', html);

  // ESC e click no backdrop fecham
  const root = document.getElementById(rootId);
  const onKey = (e)=>{ if(e.key==='Escape'){ closeById(rootId); window.removeEventListener('keydown', onKey);} };
  window.addEventListener('keydown', onKey);
  root.addEventListener('click',(e)=>{ if(e.target===root) closeById(rootId); });
}

/* protege contra injecção HTML nos textos livres */
function escapeHtml(s){
  return String(s ?? '')
    .replaceAll('&','&amp;')
    .replaceAll('<','&lt;')
    .replaceAll('>','&gt;')
    .replaceAll('"','&quot;')
    .replaceAll("'",'&#039;');
}

// expor global (opcional)
window.showCalendarEventModal = showCalendarEventModal;
