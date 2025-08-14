{{-- resources/views/layouts/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale() ?: 'pt') }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="color-scheme" content="light">
  <title>@yield('title', __('messages.nav.dashboard')) • {{ __('messages.app.name') }}</title>

  <link rel="preload" as="image" href="{{ asset('/Logo_Preto.svg') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @php
    $modals = trans('messages.modals');
    $status = trans('messages.status');
    $common = trans('messages.common');
    $actions = trans('messages.actions');
  @endphp
  <script>
    window.APP_LOCALE = @json(app()->getLocale());
    window.I18N = {
      modals: @json($modals, JSON_UNESCAPED_UNICODE),
      status: @json($status, JSON_UNESCAPED_UNICODE),
      common: @json($common, JSON_UNESCAPED_UNICODE),
      actions: @json($actions, JSON_UNESCAPED_UNICODE),
    };
  </script>
  @stack('head')
</head>

<body class="bg-gray-100 text-gray-900 antialiased">
  <a href="#conteudo-principal"
     class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50
            bg-white text-gray-900 rounded px-3 py-2 shadow">
    {{-- (opcional) criar chave messages.common.skip_to_content --}}
    Saltar para o conteúdo
  </a>

  {{-- HEADER --}}
  <header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sticky top-0 z-40">
    <div class="max-w-[1430px] mx-auto flex items-center gap-3 justify-between">
      <button id="sidebar-toggle"
              class="lg:hidden inline-flex items-center justify-center rounded-lg p-2
                     text-gray-700 hover:bg-gray-100"
              aria-controls="layout-sidebar" aria-expanded="false">
        <span class="sr-only">Alternar menu</span>
        <svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M3 6h18M3 12h18M3 18h18"/>
        </svg>
      </button>

      <a href="{{ route('home') }}" class="inline-flex items-center shrink-0" aria-label="{{ __('messages.app.name') }}">
        <img src="{{ asset('/Logo_Preto.svg') }}" alt="{{ __('messages.app.name') }}" class="h-8 sm:h-10 w-auto select-none" loading="eager" fetchpriority="high">
      </a>

      @auth
        @php $u = auth()->user(); @endphp

        {{-- User dropdown --}}
        <div class="relative">
          <button
            id="user-menu-button"
            type="button"
            aria-haspopup="menu"
            aria-expanded="false"
            class="group inline-flex items-center gap-3 rounded-full ps-1 pe-2 py-1
                  text-sm font-medium text-gray-900
                  bg-gradient-to-b from-gray-50 to-gray-100
                  border border-white/50 ring-1 ring-gray-200/80
                  shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                  hover:from-white hover:to-gray-50 hover:ring-gray-300/80
                  focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/35 transition">

            {{-- Avatar + indicador de estado --}}
            <span class="relative inline-block">
              <img src="{{ $u->avatar_url }}" alt="Avatar {{ $u->name }}"
                  class="w-9 h-9 rounded-full ring-1 ring-gray-200 shadow-sm select-none object-cover" />
              <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 ring-2 ring-white" aria-hidden="true"></span>
            </span>

            {{-- Nome + role (esconde em ecrãs muito pequenos) --}}
            <span class="hidden sm:flex sm:flex-col items-start leading-tight">
              <span class="font-semibold max-w-[18ch] truncate">{{ $u->name }}</span>
              <span class="text-[11px] text-gray-500 capitalize">{{ $u->role }}</span>
            </span>

            {{-- Chevron com rotação quando aberto --}}
            <svg id="user-chevron" class="w-3.5 h-3.5 ms-0.5 text-gray-600 transition-transform duration-150 group-aria-expanded:rotate-180"
                viewBox="0 0 10 6" fill="none" aria-hidden="true">
              <path d="m1 1 4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>

          <div
            id="user-dropdown"
            class="hidden absolute right-0 mt-2 w-72 origin-top-right rounded-2xl border border-gray-200 bg-white/95 backdrop-blur
                  shadow-lg focus:outline-none z-50 overflow-hidden">
            {{-- Cabeçalho do menu --}}
            <div class="px-4 py-3 text-sm bg-gradient-to-b from-gray-50 to-gray-100">
              <div class="font-semibold truncate text-gray-900">{{ $u->name }}</div>
              <div class="text-xs text-gray-600 truncate">{{ $u->email }}</div>
            </div>

            <ul class="py-1 text-sm text-gray-800" role="none">
              <li>
                <a href="{{ route('account.profile') }}"
                  class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50"
                  role="menuitem" tabindex="-1">
                  <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 0c-3.31 0-6 2.69-6 6v2h12v-2c0-3.31-2.69-6-6-6Z"/>
                  </svg>
                  {{ __('messages.nav.profile') }}
                </a>
              </li>
              <li>
                <a href="{{ route('account.settings') }}"
                  class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50"
                  role="menuitem" tabindex="-1">
                  <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37.96.586 2.199.07 2.573-1.066Z"/>
                  </svg>
                  {{ __('messages.nav.settings') }}
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
                  {{ __('messages.nav.logout') }}
                </button>
              </form>
            </div>
          </div>
        </div>
      @endauth
    </div>
  </header>

  {{-- LAYOUT --}}
  @php
    $role = optional(auth()->user())->role;
    $dashboardRoute = match ($role) {
      'admin'    => 'admin.dashboard',
      'medico'   => 'medico.dashboard',
      'paciente' => 'paciente.home',
      default    => 'home',
    };
  @endphp

  <div class="relative">
    <div class="flex min-h-[calc(100vh-64px)]">
      {{-- SIDEBAR (incluído) --}}
      @include('layouts.partials.sidebar', ['role' => $role, 'dashboardRoute' => $dashboardRoute])

      {{-- OVERLAY mobile --}}
      <div id="sidebar-scrim"
           class="lg:hidden fixed left-0 right-0 bg-black/40 transition-opacity z-20"
           aria-hidden="true"></div>

      {{-- CONTEÚDO --}}
      <main id="conteudo-principal" class="flex-1 p-4 sm:p-6 overflow-y-auto">
        @yield('content')
      </main>
    </div>
  </div>

  @stack('body-end')
</body>
</html>
