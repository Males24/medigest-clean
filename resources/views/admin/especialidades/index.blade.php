@extends('layouts.dashboard')
@section('title', __('messages.nav.specialties'))

@section('content')
<div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6">

  {{-- Cabeçalho --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">@lang('messages.specialties.title')</h1>
      <p class="text-sm text-gray-500 mt-1">@lang('messages.specialties.subtitle')</p>
    </div>
    <a href="{{ route('admin.especialidades.create') }}"
       class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-2xl text-white
              bg-home-medigest hover:bg-home-medigest-hover shadow-sm
              ring-1 ring-emerald-700/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
      @lang('messages.specialties.create')
    </a>
  </div>

  {{-- Mensagens --}}
  @if(session('success'))
    <div class="rounded-xl px-4 py-3 bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200/60">
      {{ session('success') }}
    </div>
  @endif

  {{-- Pré-cálculos para o footer, igual ao das Consultas --}}
  @php
    $from    = $especialidades->firstItem() ?? 0;
    $to      = $especialidades->lastItem()  ?? $especialidades->count();
    $total   = $especialidades->total();
    $current = $especialidades->currentPage();
    $last    = $especialidades->lastPage();
    $window  = 1;

    $pages = [1];
    for ($i = $current - $window; $i <= $current + $window; $i++) {
      if ($i > 1 && $i < $last) $pages[] = $i;
    }
    if ($last > 1) $pages[] = $last;
    $pages = array_values(array_unique(array_filter($pages, fn($n) => $n >= 1 && $n <= $last)));
    sort($pages);

    $prevUrl = $current > 1    ? request()->fullUrlWithQuery(['page' => $current - 1]) : null;
    $nextUrl = $current < $last? request()->fullUrlWithQuery(['page' => $current + 1]) : null;
  @endphp

  {{-- Tabela + Footer (num único card, como Consultas) --}}
  <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-[720px] w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
          <tr>
            <th class="px-6 py-3">@lang('messages.specialties.name')</th>
            <th class="px-6 py-3 text-right">@lang('messages.consultas.actions')</th>
          </tr>
        </thead>
        <tbody>
          @forelse($especialidades as $e)
            <tr class="border-t border-gray-100 hover:bg-gray-50">
              <td class="px-6 py-3 font-medium">{{ $e->nome }}</td>
              <td class="px-6 py-3 text-right">
                <button
                  type="button"
                  class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-sm
                         text-slate-800 border border-white/50 ring-1 ring-gray-200/80
                         bg-gradient-to-b from-gray-50 to-gray-100
                         shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                         hover:from-white hover:to-gray-50 hover:ring-gray-300/80 transition js-espec-actions-btn"
                  data-edit-url="{{ route('admin.especialidades.edit', $e) }}"
                  data-destroy-url="{{ route('admin.especialidades.destroy', $e) }}"
                  data-nome="{{ $e->nome }}">
                  {{ __('messages.common.action') }} <span class="text-gray-500">▼</span>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td class="px-6 py-4 text-gray-500" colspan="2">@lang('messages.specialties.empty')</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Footer da tabela (idêntico ao das Consultas) --}}
    <nav class="flex flex-col items-start gap-3 md:flex-row md:items-center md:justify-between p-4 border-t border-gray-100 bg-white"
         aria-label="Table navigation">
      <span class="text-sm text-gray-500">
        Mostrando
        <span class="font-semibold text-gray-900">{{ $from }}</span>
        –
        <span class="font-semibold text-gray-900">{{ $to }}</span>
        de
        <span class="font-semibold text-gray-900">{{ $total }}</span>
      </span>

      <ul class="inline-flex items-stretch -space-x-px">
        {{-- Prev --}}
        <li>
          @if($prevUrl)
            <a href="{{ $prevUrl }}"
               class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-600 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
              <span class="sr-only">Anterior</span>
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </a>
          @else
            <span class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-400 bg-white rounded-l-lg border border-gray-200 cursor-not-allowed">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </span>
          @endif
        </li>

        {{-- Páginas + reticências --}}
        @php $prevShown = null; @endphp
        @foreach($pages as $p)
          @if(!is_null($prevShown) && $p > $prevShown + 1)
            <li><span class="flex items-center justify-center px-3 py-2 text-sm leading-tight text-gray-500 bg-white border border-gray-300">…</span></li>
          @endif

          @if($p == $current)
            <li>
              <span aria-current="page"
                    class="z-10 flex items-center justify-center px-3 py-2 text-sm leading-tight border text-home-medigest bg-emerald-50 border-emerald-200 hover:bg-emerald-100">
                {{ $p }}
              </span>
            </li>
          @else
            <li>
              <a href="{{ request()->fullUrlWithQuery(['page' => $p]) }}"
                 class="flex items-center justify-center px-3 py-2 text-sm leading-tight text-gray-600 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-800">
                {{ $p }}
              </a>
            </li>
          @endif

          @php $prevShown = $p; @endphp
        @endforeach

        {{-- Next --}}
        <li>
          @if($nextUrl)
            <a href="{{ $nextUrl }}"
               class="flex items-center justify-center h-full py-1.5 px-3 text-gray-600 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
              <span class="sr-only">Seguinte</span>
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            </a>
          @else
            <span class="flex items-center justify-center h-full py-1.5 px-3 text-gray-400 bg-white rounded-r-lg border border-gray-200 cursor-not-allowed">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            </span>
          @endif
        </li>
      </ul>
    </nav>
  </div>
</div>

@vite('resources/js/pages/admin/especialidades/especialidades-admin-index-dropdown.js')
@vite('resources/js/pages/admin/especialidades/especialidades-admin-index-modal.js')
@endsection
