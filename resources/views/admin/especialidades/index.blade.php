@extends('layouts.dashboard')
@section('title','Especialidades')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Especialidades</h1>
      <p class="text-sm text-gray-500 mt-1">Gestão das especialidades médicas disponíveis no sistema.</p>
    </div>
    <a href="{{ route('admin.especialidades.create') }}"
       class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover shadow-sm">
      Criar Especialidade
    </a>
  </div>

  {{-- Mensagens --}}
  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
  @endif

  {{-- Tabela --}}
  <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 overflow-x-auto">
    <table class="min-w-full text-sm text-left text-gray-600">
      <thead class="bg-gray-50 text-xs uppercase text-gray-500">
        <tr>
          <th class="px-6 py-3">Nome</th>
          <th class="px-6 py-3 text-right">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse($especialidades as $e)
          <tr class="border-t border-gray-100 hover:bg-gray-50">
            <td class="px-6 py-3 font-medium">{{ $e->nome }}</td>
            <td class="px-6 py-3 text-right">
                <button
                    type="button"
                    class="inline-flex items-center gap-1 bg-zinc-100 text-zinc-700 px-3 py-1.5 rounded text-sm shadow hover:bg-zinc-200 js-espec-actions-btn"
                    data-edit-url="{{ route('admin.especialidades.edit', $e) }}"
                    data-destroy-url="{{ route('admin.especialidades.destroy', $e) }}"
                    data-nome="{{ $e->nome }}"
                >
                    Ação <span>▼</span>
                </button>
            </td>
          </tr>
        @empty
          <tr>
            <td class="px-6 py-4 text-gray-500" colspan="2">Sem especialidades ainda.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginação --}}
  <div class="mt-4">
    {{ $especialidades->links() }}
  </div>
</div>

@vite('resources/js/pages/especialidades-admin-index-dropdown.js')
@vite('resources/js/pages/especialidades-admin-index-modal.js')
@endsection
