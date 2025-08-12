@extends('layouts.app')

@section('title','Marcar Consulta')

@push('head')
<meta name="api-slots" content="{{ route('api.slots') }}">
@endpush

@section('content')
<div class="max-w-3xl mx-auto py-8">
  <h1 class="text-2xl font-semibold mb-6">Marcar consulta</h1>

  <form method="POST" action="{{ route('paciente.consultas.store') }}" id="form-paciente-create" class="space-y-5">
    @csrf

    {{-- duração fixa --}}
    <input type="hidden" id="duracao" name="duracao" value="30">

    {{-- Médico (usa users.id) --}}
    <label class="block">
      <span class="text-sm font-medium">Médico</span>
      <select name="medico_id" id="medico_id" class="mt-1 w-full border rounded p-2" required>
        <option value="">— Seleciona —</option>
        @foreach($medicos as $m)
          <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
        @endforeach
      </select>
      @error('medico_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </label>

    {{-- Especialidade (id) --}}
    <label class="block">
      <span class="text-sm font-medium">Especialidade</span>
      <input type="number" name="especialidade_id" class="mt-1 w-full border rounded p-2" placeholder="ID da especialidade" required>
      @error('especialidade_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </label>

    {{-- Tipo --}}
    <label class="block">
      <span class="text-sm font-medium">Tipo</span>
      <select id="tipo_slug" name="tipo_slug" class="mt-1 w-full border rounded p-2">
        <option value="normal">Normal</option>
        <option value="prioritaria">Prioritária</option>
        <option value="urgente">Urgente</option>
      </select>
      @error('tipo_slug')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </label>

    {{-- Data --}}
    <label class="block">
      <span class="text-sm font-medium">Data</span>
      <input type="date" id="data" name="data" class="mt-1 w-full border rounded p-2" required>
      @error('data')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </label>

    {{-- Slots --}}
    <label class="block">
      <span class="text-sm font-medium">Hora</span>
      <select id="hora" name="hora" class="mt-1 w-full border rounded p-2" required>
        <option value="">— seleciona médico e data —</option>
      </select>
      <p id="slotsMsg" class="text-xs text-gray-500 mt-1">Escolhe médico e data para ver horas disponíveis.</p>
      @error('hora')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </label>

    {{-- Motivo --}}
    <label class="block">
      <span class="text-sm font-medium">Descrição do problema (opcional)</span>
      <input type="text" name="motivo" class="mt-1 w-full border rounded p-2" placeholder="Ex.: dor de cabeça persistente">
    </label>

    <button class="px-4 py-2 bg-blue-600 text-white rounded">Confirmar</button>
  </form>
</div>

@vite('resources/js/pages/consultas-paciente-create.js')
@endsection
