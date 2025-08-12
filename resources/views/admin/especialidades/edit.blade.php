@extends('layouts.dashboard')
@section('title','Editar Especialidade')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Editar Especialidade</h1>
        <p class="text-sm text-gray-500 mt-1">Altera o nome da especialidade selecionada.</p>
    </div>
    <a href="{{ route('admin.especialidades.index') }}" class="text-gray-600 hover:underline">Voltar</a>
  </div>

  {{-- Formulário --}}
  <form action="{{ route('admin.especialidades.update', $especialidade) }}" method="POST"
        class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-6 space-y-6">
    @csrf @method('PUT')

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
      <input type="text" name="nome" value="{{ old('nome', $especialidade->nome) }}"
             class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button"
             required>
      @error('nome') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3 pt-2">
      <button class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover">Guardar</button>
      <a href="{{ route('admin.especialidades.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
    </div>
  </form>
</div>
@endsection
