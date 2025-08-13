@extends('layouts.dashboard')

@section('title', 'Configurar Horários')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" data-page="horarios-admin-configurar">

  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Configurar Horários</h1>
      <p class="text-sm text-gray-500 mt-1">Seleciona os dias e define os horários de atendimento.</p>
    </div>
    <a href="{{ route('admin.horarios.index') }}" class="text-gray-600 hover:underline">Voltar</a>
  </div>

  {{-- Erros --}}
  @if ($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 text-red-700 p-4 text-sm">
      <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    // Defaults para preencher os inputs à chegada:
    $diaDefault = $horarios->firstWhere('ativo', true) ?? $horarios->first();
    $defMi = $diaDefault?->manha_inicio ? \Carbon\Carbon::parse($diaDefault->manha_inicio)->format('H:i') : '';
    $defMf = $diaDefault?->manha_fim    ? \Carbon\Carbon::parse($diaDefault->manha_fim)->format('H:i')    : '';
    $defTi = $diaDefault?->tarde_inicio ? \Carbon\Carbon::parse($diaDefault->tarde_inicio)->format('H:i') : '';
    $defTf = $diaDefault?->tarde_fim    ? \Carbon\Carbon::parse($diaDefault->tarde_fim)->format('H:i')    : '';
  @endphp

  <form method="POST" action="{{ route('admin.horarios.atualizarTodos') }}" class="space-y-8">
    @csrf
    @method('PUT')

    {{-- Grid principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- Coluna 1: Dias --}}
      <section class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-6 lg:sticky lg:top-6">
        <h3 class="mb-4 text-lg font-medium text-gray-900">Horário semanal</h3>

        {{-- Selecionar todos --}}
        <label class="flex items-center gap-2 mb-3 cursor-pointer select-none">
          <input id="check-all" type="checkbox"
                 class="w-4 h-4 rounded border-gray-300 text-home-medigest focus:ring-home-medigest">
          <span class="text-sm text-gray-900">Selecionar todos</span>
        </label>

        {{-- Lista de dias --}}
        <div class="divide-y divide-gray-100 border-y border-gray-100">
          @php $diasSemana = \App\Models\ConfiguracaoHorario::diasSemana(); @endphp
          @foreach ($horarios as $h)
            <label class="flex items-center gap-2 py-2 cursor-pointer hover:bg-gray-50 px-2 rounded-md">
              <input
                type="checkbox"
                name="dias[]"
                value="{{ $h->dia_semana }}"
                class="dia-check w-4 h-4 rounded border-gray-300 text-home-medigest focus:ring-home-medigest"
                @checked( (is_array(old('dias')) && in_array($h->dia_semana, old('dias'))) || (!old('dias') && $h->ativo) )
              >
              <span class="text-sm text-gray-900">
                {{ $diasSemana[$h->dia_semana] ?? $h->dia_semana }}
              </span>
            </label>
          @endforeach
        </div>

        <p class="mt-4 text-xs text-gray-500">
          Seleciona os dias a que pretendes aplicar as alterações.
        </p>
        @error('dias') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </section>

      {{-- Coluna 2: Horários a aplicar --}}
      <section class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-6 flex flex-col">
        <div class="space-y-6">
          {{-- Manhã --}}
          <div>
            <h3 class="mb-4 text-lg font-medium text-gray-900">Horário da Manhã</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="manha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                <div class="relative">
                  <div class="absolute inset-y-0 right-0 top-0 flex items-center pr-3.5 pointer-events-none">
                    {{-- ícone relógio --}}
                    <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 1 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <input type="time" id="manha_inicio" name="manha_inicio"
                         value="{{ old('manha_inicio', $defMi) }}"
                         class="w-full rounded-lg border border-gray-300 bg-gray-50 text-gray-900 text-sm leading-none p-2.5 pr-10 focus:ring-home-medigest focus:border-home-medigest">
                </div>
                @error('manha_inicio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
              </div>

              <div>
                <label for="manha_fim" class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                <div class="relative">
                  <div class="absolute inset-y-0 right-0 top-0 flex items-center pr-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 1 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <input type="time" id="manha_fim" name="manha_fim"
                         value="{{ old('manha_fim', $defMf) }}"
                         class="w-full rounded-lg border border-gray-300 bg-gray-50 text-gray-900 text-sm leading-none p-2.5 pr-10 focus:ring-home-medigest focus:border-home-medigest">
                </div>
                @error('manha_fim') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
              </div>
            </div>
          </div>

          <div class="h-px bg-gray-100"></div>

          {{-- Tarde --}}
          <div>
            <h3 class="mb-4 text-lg font-medium text-gray-900">Horário da Tarde</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="tarde_inicio" class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                <div class="relative">
                  <div class="absolute inset-y-0 right-0 top-0 flex items-center pr-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 1 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <input type="time" id="tarde_inicio" name="tarde_inicio"
                         value="{{ old('tarde_inicio', $defTi) }}"
                         class="w-full rounded-lg border border-gray-300 bg-gray-50 text-gray-900 text-sm leading-none p-2.5 pr-10 focus:ring-home-medigest focus:border-home-medigest">
                </div>
                @error('tarde_inicio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
              </div>

              <div>
                <label for="tarde_fim" class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                <div class="relative">
                  <div class="absolute inset-y-0 right-0 top-0 flex items-center pr-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 1 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <input type="time" id="tarde_fim" name="tarde_fim"
                         value="{{ old('tarde_fim', $defTf) }}"
                         class="w-full rounded-lg border border-gray-300 bg-gray-50 text-gray-900 text-sm leading-none p-2.5 pr-10 focus:ring-home-medigest focus:border-home-medigest">
                </div>
                @error('tarde_fim') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
              </div>
            </div>
          </div>
        </div>

        {{-- Ações (sempre no fundo) --}}
        <div class="mt-8 pt-4 border-t border-gray-100 flex items-center gap-3 ml-0 mt-auto">
          <button type="submit" id="btnSave"
                  class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover disabled:opacity-50 disabled:cursor-not-allowed">
            Guardar alterações
          </button>
          <a href="{{ route('admin.horarios.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
        </div>
      </section>
    </div>
  </form>
</div>

@vite('resources/js/pages/horarios-admin-configurar.js')
@endsection
