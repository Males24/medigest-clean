// resources/js/layout/dropdownMenu.js
document.addEventListener('DOMContentLoaded', () => {
  const btn  = document.getElementById('user-menu-button');
  const menu = document.getElementById('user-dropdown');

  if (!btn || !menu) return;

  const open  = () => { menu.classList.remove('hidden'); btn.setAttribute('aria-expanded', 'true'); };
  const close = () => { menu.classList.add('hidden');   btn.setAttribute('aria-expanded', 'false'); };
  const toggle = () => (menu.classList.contains('hidden') ? open() : close());

  // Abre/fecha ao clicar no avatar
  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    toggle();
  });

  // Fecha ao clicar fora
  document.addEventListener('click', (e) => {
    if (!menu.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
      close();
    }
  });

  // Fecha com Esc
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
});
