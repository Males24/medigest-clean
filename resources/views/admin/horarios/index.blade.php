@extends('layouts.dashboard')

@section('title', 'Horários')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Horários</h1>
      <p class="text-sm text-gray-500 mt-1">Visualiza os horários de atendimento atuais.</p>
    </div>
    <a href="{{ route('admin.horarios.configurar') }}"
       class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover shadow-sm">
      Configurar Horários
    </a>
  </div>

  @if (session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
  @endif

  {{-- Tabela --}}
  <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 overflow-x-auto">
    <table class="min-w-full text-sm text-left text-gray-600">
      <thead class="bg-gray-50 text-xs uppercase text-gray-500">
        <tr>
          <th class="px-6 py-3">Dia</th>
          <th class="px-6 py-3">Manhã</th>
          <th class="px-6 py-3">Tarde</th>
          <th class="px-6 py-3">Ativo</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($horarios as $h)
          <tr class="border-t border-gray-100 hover:bg-gray-50">
            <td class="px-6 py-3">{{ \App\Models\ConfiguracaoHorario::diasSemana()[$h->dia_semana] ?? $h->dia_semana }}</td>
            <td class="px-6 py-3">
              {{ $h->manha_inicio ? \Carbon\Carbon::parse($h->manha_inicio)->format('H:i') : '—' }} —
              {{ $h->manha_fim ? \Carbon\Carbon::parse($h->manha_fim)->format('H:i') : '—' }}
            </td>
            <td class="px-6 py-3">
              {{ $h->tarde_inicio ? \Carbon\Carbon::parse($h->tarde_inicio)->format('H:i') : '—' }} —
              {{ $h->tarde_fim ? \Carbon\Carbon::parse($h->tarde_fim)->format('H:i') : '—' }}
            </td>
            <td class="px-6 py-3">{{ $h->ativo ? 'Sim' : 'Não' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
