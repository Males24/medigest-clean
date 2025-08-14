@extends('layouts.dashboard')
@section('title', 'Dashboard Admin')

@section('content')
<div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6">

  <div>
    <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">
      {{ __('messages.admin.welcome', ['name' => auth()->user()->name]) }}
    </h1>
    <p class="text-sm text-gray-500 mt-1">@lang('messages.admin.overview')</p>
  </div>

  <section aria-labelledby="kpis"
           class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    <article class="bg-white rounded-lg sm:rounded-xl shadow-sm p-4 sm:p-5 min-w-0">
      <p class="text-xs sm:text-sm text-gray-500">@lang('messages.dashboard.kpi_users')</p>
      <p class="text-2xl sm:text-3xl font-semibold text-gray-900">
        {{ number_format($totUsers) }}
      </p>
    </article>

    <article class="bg-white rounded-lg sm:rounded-xl shadow-sm p-4 sm:p-5 min-w-0">
      <p class="text-xs sm:text-sm text-gray-500">@lang('messages.dashboard.kpi_doctors')</p>
      <p class="text-2xl sm:text-3xl font-semibold text-gray-900">
        {{ number_format($totMedicos) }}
      </p>
    </article>

    <article class="bg-white rounded-lg sm:rounded-xl shadow-sm p-4 sm:p-5 min-w-0">
      <p class="text-xs sm:text-sm text-gray-500">@lang('messages.dashboard.kpi_active_consultations')</p>
      <p class="text-2xl sm:text-3xl font-semibold text-gray-900">
        {{ number_format($totConsultas) }}
      </p>
    </article>

    <article class="bg-white rounded-lg sm:rounded-xl shadow-sm p-4 sm:p-5 min-w-0">
      <p class="text-xs sm:text-sm text-gray-500">@lang('messages.dashboard.kpi_today_month')</p>
      <p class="text-lg sm:text-xl font-semibold text-gray-900">
        {{ $consultasHoje }} <span class="text-gray-400">/</span> {{ $consultasMes }}
      </p>
    </article>
  </section>

  <section class="mt-2">
    <form class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center">
      <label for="dashStart" class="text-sm text-gray-600">@lang('messages.dashboard.period'):</label>
      <input type="date" id="dashStart"
             class="w-full sm:w-auto border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
             value="{{ request('start') }}">
      <span class="text-gray-400 hidden sm:inline">—</span>
      <span class="text-gray-400 sm:hidden">@lang('messages.dashboard.to')</span>
      <input type="date" id="dashEnd"
             class="w-full sm:w-auto border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
             value="{{ request('end') }}">
    </form>
  </section>

  <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
    <div class="bg-white rounded-lg sm:rounded-xl shadow-sm p-4 md:p-6 lg:col-span-2 min-w-0">
      <div class="flex items-center justify-between">
        <h5 class="text-lg sm:text-xl font-bold text-gray-900">@lang('messages.dashboard.by_day')</h5>
      </div>
      <div id="chart-by-day" class="h-64 sm:h-72 lg:h-80 xl:h-96 min-w-0"></div>
      <p class="text-xs text-gray-500">@lang('messages.dashboard.daily_total')</p>
    </div>

    <div class="bg-white rounded-lg sm:rounded-xl shadow-sm p-4 md:p-6 min-w-0">
      <div class="flex items-center justify-between">
        <h5 class="text-lg sm:text-xl font-bold text-gray-900">@lang('messages.dashboard.status')</h5>
      </div>
      <div id="chart-status" class="h-64 sm:h-72 min-w-0"></div>
      <p class="text-xs text-gray-500">@lang('messages.dashboard.distribution_hint')</p>
    </div>

    <div class="bg-white rounded-lg sm:rounded-xl shadow-sm p-4 md:p-6 lg:col-span-3 min-w-0">
      <div class="flex items-center justify-between">
        <h5 class="text-lg sm:text-xl font-bold text-gray-900">@lang('messages.dashboard.by_specialty')</h5>
      </div>
      <div id="chart-esps" class="h-64 sm:h-80 xl:h-[420px] min-w-0"></div>
      <p class="text-xs text-gray-500">@lang('messages.dashboard.top_specialties_hint')</p>
    </div>
  </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>
@vite('resources/js/pages/dashboard-admin.js')
@endsection
