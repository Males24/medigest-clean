// Intervalo: aplica automaticamente ao selecionar um preset
// ou quando as duas datas personalizadas estão preenchidas.

document.addEventListener('DOMContentLoaded', () => {
  const form       = document.getElementById('filtros');
  const btnRange   = document.getElementById('btnRange');
  const menuRange  = document.getElementById('menuRange');
  const rangeLabel = document.getElementById('rangeLabel');
  const fDe        = document.getElementById('f_de');
  const fAte       = document.getElementById('f_ate');
  const customDe   = document.getElementById('customDe');
  const customAte  = document.getElementById('customAte');

  function iso(d){ return d.toISOString().slice(0,10); }
  function addDays(date,n){ const x=new Date(date); x.setDate(x.getDate()+n); return x; }
  function startOfMonth(date){ return new Date(date.getFullYear(), date.getMonth(), 1); }
  function endOfMonth(date){ return new Date(date.getFullYear(), date.getMonth()+1, 0); }

  function updateLabel(){
    if (fDe.value && fAte.value){
      const [y1,m1,d1]=fDe.value.split('-'); const [y2,m2,d2]=fAte.value.split('-');
      rangeLabel.textContent=`${d1}/${m1}/${y1} – ${d2}/${m2}/${y2}`;
    } else { rangeLabel.textContent='Intervalo'; }
  }

  function openMenu(){ menuRange?.classList.remove('hidden'); document.addEventListener('click', onDocClick); }
  function closeMenu(){ menuRange?.classList.add('hidden'); document.removeEventListener('click', onDocClick); }
  function onDocClick(e){ if (!menuRange.contains(e.target) && !btnRange.contains(e.target)) closeMenu(); }

  btnRange?.addEventListener('click', () => {
    menuRange.classList.toggle('hidden');
    if (!menuRange.classList.contains('hidden')) document.addEventListener('click', onDocClick);
  });

  // presets
  menuRange?.querySelectorAll('[data-range]').forEach(b=>{
    b.addEventListener('click', ()=>{
      const now=new Date();
      const today=new Date(now.getFullYear(),now.getMonth(),now.getDate());
      const k=b.dataset.range;
      if(k==='today'){ fDe.value=iso(today); fAte.value=iso(today); }
      else if(k==='last7'){ fDe.value=iso(addDays(today,-6)); fAte.value=iso(today); }
      else if(k==='last30'){ fDe.value=iso(addDays(today,-29)); fAte.value=iso(today); }
      else if(k==='thisMonth'){ fDe.value=iso(startOfMonth(today)); fAte.value=iso(endOfMonth(today)); }
      else if(k==='next7'){ fDe.value=iso(today); fAte.value=iso(addDays(today,7)); }

      customDe.value=fDe.value||''; customAte.value=fAte.value||'';
      updateLabel(); closeMenu(); form.submit();
    });
  });

  // personalizado (auto-aplica quando os dois valores existem)
  function trySubmitCustom(){
    if (customDe.value && customAte.value){
      fDe.value=customDe.value; fAte.value=customAte.value;
      updateLabel(); closeMenu(); form.submit();
    }
  }
  customDe?.addEventListener('change', trySubmitCustom);
  customAte?.addEventListener('change', trySubmitCustom);

  // inicializa inputs custom com valores vindos do GET
  customDe.value = fDe.value || '';
  customAte.value = fAte.value || '';
  updateLabel();
});
