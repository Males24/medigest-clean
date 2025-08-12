@extends('layouts.dashboard')

@section('title', 'Minhas Consultas')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h2 class="text-2xl font-semibold mb-6">Minhas Consultas</h2>

    @if(session('success'))
        <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded bg-red-100 text-red-800 px-4 py-2">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- BTN CORRETO: rota do MÉDICO --}}
    <a href="{{ route('medico.consultas.create') }}"
       class="mb-4 inline-block bg-home-medigest-button hover:bg-home-medigest-button-hover text-white px-4 py-2 rounded">
        Marcar Nova
    </a>

    <div class="overflow-x-auto bg-white rounded border border-gray-200">
        <table class="w-full table-auto">
            <thead class="bg-zinc-200">
                <tr>
                    <th class="px-4 py-2 text-left">Paciente</th>
                    <th class="px-4 py-2 text-left">Data</th>
                    <th class="px-4 py-2 text-left">Hora</th>
                    <th class="px-4 py-2 text-left">Estado</th>
                    <th class="px-4 py-2 text-left">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($consultas as $consulta)
                    <tr class="border-t border-gray-200">
                        <td class="px-4 py-2">
                            {{ $consulta->paciente->name
                                ?? $consulta->pacienteUser->name
                                ?? optional(optional($consulta->pacientePerfil)->user)->name
                                ?? '-' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($consulta->data)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-2">{{ $consulta->hora }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-block bg-zinc-100 text-zinc-800 text-xs px-2 py-1 rounded">
                                {{ ucfirst(str_replace('_',' ', $consulta->estado ?? 'agendada')) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            @if (($consulta->estado ?? 'agendada') === 'agendada')
                                {{-- CORRETO: cancelar do MÉDICO --}}
                                <form method="POST" action="{{ route('medico.consultas.cancelar', $consulta) }}">
                                    @csrf
                                    <button class="text-red-600 hover:underline" type="submit">
                                        Cancelar
                                    </button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-4 text-center text-gray-500" colspan="5">Sem consultas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ method_exists($consultas, 'links') ? $consultas->links() : '' }}
    </div>
</div>
@endsection
