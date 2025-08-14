@extends('layouts.dashboard')
@section('title', __('messages.doctors.title'))

@section('content')
<div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6">
  
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">@lang('messages.doctors.title')</h1>
      <p class="text-sm text-gray-500 mt-1">@lang('messages.doctors.subtitle')</p>
    </div>
    <a href="{{ route('admin.medicos.create') }}"
       class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-2xl text-white
              bg-home-medigest hover:bg-home-medigest-hover shadow-sm
              ring-1 ring-emerald-700/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
      @lang('messages.nav.create_doctor')
    </a>
  </div>

  @if(session('success'))
    <div class="rounded-xl px-4 py-3 bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200/60">
      {{ session('success') }}
    </div>
  @endif

  <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 overflow-x-auto">
    <table class="min-w-[980px] w-full text-sm text-left text-gray-700">
      <thead class="bg-gray-50 text-xs uppercase text-gray-500">
        <tr>
          <th class="px-6 py-3">@lang('messages.profile.name')</th>
          <th class="px-6 py-3">@lang('messages.profile.email')</th>
          <th class="px-6 py-3">CRM</th>
          <th class="px-6 py-3">@lang('messages.nav.specialties')</th>
          <th class="px-6 py-3 text-right">@lang('messages.consultas.actions')</th>
        </tr>
      </thead>
      <tbody>
        @foreach($medicos as $m)
          <tr class="border-t border-gray-100 hover:bg-gray-50">
            <td class="px-6 py-3">{{ $m->user->name }}</td>
            <td class="px-6 py-3">{{ $m->user->email }}</td>
            <td class="px-6 py-3">{{ $m->crm }}</td>
            <td class="px-6 py-3">{{ $m->especialidades->pluck('nome')->join(', ') ?: __('messages.common.none') }}</td>
            <td class="px-6 py-3 text-right">
              <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-sm
                       text-slate-800 border border-white/50 ring-1 ring-gray-200/80
                       bg-gradient-to-b from-gray-50 to-gray-100
                       shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                       hover:from-white hover:to-gray-50 hover:ring-gray-300/80 transition js-medico-actions-btn"
                data-edit-url="{{ route('admin.medicos.edit', $m) }}"
                data-destroy-url="{{ route('admin.medicos.destroy', $m) }}"
                data-nome="{{ $m->user->name }}">
                {{ __('messages.common.action') }} <span class="text-gray-500">▼</span>
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div>
    {{ $medicos->links() }}
  </div>
</div>

@vite('resources/js/pages/medicos-admin-index-dropdown.js')
@vite('resources/js/pages/medicos-admin-index-modal.js')
@endsection
