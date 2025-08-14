@extends('layouts.dashboard')
@section('title', __('messages.doctors.edit'))

@section('content')
<div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">@lang('messages.doctors.edit')</h1>
      <p class="text-sm text-gray-500 mt-1">@lang('messages.doctors.edit_subtitle')</p>
    </div>
    <a href="{{ route('admin.medicos.index') }}" class="text-gray-700 hover:text-gray-900 hover:underline">@lang('messages.nav.back')</a>
  </div>

  <form action="{{ route('admin.medicos.update', $medico) }}" method="POST"
        class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-6 sm:p-8">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <div>
        <x-form.input
          id="name" name="name"
          :label="__('messages.profile.name')"
          :placeholder="__('messages.doctors.name_ph')"
          icon="components.icons.admin.user"
          :value="old('name', $medico->user->name)"
          required
        />
        @error('name') <p class="text-sm text-red-600 -mt-2 mb-2">{{ $message }}</p> @enderror
      </div>

      <div>
        <x-form.input
          id="email" name="email" type="email"
          :label="__('messages.profile.email')"
          :placeholder="__('messages.doctors.email_ph')"
          icon="components.icons.admin.email"
          :value="old('email', $medico->user->email)"
          required
        />
        @error('email') <p class="text-sm text-red-600 -mt-2 mb-2">{{ $message }}</p> @enderror
      </div>

      <div>
        <x-form.input
          id="password" name="password" type="password"
          :label="__('messages.doctors.password_optional')"
          placeholder="•••••••••"
          icon="components.icons.admin.lock"
        />
        <p class="text-xs text-gray-500 mt-1">@lang('messages.doctors.leave_blank_hint')</p>
        @error('password') <p class="text-sm text-red-600 -mt-2 mb-2">{{ $message }}</p> @enderror
      </div>

      <div>
        <x-form.input
          id="password_confirmation" name="password_confirmation" type="password"
          :label="__('messages.profile.password_confirm')"
          placeholder="•••••••••"
          icon="components.icons.admin.lock"
        />
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
      <div>
        <x-form.input
          id="crm" name="crm" label="CRM"
          :placeholder="__('messages.doctors.crm_ph')"
          icon="components.icons.admin.id"
          :value="old('crm', $medico->crm)"
          required
        />
        <p class="text-xs text-gray-500 mt-1">@lang('messages.doctors.unique_hint')</p>
        @error('crm') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <x-form.textarea
          id="bio" name="bio"
          :label="__('messages.doctors.bio')" :placeholder="__('messages.doctors.bio_ph')"
          icon="components.icons.admin.info" rows="2"
          :value="old('bio', $medico->bio)"
        />
        @error('bio') <p class="text-sm text-red-600 -mt-2 mb-2">{{ $message }}</p> @enderror
      </div>
    </div>

    @php
      $espBase = 'esp';
      $oldSelected = collect(old('especialidades', $medico->especialidades->pluck('id')->all()))
                        ->map(fn($v)=>(string)$v)->all();
    @endphp

    <div class="sm:col-span-2">
      <label class="block text-sm font-medium text-zinc-700 mb-1">@lang('messages.nav.specialties')</label>

      {{-- Caixa única (chips + input) --}}
      <div id="{{ $espBase }}-box" class="relative">
        <div id="{{ $espBase }}-control"
             class="min-h-[44px] w-full flex flex-wrap items-center gap-2 rounded-lg border border-gray-300 bg-gray-50 px-2 py-2 focus-within:ring-2 focus-within:ring-medigest focus-within:border-medigest">

          {{-- chips pré-selecionadas --}}
          @foreach($especialidades as $esp)
            @if(in_array((string)$esp->id, $oldSelected))
              <span class="esp-chip inline-flex items-center gap-2 rounded-md bg-blue-50 text-blue-700 text-sm px-2.5 py-1"
                    data-id="{{ $esp->id }}" data-text="{{ $esp->nome }}">
                {{ $esp->nome }}
                <button type="button" class="esp-chip-remove text-blue-700/70 hover:text-blue-900" aria-label="@lang('messages.actions.delete')">&times;</button>
              </span>
            @endif
          @endforeach

          {{-- input de pesquisa/adição --}}
          <input id="{{ $espBase }}-input" type="text" autocomplete="off"
                 placeholder="{{ __('messages.doctors.select_specialties_ph') }}"
                 class="flex-1 min-w-[140px] border-0 focus:ring-0 focus:outline-none text-sm text-gray-900 placeholder:text-gray-400">
          <button type="button" id="{{ $espBase }}-toggle" class="px-2 text-gray-500 hover:text-gray-700">▼</button>
        </div>

        {{-- Dropdown --}}
        <div id="{{ $espBase }}-dd"
             class="absolute z-50 mt-2 hidden w-full max-h-60 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow">
          <ul id="{{ $espBase }}-list" class="py-1">
            @foreach($especialidades as $esp)
              <li class="esp-item cursor-pointer px-3 py-2 hover:bg-gray-50"
                  data-id="{{ $esp->id }}" data-text="{{ $esp->nome }}">
                {{ $esp->nome }}
              </li>
            @endforeach
          </ul>
          <div class="border-t border-gray-200 p-2 text-xs text-gray-500">
            @lang('messages.doctors.multiselect_hint')
          </div>
        </div>
      </div>

      {{-- Hidden inputs --}}
      <div id="{{ $espBase }}-hidden">
        @foreach($oldSelected as $sid)
          <input type="hidden" name="especialidades[]" value="{{ $sid }}" data-id="{{ $sid }}">
        @endforeach
      </div>

      @error('especialidades') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
      <p class="text-xs text-gray-500 mt-2">@lang('messages.doctors.multi_select_hint')</p>
    </div>

    {{-- Ações --}}
    <div class="mt-8 flex items-center gap-3">
      <button class="px-5 py-2.5 rounded-2xl text-white bg-home-medigest hover:bg-home-medigest-hover">
        @lang('messages.actions.save_changes')
      </button>
      <a href="{{ route('admin.medicos.index') }}" class="text-gray-700 hover:text-gray-900 hover:underline">@lang('messages.actions.cancel')</a>
    </div>
  </form>
</div>

@vite('resources/js/pages/medicos-multiselect.js')
@endsection
