@extends('layouts.dashboard')
@section('title', __('messages.profile.title'))

@php
  $user = $user ?? auth()->user();
  $avatarCurrent  = $user->avatar_url;
  $avatarFallback = route('avatar.initials', ['user' => $user->id, 'v' => $user->updated_at?->timestamp]);

  $role = optional($user)->role;
  $dashRoute = match ($role) {
    'admin'    => 'admin.dashboard',
    'medico'   => 'medico.dashboard',
    'paciente' => 'paciente.home',
    default    => 'home',
  };
  $prev = url()->previous();
  $safeBack = ($prev && $prev !== url()->current()) ? $prev : route($dashRoute);
@endphp

@section('content')
<div
  class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6"
  data-page="account-profile"
  data-avatar-current="{{ $avatarCurrent }}"
  data-avatar-fallback="{{ $avatarFallback }}"
>

  {{-- Cabeçalho --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">
        {{ __('messages.profile.title') }}
      </h1>
      <p class="text-sm text-gray-500 mt-1">{{ __('messages.profile.subtitle') }}</p>
    </div>
    <a href="{{ $safeBack }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 hover:underline">
      ← {{ __('messages.nav.back') }}
    </a>
  </div>

  {{-- Mensagens --}}
  @if (session('success'))
    <div class="rounded-xl px-4 py-3 bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200/60" role="status" aria-live="polite">
      {{ session('success') }}
    </div>
  @endif
  @if ($errors->any())
    <div class="rounded-xl px-4 py-3 bg-rose-50 text-rose-700 ring-1 ring-rose-200/60" role="alert" aria-live="assertive">
      <ul class="list-disc list-inside text-sm">
        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Form perfil --}}
    <form id="form-profile" method="POST" action="{{ route('account.profile.update') }}"
          enctype="multipart/form-data"
          class="lg:col-span-2 bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 flex flex-col min-h-[420px]"
          novalidate>
      @csrf @method('PUT')

      <div class="p-6 sm:p-8 space-y-6 flex-1">
        {{-- Avatar + ações --}}
        <div class="flex items-center gap-5">
          <img id="avatarPreview" src="{{ $avatarCurrent }}" alt="{{ __('messages.profile.title') }} — Avatar {{ $user->name }}"
               class="w-20 h-20 rounded-full ring-1 ring-gray-200 shadow-sm object-cover" loading="lazy" decoding="async">

          <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2">
              <label for="avatar"
                     class="inline-flex items-center gap-2 rounded-xl px-3 py-1.5 text-sm
                            text-slate-800 border border-white/50 ring-1 ring-gray-200/80
                            bg-gradient-to-b from-gray-50 to-gray-100
                            shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                            hover:from-white hover:to-gray-50 hover:ring-gray-300/80 cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                {{ __('messages.actions.change_photo') }}
              </label>
              <input id="avatar" name="avatar" type="file" class="hidden" accept="image/png,image/jpeg,image/webp" aria-describedby="avatar-hint">

              <label class="inline-flex items-center gap-2 rounded-xl px-3 py-1.5 text-sm
                            text-slate-800 border border-white/50 ring-1 ring-gray-200/80
                            bg-gradient-to-b from-gray-50 to-gray-100
                            shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                            hover:from-white hover:to-gray-50 hover:ring-gray-300/80 cursor-pointer select-none">
                <input type="checkbox" name="remove_avatar" id="remove_avatar" value="1" class="rounded" aria-describedby="avatar-hint">
                {{ __('messages.actions.remove_photo') }}
              </label>
            </div>

            <p id="avatar-hint" class="text-xs text-gray-500">{{ __('messages.profile.photo_hint') }}</p>
            @error('avatar') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Campos principais --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <x-form.input
              id="name" name="name"
              :label="__('messages.profile.name')"
              :placeholder="__('messages.doctors.name_ph')"
              :value="old('name', $user->name)"
              icon="components.icons.admin.user"
              required
            />
            @error('name') <p class="text-sm text-red-600 -mt-2 mb-2">{{ $message }}</p> @enderror
          </div>

          <div>
            <x-form.input
              id="email" name="email" type="email"
              :label="__('messages.profile.email')"
              :placeholder="__('messages.doctors.email_ph')"
              :value="old('email', $user->email)"
              icon="components.icons.admin.email"
              required
            />
            @error('email') <p class="text-sm text-red-600 -mt-2 mb-2">{{ $message }}</p> @enderror
          </div>

          <div>
            <x-form.input
              id="phone" name="phone" type="tel"
              :label="__('messages.profile.phone')"
              :placeholder="__('messages.profile.phone')"
              :value="old('phone', $user->phone ?? '')"
              icon="components.icons.admin.phone"
            />
            @error('phone') <p class="text-sm text-red-600 -mt-2 mb-2">{{ $message }}</p> @enderror
          </div>

          <div>
            <x-form.input
              id="role" name="role" type="text"
              :label="__('messages.profile.role')"
              :placeholder="__('messages.profile.role')"
              :value="ucfirst($user->role)"
              icon="components.icons.admin.id"
              disabled
            />
          </div>
        </div>
      </div>

      <div class="px-6 sm:px-8 py-4 bg-gray-50 rounded-b-2xl flex items-center justify-end">
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white bg-home-medigest hover:bg-home-medigest-hover">
          {{ __('messages.actions.save_changes') }}
        </button>
      </div>
    </form>

    {{-- Segurança (passwords com ícone + ver/ocultar) --}}
    <form id="form-password" method="POST" action="{{ route('account.password.update') }}"
          class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 flex flex-col min-h-[420px]"
          novalidate>
      @csrf @method('PUT')

      <div class="p-6 sm:p-8 space-y-4 flex-1">
        <h2 class="text-lg font-medium text-gray-900">{{ __('messages.profile.security') }}</h2>

        <div class="space-y-3">
          {{-- Nova password --}}
          <div>
            <x-form.input
              id="pwd-new" name="password" type="password"
              :label="__('messages.profile.password_new')"
              placeholder="•••••••••"
              icon="components.icons.admin.lock"
              minlength="8" maxlength="72" autocomplete="new-password"
              toggle="true"
              toggleLabel="{{ __('messages.profile.show_hide_password') }}"
              required
            />
            <div class="mt-2 h-1 rounded bg-gray-200" aria-hidden="true">
              <div id="pwd-strength-bar" class="h-1 rounded bg-red-500" style="width:0%"></div>
            </div>
            <p id="pwd-strength-text" class="text-xs text-gray-500 mt-1" aria-live="polite">
              {{ __('messages.profile.strength') }}: —
            </p>
            @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
          </div>

          {{-- Confirmar password --}}
          <div>
            <x-form.input
              id="pwd-confirm" name="password_confirmation" type="password"
              :label="__('messages.profile.password_confirm')"
              placeholder="•••••••••"
              icon="components.icons.admin.lock"
              minlength="8" maxlength="72" autocomplete="new-password"
              toggle="true"
              toggleLabel="{{ __('messages.profile.show_hide_password') }}"
              required
            />
            @error('password_confirmation') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>

      <div class="px-6 sm:px-8 py-4 bg-gray-50 rounded-b-2xl flex items-center justify-end">
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white bg-home-medigest hover:bg-home-medigest-hover">
          {{ __('messages.actions.update_password') }}
        </button>
      </div>
    </form>
  </div>
</div>

@push('body-end')
  @vite('resources/js/pages/account/account-profile.js')
@endpush
@endsection
