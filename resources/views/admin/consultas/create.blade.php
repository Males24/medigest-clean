@extends('layouts.dashboard')

@section('title','Criar Consulta - Admin')

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
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" data-page="wizard-consulta-admin" data-role="admin">
    <div class="flex items-center justify-between mb-8">
        <div>
        <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Criar Consulta</h1>
        <p class="text-sm text-gray-500 mt-1">Preenche os passos para agendar a consulta.</p>
        </div>
        <a href="{{ route('admin.consultas.index') }}" class="text-gray-600 hover:underline">Voltar</a>
    </div>

    <ol id="wizard-steps" class="flex items-center justify-between mb-5">
        @php $labels = ['Paciente','Especialidade','Médico','Tipo','Data & Hora','Descrição','Confirmar']; @endphp
        @foreach($labels as $i => $label)
        <li class="step-head flex items-center w-full">
            <div class="flex items-center justify-center w-9 h-9 rounded-full text-[13px] bg-gray-100 text-gray-500 ring-1 ring-gray-200">
            {{ $i+1 }}
            </div>
            <span class="ml-2 text-sm sm:text-base text-gray-600 whitespace-nowrap">{{ $label }}</span>
            @if($i < count($labels)-1)
            <div class="hidden sm:flex flex-1 h-0.5 bg-gray-200 mx-3"></div>
            @endif
        </li>
        @endforeach
    </ol>

    <form id="wizardForm" method="POST" action="{{ route('admin.consultas.store') }}"
            class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60">
        @csrf
        <input type="hidden" id="duracao" name="duracao" value="30">

    <div class="p-6 sm:p-8 space-y-8">
        <section class="wizard-step" data-step="0">
            <h2 class="text-lg font-medium text-gray-900 mb-3">Selecionar paciente</h2>
            <label class="block text-sm font-medium text-gray-700 mb-1">Paciente</label>
            <select name="paciente_id" id="paciente_id"
                    class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
                    required>
            <option value="">-- Seleciona --</option>
            @foreach($pacientes as $p)
                <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->email }}</option>
            @endforeach
            </select>
        </section>

        <section class="wizard-step hidden" data-step="1">
            <h2 class="text-lg font-medium text-gray-900 mb-3">Especialidade</h2>
            <label class="block text-sm font-medium text-gray-700 mb-1">Especialidade</label>
            <select name="especialidade_id" id="especialidade_id"
                    class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
                    required>
            <option value="">-- Seleciona --</option>
            @foreach($especialidades as $e)
                <option value="{{ $e->id }}">{{ $e->nome }}</option>
            @endforeach
            </select>
        </section>

        <section class="wizard-step hidden" data-step="2">
            <h2 class="text-lg font-medium text-gray-900 mb-3">Selecionar médico</h2>
            <label class="block text-sm font-medium text-gray-700 mb-1">Médico</label>
            <select name="medico_id" id="medico_id"
                    class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
                    required>
            <option value="">— seleciona a especialidade —</option>
            @foreach($medicos as $m)
                <option value="{{ $m->id }}">{{ $m->name }} — {{ $m->email }}</option>
            @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">A lista é filtrada pela especialidade.</p>
        </section>

      {{-- PASSO 3: Tipo --}}
        <section class="wizard-step hidden" data-step="3">
        <h2 class="text-lg font-medium text-gray-900 mb-3">Tipo de consulta</h2>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
        <select id="tipo_slug" name="tipo_slug"
                class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button" required>
            <option value="">-- Seleciona --</option>   {{-- << placeholder --}}
            <option value="normal">Normal</option>
            <option value="prioritaria">Prioritária</option>
            <option value="urgente">Urgente</option>
        </select>
        </section>

        <section class="wizard-step hidden" data-step="4">
            <h2 class="text-lg font-medium text-gray-900 mb-3">Disponibilidade</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                <input id="data" name="data" type="date"
                    min="{{ now()->toDateString() }}"
                    class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
                    required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hora (slots)</label>
                <select id="hora" name="hora"
                        class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
                        required>
                <option value="">— seleciona médico e data —</option>
                </select>
                <p id="slotsMsg" class="text-xs text-gray-500 mt-1">Escolhe médico e data para ver as horas disponíveis.</p>
            </div>
            </div>
        </section>

        <section class="wizard-step hidden" data-step="5">
            <h2 class="text-lg font-medium text-gray-900 mb-3">Descrição do problema</h2>
            <label class="block text-sm font-medium text-gray-700 mb-1">Detalhes (opcional)</label>
            <textarea id="motivo" name="motivo" rows="4"
                    class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
                    placeholder="Ex.: dores lombares, exame de rotina, etc."></textarea>
        </section>

        <section class="wizard-step hidden" data-step="6">
            <h2 class="text-lg font-medium text-gray-900 mb-3">Revisão</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 text-sm">
            <div class="text-gray-500">Paciente</div>
            <div class="font-medium" data-review="paciente">—</div>
            <div class="text-gray-500">Especialidade</div>
            <div class="font-medium" data-review="especialidade">—</div>
            <div class="text-gray-500">Médico</div>
            <div class="font-medium" data-review="medico">—</div>
            <div class="text-gray-500">Tipo</div>
            <div class="font-medium" data-review="tipo">—</div>
            <div class="text-gray-500">Data</div>
            <div class="font-medium" data-review="data">—</div>
            <div class="text-gray-500">Hora</div>
            <div class="font-medium" data-review="hora">—</div>
            <div class="text-gray-500">Descrição</div>
            <div class="font-medium" data-review="motivo">—</div>
            </div>
        </section>
    </div>

    <div class="px-6 sm:px-8 py-4 bg-gray-50 rounded-b-2xl flex items-center justify-between">
        <button type="button" id="btnBack"
                class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 disabled:opacity-50"
                style="visibility:hidden" disabled>
            Anterior
        </button>
        <div class="space-x-2">
            <button type="button" id="btnNext"
                    class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover disabled:opacity-50">
            Seguinte
            </button>
            <button type="submit" id="btnSubmit"
                    class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover hidden">
            Agendar
            </button>
        </div>
    </div>
  </form>
</div>

@vite('resources/js/pages/consultas-admin-create.js')
@endsection
