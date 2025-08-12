document.addEventListener('DOMContentLoaded', () => {
  const mount = document.querySelector('[data-page="wizard-consulta-admin"]');
  if (!mount) return;

  const endpointSlots = document.querySelector('meta[name="api-slots"]')?.content || '/api/slots';

  const heads = [...document.querySelectorAll('.step-head')];
  const steps = [...document.querySelectorAll('.wizard-step')];

  const back   = document.getElementById('btnBack');
  const next   = document.getElementById('btnNext');
  const submit = document.getElementById('btnSubmit');

  const pacienteSel = document.getElementById('paciente_id');
  const espSel      = document.getElementById('especialidade_id');
  const medicoSel   = document.getElementById('medico_id');
  const tipoSel     = document.getElementById('tipo_slug');
  const dataEl      = document.getElementById('data');
  const horaSel     = document.getElementById('hora');
  const motivoEl    = document.getElementById('motivo');
  const slotsMsg    = document.getElementById('slotsMsg');

  // -------- utils --------
  const fmtISO  = (d) => d.toISOString().slice(0,10);
  const addDays = (d, n) => { const x = new Date(d); x.setDate(x.getDate()+n); return x; };

  function setHead(idx){
    heads.forEach((h, n) => {
      h.classList.toggle('text-home-medigest', n <= idx);
      h.classList.toggle('font-semibold', n <= idx);
    });
  }

  let i = 0;
  async function show(idx){
    i = idx;
    steps.forEach((s, n) => s.classList.toggle('hidden', n !== i));
    setHead(i);
    back.style.visibility = i === 0 ? 'hidden' : 'visible';
    back.disabled = i === 0;
    next.classList.toggle('hidden', i === steps.length - 1);
    submit.classList.toggle('hidden', i !== steps.length - 1);

    // quando entras em "Data & Hora", auto-seta a data para o 1º dia válido com slots
    if (i === 4) {
      await ensureMinDateFromTipo(true); // true = autoPreencher
      await loadSlots();
    }
    if (i === steps.length - 1) fillReview();
  }

  function valid(idx){
    switch (idx) {
      case 0: return !!pacienteSel?.value;
      case 1: return !!espSel?.value;
      case 2: return !!medicoSel?.value;
      case 3: return !!tipoSel?.value; // placeholder obriga a escolher
      case 4: return !!dataEl?.value && !!horaSel?.value;
      default: return true;
    }
  }

  function nudge(){
    const s = steps[i];
    s.classList.add('animate-pulse');
    setTimeout(() => s.classList.remove('animate-pulse'), 200);
  }

  back?.addEventListener('click', () => show(Math.max(0, i - 1)));
  next?.addEventListener('click', () => { if (!valid(i)) return nudge(); show(Math.min(steps.length - 1, i + 1)); });

  // ------- médicos por especialidade -------
  espSel?.addEventListener('change', async () => {
    medicoSel.innerHTML = '<option value="">— a carregar… —</option>';
    try {
      const url = `/admin/especialidades/${espSel.value}/medicos`;
      const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
      if (!r.ok) throw new Error(`HTTP ${r.status}`);
      const list = await r.json();
      medicoSel.innerHTML = '';
      if (!Array.isArray(list) || list.length === 0) {
        medicoSel.innerHTML = '<option value="">Sem médicos para esta especialidade</option>';
        return;
      }
      const ph = document.createElement('option');
      ph.value = ''; ph.textContent = '— Seleciona —'; medicoSel.appendChild(ph);
      list.forEach(m => {
        const o = document.createElement('option');
        o.value = m.id;
        o.textContent = `${m.name} — ${m.email ?? ''}`;
        medicoSel.appendChild(o);
      });
    } catch (e) {
      console.error('Erro a carregar médicos:', e);
      medicoSel.innerHTML = '<option value="">Erro ao carregar médicos</option>';
    }
  });

  // ---------- Tipo/meta ----------
  async function fetchTipoMeta(slug){
    if (!slug) return null;
    try {
      const r = await fetch(`/api/consulta-tipos/${encodeURIComponent(slug)}`, { headers: { 'Accept':'application/json' }});
      if (!r.ok) return null;
      return await r.json(); // {lead_minutos, horizonte_horas, ...}
    } catch { return null; }
  }

  async function firstDayWithSlots(fromISO){
    // tenta esse dia; se vazio, avança até achar slots (máx 60 dias por segurança)
    let probe = fromISO;
    for (let k=0; k<60; k++){
      if (await hasSlots(probe)) return probe;
      probe = fmtISO(addDays(new Date(probe), 1));
    }
    return fromISO;
  }

  async function hasSlots(dateISO){
    if (!medicoSel.value || !tipoSel.value) return false;
    try {
      const url = new URL(endpointSlots, window.location.origin);
      url.searchParams.set('medico_id', medicoSel.value);
      url.searchParams.set('data', dateISO);
      url.searchParams.set('tipo', tipoSel.value);
      url.searchParams.set('duracao', 30);
      const r = await fetch(url, { headers: { 'Accept':'application/json' }});
      const j = await r.json();
      const arr = Array.isArray(j?.data) ? j.data : (Array.isArray(j) ? j : []);
      return arr.length > 0;
    } catch { return false; }
  }

  /**
   * Recalcula o MIN com base no tipo (lead) e, opcionalmente,
   * auto-preenche dataEl.value com o 1º dia que tenha slots.
   */
  async function ensureMinDateFromTipo(autoPreencher = false){
    if (!dataEl) return;

    // bloquear passadas por omissão
    const todayISO = fmtISO(new Date());
    dataEl.min = todayISO;
    dataEl.removeAttribute('max'); // nunca limitar por cima no UI

    const slug = tipoSel?.value;
    if (!slug) return;

    const meta = await fetchTipoMeta(slug);
    const leadMin = Number(meta?.lead_minutos ?? 0);
    const minStart = new Date(Date.now() + leadMin * 60 * 1000);
    let minISO = fmtISO(minStart);

    // se o dia do lead não tiver slots, avança para o 1º com slots (só se já houver médico escolhido)
    if (medicoSel.value) minISO = await firstDayWithSlots(minISO);

    dataEl.min = minISO;

    // 1) se não há valor, ou está abaixo do min, ou o dia atual **não tem** slots → fixa no minISO
    let needsSet = !dataEl.value || dataEl.value < dataEl.min;
    if (!needsSet && autoPreencher && medicoSel.value) {
      const ok = await hasSlots(dataEl.value);
      if (!ok) needsSet = true;
    }

    if (autoPreencher && needsSet) {
      dataEl.value = minISO;
    }
  }

  // ------- slots -------
  async function loadSlots(){
    if (!medicoSel.value || !dataEl.value || !tipoSel.value) {
      setHourOptions([], '— seleciona médico, tipo e data —');
      return;
    }
    horaSel.innerHTML = '<option value="">— a carregar… —</option>';
    slotsMsg.textContent = 'A carregar disponibilidade…';

    const raw = (dataEl.value || '').trim();
    const iso = raw.includes('/') ? raw.split('/').reverse().join('-') : raw;

    try {
      const url = new URL(endpointSlots, window.location.origin);
      url.searchParams.set('medico_id', medicoSel.value);
      url.searchParams.set('data',      iso);
      url.searchParams.set('tipo',      tipoSel.value);
      url.searchParams.set('duracao',   30);

      const res = await fetch(url, { headers: { 'Accept':'application/json' }});
      const j = await res.json();
      const arr = Array.isArray(j?.data) ? j.data : (Array.isArray(j) ? j : []);
      setHourOptions(arr);
      slotsMsg.textContent = arr.length
        ? `${arr.length} horários disponíveis`
        : 'Sem disponibilidade para a data escolhida';
    } catch (e) {
      console.error(e);
      setHourOptions([], 'Erro ao carregar slots');
      slotsMsg.textContent = 'Erro ao carregar slots';
    }
  }

  function setHourOptions(slots, placeholder='Sem disponibilidade'){
    horaSel.innerHTML = '';
    if (!Array.isArray(slots) || !slots.length) {
      const opt = document.createElement('option');
      opt.value = ''; opt.textContent = placeholder;
      horaSel.appendChild(opt); return;
    }
    slots.forEach(h => {
      const opt = document.createElement('option');
      opt.value = h; opt.textContent = h;
      horaSel.appendChild(opt);
    });
  }

  // listeners
  dataEl?.addEventListener('change', () => i === 4 && loadSlots());
  tipoSel?.addEventListener('change', async () => { await ensureMinDateFromTipo(true); await loadSlots(); });
  medicoSel?.addEventListener('change', async () => { await ensureMinDateFromTipo(true); await loadSlots(); });

  // revisão
  function fillReview(){
    const getText = (sel) => sel?.options?.[sel.selectedIndex]?.text ?? '';
    mount.querySelector('[data-review="paciente"]').textContent      = getText(pacienteSel) || '—';
    mount.querySelector('[data-review="especialidade"]').textContent = getText(espSel)      || '—';
    mount.querySelector('[data-review="medico"]').textContent        = getText(medicoSel)  || '—';
    mount.querySelector('[data-review="tipo"]').textContent          = tipoSel?.value      || '—';
    mount.querySelector('[data-review="data"]').textContent          = dataEl?.value       || '—';
    mount.querySelector('[data-review="hora"]').textContent          = horaSel?.value      || '—';
    mount.querySelector('[data-review="motivo"]').textContent        = motivoEl?.value     || '—';
  }

  // arranque
  dataEl.min = new Date().toISOString().slice(0,10); // passadas bloqueadas por defeito
  show(0);
});
