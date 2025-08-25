@extends('layouts.dashboard')

@section('title', __('messages.consultas.list_title').' - Admin')

@php
  // Rótulo do intervalo para o botão (com base nos GETs)
  $de  = request('data_de');
  $ate = request('data_ate');
  $rangeLabel = 'Intervalo';
  if ($de && $ate && $de === now()->subDays(6)->toDateString() && $ate === now()->toDateString()) $rangeLabel = 'Últimos 7 dias';
  elseif ($de && $ate && $de === now()->subDays(29)->toDateString() && $ate === now()->toDateString()) $rangeLabel = 'Últimos 30 dias';
  elseif ($de && $ate && \Carbon\Carbon::parse($de)->isSameMonth(\Carbon\Carbon::today()) && \Carbon\Carbon::parse($ate)->isSameMonth(\Carbon\Carbon::today())) $rangeLabel = 'Este mês';
  elseif ($de && $ate) $rangeLabel = \Carbon\Carbon::parse($de)->format('d/m/Y').' – '.\Carbon\Carbon::parse($ate)->format('d/m/Y');
@endphp

@push('head')
  <style>
    .flt-menu{box-shadow:0 10px 30px rgba(2,6,23,.10),0 2px 8px rgba(2,6,23,.06)}
  </style>
@endpush

@section('content')
<div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6">

  {{-- Cabeçalho --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">@lang('messages.consultas.list_title')</h1>
      <p class="text-sm text-gray-500 mt-1">@lang('messages.consultas.list_subtitle')</p>
    </div>

    <a href="{{ route('admin.consultas.create') }}"
       class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-2xl text-white
              bg-home-medigest hover:bg-home-medigest-hover shadow-sm
              ring-1 ring-emerald-700/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
      @lang('messages.actions.new_consultation')
    </a>
  </div>

  {{-- Filtros (Intervalo + Busca + Aplicar/Limpar) --}}
  <form id="filtros" method="GET" class="space-y-3">
    <div class="flex flex-col md:flex-row md:items-center gap-3">

      {{-- Intervalo (dropdown que aplica automaticamente) --}}
      <div class="relative">
        <input type="hidden" name="data_de" id="f_de" value="{{ request('data_de') }}">
        <input type="hidden" name="data_ate" id="f_ate" value="{{ request('data_ate') }}">

        <button type="button" id="btnRange"
          class="inline-flex items-center gap-2 px-3.5 h-[44px] rounded-2xl
                 border border-gray-300 bg-white hover:bg-gray-50
                 ring-1 ring-gray-200/80
                 shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                 focus:outline-none focus-visible:ring-2 focus-visible:ring-home-medigest-button/40">
          <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
          <span id="rangeLabel" class="text-sm text-gray-800 whitespace-nowrap">{{ $rangeLabel }}</span>
          <svg class="w-4 h-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor"><path d="M5.25 7.5l4.5 4.5 4.5-4.5"/></svg>
        </button>

        <div id="menuRange" class="flt-menu hidden absolute z-50 mt-2 w-64 rounded-xl border border-gray-200 bg-white">
          <ul class="py-1 text-sm text-gray-800">
            <li><button type="button" data-range="today"     class="w-full text-left px-3 py-2 hover:bg-gray-50">Hoje</button></li>
            <li><button type="button" data-range="last7"     class="w-full text-left px-3 py-2 hover:bg-gray-50">Últimos 7 dias</button></li>
            <li><button type="button" data-range="last30"    class="w-full text-left px-3 py-2 hover:bg-gray-50">Últimos 30 dias</button></li>
            <li><button type="button" data-range="thisMonth" class="w-full text-left px-3 py-2 hover:bg-gray-50">Este mês</button></li>
            <li><button type="button" data-range="next7"     class="w-full text-left px-3 py-2 hover:bg-gray-50">Próximos 7 dias</button></li>
          </ul>
          <div class="border-t border-gray-100 p-3 space-y-2">
            <div class="text-xs text-gray-500">Personalizado</div>
            <div class="grid grid-cols-2 gap-2">
              <input type="date" id="customDe"  class="w-full h-9 px-2 rounded-lg border border-gray-300
              shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]">
              <input type="date" id="customAte" class="w-full h-9 px-2 rounded-lg border border-gray-300
              shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]">
            </div>
          </div>
        </div>
      </div>

      {{-- Busca com hover verde (tema) e foco destacado --}}
      <div class="flex-1">
        <div class="group relative rounded-2xl ring-1 ring-transparent transition
                    hover:ring-1 hover:ring-home-medigest-button/30
                    focus-within:ring-2 focus-within:ring-home-medigest-button/40">
          <input type="search" name="q" value="{{ request('q') }}"
                 class="w-full h-[44px] pl-11 pr-3 rounded-2xl border border-gray-300 bg-white
                        placeholder:text-gray-400 transition
                        shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                        hover:border-home-medigest-button-hover/60
                        focus:border-home-medigest-button focus:outline-none"
                 placeholder="Pesquisar por paciente, médico ou especialidade…">
          <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5
                      text-gray-400 group-focus-within:text-home-medigest"
               viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.3-4.3"/>
          </svg>
        </div>
      </div>

      {{-- Ações (profundidade consistente com o dashboard) --}}
      <div class="flex items-center gap-2">
        <button type="submit"
          class="inline-flex items-center gap-2 px-4 sm:px-5 h-[44px] rounded-2xl text-white
                 bg-home-medigest hover:bg-home-medigest-hover
                 shadow-sm ring-1 ring-emerald-700/20
                 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40
                 active:translate-y-px">
          Aplicar
        </button>

        <a href="{{ route('admin.consultas.index') }}"
           class="inline-flex items-center gap-2 px-4 sm:px-5 h-[44px] rounded-2xl text-slate-800
                  border border-white/50 ring-1 ring-gray-200/80
                  bg-gradient-to-b from-gray-50 to-gray-100
                  shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                  hover:from-white hover:to-gray-50 hover:ring-gray-300/80
                  active:translate-y-px">
          Limpar
        </a>
      </div>
    </div>
  </form>

  {{-- Mensagens --}}
  @if(session('success'))
    <div class="rounded-xl px-4 py-3 bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200/60">
      {{ session('success') }}
    </div>
  @endif

  {{-- Tabela --}}
  <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 overflow-x-auto">
    <table class="min-w-[980px] w-full text-sm text-left text-gray-700">
      <thead class="bg-gray-50 text-xs uppercase text-gray-500">
        <tr>
          <th class="px-6 py-3">@lang('messages.consultas.patient')</th>
          <th class="px-6 py-3">@lang('messages.consultas.doctor')</th>
          <th class="px-6 py-3">@lang('messages.consultas.specialty')</th>
          <th class="px-6 py-3">@lang('messages.consultas.date')</th>
          <th class="px-6 py-3">@lang('messages.consultas.time')</th>
          <th class="px-6 py-3">@lang('messages.consultas.status')</th>
          <th class="px-6 py-3 text-right">@lang('messages.consultas.actions')</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($consultas as $consulta)
          @php
            $estado = strtolower($consulta->estado ?? '');
            $estadoClass = [
              'confirmada'=>'bg-emerald-600','confirmado'=>'bg-emerald-600',
              'agendada'=>'bg-emerald-500','pendente'=>'bg-amber-500',
              'pendente_medico'=>'bg-violet-500','cancelada'=>'bg-rose-500','cancelado'=>'bg-rose-500',
            ][$estado] ?? 'bg-gray-500';

            $estadoKey = [
              'confirmada'=>'confirmed','confirmado'=>'confirmed','agendada'=>'scheduled',
              'pendente'=>'pending','pendente_medico'=>'pending_doctor','cancelada'=>'canceled','cancelado'=>'canceled',
            ][$estado] ?? null;

            $estadoDisplay = $estadoKey ? __('messages.status.'.$estadoKey) : ($consulta->estado ?? '-');
          @endphp

          <tr class="border-t border-gray-100 hover:bg-gray-50">
            <td class="px-6 py-3 max-w-[28ch] truncate">{{ $consulta->paciente->name ?? __('messages.common.none') }}</td>
            <td class="px-6 py-3 max-w-[28ch] truncate">{{ $consulta->medico->name ?? __('messages.common.none') }}</td>
            <td class="px-6 py-3 max-w-[28ch] truncate">{{ $consulta->especialidade->nome ?? __('messages.common.none') }}</td>
            <td class="px-6 py-3">{{ \Carbon\Carbon::parse($consulta->data)->format('d/m/Y') }}</td>
            <td class="px-6 py-3">{{ $consulta->hora }}</td>
            <td class="px-6 py-3">
              <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white {{ $estadoClass }}">
                {{ $estadoDisplay }}
              </span>
            </td>
            <td class="px-6 py-3 text-right">
              @php
                $payload = [
                  'data_consulta'      => \Carbon\Carbon::parse($consulta->data)->format('d/m/Y') . ' ' . ($consulta->hora ?? ''),
                  'paciente_nome'      => $consulta->paciente->name ?? '-',
                  'paciente_email'     => $consulta->paciente->email ?? '-',
                  'descricao'          => $consulta->motivo ?? '-',
                  'medico_nome'        => $consulta->medico->name ?? '-',
                  'especialidade_nome' => $consulta->especialidade->nome ?? '-',
                  'estado'             => $estadoDisplay,
                  'estado_key'         => $estadoKey,
                ];
              @endphp

              <button
                type="button"
                class="js-consulta-actions-btn inline-flex items-center gap-1.5 rounded-2xl px-3 py-1.5 text-sm
                       text-slate-800 border border-white/50 ring-1 ring-gray-200/80
                       bg-gradient-to-b from-gray-50 to-gray-100
                       shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                       hover:from-white hover:to-gray-50 hover:ring-gray-300/80 transition">
                {{ __('messages.common.action') }} <span class="text-gray-500">▼</span>
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Paginação --}}
  <div>
    {{ $consultas->appends(request()->query())->links() }}
  </div>
</div>

@vite('resources/js/pages/admin/consultas/consultas-admin-index-modal.js')
@vite('resources/js/pages/admin/consultas/consultas-admin-index-dropdown.js')
@vite('resources/js/pages/admin/consultas/consultas-admin-index-filters.js')
@endsection
