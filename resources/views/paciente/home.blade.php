@extends('layouts.app')
@section('title', 'Área do Paciente | MediGest+')

@php
  use Illuminate\Support\Facades\Route;
  use Carbon\Carbon;
  use App\Notifications\ConsultaConfirmada as NConfirmada;

  $r = fn(string $name, array $params = [], string $fallback = '#')
      => Route::has($name) ? route($name, $params) : $fallback;

  // --- helper seguro para construir datetime ---
  $buildDT = function ($data = null, $hora = null) {
      if (!$data) return null;
      try {
          $dt = Carbon::parse($data);                 // aceita date ou datetime
          if ($hora) $dt->setTimeFromTimeString($hora); // só aplica hora se vier separada
          return $dt;
      } catch (\Throwable $e) {
          return null;
      }
  };

  $proximas = collect($proximas ?? []);
  $stats = array_merge(['futuras'=>$proximas->count(), 'pendentes'=>0], (array)($stats ?? []));

  // ordenar pelas datas reais sem concatenar strings
  $proxima = $proximas->sortBy(
      fn($c) => ($buildDT($c->data ?? null, $c->hora ?? null)?->timestamp) ?? PHP_INT_MAX
  )->first();

  $estadoChip = [
    'confirmed'=>'bg-emerald-600','scheduled'=>'bg-emerald-500','pending'=>'bg-amber-500',
    'pending_doctor'=>'bg-violet-500','canceled'=>'bg-rose-500',
  ];
  $tEstado = fn($raw,$key) => $key ? __('messages.status.'.$key) : ($raw ?? '-');
  $fmtData = fn($d) => $d ? Carbon::parse($d)->format('d/m/Y') : '—';

  // --- ALERTA: consulta confirmada (mostra o mais recente não lido) ---
  $notifConf = auth()->check()
      ? auth()->user()->unreadNotifications
          ->where('type', NConfirmada::class)
          ->sortByDesc('created_at')
          ->first()
      : null;

  if ($notifConf) {
    $ndata      = $notifConf->data ?? [];
    $conf_data  = $fmtData($ndata['data'] ?? null);
    $conf_hora  = $ndata['hora'] ?? '';
    $conf_med   = $ndata['medico_nome'] ?? '—';
    $conf_esp   = $ndata['especialidade'] ?? '—';
    $dismissUrl = $r('paciente.alerts.dismiss', ['notification' => $notifConf->id]);

    // payload para abrir o modal a partir do alerta (sem navegar)
    $payloadAlert = [
      'data_consulta'      => trim(($fmtData($ndata['data'] ?? null)).' '.($ndata['hora'] ?? '')),
      'paciente_nome'      => auth()->user()->name ?? '-',
      'paciente_email'     => auth()->user()->email ?? '-',
      'descricao'          => $ndata['descricao'] ?? '-',
      'medico_nome'        => $conf_med,
      'especialidade_nome' => $conf_esp,
      'estado_key'         => 'scheduled', // após confirmação
    ];
  }
@endphp

@push('head')
  <style>
    .chip{display:inline-flex;align-items:center;border-radius:9999px;padding:.125rem .5rem;font-size:.6875rem;font-weight:600;color:#fff}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:.75rem}
    .card-h{padding:.875rem 1.25rem;border-bottom:1px solid #e5e7eb}
    .card-b{padding:1rem 1.25rem}
    @media (min-width:640px){.card-h{padding:1rem 1.25rem}.card-b{padding:1.25rem}}
  </style>
@endpush

@section('content')
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início','url'=>route('home')],
    ['label'=>'Área do paciente']
  ]" />
  <x-ui.hero
    title="A sua área do paciente"
    subtitle="Marque consultas, acompanhe o histórico e atualize os seus dados com segurança."
    height="160px"
  />

  <div class="bg-zinc-50">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

      {{-- ALERTA: Consulta confirmada --}}
      @if(!empty($notifConf))
        <div id="alert-{{ $notifConf->id }}"
             class="p-4 mb-2 text-emerald-800 border border-emerald-300 rounded-lg bg-emerald-50"
             role="alert">
          <div class="flex items-center">
            <svg class="shrink-0 w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <h3 class="text-lg font-medium">Consulta confirmada</h3>
          </div>
          <div class="mt-2 mb-4 text-sm">
            A sua consulta com <strong>{{ $conf_med }}</strong>
            ({{ $conf_esp }}) em <strong>{{ $conf_data }}{{ $conf_hora ? ' • '.$conf_hora : '' }}</strong> foi confirmada.
          </div>
          <div class="flex">
            {{-- BOTÃO: abre o modal com os detalhes via JS (não navega) --}}
            <a href="{{ $r('paciente.consultas.index') }}"
               class="text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-xs px-3 py-1.5 mr-2 inline-flex items-center js-consulta-det"
               data-payload='@json($payloadAlert ?? [], JSON_UNESCAPED_UNICODE)'>
              <svg class="mr-2 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 14">
                <path d="M10 0C4.612 0 0 5.336 0 7c0 1.742 3.546 7 10 7 6.454 0 10-5.258 10-7 0-1.664-4.612-7-10-7Zm0 10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
              </svg>
              Ver detalhes
            </a>

            <form method="POST" action="{{ $dismissUrl }}">
              @csrf
              <button type="submit"
                      class="text-emerald-800 bg-transparent border border-emerald-800 hover:bg-emerald-800 hover:text-white focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-xs px-3 py-1.5">
                Dismiss
              </button>
            </form>
          </div>
        </div>
      @endif

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Próxima consulta --}}
        <section class="card lg:col-span-2" aria-labelledby="h-next">
          <header class="card-h flex items-center justify-between">
            <h2 id="h-next" class="text-lg font-semibold text-zinc-900">Próxima consulta</h2>
            <a href="{{ $r('paciente.consultas.todas') }}" class="text-sm text-emerald-700 hover:underline">ver todas</a>
          </header>
          <div class="card-b">
            @if($proxima)
              @php
                $dt  = $fmtData($proxima->data ?? null);
                $hr  = $proxima->hora ?? null;
                $med = $proxima->medico->name ?? $proxima['medico_nome'] ?? '—';
                $esp = $proxima->especialidade->nome ?? $proxima['especialidade_nome'] ?? null;

                $estadoRaw = strtolower($proxima->estado ?? '');
                $estadoKey = [
                  'confirmada'=>'confirmed','confirmado'=>'confirmed','agendada'=>'scheduled',
                  'pendente'=>'pending','pendente_medico'=>'pending_doctor','cancelada'=>'canceled','cancelado'=>'canceled',
                ][$estadoRaw] ?? ($proxima->estado_key ?? null);
                $chip = $estadoChip[$estadoKey] ?? 'bg-zinc-500';

                // usa o helper seguro (evita "Double time specification")
                $startsAt = $buildDT($proxima->data ?? null, $proxima->hora ?? null);
                $agora    = Carbon::now();
                $janelaTele = $startsAt && $startsAt->isBetween($agora->copy()->subMinutes(10), $agora->copy()->addMinutes(15));
                $isRemoto   = (bool) ($proxima->remota ?? false);

                // payload para o modal
                $payloadProx = [
                  'data_consulta'      => trim(($fmtData($proxima->data ?? null)).' '.($proxima->hora ?? '')),
                  'paciente_nome'      => auth()->user()->name ?? '-',
                  'paciente_email'     => auth()->user()->email ?? '-',
                  'descricao'          => $proxima->motivo ?? '-',
                  'medico_nome'        => $med,
                  'especialidade_nome' => $esp ?? '-',
                  'estado_key'         => $estadoKey,
                ];
              @endphp

              <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="min-w-0">
                  <div class="flex flex-wrap items-center gap-2">
                    <span class="text-base font-medium text-zinc-900 truncate">{{ $med }}</span>
                    <span class="chip {{ $chip }}">{{ $tEstado($proxima->estado ?? null, $estadoKey) }}</span>
                  </div>
                  <div class="mt-1 text-sm text-zinc-600">
                    {{ $dt }} @if($hr) • {{ $hr }} @endif @if($esp) • {{ $esp }} @endif
                  </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                  @if($isRemoto && $janelaTele)
                    <a href="{{ $r('teleconsulta.iniciar', ['consulta' => $proxima->id ?? null]) }}"
                       class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-white bg-emerald-700 hover:bg-emerald-800">
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14"/>
                        <rect x="3" y="5" width="12" height="14" rx="2"/>
                      </svg>
                      Entrar na teleconsulta
                    </a>
                  @endif
                  {{-- Abrir modal de detalhes --}}
                  <a href="{{ $r('paciente.consultas.index') }}"
                     class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-zinc-300 bg-white hover:bg-zinc-50 js-consulta-det"
                     data-payload='@json($payloadProx, JSON_UNESCAPED_UNICODE)'>
                    Detalhes
                  </a>
                </div>
              </div>
            @else
              <div class="text-center text-zinc-600 py-8">
                <p class="mb-3">Não tem consultas próximas.</p>
                <a href="{{ $r('paciente.consultas.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white bg-emerald-700 hover:bg-emerald-800">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                  Marcar consulta
                </a>
              </div>
            @endif
          </div>
        </section>

        {{-- KPIs --}}
        <aside class="card" aria-labelledby="h-kpis">
          <header class="card-h"><h2 id="h-kpis" class="text-lg font-semibold text-zinc-900">Resumo</h2></header>
          <div class="card-b">
            <ul class="grid grid-cols-2 gap-3 text-sm">
              <li class="rounded-lg border border-zinc-200 p-3">
                <div class="text-zinc-500">Consultas futuras</div>
                <div class="text-xl font-semibold text-zinc-900">{{ $stats['futuras'] }}</div>
              </li>
              <li class="rounded-lg border border-zinc-200 p-3">
                <div class="text-zinc-500">Pendentes</div>
                <div class="text-xl font-semibold text-zinc-900">{{ $stats['pendentes'] }}</div>
              </li>
            </ul>
            <div class="mt-4 text-xs text-zinc-500">Última atualização: {{ now()->format('d/m/Y H:i') }}</div>
          </div>
        </aside>
      </div>

      {{-- Próximas (lista curta) --}}
      <section class="card" aria-labelledby="h-proximas">
        <header class="card-h flex items-center justify-between">
          <h2 id="h-proximas" class="text-lg font-semibold text-zinc-900">Próximas consultas</h2>
          <a href="{{ $r('paciente.consultas.index') }}" class="text-sm text-emerald-700 hover:underline">ver agenda</a>
        </header>

        @if($proximas->count())
          <ul class="divide-y divide-zinc-200">
            @foreach($proximas->take(4) as $c)
              @php
                $dt  = $fmtData($c->data ?? null);
                $hr  = $c->hora ?? null;
                $med = $c->medico->name ?? $c['medico_nome'] ?? '—';
                $esp = $c->especialidade->nome ?? $c['especialidade_nome'] ?? null;

                $estadoRaw = strtolower($c->estado ?? '');
                $estadoKey = [
                  'confirmada'=>'confirmed','confirmado'=>'confirmed','agendada'=>'scheduled',
                  'pendente'=>'pending','pendente_medico'=>'pending_doctor','cancelada'=>'canceled','cancelado'=>'canceled',
                ][$estadoRaw] ?? ($c->estado_key ?? null);
                $chip = $estadoChip[$estadoKey] ?? 'bg-zinc-500';

                // payload para o modal
                $payloadItem = [
                  'data_consulta'      => trim(($fmtData($c->data ?? null)).' '.($c->hora ?? '')),
                  'paciente_nome'      => auth()->user()->name ?? '-',
                  'paciente_email'     => auth()->user()->email ?? '-',
                  'descricao'          => $c->motivo ?? '-',
                  'medico_nome'        => $med,
                  'especialidade_nome' => $esp ?? '-',
                  'estado_key'         => $estadoKey,
                ];
              @endphp
              <li class="card-b">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                  <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="font-medium text-zinc-900 truncate">{{ $med }}</span>
                      <span class="chip {{ $chip }}">{{ $tEstado($c->estado ?? null, $estadoKey) }}</span>
                    </div>
                    <div class="text-sm text-zinc-600">
                      {{ $dt }} @if($hr) • {{ $hr }} @endif @if($esp) • {{ $esp }} @endif
                    </div>
                  </div>
                  {{-- Abrir modal de detalhes --}}
                  <a href="{{ $r('paciente.consultas.index') }}"
                     class="text-sm text-emerald-700 hover:underline shrink-0 js-consulta-det"
                     data-payload='@json($payloadItem, JSON_UNESCAPED_UNICODE)'>
                    detalhes
                  </a>
                </div>
              </li>
            @endforeach
          </ul>
        @else
          <div class="card-b text-zinc-600">Sem itens para mostrar.</div>
        @endif
      </section>

      {{-- Serviços --}}
      <section aria-labelledby="h-servicos">
        <h2 id="h-servicos" class="text-lg font-semibold text-zinc-900 mb-3">Serviços</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <a href="{{ $r('paciente.consultas.index') }}" class="group card p-4 hover:bg-zinc-50">
            <div class="font-medium text-zinc-900">Consulta Externa</div>
            <p class="mt-1 text-sm text-zinc-600">Agende atendimento presencial ou por vídeo, de forma simples e segura.</p>
          </a>
          <a href="{{ $r('paciente.canais.index') }}" class="group card p-4 hover:bg-zinc-50">
            <div class="font-medium text-zinc-900">Canais</div>
            <p class="mt-1 text-sm text-zinc-600">Fale connosco: marcações, apoio ao cliente e urgências.</p>
          </a>
          <!-- <a href="{{ $r('paciente.resultados') }}" class="group card p-4 hover:bg-zinc-50">
            <div class="font-medium text-zinc-900">Exames & Resultados</div>
            <p class="mt-1 text-sm text-zinc-600">Aceda aos relatórios e históricos.</p>
          </a>
          <a href="{{ $r('paciente.receitas') }}" class="group card p-4 hover:bg-zinc-50">
            <div class="font-medium text-zinc-900">Receitas eletrónicas</div>
            <p class="mt-1 text-sm text-zinc-600">Reemissões e instruções de uso.</p>
          </a>
          <a href="{{ $r('paciente.pagamentos') }}" class="group card p-4 hover:bg-zinc-50">
            <div class="font-medium text-zinc-900">Pagamentos & faturas</div>
            <p class="mt-1 text-sm text-zinc-600">Métodos, recibos e histórico.</p>
          </a> -->
        </div>
      </section>

    </div>
  </div>

@vite('resources/js/pages/paciente/home-paciente-modal.js')
@endsection
