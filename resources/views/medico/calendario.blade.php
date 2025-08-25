@extends('layouts.dashboard')
@section('title', __('messages.calendar.title'))

@section('content')
<div class="max-w-none
         mx-[-1rem] sm:mx-[-1.5rem] lg:mx-[-2rem]
         px-0 pt-0 pb-0
         -mt-4 sm:-mt-6 md:-mt-6 lg:-mt-6
         -mb-9 sm:-mb-11 lg:-mb-13"
  data-page="medico-calendario">

  <div class="cal-card rounded-xl border border-gray-200 bg-white overflow-hidden">
    <div id="cal-toolbar"
         class="flex flex-wrap items-center justify-between gap-3 sm:gap-4
                px-4 sm:px-6 lg:px-8
                py-2 sm:py-5 lg:py-7
                border-b border-gray-200 bg-white">

      <div class="flex items-center gap-2">
        <span role="button" tabindex="0"
              class="cal-btn hover:bg-gray-50 active:bg-gray-100 transition-colors cursor-pointer"
              data-cal="prev" aria-label="{{ __('messages.wizard.previous') }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        </span>
        <span role="button" tabindex="0"
              class="cal-btn hover:bg-gray-50 active:bg-gray-100 transition-colors cursor-pointer"
              data-cal="next" aria-label="{{ __('messages.wizard.next') }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </span>

        <h1 id="cal-title" class="text-lg sm:text-xl font-semibold text-gray-900 ml-3">—</h1>
        <span role="button" tabindex="0"
              class="cal-chip ml-2 hover:bg-gray-50 active:bg-gray-100 transition-colors cursor-pointer"
              data-cal="today">{{ __('messages.calendar.today') }}</span>
      </div>

      <div class="flex items-center gap-3">
        <div class="inline-flex rounded-lg overflow-hidden border border-gray-300 bg-white hover:bg-gray-50
                    ring-1 ring-gray-200/80
                    shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                    focus:outline-none focus-visible:ring-2 focus-visible:ring-home-medigest-button/40">
          <span role="button" tabindex="0" class="px-3 py-2 text-sm hover:text-home-medigest" data-cal="view" data-view="dayGridMonth">{{ __('messages.calendar.month') }}</span>
          <span role="button" tabindex="0" class="px-3 py-2 text-sm hover:text-home-medigest" data-cal="view" data-view="timeGridWeek">{{ __('messages.calendar.week') }}</span>
          <span role="button" tabindex="0" class="px-3 py-2 text-sm hover:text-home-medigest" data-cal="view" data-view="timeGridDay">{{ __('messages.calendar.day') }}</span>
          <span role="button" tabindex="0" class="px-3 py-2 text-sm hover:text-home-medigest" data-cal="view" data-view="listWeek">{{ __('messages.calendar.list') }}</span>
        </div>

        <a href="{{ route('medico.consultas.create') }}"
           class="inline-flex items-center rounded-lg bg-home-medigest px-3 py-2 text-sm font-medium text-white hover:bg-home-medigest-hover shadow-sm">
          + {{ __('messages.actions.new_consultation') }}
        </a>
      </div>
    </div>

    <div id="medico-calendar" class="p-0"></div>
  </div>
</div>

<script>
  window.__MEDICO_EVENTS = @json($events ?? []);
  window.__APP_LOCALE    = "{{ app()->getLocale() }}";
</script>

@php
  // prepara o payload do médico autenticado em PHP (sem optional()/?? dentro do @json)
  $u = auth()->user();
  $medicoPayload = [
      'nome'               => $u?->name,
      'email'              => $u?->email,
      'especialidade_nome' => $u?->medico?->especialidade?->nome,
  ];
@endphp

<script>
  // agora sim, injecta o JSON já “fechado”
  window.__MEDICO_LOGADO = @json($medicoPayload);
</script>

@vite([
  'resources/js/pages/medico/calendario/medico-calendario.js',
  'resources/js/pages/medico/calendario/medico-calendario-modal.js',
])
@endsection
