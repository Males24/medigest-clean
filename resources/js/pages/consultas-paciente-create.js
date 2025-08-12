document.addEventListener('DOMContentLoaded', () => {
  const api = document.querySelector('meta[name="api-slots"]')?.content || '/api/slots';
  const medico = document.getElementById('medico_id');
  const tipo = document.getElementById('tipo_slug');
  const dataIn = document.getElementById('data');
  const horaSel = document.getElementById('hora');
  const msg = document.getElementById('slotsMsg');

  async function loadSlots() {
    if (!medico.value || !dataIn.value) return;
    horaSel.innerHTML = '<option value="">— a carregar… —</option>';
    msg.textContent = 'A carregar disponibilidade…';

    const url = new URL(api, window.location.origin);
    url.searchParams.set('medico_id', medico.value);
    url.searchParams.set('data', dataIn.value);
    url.searchParams.set('tipo', tipo.value);
    url.searchParams.set('duracao', 30);

    try {
      const r = await fetch(url, { headers: {'X-Requested-With':'XMLHttpRequest'} });
      const j = await r.json();
      const arr = Array.isArray(j?.data) ? j.data : (Array.isArray(j) ? j : []);
      horaSel.innerHTML = '';
      if (!arr.length) {
        horaSel.innerHTML = '<option value="">— sem disponibilidade —</option>';
        msg.textContent = 'Sem disponibilidade para os critérios escolhidos.';
        return;
      }
      arr.forEach(h => {
        const o = document.createElement('option');
        o.value = o.textContent = h;
        horaSel.appendChild(o);
      });
      msg.textContent = 'Seleciona uma hora.';
    } catch {
      horaSel.innerHTML = '<option value="">— erro ao carregar —</option>';
      msg.textContent = 'Não foi possível carregar os slots.';
    }
  }

  medico.addEventListener('change', loadSlots);
  tipo.addEventListener('change', loadSlots);
  dataIn.addEventListener('change', loadSlots);
});