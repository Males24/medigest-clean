// resources/js/pages/dashboard-admin.js
document.addEventListener('DOMContentLoaded', () => {
  const el1 = document.querySelector('#chart-by-day');
  const el2 = document.querySelector('#chart-status');
  const el3 = document.querySelector('#chart-esps');
  if (!el1 || !el2 || !el3) return;

  const startEl = document.getElementById('dashStart');
  const endEl   = document.getElementById('dashEnd');

  // === Cores de marca ===
  const BRAND    = '#00795C'; // principal
  const BRAND_LT = '#00A37A'; // apoio (donut/agendada)

  // Paleta por estado para o donut
  const STATUS_COLORS = {
    confirmada:      BRAND,
    confirmado:      BRAND,
    agendada:        BRAND_LT,
    pendente:        '#F59E0B',
    pendente_medico: '#A855F7',
    cancelada:       '#EF4444',
    cancelado:       '#EF4444',
    default:         '#6B7280',
  };

  // Helpers de breakpoint (alinhados ao Tailwind)
  const mq = {
    sm: window.matchMedia('(min-width: 640px)'),
    md: window.matchMedia('(min-width: 768px)'),
    lg: window.matchMedia('(min-width: 1024px)'),
    xl: window.matchMedia('(min-width: 1280px)'),
  };

  // Período por defeito: -30 a +30 dias (não sobrepõe se já vier preenchido)
  const today   = new Date();
  const minus30 = new Date(today); minus30.setDate(today.getDate() - 30);
  const plus30  = new Date(today); plus30.setDate(today.getDate() + 30);
  if (!startEl.value) startEl.value = minus30.toISOString().slice(0,10);
  if (!endEl.value)   endEl.value   = plus30.toISOString().slice(0,10);

  let c1, c2, c3;                // instâncias ApexCharts
  let catsByDay = [], catsEsp = []; // guardamos as categorias para reaplicar no resize

  // Opções responsivas
  function responsiveHeights() {
    const area  = mq.xl.matches ? 380 : mq.lg.matches ? 340 : mq.sm.matches ? 300 : 260;
    const donut = mq.xl.matches ? 340 : mq.lg.matches ? 320 : mq.sm.matches ? 300 : 260;
    const bars  = mq.xl.matches ? 420 : mq.lg.matches ? 380 : mq.sm.matches ? 340 : 300;
    return { area, donut, bars };
  }

  function xLabelOptions() {
    return mq.sm.matches
      ? { rotate: 0,  trim: true, maxHeight: 120 }
      : { rotate: -45, trim: true, maxHeight: 80 };
  }
  const donutLegendPos = () => (mq.sm.matches ? 'bottom' : 'top');

  const noData = { text: 'Sem dados no período', align: 'center', verticalAlign: 'middle' };
  const yInt   = { labels: { formatter: (v) => Number.isFinite(v) ? Math.round(v).toString() : '' }, min: 0, forceNiceScale: true };
  const tooltipInt   = { y: { formatter: (v) => Math.round(v) } };
  const dataLabelInt = { enabled: false, formatter: (v) => Math.round(v) };

  async function load() {
    try {
      const url = new URL(`${location.origin}/admin/dashboard/charts`);
      url.searchParams.set('start', startEl.value);
      url.searchParams.set('end',   endEl.value);

      const res = await fetch(url, { headers: { 'Accept':'application/json' }});
      const data = await res.json();

      // Guardar categorias (como strings) para não as perdermos no updateOptions
      catsByDay = (data.byDay?.labels || []).map(String);
      catsEsp   = (data.byEspecialidade?.labels || []).map(String);

      const seriesByDay = (data.byDay?.series || []).map(n => Number(n) || 0);
      const seriesEsp   = (data.byEspecialidade?.series || []).map(n => Number(n) || 0);

      const { area, donut, bars } = responsiveHeights();

      // --- Área: consultas por dia ---
      const opt1 = {
        chart: { type:'area', height: area, toolbar:{show:false}, animations:{speed: 250} },
        noData,
        colors: [BRAND],
        series: [{ name:'Consultas', data: seriesByDay }],
        xaxis: { type: 'category', categories: catsByDay, labels: xLabelOptions() }, // <- força categoria
        yaxis: yInt,
        dataLabels: dataLabelInt,
        stroke: { curve:'smooth', width:2, colors:[BRAND] },
        fill: { type:'gradient', gradient: { shadeIntensity: 0, opacityFrom: .30, opacityTo: .06, stops:[0,100] }, colors: [BRAND] },
        markers: { size: 0, colors:[BRAND], strokeColors:[BRAND] },
        tooltip: tooltipInt,
        grid: { padding: { left: 4, right: 8 } }
      };

      // --- Donut: estados ---
      const donutColors = (data.status?.labels || []).map(
        s => STATUS_COLORS[(s || '').toLowerCase()] || STATUS_COLORS.default
      );
      const opt2 = {
        chart: { type:'donut', height: donut, animations:{speed: 250} },
        noData,
        labels: (data.status?.labels || []).map(String),
        series: (data.status?.series || []).map(n => Number(n) || 0),
        colors: donutColors,
        legend: { position: donutLegendPos() },
        dataLabels: { enabled: true, formatter: (val) => `${Math.round(val)}%` },
        tooltip: tooltipInt,
        stroke: { width: 0 }
      };

      // --- Barras: especialidades ---
      const opt3 = {
        chart: { type:'bar', height: bars, toolbar:{show:false}, animations:{speed: 250} },
        noData,
        colors: [BRAND],
        series: [{ name:'Consultas', data: seriesEsp }],
        xaxis: { type: 'category', categories: catsEsp, labels: xLabelOptions() }, // <- força categoria
        yaxis: yInt,
        plotOptions: { bar: { borderRadius: 8, columnWidth: mq.md.matches ? '45%' : '55%' } },
        dataLabels: dataLabelInt,
        tooltip: tooltipInt,
        grid: { padding: { left: 4, right: 8 } }
      };

      // (Re)criar gráficos
      c1?.destroy(); c2?.destroy(); c3?.destroy();
      c1 = new ApexCharts(el1, opt1); c1.render();
      c2 = new ApexCharts(el2, opt2); c2.render();
      c3 = new ApexCharts(el3, opt3); c3.render();
    } catch (e) {
      console.error('Erro ao carregar gráficos do dashboard:', e);
    }
  }

  // === Atualização responsiva ===
  let resizeTimer = null;
  function scheduleResize() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      if (!c1 || !c2 || !c3) return;
      const { area, donut, bars } = responsiveHeights();
      // Reaplicar categorias SEMPRE (algumas versões do Apex limpam-nas)
      c1.updateOptions({
        chart: { height: area },
        xaxis: { type: 'category', categories: catsByDay, labels: xLabelOptions() }
      }, false, true);
      c2.updateOptions({
        chart: { height: donut },
        legend: { position: donutLegendPos() }
      }, false, true);
      c3.updateOptions({
        chart: { height: bars },
        xaxis: { type: 'category', categories: catsEsp, labels: xLabelOptions() },
        plotOptions: { bar: { columnWidth: mq.md.matches ? '45%' : '55%' } }
      }, false, true);
    }, 120);
  }

  window.addEventListener('resize', scheduleResize);
  Object.values(mq).forEach(m => m.addEventListener('change', scheduleResize));

  const sidebarToggle = document.getElementById('sidebar-toggle');
  if (sidebarToggle) sidebarToggle.addEventListener('click', () => setTimeout(scheduleResize, 220));

  const main = document.getElementById('conteudo-principal');
  if (main && 'ResizeObserver' in window) {
    const ro = new ResizeObserver(() => scheduleResize());
    ro.observe(main);
  }

  // Filtros
  startEl.addEventListener('change', load);
  endEl.addEventListener('change', load);

  // Primeira carga
  load();
});
