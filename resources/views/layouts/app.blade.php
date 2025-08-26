<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="color-scheme" content="light">
<meta name="theme-color" content="#0f766e"><!-- fallback do verde -->

<title>@yield('title', 'MediGest')</title>

<link rel="icon" type="image/svg+xml" href="{{ asset('/favicon.svg') }}">
<link rel="icon" type="image/png" href="{{ asset('/favicon.png') }}">

@vite([
    'resources/css/app.css',
    'resources/js/app.js',
    'resources/js/pages/auth/auth-modals.js',
])
@stack('head')
</head>

<body class="bg-gray-100 text-gray-900 antialiased min-h-screen flex flex-col">
<a href="#conteudo" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2
                            bg-white text-gray-900 rounded px-3 py-2 shadow">
    Saltar para o conteúdo
</a>

<header id="site-header" class="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sticky top-0 z-50" role="banner">
  <div class="max-w-[1430px] mx-auto flex items-center gap-3 justify-between">
    <a href="{{ route('home') }}" class="inline-flex items-center shrink-0" aria-label="MediGest+">
      <img src="{{ asset('/Logo_Preto.svg') }}"
           alt="MediGest+"
           class="h-8 sm:h-11 w-auto select-none"
           loading="eager" fetchpriority="high" decoding="async">
    </a>

    @auth
      @php $u = auth()->user(); @endphp
      <div class="relative">
        <button id="user-menu-button" type="button" aria-haspopup="menu" aria-expanded="false"
                class="group inline-flex items-center gap-3 rounded-full ps-1 pe-2 py-1
                        text-sm font-medium text-gray-900
                        bg-gradient-to-b from-gray-50 to-gray-100
                        border border-white/50 ring-1 ring-gray-200/80
                        shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                        hover:from-white hover:to-gray-50 hover:ring-gray-300/80
                        focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/35 transition">
          <span class="relative inline-block">
            <img src="{{ $u->avatar_url }}" alt="Avatar {{ $u->name }}"
                 class="w-9 h-9 rounded-full ring-1 ring-gray-200 shadow-sm select-none object-cover" />
            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 ring-2 ring-white" aria-hidden="true"></span>
          </span>

          <span class="hidden sm:flex sm:flex-col items-start leading-tight">
            <span class="font-semibold max-w-[18ch] truncate">{{ $u->name }}</span>
            <span class="text-[11px] text-gray-500 capitalize">{{ $u->role }}</span>
          </span>

          <svg id="user-chevron" class="w-3.5 h-3.5 ms-0.5 text-gray-600 transition-transform duration-150 group-aria-expanded:rotate-180"
               viewBox="0 0 10 6" fill="none" aria-hidden="true"><path d="m1 1 4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>

        <div id="user-dropdown"
             class="hidden absolute right-0 mt-2 w-72 origin-top-right rounded-2xl border border-gray-200 bg-white/95 backdrop-blur
                    shadow-lg focus:outline-none z-50 overflow-hidden">
          <div class="px-4 py-3 text-sm bg-gradient-to-b from-gray-50 to-gray-100">
            <div class="font-semibold truncate text-gray-900">{{ $u->name }}</div>
            <div class="text-xs text-gray-600 truncate">{{ $u->email }}</div>
          </div>

          <ul class="py-1 text-sm text-gray-800" role="menu" aria-label="Menu do utilizador">
            <li>
              <a href="{{ route('account.profile') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50" role="menuitem" tabindex="-1">
                <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 0c-3.31 0-6 2.69-6 6v2h12v-2c0-3.31-2.69-6-6-6Z"/>
                </svg>
                Perfil
              </a>
            </li>
            <li>
              <a href="{{ route('account.settings') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50" role="menuitem" tabindex="-1">
                <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37.96.586 2.199.07 2.573-1.066Z"/>
                </svg>
                Configurações
              </a>
            </li>
          </ul>

          <div class="py-1">
            <form method="POST" action="{{ route('logout') }}" role="none">@csrf
              <button type="submit"
                      class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                      role="menuitem" tabindex="-1">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M15 3h4a2 2 0 0 1 2 2v3"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/>
                </svg>
                Terminar sessão
              </button>
            </form>
          </div>
        </div>
      </div>
    @endauth

    @guest
      <div class="flex items-center gap-4">
        <button type="button" data-auth-open="login"
          class="inline-flex items-center justify-center gap-2 bg-home-medigest-button text-home-medigest font-semibold w-[180px] h-[50px] rounded-xl hover:bg-home-medigest-button-hover transition-all">
          <x-icons.user class="w-6 h-6 text-home-main" />
          <span>Iniciar sessão</span>
        </button>
      </div>
    @endguest
  </div>
</header>

{{-- NAVBAR DO PACIENTE (apenas quando role = paciente) --}}
@auth
  @if ((auth()->user()->role ?? null) === 'paciente')
    @include('layouts.partials.navbar-paciente')
    @vite('resources/js/layout/navbar-paciente.js')
  @endif
@endauth

<main id="conteudo" class="flex-1">
  @yield('content')
</main>

<footer class="bg-home-medigest text-white text-center py-4 text-sm" role="contentinfo">
  &copy; {{ date('Y') }} Todos os direitos reservados | <strong>MediGest+</strong>
</footer>

{{-- Modais de autenticação --}}
@guest
  @include('auth.auth_modal')
  @php
    $oldForm = old('form_name');
    $openByErrors = $errors->any() && in_array($oldForm, ['login','register','forgot']) ? $oldForm : null;
    $openByStatus = session('status') ? 'forgot' : null;
    $want = session('auth_modal') ?? $openByErrors ?? $openByStatus;
  @endphp
  @if ($want)
    <script>
      window.addEventListener('DOMContentLoaded', () => {
        window.openAuthModal && window.openAuthModal(@json($want));
      });
    </script>
  @endif
@endguest

{{-- calcula a altura do header e expõe --hdr-h para a navbar sticky --}}
<script>
  (function () {
    const setHdrH = () => {
      const h = document.getElementById('site-header');
      if (!h) return;
      document.documentElement.style.setProperty('--header-h', h.offsetHeight + 'px');
    };
    window.addEventListener('DOMContentLoaded', setHdrH, { once: true });
    window.addEventListener('resize', setHdrH);
  })();
</script>

@stack('body-end')
</body>
</html>
