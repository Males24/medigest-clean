import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import { showCalendarEventModal } from './medico-calendario-modal';

import ptLocale from '@fullcalendar/core/locales/pt';
import esLocale from '@fullcalendar/core/locales/es';

const TITLE_LOCALE_MAP = { pt: 'pt-PT', en: 'en-US', es: 'es-ES' };

function setActiveViewButton(viewName){
  document.querySelectorAll('#cal-toolbar [data-cal="view"]').forEach(btn => {
    btn.setAttribute('data-active', btn.getAttribute('data-view') === viewName ? 'true' : 'false');
  });
}

function ddmmyyyy(date){
  const d = String(date.getDate()).padStart(2,'0');
  const m = String(date.getMonth()+1).padStart(2,'0');
  const y = date.getFullYear();
  return `${d}/${m}/${y}`;
}
function hhmm(date, locale='pt-PT'){
  return date.toLocaleTimeString(locale, { hour:'2-digit', minute:'2-digit', hour12:false });
}
function normalizeEstadoKey(s){
  const v = String(s||'').toLowerCase();
  if (['confirmada','confirmado','confirmed'].includes(v)) return 'confirmed';
  if (['agendada','scheduled'].includes(v)) return 'scheduled';
  if (['pendente','pending'].includes(v)) return 'pending';
  if (['pendente_medico','pending_doctor'].includes(v)) return 'pending_doctor';
  if (['cancelada','cancelado','canceled'].includes(v)) return 'canceled';
  return '';
}

document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('medico-calendar');
  const toolbar = document.getElementById('cal-toolbar');
  if (!el || !toolbar) return;

  const laravelLocale = (window.__APP_LOCALE || document.documentElement.lang || 'pt').split('-')[0];
  const titleLocale = TITLE_LOCALE_MAP[laravelLocale] || 'en-US';
  const events = Array.isArray(window.__MEDICO_EVENTS) ? window.__MEDICO_EVENTS : [];

  // Detecta touch
  const isTouch = window.matchMedia('(hover: none), (pointer: coarse)').matches;

  // Em touch não carregamos interactionPlugin (prioriza scroll)
  const plugins = [dayGridPlugin, timeGridPlugin, listPlugin, ...(isTouch ? [] : [interactionPlugin])];

  const cal = new Calendar(el, {
    plugins,
    locales: [ptLocale, esLocale],
    locale: laravelLocale,
    headerToolbar: false,
    initialView: 'dayGridMonth',
    firstDay: 0,
    navLinks: !isTouch,
    nowIndicator: true,
    expandRows: true,
    height: 'auto',
    dayMaxEvents: false,
    eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },
    dayHeaderFormat: { weekday: 'short' },
    titleFormat: { year: 'numeric', month: 'long' },
    events,

    // Touch: priorizar scroll
    editable: !isTouch,
    eventStartEditable: !isTouch,
    eventDurationEditable: !isTouch,
    selectable: !isTouch,
    droppable: !isTouch,
    eventDragMinDistance: isTouch ? 16 : 5,
    longPressDelay: isTouch ? 700 : 0,
    eventLongPressDelay: isTouch ? 700 : 0,
    selectLongPressDelay: isTouch ? 700 : 0,
    dragScroll: true,

    eventClassNames(arg){
      const estado = (arg.event.extendedProps?.estado || arg.event.extendedProps?.status || '').toString().toLowerCase();
      const tipo = (arg.event.extendedProps?.tipo || 'normal').toString().toLowerCase();
      const cls = ['ev', `ev--${tipo}`];
      if (estado.includes('cancelada') || estado.includes('canceled')) cls.push('ev--cancelada');
      return cls;
    },

    eventContent(arg){
      const fmt = new Intl.DateTimeFormat(titleLocale, { hour: '2-digit', minute: '2-digit', hour12: false });

      const start = arg.event.start;
      const end   = arg.event.end;
      let timeRange = '';
      if (start) {
        timeRange = fmt.format(start);
        if (end && +end !== +start) timeRange += ' – ' + fmt.format(end);
      } else if (arg.timeText) {
        timeRange = arg.timeText;
      }

      const paciente =
        arg.event.extendedProps?.paciente ??
        arg.event.extendedProps?.paciente_nome ??
        arg.event.extendedProps?.patient ??
        arg.event.title ?? '';

      const wrap   = document.createElement('span'); wrap.className = 'ev-inner';
      const lines  = document.createElement('span'); lines.className = 'ev-lines';

      const topLine = document.createElement('span'); topLine.className = 'ev-line ev-top';
      const dot     = document.createElement('span'); dot.className     = 'ev-dot';
      const timeEl  = document.createElement('span'); timeEl.className  = 'ev-time';
      timeEl.textContent = timeRange;
      topLine.appendChild(dot);
      topLine.appendChild(timeEl);

      const bottom  = document.createElement('span');
      bottom.className = 'ev-bottom ev-paciente';
      bottom.textContent = paciente;

      lines.appendChild(topLine);
      lines.appendChild(bottom);
      wrap.appendChild(lines);

      return { domNodes: [wrap] };
    },

    // Abre o modal
    eventClick(info){
      info.jsEvent.preventDefault();
      info.jsEvent.stopPropagation();

      const e   = info.event;
      const ex  = e.extendedProps || {};
      const dt  = e.start ? ddmmyyyy(e.start) : '';
      const ini = e.start ? hhmm(e.start, TITLE_LOCALE_MAP[laravelLocale] || 'pt-PT') : '';
      const fim = e.end   ? hhmm(e.end,   TITLE_LOCALE_MAP[laravelLocale] || 'pt-PT') : '';
      const hora = ini ? (fim && (+e.end !== +e.start) ? `${ini}–${fim}` : ini) : '';

      const estadoKey = ex.estado_key || normalizeEstadoKey(ex.estado || ex.status);
      const estado    = ex.estado_display || ex.estado || ex.status || estadoKey || '—';

      const medicoNome  = ex.medico_nome || ex.medico || (window.__MEDICO_LOGADO?.nome ?? '—');
      const medicoEmail = ex.medico_email || (window.__MEDICO_LOGADO?.email ?? '');
      const especNome   = ex.especialidade_nome || ex.especialidade || (window.__MEDICO_LOGADO?.especialidade_nome ?? '');

      const props = {
        // cabeçalho
        data_consulta: dt && hora ? `${dt} ${hora}` : (dt || '—'),

        // paciente / medico
        paciente_nome:  ex.paciente_nome || ex.paciente || ex.patient || e.title || '—',
        paciente_email: ex.paciente_email || ex.email || '',
        medico_nome:    medicoNome,
        medico_email:   medicoEmail,
        especialidade_nome: especNome,

        // outros
        tipo:           ex.tipo || ex.consulta_tipo || '—',
        descricao:      ex.descricao || ex.motivo || ex.notes || '',
        estado,
        estado_key:     estadoKey,

        // datas cruas (fallback no modal)
        start: e.start ? e.start.toISOString() : null,
        end:   e.end   ? e.end.toISOString()   : null,
      };

      showCalendarEventModal(props);
    },

    datesSet(){
      updateTitle();
      setActiveViewButton(cal.view.type);
    },
  });

  cal.render();
  updateTitle();
  setActiveViewButton(cal.view.type);

  function updateTitle(){
    const d = cal.getDate();
    const t = d.toLocaleDateString(TITLE_LOCALE_MAP[laravelLocale] || 'en-US', { month: 'long', year: 'numeric' });
    const tt = t.charAt(0).toUpperCase() + t.slice(1);
    const title = document.getElementById('cal-title');
    if (title) title.textContent = tt;
  }

  toolbar.addEventListener('click', (e) => {
    const node = e.target.closest('[data-cal]');
    if(!node) return;
    const act = node.getAttribute('data-cal');
    if (act === 'prev') cal.prev();
    else if (act === 'next') cal.next();
    else if (act === 'today') cal.today();
    else if (act === 'view') cal.changeView(node.getAttribute('data-view'));
  });

  toolbar.addEventListener('keydown', (e) => {
    if(e.key !== 'Enter' && e.key !== ' ') return;
    const node = e.target.closest('[data-cal]');
    if (!node) return;
    e.preventDefault();
    node.click();
  });
});
