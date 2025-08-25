{{-- resources/views/layouts/partials/sidebar.blade.php --}}
@php
  $navBase = 'flex items-center rounded-xl px-3 py-2 text-sm font-medium transition-all duration-150
              focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/35
              bg-gradient-to-b ring-1 min-h-10 justify-start gap-0';

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
  class="group shrink-0 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/90
         transition-all duration-200 border-r border-gray-200 shadow-sm p-4 overflow-y-auto z-30">

  <nav aria-label="Menu lateral" class="space-y-6">

    {{-- ============ GERAL ============ --}}
    <div role="group" aria-labelledby="grp-geral">
      <h3 id="grp-geral" class="px-2 text-[11px] font-semibold text-gray-700 uppercase tracking-wide">
        {{ __('messages.sidebar.general') }}
      </h3>

      <ul class="mt-2 space-y-2">
        <li>
          <a href="{{ route($dashboardRoute) }}"
             aria-current="{{ request()->routeIs($dashboardRoute) ? 'page' : 'false' }}"
             class="{{ $nav($dashboardRoute) }}" title="{{ __('messages.nav.dashboard') }}">
            <svg class="w-5 h-5 shrink-0 stroke-current" viewBox="0 0 24 24" fill="none" stroke-width="1.8">
              <path d="M3 11.5 12 4l9 7.5M5 10.5V20h14v-9.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="sidebar-label">{{ __('messages.nav.dashboard') }}</span>
          </a>
        </li>
      </ul>
    </div>

    {{-- ============ MÉDICO ============ --}}
    @if ($role === 'medico')
      <div class="border-t border-gray-200 pt-4" role="group" aria-labelledby="grp-consultas-medico">
        <h3 id="grp-consultas-medico" class="px-2 text-[11px] font-semibold text-gray-700 uppercase tracking-wide">
          {{ __('messages.sidebar.consultations') }}
        </h3>

        <ul class="mt-2 space-y-2">
          <li>
            <a href="{{ route('medico.consultas.index') }}"
               aria-current="{{ request()->routeIs('medico.consultas.index') ? 'page' : 'false' }}"
               class="{{ $nav('medico.consultas.index') }}"
               title="{{ __('messages.sidebar.assigned_consultations') }}">
              <svg class="w-5 h-5 shrink-0 stroke-current" viewBox="0 0 24 24" fill="none" stroke-width="1.8" xmlns="http://www.w3.org/2000/svg">
                <path d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span class="sidebar-label">{{ __('messages.sidebar.assigned_consultations') }}</span>
            </a>
          </li>

          <li>
            <a href="{{ route('medico.consultas.create') }}"
               aria-current="{{ request()->routeIs('medico.consultas.create') ? 'page' : 'false' }}"
               class="{{ $nav('medico.consultas.create') }}"
               title="{{ __('messages.actions.new_consultation') }}">
              <svg class="w-5 h-5 shrink-0 stroke-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12A2.25 2.25 0 0 0 4.5 20.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/>
              </svg>
              <span class="sidebar-label">{{ __('messages.actions.new_consultation') }}</span>
            </a>
          </li>

          <li>
            <a href="{{ route('medico.calendario') }}"
               aria-current="{{ request()->routeIs('medico.calendario') ? 'page' : 'false' }}"
               class="{{ $nav('medico.calendario') }}"
               title="{{ __('messages.nav.calendar') }}">
              <svg class="w-5 h-5 shrink-0 stroke-current" viewBox="0 0 24 24" fill="none" stroke-width="1.8">
                <path d="M16 2v4M8 2v4M3 10h18M5 6h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span class="sidebar-label">{{ __('messages.nav.calendar') }}</span>
            </a>
          </li>
        </ul>
      </div>
    @endif

    {{-- ============ ADMIN ============ --}}
    @if ($role === 'admin')
      <div class="border-t border-gray-200 pt-4" role="group" aria-labelledby="grp-consultas-admin">
        <h3 id="grp-consultas-admin" class="px-2 text-[11px] font-semibold text-gray-700 uppercase tracking-wide">
          {{ __('messages.sidebar.consultations') }}
        </h3>

        <ul class="mt-2 space-y-2">
          <li>
            <a href="{{ route('admin.consultas.index') }}"
               aria-current="{{ request()->routeIs('admin.consultas.index') ? 'page' : 'false' }}"
               class="{{ $nav('admin.consultas.index') }}"
               title="{{ __('messages.nav.all_consultations') }}">
              <svg class="w-5 h-5 shrink-0 stroke-current" viewBox="0 0 24 24" fill="none" stroke-width="1.8" xmlns="http://www.w3.org/2000/svg">
                <path d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span class="sidebar-label">{{ __('messages.nav.all_consultations') }}</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="border-t border-gray-200 pt-4" role="group" aria-labelledby="grp-gestao">
        <h3 id="grp-gestao" class="px-2 text-[11px] font-semibold text-gray-700 uppercase tracking-wide">
          {{ __('messages.sidebar.management') }}
        </h3>

        <ul class="mt-2 space-y-2">
          <li>
            <a href="{{ route('admin.especialidades.index') }}"
               aria-current="{{ request()->routeIs('admin.especialidades.*') ? 'page' : 'false' }}"
               class="{{ $nav('admin.especialidades.*') }}"
               title="{{ __('messages.nav.specialties') }}">
              <svg class="w-5 h-5 shrink-0 stroke-current" viewBox="0 0 24 24" fill="none" stroke-width="1.8" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span class="sidebar-label">{{ __('messages.nav.specialties') }}</span>
            </a>
          </li>

          <li>
            <a href="{{ route('admin.medicos.index') }}"
               aria-current="{{ request()->routeIs('admin.medicos.*') ? 'page' : 'false' }}"
               class="{{ $nav('admin.medicos.*') }}"
               title="{{ __('messages.nav.create_doctor') }}">
              <svg class="w-5 h-5 shrink-0 stroke-current" viewBox="0 0 24 24" fill="none" stroke-width="1.8">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M8.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M19 8v6M22 11h-6" stroke-linecap="round"/>
              </svg>
              <span class="sidebar-label">{{ __('messages.nav.create_doctor') }}</span>
            </a>
          </li>

          <li>
            <a href="{{ route('admin.horarios.index') }}"
               aria-current="{{ request()->routeIs('admin.horarios.*') ? 'page' : 'false' }}"
               class="{{ $nav('admin.horarios.*') }}"
               title="{{ __('messages.nav.schedules') }}">
              <svg class="w-5 h-5 shrink-0 stroke-current" viewBox="0 0 24 24" fill="none" stroke-width="1.8">
                <path d="M12 6v6l4 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span class="sidebar-label">{{ __('messages.nav.schedules') }}</span>
            </a>
          </li>
        </ul>
      </div>
    @endif

  </nav>
</aside>
