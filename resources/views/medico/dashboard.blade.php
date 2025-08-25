@extends('layouts.dashboard')

@section('title', 'Dashboard - Médico')

@php
  use Illuminate\Support\Facades\Auth;
  use Carbon\Carbon;

  $nomeMedico = Auth::user()->name ?? 'Médico/ª';

  $stats = $stats ?? [
    'hoje'           => $stats['hoje']           ?? null,
    'semana'         => $stats['semana']         ?? null,
    'canceladas_mes' => $stats['canceladas_mes'] ?? null,
    'pacientes_mes'  => $stats['pacientes_mes']  ?? null,
  ];

  /** @var \Illuminate\Support\Collection|\App\Models\Consulta[]|null $consultasHoje */
  /** @var \Illuminate\Support\Collection|\App\Models\Consulta[]|null $proximas */
  $consultasHoje = $consultasHoje ?? null;
  $proximas      = $proximas ?? null;
@endphp

@section('content')
<div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10" data-page="dashboard-medico">
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">
        Olá, {{ $nomeMedico }}
      </h1>
      <p class="text-sm text-gray-500 mt-1">
        Bem-vindo(a) ao seu painel. Acompanhe rapidamente as suas consultas e crie novas marcações.
      </p>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200/60">
      <div class="text-sm text-gray-500">Consultas hoje</div>
      <div class="mt-1 flex items-baseline gap-2">
        <div class="text-3xl font-semibold text-gray-900">{{ $stats['hoje'] ?? '—' }}</div>
        <span class="text-xs text-gray-400">{{ Carbon::today()->translatedFormat('d M') }}</span>
      </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200/60">
      <div class="text-sm text-gray-500">Próximos 7 dias</div>
      <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['semana'] ?? '—' }}</div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200/60">
      <div class="text-sm text-gray-500">Canceladas (mês)</div>
      <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['canceladas_mes'] ?? '—' }}</div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200/60">
      <div class="text-sm text-gray-500">Pacientes únicos (mês)</div>
      <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['pacientes_mes'] ?? '—' }}</div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <section class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-6">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-900">Próximas consultas</h2>
        <a href="{{ route('medico.consultas.index') }}" class="text-sm text-gray-700 hover:text-gray-900 hover:underline">Ver todas</a>
      </div>

      @if($consultasHoje && $consultasHoje->count())
        <div class="mt-4 flow-root">
          <ul role="list" class="-my-3 divide-y divide-gray-100">
            @foreach($consultasHoje as $c)
              <li class="py-3 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v5l3 3"/><circle cx="12" cy="12" r="10"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="text-sm font-medium text-gray-900 truncate">
                    {{ $c->paciente->name ?? 'Paciente' }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ optional($c->inicio)->translatedFormat('d M, H:i') }}
                    @php $tipoNome = $c->tipo->nome ?? ucfirst($c->tipo_slug ?? 'Normal'); @endphp
                    • {{ $tipoNome }}
                  </div>
                </div>
                @if(!empty($c->estado))
                  <span class="text-xs px-2 py-1 rounded-lg border border-gray-200 bg-gray-50 text-gray-700">{{ ucfirst($c->estado) }}</span>
                @endif
              </li>
            @endforeach
          </ul>
        </div>
      @else
        <div class="mt-6 text-sm text-gray-500">
          Não existem consultas para hoje?
          <a href="{{ route('medico.consultas.create') }}" class="text-emerald-700 hover:underline">Agendar agora</a>.
        </div>
      @endif
    </section>

    <section class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-6">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-900">Agenda</h2>
        <a href="{{ route('medico.calendario') }}" class="text-sm text-gray-700 hover:text-gray-900 hover:underline">Ver calendário</a>
      </div>

      @if($proximas && $proximas->count())
        <div class="mt-4 flow-root">
          <ul role="list" class="-my-3 divide-y divide-gray-100">
            @foreach($proximas as $c)
              <li class="py-3 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 ring-1 ring-blue-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="text-sm font-medium text-gray-900 truncate">
                    {{ $c->paciente->name ?? 'Paciente' }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ optional($c->inicio)->translatedFormat('d M, H:i') }}
                    @php $tipoNome = $c->tipo->nome ?? ucfirst($c->tipo_slug ?? 'Normal'); @endphp
                    • {{ $tipoNome }}
                  </div>
                </div>
                @if(!empty($c->estado))
                  <span class="text-xs px-2 py-1 rounded-lg border border-gray-200 bg-gray-50 text-gray-700">{{ ucfirst($c->estado) }}</span>
                @endif
              </li>
            @endforeach
          </ul>
        </div>
      @else
        <div class="mt-6 text-sm text-gray-500">
          Sem próximas consultas listadas. Veja todas em
          <a href="{{ route('medico.consultas.index') }}" class="text-emerald-700 hover:underline">Minhas Consultas</a>.
        </div>
      @endif
    </section>
  </div>
</div>
@endsection
