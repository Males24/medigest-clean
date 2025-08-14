// resources/js/layout/user-menu.js
(function () {
  // Evita inicializar duas vezes (ex.: hot reload, imports múltiplos)
  if (window.__userMenuInited) return;
  window.__userMenuInited = true;

  const init = () => {
    const btn  = document.getElementById('user-menu-button');
    const menu = document.getElementById('user-dropdown');
    if (!btn || !menu) return;

    const getItems = () => Array.from(menu.querySelectorAll('[role="menuitem"]'));

    const open = () => {
      if (!menu.classList.contains('hidden')) return;
      menu.classList.remove('hidden');
      btn.setAttribute('aria-expanded', 'true');
      // Foca o primeiro item do menu, se existir
      const items = getItems();
      items[0]?.focus({ preventScroll: true });
    };

    const close = () => {
      if (menu.classList.contains('hidden')) return;
      menu.classList.add('hidden');
      btn.setAttribute('aria-expanded', 'false');
      // Devolve o foco ao botão
      btn.focus({ preventScroll: true });
    };

    const toggle = () => (menu.classList.contains('hidden') ? open() : close());

    // Abre/fecha ao clicar no botão
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation(); // impede o "click fora" de disparar no bubbling
      toggle();
    });

    // Fecha ao clicar fora
    document.addEventListener('click', (e) => {
      if (
        !menu.classList.contains('hidden') &&
        !menu.contains(e.target) &&
        e.target !== btn &&
        !btn.contains(e.target)
      ) {
        close();
      }
    });

    // Acessibilidade por teclado
    document.addEventListener('keydown', (e) => {
      // Fecha com Esc
      if (e.key === 'Escape') {
        close();
        return;
      }

      // Navegação dentro do menu com setas e Home/End
      if (!menu.classList.contains('hidden')) {
        const items = getItems();
        if (!items.length) return;

        const active = document.activeElement;
        let i = items.indexOf(active);

        if (e.key === 'ArrowDown') {
          e.preventDefault();
          i = (i + 1 + items.length) % items.length;
          items[i].focus();
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          i = (i - 1 + items.length) % items.length;
          items[i].focus();
        } else if (e.key === 'Home') {
          e.preventDefault();
          items[0].focus();
        } else if (e.key === 'End') {
          e.preventDefault();
          items[items.length - 1].focus();
        } else if (e.key === 'Tab') {
          // Se tab sai do menu, fecha
          const atFirst = active === items[0];
          const atLast  = active === items[items.length - 1];
          if ((!e.shiftKey && atLast) || (e.shiftKey && atFirst)) {
            close();
          }
        }
      }
    });

    // Marca relação ARIA
    btn.setAttribute('aria-controls', 'user-dropdown');
    btn.setAttribute('aria-haspopup', 'menu');
    if (!btn.hasAttribute('aria-expanded')) btn.setAttribute('aria-expanded', 'false');
  };

  // Inicia quando o DOM estiver pronto
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})();
