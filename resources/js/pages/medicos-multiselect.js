// Multiselect (uma única box com chips + input + dropdown)
(function () {
  const base = 'esp'; // deve bater com o $espBase do Blade

  const box    = document.getElementById(base + '-box');
  const input  = document.getElementById(base + '-input');
  const toggle = document.getElementById(base + '-toggle');
  const dd     = document.getElementById(base + '-dd');
  const list   = document.getElementById(base + '-list');
  const chips  = document.getElementById(base + '-control');
  const hidden = document.getElementById(base + '-hidden');

  if (!box || !input || !list) return;

  const selected = new Set(
    [...hidden.querySelectorAll('input[name="especialidades[]"]')].map(i => i.value)
  );

  // esconder já selecionados
  updateListFilter();

  function open()  { dd.classList.remove('hidden'); }
  function close() { dd.classList.add('hidden'); }
  function toggleDD(){ dd.classList.toggle('hidden'); input.focus(); }

  function add(id, text) {
    if (selected.has(id)) return;

    // chip
    const chip = document.createElement('span');
    chip.className = 'esp-chip inline-flex items-center gap-2 rounded-md bg-blue-50 text-blue-700 text-sm px-2.5 py-1';
    chip.dataset.id = id;
    chip.dataset.text = text;
    chip.innerHTML = `${text}<button type="button" class="esp-chip-remove text-blue-700/70 hover:text-blue-900" aria-label="Remover">&times;</button>`;

    // insere o chip antes do input
    chips.insertBefore(chip, input);

    // hidden
    const hid = document.createElement('input');
    hid.type = 'hidden';
    hid.name = 'especialidades[]';
    hid.value = id;
    hid.dataset.id = id;
    hidden.appendChild(hid);

    selected.add(id);
    updateListFilter();
    input.value = '';
    input.focus();
  }

  function remove(id) {
    // chip
    const chip = chips.querySelector(`.esp-chip[data-id="${id}"]`);
    if (chip) chip.remove();
    // hidden
    const hid = hidden.querySelector(`input[name="especialidades[]"][data-id="${id}"]`);
    if (hid) hid.remove();
    // desbloquear item
    selected.delete(id);
    updateListFilter();
  }

  function updateListFilter() {
    const q = (input.value || '').trim().toLowerCase();
    [...list.querySelectorAll('.esp-item')].forEach(li => {
      const inSelected = selected.has(li.dataset.id);
      const match = li.dataset.text.toLowerCase().includes(q);
      li.classList.toggle('hidden', inSelected || !match);
    });
  }

  // Filtering
  input.addEventListener('input', () => { updateListFilter(); open(); });
  input.addEventListener('focus', open);

  // Enter adiciona primeiro visível
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      const first = [...list.querySelectorAll('.esp-item:not(.hidden)')][0];
      if (first) add(first.dataset.id, first.dataset.text);
    } else if (e.key === 'Backspace' && input.value === '') {
      // backspace com input vazio remove último chip
      const lastChip = [...chips.querySelectorAll('.esp-chip')].pop();
      if (lastChip) remove(lastChip.dataset.id);
    }
  });

  // Dropdown interactions
  toggle.addEventListener('click', toggleDD);
  document.addEventListener('click', (e) => {
    if (!box.contains(e.target)) close();
  });

  list.addEventListener('click', (e) => {
    const item = e.target.closest('.esp-item');
    if (!item) return;
    add(item.dataset.id, item.dataset.text);
  });

  chips.addEventListener('click', (e) => {
    const btn = e.target.closest('.esp-chip-remove');
    if (!btn) return;
    const chip = btn.closest('.esp-chip');
    remove(chip.dataset.id);
  });
})();
