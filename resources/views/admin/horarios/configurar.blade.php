@extends('layouts.dashboard')

@section('title', 'Configurar Horários')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Configurar Horários</h1>
      <p class="text-sm text-gray-500 mt-1">Seleciona os dias e define os horários de atendimento.</p>
    </div>
    <a href="{{ route('admin.horarios.index') }}" class="text-gray-600 hover:underline">Voltar</a>
  </div>

  <form method="POST" action="{{ route('admin.horarios.atualizarTodos') }}" class="space-y-8">
    @csrf   
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      
      {{-- Coluna 1: Checklist de dias --}}
      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-6">
        <h3 class="mb-4 text-lg font-medium text-gray-900">Horário semanal</h3>

        <label class="inline-flex items-center mb-3 cursor-pointer">
          <input id="check-all" type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
          <span class="ms-2 text-sm text-gray-900">Selecionar todos</span>
        </label>

        <div class="space-y-2">
          @php $diasSemana = \App\Models\ConfiguracaoHorario::diasSemana(); @endphp
          @foreach ($horarios as $h)
            <label class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-gray-50">
              <div class="flex items-center">
                <input type="checkbox" name="dias[]" value="{{ $h->dia_semana }}"
                       class="dia-check w-4 h-4 text-blue-600 border-gray-300 rounded">
                <span class="ms-2 text-sm text-gray-900">
                  {{ $diasSemana[$h->dia_semana] ?? $h->dia_semana }}
                </span>
              </div>
              <span class="text-xs text-gray-500">
                {{ $h->ativo ? 'Ativo' : 'Inativo' }}
              </span>
            </label>
          @endforeach
        </div>

        <p class="mt-4 text-xs text-gray-500">Seleciona os dias a que pretendes aplicar as alterações.</p>
        @error('dias') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      {{-- Coluna 2: Horários a aplicar --}}
      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-6 space-y-6">
        <div>
          <h3 class="mb-4 text-lg font-medium text-gray-900">Manhã</h3>
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="w-full">
              <label for="manha_inicio" class="block mb-1 text-sm font-medium">Início</label>
              <input type="time" id="manha_inicio" name="manha_inicio" class="w-full rounded-xl border-gray-300">
            </div>
            <div class="w-full">
              <label for="manha_fim" class="block mb-1 text-sm font-medium">Fim</label>
              <input type="time" id="manha_fim" name="manha_fim" class="w-full rounded-xl border-gray-300">
            </div>
          </div>
        </div>

        <div>
          <h3 class="mb-4 text-lg font-medium text-gray-900">Tarde</h3>
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="w-full">
              <label for="tarde_inicio" class="block mb-1 text-sm font-medium">Início</label>
              <input type="time" id="tarde_inicio" name="tarde_inicio" class="w-full rounded-xl border-gray-300">
            </div>
            <div class="w-full">
              <label for="tarde_fim" class="block mb-1 text-sm font-medium">Fim</label>
              <input type="time" id="tarde_fim" name="tarde_fim" class="w-full rounded-xl border-gray-300">
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3">
            <label class="inline-flex items-center cursor-pointer">
                {{-- Hidden para garantir envio de 0 se desmarcado --}}
                <input type="hidden" name="ativo" value="0">
                <input type="checkbox" name="ativo" value="1" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-home-medigest relative after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:w-5 after:h-5 after:bg-white after:rounded-full after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-medium text-gray-900">Ativo</span>
            </label>
            <span class="text-xs text-gray-500">Ativa os dias selecionados.</span>
        </div>

        <div class="pt-2">
          <button type="submit" class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover">
            Guardar alterações
          </button>
          <a href="{{ route('admin.horarios.index') }}" class="ml-3 text-gray-600 hover:underline">Cancelar</a>
        </div>
      </div>
    </div>
  </form>
</div>

{{-- Script selecionar todos --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const all = document.getElementById('check-all');
  const checks = document.querySelectorAll('.dia-check');
  all?.addEventListener('change', () => checks.forEach(c => c.checked = all.checked));
});
</script>
@endsection
