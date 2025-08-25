document.addEventListener('DOMContentLoaded', () => {
  const $ = (s, ctx = document) => ctx.querySelector(s);

  const slotsURL = document.querySelector('meta[name="api-slots"]')?.content || '/api/slots';
  const medicoId = document.querySelector('meta[name="doctor-id"]')?.content || '';

  const dataEl   = $('#slotData');
  const tipoEl   = $('#slotTipo');
  const wrap     = $('#slotsWrap');
  const statusEl = $('#slotsStatus');
  const cta      = $('#slotCTA');

  if (!wrap || !dataEl || !cta) return;

  function renderSlots(slots, msg) {
    wrap.innerHTML = '';
    if (statusEl && msg) statusEl.textContent = msg;

    if (!Array.isArray(slots) || !slots.length) {
      wrap.innerHTML = '<div class="text-sm text-zinc-500 px-1">Sem disponibilidade para os filtros selecionados.</div>';
      return;
    }

    const list = document.createElement('div');
    list.className = 'flex flex-wrap gap-2';

    slots.forEach(h => {
      const params = new URLSearchParams({
        medico: medicoId,
        medico_id: medicoId, // compat
        data: dataEl.value,
        hora: h
      });

      const a = document.createElement('a');
      a.href = cta.href + (cta.href.includes('?') ? '&' : '?') + params.toString();
      a.className = 'slot-chip hover:bg-emerald-50';
      a.textContent = h;
      list.appendChild(a);
    });

    wrap.appendChild(list);
  }

  async function load() {
    if (!medicoId || !dataEl.value) {
      renderSlots([], '—');
      return;
    }
    renderSlots([], 'A carregar…');

    try {
      const url = new URL(slotsURL, window.location.origin);
      url.searchParams.set('medico_id', medicoId);
      url.searchParams.set('data', dataEl.value);
      if (tipoEl?.value) url.searchParams.set('tipo', tipoEl.value);
      url.searchParams.set('duracao', 30);

      const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const j = await r.json();
      const arr = Array.isArray(j?.data) ? j.data : (Array.isArray(j) ? j : []);
      renderSlots(arr, arr.length ? `${arr.length} horários disponíveis` : 'Sem disponibilidade');
    } catch (e) {
      console.error(e);
      renderSlots([], 'Erro ao carregar');
    }
  }

  dataEl.addEventListener('change', load);
  tipoEl?.addEventListener('change', load);

  load();
});
