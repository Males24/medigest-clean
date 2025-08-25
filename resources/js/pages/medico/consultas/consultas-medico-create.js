document.addEventListener('DOMContentLoaded', () => {
  const mount = document.querySelector('[data-page="wizard-consulta-medico"]');
  if (!mount) return;

  const endpointSlots = document.querySelector('meta[name="api-slots"]')?.content || '/api/slots';

  const heads   = [...document.querySelectorAll('.step-head')];
  const steps   = [...document.querySelectorAll('.wizard-step')];
  const rail    = document.getElementById('wizard-rail');
  const railFill= document.getElementById('wizard-rail-fill');

  const back   = document.getElementById('btnBack');
  const next   = document.getElementById('btnNext');
  const submit = document.getElementById('btnSubmit');

  const pacienteSel = document.getElementById('paciente_id');
  const espSel      = document.getElementById('especialidade_id');
  const medicoSel   = document.getElementById('medico_id');
  const tipoSel     = document.getElementById('tipo_slug');
  const dataEl      = document.getElementById('data');  // hidden
  const horaSel     = document.getElementById('hora');  // hidden
  const motivoEl    = document.getElementById('motivo');
  const motivoCount = document.getElementById('motivo-count');
  const motivoClear = document.getElementById('motivoClear');
  const slotsMsg    = document.getElementById('slotsMsg');

  /* ===== Selects com visual coerente ===== */
  (function enhanceSelects(){
    function build(select){
      if (select.__mg) return;

      select.classList.add('sr-only','absolute','opacity-0','-z-10');
      const wrap = document.createElement('div');
      wrap.className = 'relative w-full';
      select.parentNode.insertBefore(wrap, select);
      wrap.appendChild(select);

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'mg-sel-btn w-full h-11 px-3.5 pe-10 text-left text-sm rounded-xl bg-white border border-gray-300 ring-1 ring-transparent hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-home-medigest-button/70 transition';
      wrap.appendChild(btn);

      const caret = document.createElement('span');
      caret.innerHTML = '<svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.25 7.5l4.5 4.5 4.5-4.5"/></svg>';
      caret.className = 'pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-500';
      wrap.appendChild(caret);

      const menu = document.createElement('div');
      menu.className = 'mg-sel-menu hidden absolute z-50 mt-1 w-full rounded-xl border border-gray-200 bg-white shadow-lg';
      menu.setAttribute('role','listbox');
      wrap.appendChild(menu);

      const searchBox = document.createElement('div');
      searchBox.className = 'p-2 border-b border-gray-100';
      searchBox.innerHTML = '<input type="search" placeholder="Pesquisar…" class="w-full h-9 px-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40"/>';
      menu.appendChild(searchBox);
      const searchInput = searchBox.querySelector('input');

      const list = document.createElement('ul');
      list.className = 'max-h-64 overflow-auto py-1';
      menu.appendChild(list);

      function renderOptions(filter=''){
        list.innerHTML = '';
        const term = filter.trim().toLowerCase();
        [...select.options].forEach(opt => {
          const li = document.createElement('li');
          const btnOpt = document.createElement('button');
          btnOpt.type = 'button';
          btnOpt.className = 'w-full text-left px-3 py-2 text-sm hover:bg-gray-50 flex items-center justify-between';
          btnOpt.dataset.value = opt.value;
          if (opt.value === '') btnOpt.classList.add('text-gray-500');
          const match = opt.textContent.toLowerCase().includes(term);
          if (term && !match) { li.style.display = 'none'; }
          btnOpt.innerHTML = `<span class="truncate">${opt.textContent}</span>${opt.selected ? '<svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>' : ''}`;
          btnOpt.addEventListener('click', () => {
            select.value = opt.value;
            [...select.options].forEach(o => (o.selected = (o.value === opt.value)));
            updateLabel(); close();
            select.dispatchEvent(new Event('change',{bubbles:true}));
          });
          li.appendChild(btnOpt);
          list.appendChild(li);
        });
      }

      function updateLabel(){
        const opt = select.options[select.selectedIndex];
        btn.textContent = opt?.textContent || (select.options[0]?.textContent ?? '—');
        if (!opt || opt.value === '') btn.classList.add('text-gray-500'); else btn.classList.remove('text-gray-500');
      }

      function open(){
        menu.classList.remove('hidden');
        btn.setAttribute('aria-expanded','true');
        searchInput.value = '';
        renderOptions();
        setTimeout(() => searchInput.focus(), 0);
        document.addEventListener('click', onDocClick);
        document.addEventListener('keydown', onKeydown);
      }
      function close(){
        menu.classList.add('hidden');
        btn.setAttribute('aria-expanded','false');
        document.removeEventListener('click', onDocClick);
        document.removeEventListener('keydown', onKeydown);
      }
      function onDocClick(e){ if (!wrap.contains(e.target)) close(); }
      function onKeydown(e){ if (e.key === 'Escape') { e.preventDefault(); close(); btn.focus(); } }

      btn.addEventListener('click', () => {
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        expanded ? close() : open();
      });
      searchInput.addEventListener('input', () => renderOptions(searchInput.value));

      updateLabel(); renderOptions();
      select.__mg = { refresh(){ renderOptions(); updateLabel(); } };
    }
    document.querySelectorAll('select[data-mg-select]').forEach(build);
    window.__refreshMgSelect = (sel) => { if (sel && sel.__mg) sel.__mg.refresh(); };
  })();

  /* ===== Helpers ===== */
  const fmtISO = (d) => d.toISOString().slice(0,10);
  const addDays = (d, n) => { const x = new Date(d); x.setDate(x.getDate()+n); return x; };
  const monthsPT = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

  /* ===== Pintura dos steps ===== */
  function paintHeadState(li, state) {
    const circle = li.querySelector('.step-circle');
    const ico    = li.querySelector('.step-ico');
    const check  = li.querySelector('.step-check');

    circle.className = 'step-circle grid place-items-center w-12 h-12 rounded-full border shadow-sm transition';
    circle.classList.add('bg-white','border-gray-300','text-gray-600');
    ico.classList.remove('hidden');
    check.classList.add('hidden');

    if (state === 'done') {
      circle.classList.remove('text-gray-600','border-gray-300','bg-white');
      circle.classList.add('bg-emerald-600','border-emerald-600','text-white');
      ico.classList.add('hidden');
      check.classList.remove('hidden');
    } else if (state === 'current') {
      circle.classList.add('ring-2','ring-emerald-400/60','border-emerald-400','text-emerald-700');
    }
  }

  function setHead(idx){
    heads.forEach((h, n) => {
      if (n < idx) paintHeadState(h,'done');
      else if (n === idx) paintHeadState(h,'current');
      else paintHeadState(h,'todo');
    });
  }

  function updateRail() {
    if (!rail || !railFill || !heads.length) return;
    const active   = heads[i] || heads[0];
    const railRect = rail.getBoundingClientRect();
    const liRect   = active.getBoundingClientRect();
    const center   = liRect.left + liRect.width / 2 - railRect.left;
    const width    = Math.max(0, Math.min(railRect.width, center));
    railFill.style.width = width + 'px';
  }

  // devolve o índice máximo que pode ser clicado
  function maxUnlocked(){
    let max = 0;
    if (!pacienteSel.value) return 0; max = 1;
    if (!espSel?.value)     return max; max = 2;
    if (!medicoSel?.value)  return max; max = 3;
    if (!tipoSel?.value)    return max; max = 4;
    if (!(dataEl.value && horaSel.value)) return max; max = 5;
    return 6;
  }
  function updateClickable(){
    const m = maxUnlocked();
    heads.forEach((li, idx) => {
      const clickable = idx <= m;
      li.classList.toggle('cursor-pointer', clickable);
      li.classList.toggle('pointer-events-none', !clickable);
    });
  }

  /* ===== Navegação ===== */
  let i = 0;
  async function show(idx){
    i = idx;
    steps.forEach((s, n) => s.classList.toggle('hidden', n !== i));
    setHead(i);
    updateRail();
    updateClickable();

    back.style.visibility = i === 0 ? 'hidden' : 'visible';
    back.disabled = i === 0;
    next.classList.toggle('hidden', i === steps.length - 1);
    submit.classList.toggle('hidden', i !== steps.length - 1);

    if (i === 4) {
      await ensureMinDateFromTipo(true);
      renderCalendar();
      await loadSlots();
    }
    if (i === steps.length - 1) fillReview();
  }
  function valid(idx){
    switch (idx) {
      case 0: return !!pacienteSel?.value;
      case 1: return !!espSel?.value;
      case 2: return !!medicoSel?.value;
      case 3: return !!tipoSel?.value;
      case 4: return !!dataEl?.value && !!horaSel?.value;
      default: return true;
    }
  }
  function nudge(){ const s = steps[i]; s.classList.add('animate-pulse'); setTimeout(() => s.classList.remove('animate-pulse'), 200); }

  back?.addEventListener('click', () => show(Math.max(0, i - 1)));
  next?.addEventListener('click', () => { if (!valid(i)) return nudge(); show(Math.min(steps.length - 1, i + 1)); });

  heads.forEach((li, idx) => {
    li.addEventListener('click', () => {
      if (idx <= maxUnlocked()) show(idx);
      else nudge();
    });
  });
  window.addEventListener('resize', updateRail);

  // Bloqueia a UI do seletor de médico (sem desabilitar o <select>, para enviar no POST)
  (function lockDoctorSelect(){
    if (!medicoSel) return;
    const btn = medicoSel.parentElement?.querySelector('.mg-sel-btn');
    if (btn) { btn.setAttribute('disabled','disabled'); btn.classList.add('opacity-60','cursor-not-allowed'); }
  })();

  /* ===== Tipo/meta & datas ===== */
  async function fetchTipoMeta(slug){
    if (!slug) return null;
    try {
      const r = await fetch(`/api/consulta-tipos/${encodeURIComponent(slug)}`, { headers: { 'Accept':'application/json' }});
      if (!r.ok) return null;
      return await r.json();
    } catch { return null; }
  }
  async function firstDayWithSlots(fromISO){
    let probe = fromISO;
    for (let k=0; k<60; k++){
      if (await hasSlots(probe)) return probe;
      probe = fmtISO(addDays(new Date(probe), 1));
    }
    return fromISO;
  }
  async function hasSlots(dateISO){
    if (!medicoSel?.value || !tipoSel?.value) return false;
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
  async function ensureMinDateFromTipo(autoPreencher = false){
    const todayISO = fmtISO(new Date());
    let minISO = todayISO;

    const slug = tipoSel?.value;
    if (slug) {
      const meta = await fetchTipoMeta(slug);
      const leadMin = Number(meta?.lead_minutos ?? 0);
      const minStart = new Date(Date.now() + leadMin * 60 * 1000);
      minISO = fmtISO(minStart);
    }
    if (medicoSel?.value && tipoSel?.value) minISO = await firstDayWithSlots(minISO);

    dataEl.dataset.min = minISO;
    const needsSet = !dataEl.value || dataEl.value < minISO;
    if (autoPreencher && needsSet) dataEl.value = minISO;

    const m = new Date(currentMonth);
    const min = new Date(minISO);
    if (m.getFullYear() < min.getFullYear() || (m.getFullYear() === min.getFullYear() && m.getMonth() < min.getMonth())) {
      currentMonth = new Date(min.getFullYear(), min.getMonth(), 1);
    }
  }

  /* ===== Slots ===== */
  async function loadSlots(){
    if (!medicoSel?.value || !dataEl.value || !tipoSel?.value) {
      setHourOptions([], '— seleciona médico, tipo e data —');
      setSlotChips([]); slotsMsg.textContent = 'Seleciona médico, tipo e data';
      return;
    }
    setHourOptions([], '— a carregar… —');
    setSlotChips([], true); slotsMsg.textContent = 'A carregar disponibilidade…';

    const iso = dataEl.value;
    try {
      const url = new URL(endpointSlots, window.location.origin);
      url.searchParams.set('medico_id', medicoSel.value);
      url.searchParams.set('data', iso);
      url.searchParams.set('tipo', tipoSel.value);
      url.searchParams.set('duracao', 30);
      const res = await fetch(url, { headers: { 'Accept':'application/json' }});
      const j = await res.json();
      const arr = Array.isArray(j?.data) ? j.data : (Array.isArray(j) ? j : []);
      setHourOptions(arr); setSlotChips(arr);
      slotsMsg.textContent = arr.length ? `${arr.length} horários disponíveis` : 'Sem disponibilidade para a data escolhida';
    } catch (e) {
      console.error(e);
      setHourOptions([], 'Erro ao carregar slots'); setSlotChips([]);
      slotsMsg.textContent = 'Erro ao carregar slots';
    }
  }
  function setHourOptions(slots, placeholder='Sem disponibilidade'){
    horaSel.innerHTML = '';
    if (!Array.isArray(slots) || !slots.length) {
      const opt = document.createElement('option'); opt.value=''; opt.textContent=placeholder; horaSel.appendChild(opt); return;
    }
    slots.forEach(h => { const opt = document.createElement('option'); opt.value=h; opt.textContent=h; horaSel.appendChild(opt); });
  }
  function setSlotChips(slots, loading = false){
    const chipsBox = document.getElementById('slotChips');
    chipsBox.innerHTML = '';
    if (loading) { chipsBox.innerHTML = '<div class="text-sm text-gray-500">A carregar…</div>'; return; }
    if (!Array.isArray(slots) || !slots.length) { chipsBox.innerHTML = '<div class="text-sm text-gray-500">Sem disponibilidade</div>'; return; }

    const wrap = document.createElement('div'); wrap.className = 'flex flex-wrap gap-2';
    slots.forEach(h => {
      const b = document.createElement('button');
      b.type = 'button'; b.textContent = h; b.dataset.slot = h;
      b.className = 'slot-chip px-3 py-1.5 text-sm rounded-xl border border-gray-200 bg-white hover:bg-gray-50 shadow-sm';
      if (horaSel.value === h) b.classList.add('ring-2','ring-emerald-500','bg-emerald-50','border-emerald-200');
      b.addEventListener('click', () => {
        horaSel.value = h;
        wrap.querySelectorAll('.slot-chip').forEach(x => x.classList.remove('ring-2','ring-emerald-500','bg-emerald-50','border-emerald-200'));
        b.classList.add('ring-2','ring-emerald-500','bg-emerald-50','border-emerald-200');
      });
      wrap.appendChild(b);
    });
    chipsBox.appendChild(wrap);
  }

  /* ===== Revisão ===== */
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
  document.querySelectorAll('[data-goto-step]').forEach(btn => btn.addEventListener('click', () => show(Number(btn.dataset.gotoStep || 0))));

  /* ===== Calendário ===== */
  let currentMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
  function startOfGrid(d){ const first = new Date(d.getFullYear(), d.getMonth(), 1); const day = first.getDay(); return addDays(first, -day); }
  function renderCalendar(){
    const calWrap  = document.getElementById('date-cal');
    const calTitle = document.getElementById('calTitle');
    const calGrid  = document.getElementById('calGrid');
    const calPrev  = document.getElementById('calPrev');
    const calNext  = document.getElementById('calNext');
    if (!calWrap) return;

    const minISO = dataEl.dataset.min || fmtISO(new Date());
    const minD = new Date(minISO);

    calTitle.textContent = `${monthsPT[currentMonth.getMonth()]} ${currentMonth.getFullYear()}`;

    const prevMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth()-1, 1);
    const canPrev = prevMonth >= new Date(minD.getFullYear(), minD.getMonth(), 1);
    calPrev.classList.toggle('opacity-40', !canPrev); calPrev.disabled = !canPrev;

    calGrid.innerHTML = '';
    const start = startOfGrid(currentMonth);
    const selectedISO = dataEl.value || '';
    for (let i=0;i<42;i++){
      const date = addDays(start, i);
      const iso  = fmtISO(date);
      const inMonth = date.getMonth() === currentMonth.getMonth();
      const disabled = date < new Date(minISO);

      const btn = document.createElement('button');
      btn.type='button'; btn.dataset.date=iso;
      btn.className = 'w-full aspect-square rounded-lg text-sm flex items-center justify-center border ' +
        (disabled ? 'text-gray-300 border-gray-100 cursor-not-allowed bg-gray-50' :
          (iso === selectedISO ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-500 text-emerald-800' :
            (inMonth ? 'border-gray-200 hover:bg-gray-50' : 'border-gray-100 text-gray-400 hover:bg-gray-50')));
      btn.textContent = String(date.getDate());

      if (!disabled) btn.addEventListener('click', async () => { dataEl.value = iso; renderCalendar(); await loadSlots(); updateClickable(); });
      calGrid.appendChild(btn);
    }
  }
  document.getElementById('calPrev')?.addEventListener('click', () => { currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth()-1, 1); renderCalendar(); });
  document.getElementById('calNext')?.addEventListener('click', () => { currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth()+1, 1); renderCalendar(); });

  /* ===== UX descrição ===== */
  function updateMotivoCounter(){
    if (!motivoEl || !motivoCount) return;
    const max = Number(motivoEl.getAttribute('maxlength') || 400);
    const len = (motivoEl.value || '').length;
    motivoCount.textContent = `${len}/${max}`;
    motivoCount.classList.toggle('text-rose-600', len >= max - 20 && len > 0);
    motivoCount.classList.toggle('text-gray-400', !(len >= max - 20 && len > 0));
  }
  function autoSize(el){
    if (!el) return;
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 56*4) + 'px';
  }
  if (motivoEl){
    updateMotivoCounter(); autoSize(motivoEl);
    motivoEl.addEventListener('input', () => { updateMotivoCounter(); autoSize(motivoEl); });
  }
  motivoClear?.addEventListener('click', () => { if (!motivoEl) return; motivoEl.value=''; updateMotivoCounter(); autoSize(motivoEl); motivoEl.focus(); });

  /* ===== Atualizações que alteram “maxUnlocked” ===== */
  [pacienteSel, espSel, medicoSel, tipoSel, horaSel].forEach(el => el?.addEventListener('change', updateClickable));

  /* ===== Arranque ===== */
  const todayISO = fmtISO(new Date());
  dataEl.dataset.min = todayISO;
  if (!dataEl.value) dataEl.value = todayISO;

  renderCalendar();
  show(0);
});
