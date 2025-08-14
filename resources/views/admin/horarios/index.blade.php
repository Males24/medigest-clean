@extends('layouts.dashboard')
@section('title', __('messages.nav.schedules'))

@section('content')
<div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6">

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">@lang('messages.schedules.title')</h1>
      <p class="text-sm text-gray-500 mt-1">@lang('messages.schedules.subtitle')</p>
    </div>
    <a href="{{ route('admin.horarios.configurar') }}"
       class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-2xl text-white
              bg-home-medigest hover:bg-home-medigest-hover shadow-sm
              ring-1 ring-emerald-700/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
      @lang('messages.schedules.configure')
    </a>
  </div>

  @if (session('success'))
    <div class="rounded-xl px-4 py-3 bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200/60">
      {{ session('success') }}
    </div>
  @endif

  <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 overflow-x-auto">
    <table class="min-w-[720px] w-full text-sm text-left text-gray-700">
      <thead class="bg-gray-50 text-xs uppercase text-gray-500">
        <tr>
          <th class="px-6 py-3">@lang('messages.schedules.day')</th>
          <th class="px-6 py-3">@lang('messages.schedules.morning')</th>
          <th class="px-6 py-3">@lang('messages.schedules.afternoon')</th>
          <th class="px-6 py-3">@lang('messages.schedules.active')</th>
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
            <td class="px-6 py-3">{{ $h->ativo ? __('messages.common.yes') : __('messages.common.no') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
