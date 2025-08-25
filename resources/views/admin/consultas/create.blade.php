@extends('layouts.dashboard')

@section('title', __('messages.consultas.create_title').' - Admin')

@php
  use Carbon\Carbon;
  $hoje = Carbon::now();
  $ch = \App\Models\ConfiguracaoHorario::where('dia_semana', $hoje->dayOfWeek)->first();
  $ativoHoje = (bool)($ch?->ativo);
  $ultimoFim = null;
  if ($ativoHoje) {
      $cands = array_filter([$ch->manha_fim, $ch->tarde_fim]);
      if ($cands) $ultimoFim = collect($cands)->max();
  }
@endphp

@push('head')
  <meta name="api-slots" content="{{ route('api.slots') }}">
  <meta name="agenda-active-today" content="{{ $ativoHoje ? '1' : '0' }}">
  <meta name="agenda-last-end" content="{{ $ultimoFim ?? '' }}">
  <style>
    /* o header dos passos não mostra barras e não corta os círculos */
    #wizard-steps { scrollbar-width: none; -ms-overflow-style: none; overflow: visible; padding-block: .35rem; }
    #wizard-steps::-webkit-scrollbar { display: none; }
  </style>
@endpush

@section('content')
<div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10" data-page="wizard-consulta-admin" data-role="admin">

  {{-- Cabeçalho --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 sm:mb-8">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">@lang('messages.consultas.create_title')</h1>
      <p class="text-sm text-gray-500 mt-1">@lang('messages.consultas.create_subtitle')</p>
    </div>
    <a href="{{ route('admin.consultas.index') }}"
       class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 hover:underline">
      ← @lang('messages.nav.back')
    </a>
  </div>

  @php
    // mantemos só para contagem dos passos (7 passos)
    $labels = [
      __('messages.consultas.patient'),
      __('messages.consultas.specialty'),
      __('messages.consultas.doctor'),
      __('messages.consultas.type'),
      __('messages.consultas.date').' & '.__('messages.consultas.time'),
      __('messages.consultas.description'),
      __('messages.consultas.review'),
    ];
    // ícones inline (hero/outline style)
    $icons = [
      // 0: paciente
      '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
      // 1: especialidade (estrela)
      '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v13H7a2 2 0 0 0-2 2Zm0 0a2 2 0 0 0 2 2h12M9 3v14m7 0v4"/></svg>',
      // 2: médico (mala)
      '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M12 10v6M9 13h6"/></svg>',
      // 3: tipo (relógio)
      '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
      // 4: data & hora (calendário)
      '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
      // 5: descrição (lista)
      '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
      // 6: revisão (check-square)
      '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 12l2 2 4-4"/></svg>',
    ];
  @endphp

  {{-- Trilho/steps --}}
  <div id="wizard-rail" class="relative mb-5 pt-1 pb-2">
    <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-1 bg-gray-200 rounded-full"></div>
    <div id="wizard-rail-fill" class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-emerald-500 rounded-full transition-all duration-500" style="width:0"></div>

    <ol id="wizard-steps" class="relative flex items-center gap-6 flex-wrap md:flex-nowrap">
      @foreach($labels as $i => $label)
        <li class="step-head relative flex items-center md:flex-1" data-step-index="{{ $i }}">
          <div class="step-circle grid place-items-center w-12 h-12 rounded-full border border-gray-300 bg-white text-gray-600 shadow-sm transition">
            <span class="step-ico text-current">{!! $icons[$i] !!}</span>
            <svg class="step-check w-6 h-6 hidden text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
          </div>
          {{-- etiqueta escondida (acessibilidade) --}}
          <span class="sr-only">{{ $label }}</span>
        </li>
      @endforeach
    </ol>
  </div>

  {{-- Formulário --}}
  <form id="wizardForm" method="POST" action="{{ route('admin.consultas.store') }}"
        class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60">
    @csrf
    <input type="hidden" id="duracao" name="duracao" value="30">

    <div class="p-6 sm:p-8 space-y-8">
      {{-- PASSO 0: Paciente --}}
      <section class="wizard-step" data-step="0">
        <h2 class="text-lg font-medium text-gray-900 mb-3">@lang('messages.consultas.select_patient')</h2>
        <label class="block text-sm font-medium text-gray-700 mb-1">@lang('messages.consultas.patient')</label>
        <select name="paciente_id" id="paciente_id"
                data-mg-select
                class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
                required>
          <option value="">{{ __('messages.common.select_placeholder') }}</option>
          @foreach($pacientes as $p)
            <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->email }}</option>
          @endforeach
        </select>
      </section>

      {{-- PASSO 1: Especialidade --}}
      <section class="wizard-step hidden" data-step="1">
        <h2 class="text-lg font-medium text-gray-900 mb-3">@lang('messages.consultas.specialty')</h2>
        <label class="block text-sm font-medium text-gray-700 mb-1">@lang('messages.consultas.specialty')</label>
        <select name="especialidade_id" id="especialidade_id"
                data-mg-select
                class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
                required>
          <option value="">{{ __('messages.common.select_placeholder') }}</option>
          @foreach($especialidades as $e)
            <option value="{{ $e->id }}">{{ $e->nome }}</option>
          @endforeach
        </select>
      </section>

      {{-- PASSO 2: Médico --}}
      <section class="wizard-step hidden" data-step="2">
        <h2 class="text-lg font-medium text-gray-900 mb-3">@lang('messages.consultas.select_doctor')</h2>
        <label class="block text-sm font-medium text-gray-700 mb-1">@lang('messages.consultas.doctor')</label>
        <select name="medico_id" id="medico_id"
                data-mg-select
                class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
                required>
          <option value="">— @lang('messages.consultas.select_specialty_first') —</option>
          @foreach($medicos as $m)
            <option value="{{ $m->id }}">{{ $m->name }} — {{ $m->email ?? '' }}</option>
          @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">@lang('messages.consultas.filtered_by_specialty')</p>
      </section>

      {{-- PASSO 3: Tipo --}}
      <section class="wizard-step hidden" data-step="3">
        <h2 class="text-lg font-medium text-gray-900 mb-3">@lang('messages.consultas.type')</h2>
        <label class="block text-sm font-medium text-gray-700 mb-1">@lang('messages.consultas.type')</label>
        <select id="tipo_slug" name="tipo_slug"
                data-mg-select
                class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button" required>
          <option value="">{{ __('messages.common.select_placeholder') }}</option>
          <option value="normal">@lang('messages.consultas.types.normal')</option>
          <option value="prioritaria">@lang('messages.consultas.types.prioritaria')</option>
          <option value="urgente">@lang('messages.consultas.types.urgente')</option>
        </select>
      </section>

      {{-- PASSO 4: Data & Hora --}}
      <section class="wizard-step hidden" data-step="4">
        <h2 class="text-lg font-medium text-gray-900 mb-3">@lang('messages.consultas.availability')</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {{-- DATA --}}
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">@lang('messages.consultas.date')</label>
            <input id="data" name="data" type="hidden" required>

            <div id="date-cal" class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm">
              <div class="flex items-center justify-between px-1 py-1.5">
                <button type="button" id="calPrev" class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100" aria-label="Mês anterior">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <div id="calTitle" class="text-sm font-medium text-gray-900">—</div>
                <button type="button" id="calNext" class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100" aria-label="Mês seguinte">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg>
                </button>
              </div>

              <div class="mt-2 grid grid-cols-7 text-[11px] font-medium text-gray-500">
                <div class="text-center py-1">S</div><div class="text-center py-1">T</div><div class="text-center py-1">Q</div>
                <div class="text-center py-1">Q</div><div class="text-center py-1">S</div><div class="text-center py-1">S</div><div class="text-center py-1">D</div>
              </div>

              <div id="calGrid" class="mt-1 grid grid-cols-7 gap-1.5"></div>
            </div>
          </div>

          {{-- HORA (SLOTS) --}}
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">@lang('messages.consultas.time') (slots)</label>

            <select id="hora" name="hora" class="hidden" required><option value="">—</option></select>

            <div id="slotChips" class="min-h-[120px] rounded-xl border border-gray-200 bg-white p-3 shadow-sm">
              <div class="text-sm text-gray-500">—</div>
            </div>

            <p id="slotsMsg" class="text-xs text-gray-500 mt-2">@lang('messages.consultas.slots_hint')</p>
          </div>
        </div>
      </section>

      {{-- PASSO 5: Descrição (opcional) --}}
      <section class="wizard-step hidden" data-step="5">
        <h2 class="text-lg font-medium text-gray-900 mb-3">@lang('messages.consultas.description')</h2>

        <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1">
          @lang('messages.consultas.description')
        </label>

        <div class="relative">
          <div class="absolute top-3 left-3.5 pointer-events-none text-gray-400">
            @include('components.icons.admin.info')
          </div>

          <textarea
            id="motivo" name="motivo" rows="5" maxlength="400"
            aria-describedby="motivo-hint motivo-count"
            class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 text-sm
                   pl-10 pr-3 py-3 min-h-[120px] max-h-56 overflow-auto resize-none
                   placeholder:text-gray-400 focus:border-home-medigest-button focus:ring-home-medigest-button"
            placeholder="@lang('messages.consultas.description_ph')"></textarea>

          <button type="button" id="motivoClear"
                  class="absolute bottom-2 right-2 text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50">
            @lang('messages.actions.clear')
          </button>
        </div>

        <div class="mt-1 flex items-center justify-between text-xs">
          <span id="motivo-hint" class="text-gray-500">Máx. 400 caracteres</span>
          <span id="motivo-count" class="text-gray-400">0/400</span>
        </div>

        @error('motivo') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
      </section>

      {{-- PASSO 6: Revisão --}}
      <section class="wizard-step hidden" data-step="6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">@lang('messages.consultas.review')</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="min-w-0">
              <div class="text-xs text-gray-500">@lang('messages.consultas.patient')</div>
              <div class="font-medium text-gray-900 truncate" data-review="paciente">—</div>
            </div>
            <button type="button" class="ml-auto text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="0">
              @lang('messages.actions.edit')
            </button>
          </div>

          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l4 8 8 1-6 6 1 9-7-4-7 4 1-9-6-6 8-1 4-8z"/></svg>
            </div>
            <div class="min-w-0">
              <div class="text-xs text-gray-500">@lang('messages.consultas.specialty')</div>
              <div class="font-medium text-gray-900 truncate" data-review="especialidade">—</div>
            </div>
            <button type="button" class="ml-auto text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="1">
              @lang('messages.actions.edit')
            </button>
          </div>

          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V3h8v4"/><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M12 10v6M9 13h6"/></svg>
            </div>
            <div class="min-w-0">
              <div class="text-xs text-gray-500">@lang('messages.consultas.doctor')</div>
              <div class="font-medium text-gray-900 truncate" data-review="medico">—</div>
            </div>
            <button type="button" class="ml-auto text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="2">
              @lang('messages.actions.edit')
            </button>
          </div>

          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <div class="min-w-0">
              <div class="text-xs text-gray-500">@lang('messages.consultas.type')</div>
              <div class="font-medium text-gray-900 truncate" data-review="tipo">—</div>
            </div>
            <button type="button" class="ml-auto text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="3">
              @lang('messages.actions.edit')
            </button>
          </div>

          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            </div>
            <div class="min-w-0">
              <div class="text-xs text-gray-500">@lang('messages.consultas.date')</div>
              <div class="font-medium text-gray-900 truncate" data-review="data">—</div>
            </div>
            <button type="button" class="ml-auto text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="4">
              @lang('messages.actions.edit')
            </button>
          </div>

          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v5l3 3"/><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <div class="min-w-0">
              <div class="text-xs text-gray-500">@lang('messages.consultas.time')</div>
              <div class="font-medium text-gray-900 truncate" data-review="hora">—</div>
            </div>
            <button type="button" class="ml-auto text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="4">
              @lang('messages.actions.edit')
            </button>
          </div>

          <div class="sm:col-span-2 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs text-gray-500 mb-1">@lang('messages.consultas.description')</div>
            <div class="font-medium text-gray-900 whitespace-pre-line" data-review="motivo">—</div>
            <div class="mt-2">
              <button type="button" class="text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="5">
                @lang('messages.actions.edit')
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>

    {{-- Footer do wizard --}}
    <div class="px-6 sm:px-8 py-4 bg-gray-50 rounded-b-2xl flex items-center justify-between">
      <button type="button" id="btnBack"
              class="px-4 py-2 rounded-xl text-slate-800
                     border border-white/50 ring-1 ring-gray-200/80
                     bg-gradient-to-b from-gray-50 to-gray-100
                     shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                     hover:from-white hover:to-gray-50 hover:ring-gray-300/80
                     disabled:opacity-50"
              style="visibility:hidden" disabled>
        @lang('messages.wizard.previous')
      </button>

      <div class="space-x-2">
        <button type="button" id="btnNext"
                class="px-5 py-2.5 rounded-xl text-white bg-home-medigest hover:bg-home-medigest-hover disabled:opacity-50">
          @lang('messages.wizard.next')
        </button>
        <button type="submit" id="btnSubmit"
                class="px-5 py-2.5 rounded-xl text-white bg-home-medigest hover:bg-home-medigest-hover hidden">
          @lang('messages.consultas.schedule')
        </button>
      </div>
    </div>
  </form>
</div>

@vite('resources/js/pages/admin/consultas/consultas-admin-create.js')
@endsection
