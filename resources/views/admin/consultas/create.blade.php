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

  {{-- Passos --}}
  @php
    $labels = [
      __('messages.consultas.patient'),
      __('messages.consultas.specialty'),
      __('messages.consultas.doctor'),
      __('messages.consultas.type'),
      __('messages.consultas.date').' & '.__('messages.consultas.time'),
      __('messages.consultas.description'),
      __('messages.consultas.review'),
    ];
  @endphp

  <ol id="wizard-steps" class="flex items-center gap-3 overflow-x-auto pb-1 mb-5">
    @foreach($labels as $i => $label)
      <li class="step-head flex items-center w-full min-w-[180px]">
        <div class="flex items-center justify-center w-9 h-9 rounded-full text-[13px]
                    bg-gradient-to-b from-gray-50 to-gray-100 border border-white/50 ring-1 ring-gray-200/80
                    shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]">
          {{ $i+1 }}
        </div>
        <span class="ml-2 text-sm sm:text-base text-gray-700 whitespace-nowrap">{{ $label }}</span>
        @if($i < count($labels)-1)
          <div class="hidden sm:flex flex-1 h-0.5 bg-gray-200 mx-3 rounded-full"></div>
        @endif
      </li>
    @endforeach
  </ol>

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
                class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button" required>
          <option value="">{{ __('messages.common.select_placeholder') }}</option>
          <option value="normal">@lang('messages.consultas.types.normal')</option>
          <option value="prioritaria">@lang('messages.consultas.types.prioritaria')</option>
          <option value="urgente">@lang('messages.consultas.types.urgente')</option>
        </select>
      </section>

      {{-- PASSo 4: Data & Hora --}}
      <section class="wizard-step hidden" data-step="4">
        <h2 class="text-lg font-medium text-gray-900 mb-3">@lang('messages.consultas.availability')</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">@lang('messages.consultas.date')</label>
            <input id="data" name="data" type="date"
                   min="{{ now()->toDateString() }}"
                   class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
                   required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">@lang('messages.consultas.time') (slots)</label>
            <select id="hora" name="hora"
                    class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
                    required>
              <option value="">— @lang('messages.consultas.select_doctor_and_date') —</option>
            </select>
            <p id="slotsMsg" class="text-xs text-gray-500 mt-1">@lang('messages.consultas.slots_hint')</p>
          </div>
        </div>
      </section>

      {{-- PASSO 5: Descrição --}}
      <section class="wizard-step hidden" data-step="5">
        <h2 class="text-lg font-medium text-gray-900 mb-3">@lang('messages.consultas.description')</h2>
        <label class="block text-sm font-medium text-gray-700 mb-1">@lang('messages.consultas.description')</label>
        <textarea id="motivo" name="motivo" rows="4"
                  class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
                  placeholder="@lang('messages.consultas.description_ph')"></textarea>
      </section>

      {{-- PASSO 6: Revisão --}}
      <section class="wizard-step hidden" data-step="6">
        <h2 class="text-lg font-medium text-gray-900 mb-3">@lang('messages.consultas.review')</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 text-sm">
          <div class="text-gray-500">@lang('messages.consultas.patient')</div>
          <div class="font-medium" data-review="paciente">—</div>
          <div class="text-gray-500">@lang('messages.consultas.specialty')</div>
          <div class="font-medium" data-review="especialidade">—</div>
          <div class="text-gray-500">@lang('messages.consultas.doctor')</div>
          <div class="font-medium" data-review="medico">—</div>
          <div class="text-gray-500">@lang('messages.consultas.type')</div>
          <div class="font-medium" data-review="tipo">—</div>
          <div class="text-gray-500">@lang('messages.consultas.date')</div>
          <div class="font-medium" data-review="data">—</div>
          <div class="text-gray-500">@lang('messages.consultas.time')</div>
          <div class="font-medium" data-review="hora">—</div>
          <div class="text-gray-500">@lang('messages.consultas.description')</div>
          <div class="font-medium" data-review="motivo">—</div>
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

@vite('resources/js/pages/consultas-admin-create.js')
@endsection
