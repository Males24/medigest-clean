@extends('layouts.app')

@section('title', 'Minhas Consultas')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h2 class="text-2xl font-semibold mb-6">Minhas Consultas</h2>

    <a href="{{ route('paciente.consultas.create') }}" class="mb-4 inline-block bg-home-medigest-button hover:bg-home-medigest-button-hover text-white px-4 py-2 rounded">Marcar Nova</a>

    <table class="w-full table-auto border-collapse border border-gray-200">
        <thead class="bg-zinc-200">
            <tr>
                <th class="px-4 py-2">Médico</th>
                <th class="px-4 py-2">Data</th>
                <th class="px-4 py-2">Hora</th>
                <th class="px-4 py-2">Estado</th>
                <th class="px-4 py-2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($consultas as $consulta)
            <tr class="border-t border-gray-200">
                <td class="px-4 py-2">{{ $consulta->medico->name ?? '-' }}</td>
                <td class="px-4 py-2">{{ $consulta->data }}</td>
                <td class="px-4 py-2">{{ $consulta->hora }}</td>
                <td class="px-4 py-2">{{ ucfirst($consulta->estado) }}</td>
                <td class="px-4 py-2">
                    @if ($consulta->estado === 'agendada')
                    <form method="POST" action="{{ route('paciente.consultas.cancelar', $consulta) }}">
                        @csrf
                        <button class="text-red-600 hover:underline" type="submit">Cancelar</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
