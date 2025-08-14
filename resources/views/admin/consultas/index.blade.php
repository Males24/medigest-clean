@extends('layouts.dashboard')

@section('title', __('messages.consultas.list_title').' - Admin')

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
              'confirmada'       => 'bg-emerald-600',
              'confirmado'       => 'bg-emerald-600',
              'agendada'         => 'bg-emerald-500',
              'pendente'         => 'bg-amber-500',
              'pendente_medico'  => 'bg-violet-500',
              'cancelada'        => 'bg-rose-500',
              'cancelado'        => 'bg-rose-500',
            ][$estado] ?? 'bg-gray-500';

            // mapear os estados para chaves de tradução
            $estadoKey = [
              'confirmada' => 'confirmed',
              'confirmado' => 'confirmed',
              'agendada'   => 'scheduled',
              'pendente'   => 'pending',
              'pendente_medico' => 'pending_doctor',
              'cancelada'  => 'canceled',
              'cancelado'  => 'canceled',
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
                class="js-consulta-actions-btn inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-sm
                       text-slate-800 border border-white/50 ring-1 ring-gray-200/80
                       bg-gradient-to-b from-gray-50 to-gray-100
                       shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                       hover:from-white hover:to-gray-50 hover:ring-gray-300/80 transition"
                data-payload='@json($payload)'
                data-cancel-url="{{ $consulta->estado === 'agendada' ? route('admin.consultas.cancelar', $consulta) : '' }}"
                data-has-cancel="{{ $consulta->estado === 'agendada' ? '1' : '0' }}"
              >
                {{ __('messages.common.action') }} <span class="text-gray-500">▼</span>
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@vite('resources/js/pages/consultas-admin-index-modal.js')
@vite('resources/js/pages/consultas-admin-index-dropdown.js')
@endsection
