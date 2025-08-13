@extends('layouts.dashboard')
@section('title','Médicos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  
  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Médicos</h1>
      <p class="text-sm text-gray-500 mt-1">Lista de todos os médicos registados no sistema.</p>
    </div>
    <a href="{{ route('admin.medicos.create') }}" class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover">
      Criar Médico
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
  @endif

  <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 overflow-x-auto">
    <table class="min-w-full text-sm text-left text-gray-600">
      <thead class="bg-gray-50 text-xs uppercase text-gray-500">
        <tr>
          <th class="px-6 py-3">Nome</th>
          <th class="px-6 py-3">Email</th>
          <th class="px-6 py-3">CRM</th>
          <th class="px-6 py-3">Especialidades</th>
          <th class="px-6 py-3 text-right">Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach($medicos as $m)
          <tr class="border-t border-gray-100 hover:bg-gray-50">
            <td class="px-6 py-3">{{ $m->user->name }}</td>
            <td class="px-6 py-3">{{ $m->user->email }}</td>
            <td class="px-6 py-3">{{ $m->crm }}</td>
            <td class="px-6 py-3">
              {{ $m->especialidades->pluck('nome')->join(', ') ?: '—' }}
            </td>
            <td class="px-6 py-3 text-right">
            <button
                type="button"
                class="inline-flex items-center gap-1 bg-zinc-100 text-zinc-700 px-3 py-1.5 rounded text-sm shadow hover:bg-zinc-200 js-medico-actions-btn"
                data-edit-url="{{ route('admin.medicos.edit', $m) }}"
                data-destroy-url="{{ route('admin.medicos.destroy', $m) }}"
                data-nome="{{ $m->user->name }}"
            >
                Ação <span>▼</span>
            </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $medicos->links() }}
  </div>
</div>

@vite('resources/js/pages/medicos-admin-index-dropdown.js')
@vite('resources/js/pages/medicos-admin-index-modal.js')
@endsection
