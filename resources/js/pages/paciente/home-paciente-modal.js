// resources/js/pages/paciente/home-paciente-modal.js
import '../../../css/modal.css';

/* ===== i18n pequena (fallback) ===== */
const T = {
  status: {
    confirmed: 'confirmada',
    scheduled: 'agendada',
    pending: 'pendente',
    pending_doctor: 'pendente (médico)',
    canceled: 'cancelada',
  },
  modals: {
    consultation: {
      title: 'Consulta',
      patient: 'Paciente',
      doctor: 'Médico',
      description: 'Descrição do problema',
      no_description: 'Sem descrição informada.',
      duration: 'Duração',
    },
    common: { close: 'Fechar' },
  },
};
const t = (path, fb = '') =>
  path.split('.').reduce((o, k) => (o ? o[k] : undefined), window.I18N || T) ?? fb;

/* ===== opções de apresentação ===== */
const SHOW_SECONDS = false; // <- muda para true se quiseres HH:MM:SS em vez de HH:MM

/* ===== helpers ===== */
function statusColor(s) {
  const k = String(s).toLowerCase();
  if (k === 'confirmed' || k === 'confirmada' || k === 'confirmado') return 'bg-confirmado';
  if (k === 'scheduled' || k === 'agendada') return 'bg-emerald-500';
  if (k === 'pending' || k === 'pendente') return 'bg-pendente';
  if (k === 'pending_doctor' || k === 'pendente_medico') return 'bg-violet-500';
  if (k === 'canceled' || k === 'cancelada' || k === 'cancelado') return 'bg-cancelado';
  return 'bg-gray-500';
}
function wireEscAndBackdrop(id) {
  const root = document.getElementById(id);
  if (!root) return;
  function onKey(e) { if (e.key === 'Escape') { root.remove(); window.removeEventListener('keydown', onKey); } }
  window.addEventListener('keydown', onKey);
  root.addEventListener('click', (e) => { if (e.target === root) root.remove(); });
}
function escapeHTML(s = '') {
  return String(s)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}
function isTimeLike(s) { return /^\s*\d{1,2}:\d{2}(:\d{2})?\s*$/.test(String(s || '')); }
function pad(n) { return n < 10 ? '0' + n : '' + n; }
function parseTime(s) {
  const m = String(s || '').match(/^\s*(\d{1,2}):(\d{2})(?::(\d{2}))?\s*$/);
  if (!m) return null;
  return { h: +m[1], m: +m[2], s: +(m[3] || 0) };
}
/* -> Formata qualquer string de hora segundo SHOW_SECONDS */
function fmtTime(s) {
  const t = parseTime(s);
  if (!t) return (s || '').trim();
  return SHOW_SECONDS ? `${pad(t.h)}:${pad(t.m)}:${pad(t.s)}` : `${pad(t.h)}:${pad(t.m)}`;
}
/* -> Soma minutos e devolve já formatado conforme SHOW_SECONDS */
function addMinutesToTime(timeStr, minutes) {
  const t = parseTime(timeStr);
  if (!t) return '';
  const d = new Date(2000, 0, 1, t.h, t.m, t.s);
  d.setMinutes(d.getMinutes() + (+minutes || 0));
  return SHOW_SECONDS
    ? `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`
    : `${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

/* ===== modal (layout melhorado + hora início/fim + descrição) ===== */
export function mostrarModalConsulta(props = {}) {
  const rootId = 'modal-consulta';
  document.getElementById(rootId)?.remove();

  const estadoKey = (props.estado_key || '').toString().toLowerCase();
  const estadoTxt =
    props.estado ||
    (estadoKey ? t(`status.${estadoKey}`, estadoKey) : '') ||
    (props.estado_key ? String(props.estado_key) : '—');

  // data + horas
  const [dStrFromDC, hStrFromDC] = String(props.data_consulta || '').split(' ');
  const dataLabel = escapeHTML(props.data || dStrFromDC || props.data_consulta || '—');

  const duracaoMin =
    Number(props.duracao_minutos ?? props.duracao ?? props.duration ?? (isTimeLike(hStrFromDC) ? 30 : 0));

  const horaInicioRaw = String(props.hora_inicio || hStrFromDC || '').trim();
  const horaInicio = isTimeLike(horaInicioRaw) ? fmtTime(horaInicioRaw) : horaInicioRaw;

  let horaFimRaw = String(props.hora_fim || '').trim();
  if (!horaFimRaw && isTimeLike(horaInicio) && duracaoMin > 0) {
    horaFimRaw = addMinutesToTime(horaInicio, duracaoMin);
  }
  const horaFim = isTimeLike(horaFimRaw) ? fmtTime(horaFimRaw) : horaFimRaw;

  const timeRange = horaInicio ? escapeHTML(horaInicio + (horaFim ? ` – ${horaFim}` : '')) : '';

  const estadoClass = statusColor(estadoKey || props.estado || '');
  const estadoLabel = escapeHTML(estadoTxt);

  const pacNome = escapeHTML(props.paciente_nome || '—');
  const pacEmail = escapeHTML(props.paciente_email || '');
  const medNome = escapeHTML(props.medico_nome || '—');
  const espNome = escapeHTML(props.especialidade_nome || '—');

  const descRaw = (props.descricao ?? '').toString().trim();
  const descText = escapeHTML(descRaw && descRaw !== '-' ? descRaw : t('modals.consultation.no_description'));

  const duracaoLabel = duracaoMin > 0 ? `${duracaoMin} min` : '';

  const html = `
    <div id="${rootId}" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="mz-det-title">
      <div class="modal-content mz-card mz-card-compact">
        <button class="mz-x" aria-label="${t('modals.common.close','Fechar')}"
                onclick="document.getElementById('${rootId}')?.remove()">×</button>

        <h1 id="mz-det-title" class="mz-title-compact">${t('modals.consultation.title','Consulta')}</h1>

        <div class="mz-meta">
          <span class="mz-date" title="${dataLabel}">
            <svg class="inline-block align-[-2px] w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            ${dataLabel}
          </span>

          ${timeRange ? `
            <span class="mz-dot">•</span>
            <span class="mz-time" title="${timeRange}">
              <svg class="inline-block align-[-2px] w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
              ${timeRange}
            </span>` : ''}

          <span class="badge ${estadoClass} mz-chip ml-2">${estadoLabel}</span>
          ${duracaoLabel ? `<span class="mz-dot">•</span><span class="mz-sub">${t('modals.consultation.duration','Duração')}: ${duracaoLabel}</span>` : ''}
        </div>

        <div class="mz-grid">
          <div class="mz-col">
            <div class="mz-label">${t('modals.consultation.patient','Paciente')}</div>
            <div class="mz-value">${pacNome}</div>
            ${pacEmail ? `<div class="mz-sub">${pacEmail}</div>` : ''}
          </div>
          <div class="mz-col">
            <div class="mz-label">${t('modals.consultation.doctor','Médico')}</div>
            <div class="mz-value">${medNome}</div>
            <div class="mz-sub">${espNome}</div>
          </div>
        </div>

        <div class="mz-section">
          <div class="mz-label">${t('modals.consultation.description','Descrição do problema')}</div>
          <div class="mz-text" style="white-space:pre-wrap">${descText}</div>
        </div>

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

window.mostrarModalConsulta = mostrarModalConsulta;

/* ===== normalização / extração de payload ===== */
function normalizePayload(p = {}) {
  const estadoKey =
    (p.estado_key ||
      ({
        confirmada: 'confirmed',
        confirmado: 'confirmed',
        agendada: 'scheduled',
        pendente: 'pending',
        pendente_medico: 'pending_doctor',
        cancelada: 'canceled',
        cancelado: 'canceled',
      }[String(p.estado || '').toLowerCase()])) || '';

  const estado =
    p.estado ||
    (estadoKey ? t(`status.${estadoKey}`, estadoKey) : '') ||
    '-';

  const [dStrFromDC, hStrFromDC] = String(p.data_consulta || '').split(' ');
  const hora_inicio_raw = (p.hora_inicio || hStrFromDC || '').trim();
  const hora_inicio = isTimeLike(hora_inicio_raw) ? fmtTime(hora_inicio_raw) : hora_inicio_raw;

  const duracao_minutos = Number(p.duracao_minutos ?? p.duracao ?? p.duration ?? (isTimeLike(hora_inicio) ? 30 : 0));
  let hora_fim_raw = (p.hora_fim || '').trim();
  if (!hora_fim_raw && isTimeLike(hora_inicio) && duracao_minutos > 0) {
    hora_fim_raw = addMinutesToTime(hora_inicio, duracao_minutos);
  }
  const hora_fim = isTimeLike(hora_fim_raw) ? fmtTime(hora_fim_raw) : hora_fim_raw;

  return {
    data_consulta: p.data_consulta || '—',
    data: p.data || dStrFromDC || '',
    hora_inicio,
    hora_fim,
    duracao_minutos,

    paciente_nome: p.paciente_nome || '-',
    paciente_email: p.paciente_email || '-',
    descricao: p.descricao || '-',
    medico_nome: p.medico_nome || '-',
    especialidade_nome: p.especialidade_nome || '-',

    estado,
    estado_key: estadoKey,
  };
}

function extractFromDOM(triggerEl) {
  const root =
    triggerEl.closest('.card') ||
    triggerEl.closest('li') ||
    triggerEl.closest('section') ||
    document.body;

  const medEl =
    root.querySelector('.text-base.font-medium.text-zinc-900') ||
    root.querySelector('.font-medium.text-zinc-900');
  const medico_nome = medEl?.textContent?.trim() || '-';

  const infoEl = root.querySelector('.text-sm.text-zinc-600');
  const info = infoEl?.textContent || '';
  const parts = info.split('•').map((s) => s.trim()).filter(Boolean);
  const data = parts[0] || '';
  const hora_inicio_raw = parts.find((p) => isTimeLike(p)) || '';
  const hora_inicio = isTimeLike(hora_inicio_raw) ? fmtTime(hora_inicio_raw) : hora_inicio_raw;
  const esp = parts[parts.length - 1] && !isTimeLike(parts[parts.length - 1]) ? parts[parts.length - 1] : '';

  const duracao_minutos = isTimeLike(hora_inicio) ? 30 : 0;
  const hora_fim = isTimeLike(hora_inicio) ? addMinutesToTime(hora_inicio, duracao_minutos) : '';

  const estadoRaw = root.querySelector('.chip')?.textContent?.trim().toLowerCase() || '';
  const estado_key =
    {
      'confirmada': 'confirmed',
      'confirmado': 'confirmed',
      'agendada': 'scheduled',
      'pendente': 'pending',
      'pendente (médico)': 'pending_doctor',
      'pendente (medico)': 'pending_doctor',
      'cancelada': 'canceled',
      'cancelado': 'canceled',
    }[estadoRaw] || '';
  const estado = estado_key ? t(`status.${estado_key}`, estado_key) : (estadoRaw || '-');

  const data_consulta = (data || '—') + (hora_inicio ? ` ${hora_inicio}` : '');

  return {
    data_consulta,
    data,
    hora_inicio,
    hora_fim,
    duracao_minutos,

    paciente_nome: '-',
    paciente_email: '-',
    descricao: '-', // cartões não trazem descrição
    medico_nome,
    especialidade_nome: esp || '-',
    estado,
    estado_key,
  };
}

function readPayloadFrom(el) {
  try {
    const raw = el.dataset?.payload ? JSON.parse(el.dataset.payload) : null;
    if (raw && typeof raw === 'object') return normalizePayload(raw);
  } catch (_) {}
  return normalizePayload(extractFromDOM(el));
}

/* ===== delegação de clique ===== */
document.addEventListener('click', (e) => {
  const trg = e.target.closest('.js-consulta-det');
  if (!trg) return;
  e.preventDefault();
  const payload = readPayloadFrom(trg);
  mostrarModalConsulta(payload);
});
