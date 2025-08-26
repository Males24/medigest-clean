@extends('layouts.app')

@section('title', 'Nova consulta | MediGest+')

@push('head')
  <meta name="api-slots" content="{{ route('api.slots') }}">
  <meta name="api-medicos-template" content="{{ url('/api/especialidades/{id}/medicos') }}">
  <style>
    #wizard-steps{scrollbar-width:none;-ms-overflow-style:none;overflow:visible;padding-block:.35rem}
    #wizard-steps::-webkit-scrollbar{display:none}
  </style>
@endpush

@section('content')
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início','url'=>route('home')],
    ['label'=>'Serviços','url'=>Route::has('paciente.consultas.index') ? route('paciente.consultas.index') : '#'],
    ['label'=>'Marcação de consultas','url'=>Route::has('paciente.consultas.index') ? route('paciente.consultas.index') : '#'],
    ['label'=>'Nova consulta']
  ]" />

  <x-ui.hero
    title="Nova consulta"
    subtitle="Siga os passos: especialidade, médico, tipo, data &amp; hora e revisão."
    height="160px"
  />
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10"
     data-page="wizard-consulta-paciente">


  @php
    $labels = ['Especialidade','Médico','Tipo','Data & hora','Descrição','Revisão'];
    $icons = [
      '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v13H7a2 2 0 0 0-2 2Zm0 0a2 2 0 0 0 2 2h12M9 3v14m7 0v4"/></svg>',
      '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M12 10v6M9 13h6"/></svg>',
      '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
      '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
      '<svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
      '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 12l2 2 4-4"/></svg>',
    ];
  @endphp

  {{-- Trilho --}}
  <div id="wizard-rail" class="relative mb-5 pt-1 pb-2">
    <div id="wizard-rail-base" class="absolute top-1/2 -translate-y-1/2 h-1 bg-gray-200 rounded-full"></div>
    <div id="wizard-rail-fill" class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-emerald-500 rounded-full transition-all duration-500" style="width:0"></div>

    <ol id="wizard-steps" class="relative z-10 flex items-center gap-6 flex-wrap md:flex-nowrap">
      @foreach($labels as $i => $label)
        <li class="step-head relative flex items-center md:flex-1" data-step-index="{{ $i }}">
          <div class="step-circle grid place-items-center w-12 h-12 rounded-full border border-gray-300 bg-white text-gray-600 shadow-sm transition">
            <span class="step-ico text-current">{!! $icons[$i] !!}</span>
            <svg class="step-check w-6 h-6 hidden text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
          </div>
          <span class="sr-only">{{ $label }}</span>
        </li>
      @endforeach
    </ol>
  </div>

  @if($errors->any())
  <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-800 p-3">
    {{ $errors->first() }}
  </div>
@endif
  {{-- Formulário --}}
  <form id="wizardForm" method="POST" action="{{ route('paciente.consultas.store') }}"
        class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60">
    @csrf
    <input type="hidden" id="duracao" name="duracao" value="{{ old('duracao',30) }}">

    <div class="p-6 sm:p-8 space-y-8">
      {{-- 0: Especialidade --}}
      <section class="wizard-step" data-step="0">
        <h2 class="text-lg font-medium text-gray-900 mb-3">Especialidade</h2>
        <label class="block text-sm font-medium text-gray-700 mb-1">Especialidade</label>
        <select name="especialidade_id" id="especialidade_id" data-mg-select
                class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button" required>
          <option value="">— Selecionar —</option>
          @foreach(($especialidades ?? []) as $e)
            <option value="{{ $e->id }}" {{ old('especialidade_id')==$e->id?'selected':'' }}>{{ $e->nome }}</option>
          @endforeach
        </select>
        @error('especialidade_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
      </section>

      {{-- 1: Médico (carregado por especialidade) --}}
      <section class="wizard-step hidden" data-step="1">
        <h2 class="text-lg font-medium text-gray-900 mb-3">Médico</h2>
        <label class="block text-sm font-medium text-gray-700 mb-1">Médico</label>
        <select name="medico_id" id="medico_id" data-mg-select
                class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button" required disabled>
          <option value="">— selecione a especialidade —</option>
        </select>
        @error('medico_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
        <p class="text-xs text-gray-500 mt-1">A lista é filtrada pela especialidade escolhida.</p>
      </section>

      {{-- 2: Tipo --}}
      <section class="wizard-step hidden" data-step="2">
        <h2 class="text-lg font-medium text-gray-900 mb-3">Tipo</h2>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
        <select id="tipo_slug" name="tipo_slug" data-mg-select
                class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button" required>
          <option value="">— Selecionar —</option>
          <option value="normal"      {{ old('tipo_slug')==='normal'?'selected':'' }}>Normal</option>
          <option value="prioritaria" {{ old('tipo_slug')==='prioritaria'?'selected':'' }}>Prioritária</option>
          <option value="urgente"     {{ old('tipo_slug')==='urgente'?'selected':'' }}>Urgente</option>
        </select>
        @error('tipo_slug') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
      </section>

      {{-- 3: Data & Hora (slots) --}}
      <section class="wizard-step hidden" data-step="3">
        <h2 class="text-lg font-medium text-gray-900 mb-3">Disponibilidade</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {{-- Data --}}
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
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
          {{-- Hora --}}
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hora (slots)</label>
            <select id="hora" name="hora" class="hidden" required><option value="">—</option></select>
            <div id="slotChips" class="min-h-[120px] rounded-xl border border-gray-200 bg-white p-3 shadow-sm">
              <div class="text-sm text-gray-500">—</div>
            </div>
            <p id="slotsMsg" class="text-xs text-gray-500 mt-2">Seleciona médico, tipo e data.</p>
            @error('hora') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
          </div>
        </div>
      </section>

      {{-- 4: Descrição --}}
      <section class="wizard-step hidden" data-step="4">
        <h2 class="text-lg font-medium text-gray-900 mb-3">Descrição (opcional)</h2>
        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea id="descricao" name="descricao" rows="5" maxlength="400"
          class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 text-sm px-3 py-3
                 min-h-[120px] max-h-56 overflow-auto resize-none
                 placeholder:text-gray-400 focus:border-home-medigest-button focus:ring-home-medigest-button"
          placeholder="Explique brevemente o motivo da consulta…">{{ old('descricao') }}</textarea>
        @error('descricao') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
      </section>

      {{-- 5: Revisão --}}
      <section class="wizard-step hidden" data-step="5">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Revisão</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center shrink-0">
              {!! $icons[0] !!}
            </div>
            <div class="min-w-0">
              <div class="text-xs text-gray-500">Especialidade</div>
              <div class="font-medium text-gray-900 truncate" data-review="esp">—</div>
            </div>
            <button type="button" class="ml-auto text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="0">Editar</button>
          </div>

          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center shrink-0">
              {!! $icons[1] !!}
            </div>
            <div class="min-w-0">
              <div class="text-xs text-gray-500">Médico</div>
              <div class="font-medium text-gray-900 truncate" data-review="med">—</div>
            </div>
            <button type="button" class="ml-auto text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="1">Editar</button>
          </div>

          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center shrink-0">
              {!! $icons[2] !!}
            </div>
            <div class="min-w-0">
              <div class="text-xs text-gray-500">Tipo</div>
              <div class="font-medium text-gray-900 truncate" data-review="tipo">—</div>
            </div>
            <button type="button" class="ml-auto text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="2">Editar</button>
          </div>

          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center shrink-0">
              {!! $icons[3] !!}
            </div>
            <div class="min-w-0">
              <div class="text-xs text-gray-500">Data</div>
              <div class="font-medium text-gray-900 truncate" data-review="data">—</div>
            </div>
            <button type="button" class="ml-auto text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="3">Editar</button>
          </div>

          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center shrink-0">
              {!! $icons[4] !!}
            </div>
            <div class="min-w-0">
              <div class="text-xs text-gray-500">Hora</div>
              <div class="font-medium text-gray-900 truncate" data-review="hora">—</div>
            </div>
            <button type="button" class="ml-auto text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="3">Editar</button>
          </div>

          <div class="sm:col-span-2 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs text-gray-500 mb-1">Descrição</div>
            <div class="font-medium text-gray-900 whitespace-pre-line" data-review="desc">—</div>
            <div class="mt-2">
              <button type="button" class="text-xs px-2 py-1 rounded-lg border border-gray-200 hover:bg-gray-50" data-goto-step="4">Editar</button>
            </div>
          </div>
        </div>
      </section>
    </div>

    {{-- Footer --}}
    <div class="px-6 sm:px-8 py-4 bg-gray-50 rounded-b-2xl flex items-center justify-between">
      <button type="button" id="btnBack"
              class="px-4 py-2 rounded-xl text-slate-800 border border-white/50 ring-1 ring-gray-200/80
                     bg-gradient-to-b from-gray-50 to-gray-100 shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                     hover:from-white hover:to-gray-50 hover:ring-gray-300/80 disabled:opacity-50"
              style="visibility:hidden" disabled>
        Anterior
      </button>

      <div class="space-x-2">
        <button type="button" id="btnNext"
                class="px-5 py-2.5 rounded-xl text-white bg-home-medigest hover:bg-home-medigest-hover disabled:opacity-50">
          Seguinte
        </button>
        <button type="submit" id="btnSubmit"
                class="px-5 py-2.5 rounded-xl text-white bg-home-medigest hover:bg-home-medigest-hover hidden">
          Agendar
        </button>
      </div>
    </div>
  </form>
</div>

@vite('resources/js/pages/paciente/consultas/consultas-paciente-create.js')
@endsection
