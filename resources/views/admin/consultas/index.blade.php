@extends('layouts.dashboard')

@section('title', 'Consultas - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Consultas</h1>
      <p class="text-sm text-gray-500 mt-1">Lista de todas as consultas registadas no sistema.</p>
    </div>
    <a href="{{ route('admin.consultas.create') }}"
       class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover shadow-sm">
      Nova Consulta
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
          <th class="px-6 py-3">Paciente</th>
          <th class="px-6 py-3">Médico</th>
          <th class="px-6 py-3">Especialidade</th>
          <th class="px-6 py-3">Data</th>
          <th class="px-6 py-3">Hora</th>
          <th class="px-6 py-3">Estado</th>
          <th class="px-6 py-3 text-right">Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($consultas as $consulta)
          <tr class="border-t border-gray-100 hover:bg-gray-50">
            <td class="px-6 py-3">{{ $consulta->paciente->name ?? '-' }}</td>
            <td class="px-6 py-3">{{ $consulta->medico->name ?? '-' }}</td>
            <td class="px-6 py-3">{{ $consulta->especialidade->nome ?? '-' }}</td>
            <td class="px-6 py-3">{{ \Carbon\Carbon::parse($consulta->data)->format('d/m/Y') }}</td>
            <td class="px-6 py-3">{{ $consulta->hora }}</td>
            <td class="px-6 py-3">
                <span class="inline-block bg-zinc-100 text-zinc-800 text-xs px-2 py-1 rounded">
                    {{ ucfirst($consulta->estado) }}
                </span>
            </td>
            <td class="px-6 py-3 text-right space-x-3">
            @php
                $payload = [
                'data_consulta'      => \Carbon\Carbon::parse($consulta->data)->format('d/m/Y') . ' ' . ($consulta->hora ?? ''),
                'paciente_nome'      => $consulta->paciente->name ?? '-',
                'paciente_email'     => $consulta->paciente->email ?? '-',
                'descricao'          => $consulta->motivo ?? '-',
                'medico_nome'        => $consulta->medico->name ?? '-',
                'especialidade_nome' => $consulta->especialidade->nome ?? '-',
                'estado'             => ucfirst($consulta->estado ?? '-'),
                ];
            @endphp

            <button
                type="button"
                class="text-blue-600 hover:underline"
                onclick='mostrarModalConsulta(@json($payload))'>
                Ver
            </button>

            @if ($consulta->estado === 'agendada')
                <button type="button" class="text-red-600 hover:underline"
                    onclick="confirmarCancelamento({ action: '{{ route('admin.consultas.cancelar', $consulta) }}', csrf: '{{ csrf_token() }}' })">
                    Cancelar
                </button>
            @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@vite('resources/js/pages/consultas-admin-index-modal.js')
@endsection


