{{-- resources/views/layouts/partials/sidebar.blade.php --}}
@php
  $navBase = 'block rounded-xl px-3 py-2 text-sm font-medium transition-all duration-150
              focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/35
              bg-gradient-to-b ring-1';

  $navInactive = 'text-slate-800 border border-white/50 ring-gray-200/80
                  from-gray-50 to-gray-100
                  shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                  hover:from-white hover:to-gray-50
                  hover:shadow-[inset_-2px_-2px_7px_rgba(255,255,255,0.98),inset_2px_2px_7px_rgba(15,23,42,0.12)]
                  hover:ring-gray-300/80';

  $navActive   = 'text-white border-slate-900 ring-slate-900/40
                  from-slate-900 to-slate-900
                  shadow-[0_1px_2px_rgba(0,0,0,0.25),0_8px_16px_rgba(2,6,23,0.25),inset_0_1px_0_rgba(255,255,255,0.06)]';

  $nav = function (string $pattern) use ($navBase, $navInactive, $navActive) {
      return $navBase.' '.(request()->routeIs($pattern) ? $navActive : $navInactive);
  };
@endphp

<aside id="layout-sidebar"
  class="w-64 shrink-0 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/90
         lg:static lg:transform-none lg:h-auto lg:top-auto lg:left-auto lg:translate-x-0
         transition-transform duration-200 border-r border-gray-200 shadow-sm
         p-4 overflow-y-auto z-30">

  <nav aria-label="Menu lateral" class="space-y-6">

    {{-- ============ GERAL ============ --}}
    <div role="group" aria-labelledby="grp-geral">
      <h3 id="grp-geral"
          class="px-2 text-[11px] font-semibold text-gray-700 uppercase tracking-wide">
        {{ __('messages.sidebar.general') }}
      </h3>

      <ul class="mt-2 space-y-2">
        <li>
          <a
            href="{{ route($dashboardRoute) }}"
            aria-current="{{ request()->routeIs($dashboardRoute) ? 'page' : 'false' }}"
            class="{{ $nav($dashboardRoute) }}">
            {{ __('messages.nav.dashboard') }}
          </a>
        </li>
      </ul>
    </div>

    {{-- ============ MÉDICO ============ --}}
    @if ($role === 'medico')
      <div class="border-t border-gray-200 pt-4" role="group" aria-labelledby="grp-consultas-medico">
        <h3 id="grp-consultas-medico"
            class="px-2 text-[11px] font-semibold text-gray-700 uppercase tracking-wide">
          {{ __('messages.sidebar.consultations') }}
        </h3>

        <ul class="mt-2 space-y-2">
          <li>
            <a
              href="{{ route('medico.consultas.index') }}"
              aria-current="{{ request()->routeIs('medico.consultas.index') ? 'page' : 'false' }}"
              class="{{ $nav('medico.consultas.index') }}">
              {{ __('messages.sidebar.assigned_consultations') }}
            </a>
          </li>

          <li>
            <a
              href="{{ route('medico.consultas.create') }}"
              aria-current="{{ request()->routeIs('medico.consultas.create') ? 'page' : 'false' }}"
              class="{{ $nav('medico.consultas.create') }}">
              {{ __('messages.actions.new_consultation') }}
            </a>
          </li>
        </ul>
      </div>
    @endif

    {{-- ============ ADMIN ============ --}}
    @if ($role === 'admin')
      <div class="border-t border-gray-200 pt-4" role="group" aria-labelledby="grp-consultas-admin">
        <h3 id="grp-consultas-admin"
            class="px-2 text-[11px] font-semibold text-gray-700 uppercase tracking-wide">
          {{ __('messages.sidebar.consultations') }}
        </h3>

        <ul class="mt-2 space-y-2">
          <li>
            <a
              href="{{ route('admin.consultas.index') }}"
              aria-current="{{ request()->routeIs('admin.consultas.index') ? 'page' : 'false' }}"
              class="{{ $nav('admin.consultas.index') }}">
              {{ __('messages.nav.all_consultations') }}
            </a>
          </li>
        </ul>
      </div>

      <div class="border-t border-gray-200 pt-4" role="group" aria-labelledby="grp-gestao">
        <h3 id="grp-gestao"
            class="px-2 text-[11px] font-semibold text-gray-700 uppercase tracking-wide">
          {{ __('messages.sidebar.management') }}
        </h3>

        <ul class="mt-2 space-y-2">
          <li>
            <a
              href="{{ route('admin.especialidades.index') }}"
              aria-current="{{ request()->routeIs('admin.especialidades.*') ? 'page' : 'false' }}"
              class="{{ $nav('admin.especialidades.*') }}">
              {{ __('messages.nav.specialties') }}
            </a>
          </li>

          <li>
            <a
              href="{{ route('admin.medicos.index') }}"
              aria-current="{{ request()->routeIs('admin.medicos.*') ? 'page' : 'false' }}"
              class="{{ $nav('admin.medicos.*') }}">
              {{ __('messages.nav.create_doctor') }}
            </a>
          </li>

          <li>
            <a
              href="{{ route('admin.horarios.index') }}"
              aria-current="{{ request()->routeIs('admin.horarios.*') ? 'page' : 'false' }}"
              class="{{ $nav('admin.horarios.*') }}">
              {{ __('messages.nav.schedules') }}
            </a>
          </li>
        </ul>
      </div>
    @endif

  </nav>
</aside>
