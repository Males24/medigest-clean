@extends('layouts.dashboard')
@section('title','Criar Especialidade')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  
  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Nova Especialidade</h1>
        <p class="text-sm text-gray-500 mt-1">Adiciona uma nova especialidade médica ao sistema.</p>
    </div>
    <a href="{{ route('admin.especialidades.index') }}" class="text-gray-600 hover:underline">Voltar</a>
  </div>

  {{-- Formulário --}}
  <form action="{{ route('admin.especialidades.store') }}" method="POST"
        class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-6 space-y-6">
    @csrf

    <div>
      <x-form.input
        id="nome"
        name="nome"
        label="Nome da Especialidade"
        placeholder="Ex.: Cardiologia"
        icon="components.icons.admin.book"
        class="block text-sm font-medium text-gray-700 mb-1"
        />
        @error('nome')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-3 pt-2">
      <button class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover">Criar</button>
      <a href="{{ route('admin.especialidades.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
    </div>
  </form>
</div>
@endsection
