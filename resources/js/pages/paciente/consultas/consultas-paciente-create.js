// resources/js/pages/paciente/consultas/consultas-paciente-create.js
document.addEventListener('DOMContentLoaded', () => {
  const mount = document.querySelector('[data-page="wizard-consulta-paciente"]');
  if (!mount) return;

  const endpointSlots = document.querySelector('meta[name="api-slots"]')?.content || '/api/slots';
  const medicosTmpl   = document.querySelector('meta[name="api-medicos-template"]')?.content || '/api/especialidades/{id}/medicos';

  const heads    = [...document.querySelectorAll('.step-head')];
  const steps    = [...document.querySelectorAll('.wizard-step')];
  const rail     = document.getElementById('wizard-rail');
  const railBase = document.getElementById('wizard-rail-base');
  const railFill = document.getElementById('wizard-rail-fill');

  const back   = document.getElementById('btnBack');
  const next   = document.getElementById('btnNext');
  const submit = document.getElementById('btnSubmit');

  const espSel    = document.getElementById('especialidade_id');
  const medicoSel = document.getElementById('medico_id');
  const tipoSel   = document.getElementById('tipo_slug');
  const dataEl    = document.getElementById('data');
  const horaSel   = document.getElementById('hora');
  const descEl    = document.getElementById('descricao');
  const slotsMsg  = document.getElementById('slotsMsg');

  // ===== Selects bonitos =====
  (function enhanceSelects () {
    function build (select) {
      if (select.__mg) return;
      select.classList.add('sr-only','absolute','opacity-0','-z-10');
      const wrap = document.createElement('div'); wrap.className = 'relative w-full'; select.parentNode.insertBefore(wrap, select); wrap.appendChild(select);
      const btn  = document.createElement('button'); btn.type='button'; btn.className='mg-sel-btn w-full h-11 px-3.5 pe-10 text-left text-sm rounded-xl bg-white border border-gray-300 ring-1 ring-transparent hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-home-medigest-button/70 transition'; wrap.appendChild(btn);
      const caret= document.createElement('span'); caret.innerHTML='<svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5.25 7.5l4.5 4.5 4.5-4.5"/></svg>'; caret.className='pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-500'; wrap.appendChild(caret);
      const menu = document.createElement('div'); menu.className='mg-sel-menu hidden absolute z-50 mt-1 w-full rounded-xl border border-gray-200 bg-white shadow-lg'; menu.setAttribute('role','listbox'); wrap.appendChild(menu);
      const searchBox=document.createElement('div'); searchBox.className='p-2 border-b border-gray-100'; searchBox.innerHTML='<input type="search" placeholder="Pesquisar…" class="w-full h-9 px-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40"/>'; menu.appendChild(searchBox);
      const searchInput=searchBox.querySelector('input');
      const list=document.createElement('ul'); list.className='max-h-64 overflow-auto py-1'; menu.appendChild(list);

      function renderOptions (f = '') {
        list.innerHTML=''; const term=f.trim().toLowerCase();
        [...select.options].forEach(opt => {
          const li=document.createElement('li');
          const b=document.createElement('button'); b.type='button';
          b.className='w-full text-left px-3 py-2 text-sm hover:bg-gray-50 flex items-center justify-between';
          b.dataset.value=opt.value; if(opt.value==='') b.classList.add('text-gray-500');
          if(term && !opt.textContent.toLowerCase().includes(term)) li.style.display='none';
          b.innerHTML=`<span class="truncate">${opt.textContent}</span>${opt.selected?'<svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>':''}`;
          b.addEventListener('click', () => {
            select.value = opt.value;
            [...select.options].forEach(o => o.selected = (o.value === opt.value));
            updateLabel(); close(); select.dispatchEvent(new Event('change', { bubbles:true }));
          });
          li.appendChild(b); list.appendChild(li);
        });
      }
      function updateLabel(){ const opt=select.options[select.selectedIndex]; btn.textContent=opt?.textContent || (select.options[0]?.textContent ?? '—'); btn.classList.toggle('text-gray-500', !opt || opt.value===''); }
      function open(){ menu.classList.remove('hidden'); btn.setAttribute('aria-expanded','true'); searchInput.value=''; renderOptions(); setTimeout(()=>searchInput.focus(),0); document.addEventListener('click',onDoc); document.addEventListener('keydown',onKey); }
      function close(){ menu.classList.add('hidden'); btn.setAttribute('aria-expanded','false'); document.removeEventListener('click',onDoc); document.removeEventListener('keydown',onKey); }
      function onDoc(e){ if(!wrap.contains(e.target)) close(); }
      function onKey(e){ if(e.key==='Escape'){ e.preventDefault(); close(); btn.focus(); } }

      btn.addEventListener('click', () => (btn.getAttribute('aria-expanded')==='true'?close():open()));
      searchInput.addEventListener('input', () => renderOptions(searchInput.value));
      updateLabel(); renderOptions(); select.__mg = { refresh(){ renderOptions(); updateLabel(); } };
    }
    document.querySelectorAll('select[data-mg-select]').forEach(build);
    window.__refreshMgSelect = sel => { if(sel?.__mg) sel.__mg.refresh(); };
  })();

  // ===== Helpers =====
  const fmtISO  = d => d.toISOString().slice(0,10);
  const addDays = (d,n) => { const x=new Date(d); x.setDate(x.getDate()+n); return x; };
  const monthsPT = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

  // ===== Stepper / Rail =====
  function paintHeadState(li, state){
    const circle=li.querySelector('.step-circle'), ico=li.querySelector('.step-ico'), check=li.querySelector('.step-check');
    circle.className='step-circle grid place-items-center w-12 h-12 rounded-full border shadow-sm transition bg-white border-gray-300 text-gray-600';
    ico.classList.remove('hidden'); check.classList.add('hidden');
    if (state==='done'){ circle.classList.replace('bg-white','bg-emerald-600'); circle.classList.replace('border-gray-300','border-emerald-600'); circle.classList.replace('text-gray-600','text-white'); ico.classList.add('hidden'); check.classList.remove('hidden'); }
    if (state==='current'){ circle.classList.add('ring-2','ring-emerald-400/60','border-emerald-400','text-emerald-700'); }
  }
  function setHead(idx){ heads.forEach((h,n)=>paintHeadState(h, n<idx?'done':n===idx?'current':'todo')); }

  // >>> Alinhamento perfeito do trilho com os círculos
  function updateRail(){
    if (!rail || !railFill || !railBase || !heads.length) return;

    const railRect = rail.getBoundingClientRect();
    const circles  = heads.map(li => li.querySelector('.step-circle'));
    const pts = circles.map(c => {
      const r = c.getBoundingClientRect();
      return { x: r.left + r.width/2 - railRect.left, y: r.top + r.height/2 - railRect.top };
    });

    const first = pts[0], last = pts[pts.length - 1];
    if (!first || !last) return;

    // posiciona verticalmente pelo centro do 1.º círculo
    railBase.style.top = first.y + 'px';
    railFill.style.top = first.y + 'px';

    // base: do 1.º ao último círculo
    railBase.style.left  = first.x + 'px';
    railBase.style.width = Math.max(0, last.x - first.x) + 'px';

    // preenchido: do 1.º até ao passo ativo
    const active = pts[i] ?? first;
    const fillW  = Math.max(0, Math.min(active.x - first.x, last.x - first.x));
    railFill.style.left  = first.x + 'px';
    railFill.style.width = fillW + 'px';
  }

  function maxUnlocked(){
    if(!espSel?.value) return 0;
    if(!medicoSel?.value) return 1;
    if(!tipoSel?.value) return 2;
    if(!(dataEl?.value && horaSel?.value)) return 3;
    return 5;
  }
  function updateClickable(){ const m=maxUnlocked(); heads.forEach((li,idx)=>{ const c=idx<=m; li.classList.toggle('cursor-pointer',c); li.classList.toggle('pointer-events-none',!c); }); }

  // ===== Navegação =====
  let i=0;
  async function show(idx){
    i=idx;
    steps.forEach((s,n)=>s.classList.toggle('hidden', n!==i));
    setHead(i);
    // mede depois do layout estabilizar
    requestAnimationFrame(updateRail);
    updateClickable();

    back.style.visibility = i===0 ? 'hidden' : 'visible';
    back.disabled = i===0;
    next.classList.toggle('hidden', i===steps.length-1);
    submit.classList.toggle('hidden', i!==steps.length-1);

    if (i===3){ await ensureMinDateFromTipo(true); renderCalendar(); await loadSlots(); }
    if (i===5)  fillReview();
  }
  function valid(idx){ switch(idx){ case 0: return !!espSel.value; case 1: return !!medicoSel.value; case 2: return !!tipoSel.value; case 3: return !!dataEl.value && !!horaSel.value; default: return true; } }
  function nudge(){ const s=steps[i]; s.classList.add('animate-pulse'); setTimeout(()=>s.classList.remove('animate-pulse'),200); }

  back?.addEventListener('click', () => show(Math.max(0, i-1)));
  next?.addEventListener('click', () => { if(!valid(i)) return nudge(); show(Math.min(steps.length-1, i+1)); });
  heads.forEach((li,idx)=>li.addEventListener('click',()=>{ if(idx<=maxUnlocked()) show(idx); else nudge(); }));
  window.addEventListener('resize', updateRail);
  window.addEventListener('load', updateRail);

  // ===== Médicos por especialidade
  async function loadMedicos(){
    horaSel.innerHTML = '<option value="">—</option>';
    setSlotChips([]); if (slotsMsg) slotsMsg.textContent='Seleciona os campos';
    const id = espSel.value;
    if(!id){
      medicoSel.innerHTML='<option value="">Sem médicos</option>'; medicoSel.disabled=true; window.__refreshMgSelect?.(medicoSel); updateClickable(); return;
    }
    medicoSel.innerHTML='<option value="">— a carregar… —</option>'; medicoSel.disabled=true; window.__refreshMgSelect?.(medicoSel);
    try{
      const url = medicosTmpl.replace('{id}', encodeURIComponent(id));
      const r   = await fetch(url, { headers:{ 'Accept':'application/json' }});
      const list= await r.json();
      medicoSel.innerHTML='';
      if(!Array.isArray(list) || !list.length){
        medicoSel.innerHTML='<option value="">Sem médicos</option>'; medicoSel.disabled=true; window.__refreshMgSelect?.(medicoSel); updateClickable(); return;
      }
      const ph=document.createElement('option'); ph.value=''; ph.textContent='— Selecionar —'; medicoSel.appendChild(ph);
      list.forEach(m=>{ const o=document.createElement('option'); o.value=m.id; o.textContent=`${m.name}${m.email?' — '+m.email:''}`; medicoSel.appendChild(o); });
      medicoSel.disabled=false; window.__refreshMgSelect?.(medicoSel);
    }catch{
      medicoSel.innerHTML='<option value="">Erro ao carregar</option>'; medicoSel.disabled=true; window.__refreshMgSelect?.(medicoSel);
    }finally{ updateClickable(); }
  }
  espSel?.addEventListener('change', loadMedicos);

  // Quando mudar médico ou tipo → recalcular mínima e slots
  async function onDoctorOrType(){
    await ensureMinDateFromTipo(true);
    renderCalendar();
    await loadSlots();
    updateClickable();
  }
  medicoSel?.addEventListener('change', onDoctorOrType);
  tipoSel?.addEventListener('change', onDoctorOrType);

  // ===== Tipo/meta & datas
  async function fetchTipoMeta(slug){
    if (!slug) return null;
    try{ const r=await fetch(`/api/consulta-tipos/${encodeURIComponent(slug)}`, { headers:{'Accept':'application/json'}}); if(!r.ok) return null; return await r.json(); }catch{ return null; }
  }
  async function hasSlots(dateISO){
    if(!medicoSel.value || !tipoSel.value) return false;
    try{
      const url = new URL(endpointSlots, window.location.origin);
      url.searchParams.set('medico_id', medicoSel.value);
      url.searchParams.set('data', dateISO);
      url.searchParams.set('tipo', tipoSel.value);
      url.searchParams.set('duracao', Number(document.getElementById('duracao')?.value || 30));
      const r = await fetch(url, { headers:{ 'Accept':'application/json' }});
      const j = await r.json();
      const arr = Array.isArray(j?.data) ? j.data : (Array.isArray(j) ? j : []);
      return arr.length>0;
    }catch{ return false; }
  }
  async function firstDayWithSlots(fromISO){
    let probe = fromISO;
    for(let k=0;k<60;k++){ if(await hasSlots(probe)) return probe; probe = fmtISO(addDays(new Date(probe),1)); }
    return fromISO;
  }
  async function ensureMinDateFromTipo(auto=false){
    const todayISO = fmtISO(new Date());
    let minISO = todayISO;
    const slug = tipoSel.value;
    if (slug){
      const meta=await fetchTipoMeta(slug);
      const lead=Number(meta?.lead_minutos ?? 0);
      const minStart=new Date(Date.now() + lead*60*1000);
      minISO = fmtISO(minStart);
    }
    if (medicoSel.value && tipoSel.value) minISO = await firstDayWithSlots(minISO);
    dataEl.dataset.min = minISO;
    const needsSet = !dataEl.value || dataEl.value < minISO;
    if (auto && needsSet) dataEl.value = minISO;

    const m = new Date(currentMonth), min=new Date(minISO);
    if (m.getFullYear()<min.getFullYear() || (m.getFullYear()===min.getFullYear() && m.getMonth()<min.getMonth())){
      currentMonth = new Date(min.getFullYear(), min.getMonth(), 1);
    }
  }

  // ===== Slots
  async function loadSlots(){
    horaSel.innerHTML = '<option value="">—</option>';
    if(!(medicoSel.value && tipoSel.value && dataEl.value)){
      setSlotChips([]); if (slotsMsg) slotsMsg.textContent='Seleciona médico, tipo e data'; return;
    }
    setSlotChips([], true); if (slotsMsg) slotsMsg.textContent='A carregar disponibilidade…';
    try{
      const url = new URL(endpointSlots, window.location.origin);
      url.searchParams.set('medico_id', medicoSel.value);
      url.searchParams.set('data', dataEl.value);
      url.searchParams.set('tipo', tipoSel.value);
      url.searchParams.set('duracao', Number(document.getElementById('duracao')?.value || 30));
      const res = await fetch(url, { headers:{ 'Accept':'application/json' }});
      const j = await res.json();
      const arr = Array.isArray(j?.data) ? j.data : (Array.isArray(j) ? j : []);
      setSlotChips(arr);
      slotsMsg && (slotsMsg.textContent = arr.length ? `${arr.length} horários disponíveis` : 'Sem disponibilidade para a data escolhida');
    }catch{
      setSlotChips([]); slotsMsg && (slotsMsg.textContent='Erro ao carregar slots');
    }
  }
  function setSlotChips(slots, loading=false){
    const chipsBox = document.getElementById('slotChips');
    chipsBox.innerHTML='';
    if (loading){ chipsBox.innerHTML='<div class="text-sm text-gray-500">A carregar…</div>'; return; }
    if (!Array.isArray(slots) || !slots.length){ chipsBox.innerHTML='<div class="text-sm text-gray-500">Sem disponibilidade</div>'; return; }

    const wrap=document.createElement('div'); wrap.className='flex flex-wrap gap-2';
    slots.forEach(h=>{
      const b=document.createElement('button'); b.type='button'; b.textContent=h;
      b.className='slot-chip px-3 py-1.5 text-sm rounded-xl border border-gray-200 bg-white hover:bg-gray-50 shadow-sm';
      if (horaSel.value===h) b.classList.add('ring-2','ring-emerald-500','bg-emerald-50','border-emerald-200');
      b.addEventListener('click',()=>{
        horaSel.value=h;
        wrap.querySelectorAll('button').forEach(x=>x.classList.remove('ring-2','ring-emerald-500','bg-emerald-50','border-emerald-200'));
        b.classList.add('ring-2','ring-emerald-500','bg-emerald-50','border-emerald-200');
        updateClickable();                       // desbloqueia o último passo
      });
      wrap.appendChild(b);
      const opt=document.createElement('option'); opt.value=h; opt.textContent=h; horaSel.appendChild(opt);
    });
    chipsBox.appendChild(wrap);
  }

  // ===== Calendário
  let currentMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
  function startOfGrid(d){ const first=new Date(d.getFullYear(), d.getMonth(), 1); const day=first.getDay(); return addDays(first,-day); }
  function renderCalendar(){
    const calTitle=document.getElementById('calTitle'), calGrid=document.getElementById('calGrid'), calPrev=document.getElementById('calPrev'), calNext=document.getElementById('calNext');
    if(!calGrid) return;
    const minISO = dataEl.dataset.min || fmtISO(new Date()); const minD=new Date(minISO);
    calTitle.textContent = `${monthsPT[currentMonth.getMonth()]} ${currentMonth.getFullYear()}`;
    const prevMonth=new Date(currentMonth.getFullYear(), currentMonth.getMonth()-1, 1);
    const canPrev = prevMonth >= new Date(minD.getFullYear(), minD.getMonth(), 1);
    calPrev.classList.toggle('opacity-40', !canPrev); calPrev.disabled=!canPrev;

    calGrid.innerHTML=''; const start=startOfGrid(currentMonth); const selectedISO=dataEl.value || '';
    for(let k=0;k<42;k++){
      const date=addDays(start,k); const iso=fmtISO(date);
      const inMonth=date.getMonth()===currentMonth.getMonth(); const disabled=date < new Date(minISO);
      const btn=document.createElement('button'); btn.type='button'; btn.dataset.date=iso;
      btn.className='w-full aspect-square rounded-lg text-sm flex items-center justify-center border ' +
        (disabled ? 'text-gray-300 border-gray-100 cursor-not-allowed bg-gray-50' :
          (iso===selectedISO ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-500 text-emerald-800' :
            (inMonth ? 'border-gray-200 hover:bg-gray-50' : 'border-gray-100 text-gray-400 hover:bg-gray-50')));
      btn.textContent=String(date.getDate());
      if (!disabled) btn.addEventListener('click', async ()=>{
        dataEl.value=iso; renderCalendar(); await loadSlots(); updateClickable();
      });
      calGrid.appendChild(btn);
    }
  }
  document.getElementById('calPrev')?.addEventListener('click',()=>{ currentMonth=new Date(currentMonth.getFullYear(), currentMonth.getMonth()-1, 1); renderCalendar(); });
  document.getElementById('calNext')?.addEventListener('click',()=>{ currentMonth=new Date(currentMonth.getFullYear(), currentMonth.getMonth()+1, 1); renderCalendar(); });

  // ===== Revisão
  function getText(sel){ return sel?.options?.[sel.selectedIndex]?.text ?? ''; }
  function fillReview(){
    mount.querySelector('[data-review="esp"]').textContent  = getText(espSel)   || '—';
    mount.querySelector('[data-review="med"]').textContent  = getText(medicoSel)|| '—';
    mount.querySelector('[data-review="tipo"]').textContent = getText(tipoSel)  || (tipoSel?.value ?? '—');
    mount.querySelector('[data-review="data"]').textContent = dataEl?.value     || '—';
    mount.querySelector('[data-review="hora"]').textContent = horaSel?.value    || '—';
    mount.querySelector('[data-review="desc"]').textContent = descEl?.value     || '—';
  }

  // ===== Arranque
  const todayISO = fmtISO(new Date());
  dataEl.dataset.min = todayISO;
  if (!dataEl.value) dataEl.value = todayISO;

  renderCalendar();
  show(0);
});
