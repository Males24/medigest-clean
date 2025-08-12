document.addEventListener('DOMContentLoaded', () => {
  const mount = document.querySelector('[data-page="medico-create"]');
  if (!mount) return;

  const api = document.querySelector('meta[name="api-slots"]')?.content || '/api/slots';

  const medicoId = document.getElementById('medico_id');
  const tipoSel  = document.getElementById('tipo_slug');
  const dataIn   = document.getElementById('data');
  const horaSel  = document.getElementById('hora');
  const msg      = document.getElementById('slotsMsg');

  // ---------- helpers ----------
  const fmtISO  = (d) => d.toISOString().slice(0,10);
  const addDays = (d, n) => { const x = new Date(d); x.setDate(x.getDate()+n); return x; };

  async function fetchTipoMeta(slug){
    if (!slug) return null;
    try {
      const r = await fetch(`/api/consulta-tipos/${encodeURIComponent(slug)}`, { headers: { 'Accept':'application/json' }});
      if (!r.ok) return null;
      return await r.json(); // {lead_minutos, horizonte_horas, ...}
    } catch { return null; }
  }

  async function hasSlots(dateISO){
    if (!medicoId?.value || !tipoSel?.value) return false;
    try {
      const url = new URL(api, window.location.origin);
      url.searchParams.set('medico_id', medicoId.value);
      url.searchParams.set('data', dateISO);
      url.searchParams.set('tipo', tipoSel.value);
      url.searchParams.set('duracao', 30);
      const r = await fetch(url, { headers: {'Accept':'application/json'} });
      const j = await r.json();
      const arr = Array.isArray(j?.data) ? j.data : (Array.isArray(j) ? j : []);
      return arr.length > 0;
    } catch {
      return false;
    }
  }

  async function firstDayWithSlots(fromISO){
    let probe = fromISO;
    for (let k=0; k<60; k++){
      if (await hasSlots(probe)) return probe;
      probe = fmtISO(addDays(new Date(probe), 1));
    }
    return fromISO;
  }

  async function ensureMinDateFromTipo(autoFill = false){
    if (!dataIn) return;

    // nunca datas passadas no UI
    const todayISO = fmtISO(new Date());
    dataIn.min = todayISO;
    dataIn.removeAttribute('max');

    const slug = tipoSel?.value;
    if (!slug) return;

    const meta = await fetchTipoMeta(slug);
    const leadMin = Number(meta?.lead_minutos ?? 0);   // minutos
    const minStart = new Date(Date.now() + leadMin * 60 * 1000);
    let minISO = fmtISO(minStart);

    // salta para o 1º dia com slots (só faz sentido se já tens tipo escolhido)
    if (medicoId?.value) {
      minISO = await firstDayWithSlots(minISO);
    }

    dataIn.min = minISO;

    // se não há valor, ou está abaixo do min, ou o dia não tem slots -> fixa
    let needsSet = !dataIn.value || dataIn.value < dataIn.min;
    if (!needsSet && autoFill && medicoId?.value) {
      const ok = await hasSlots(dataIn.value);
      if (!ok) needsSet = true;
    }
    if (autoFill && needsSet) {
      dataIn.value = minISO;
    }
  }

  async function loadSlots(){
    if (!medicoId?.value || !tipoSel?.value || !dataIn?.value) {
      setHourOptions([], '— seleciona tipo e data —');
      return;
    }
    horaSel.innerHTML = '<option value="">— a carregar… —</option>';
    msg.textContent = 'A carregar disponibilidade…';

    const iso = dataIn.value.includes('/') ? dataIn.value.split('/').reverse().join('-') : dataIn.value;

    try {
      const url = new URL(api, window.location.origin);
      url.searchParams.set('medico_id', medicoId.value);
      url.searchParams.set('data', iso);
      url.searchParams.set('tipo', tipoSel.value);
      url.searchParams.set('duracao', 30);

      const r = await fetch(url, { headers: {'Accept':'application/json'} });
      const j = await r.json();
      const arr = Array.isArray(j?.data) ? j.data : (Array.isArray(j) ? j : []);

      setHourOptions(arr);
      msg.textContent = arr.length ? `${arr.length} horários disponíveis` : 'Sem disponibilidade para a data escolhida';
    } catch {
      setHourOptions([], '— erro ao carregar —');
      msg.textContent = 'Não foi possível carregar os slots.';
    }
  }

  function setHourOptions(slots, placeholder='Sem disponibilidade'){
    horaSel.innerHTML = '';
    if (!Array.isArray(slots) || !slots.length) {
      const o = document.createElement('option');
      o.value = ''; o.textContent = placeholder;
      horaSel.appendChild(o);
      return;
    }
    slots.forEach(h => {
      const o = document.createElement('option');
      o.value = o.textContent = h;
      horaSel.appendChild(o);
    });
  }

  // listeners
  tipoSel?.addEventListener('change', async () => { await ensureMinDateFromTipo(true); await loadSlots(); });
  dataIn?.addEventListener('change', async () => { await loadSlots(); });

  // arranque
  if (dataIn) dataIn.min = fmtISO(new Date());
  // não auto-preenche enquanto não escolherem o tipo
  // se quiseres preencher ao abrir quando já houver tipo preset, basta:
  // if (tipoSel.value) ensureMinDateFromTipo(true).then(loadSlots);

});
