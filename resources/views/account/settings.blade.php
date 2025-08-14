@extends('layouts.dashboard')
@section('title', __('messages.settings.title'))

@php
  $user = $user ?? auth()->user();

  // Defaults seguros
  $defaults = [
    'theme'          => 'system',
    'language'       => 'pt',
    'notify_email'   => true,
    'notify_push'    => false,
    'weekly_digest'  => true,
  ];

  // Preferências vindas do controller (se existirem)
  $prefs = array_merge($defaults, (array) ($prefs ?? []));

  $theme = old('theme', $prefs['theme']);

  // Back seguro: tenta voltar à página anterior; se não houver ou for a mesma, vai para a dashboard
  $prev = url()->previous();
  $safeBack = ($prev && $prev !== url()->current()) ? $prev : route('admin.dashboard');
@endphp

@section('content')
<div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6">

  {{-- Cabeçalho --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">
        {{ __('messages.settings.title') }}
      </h1>
      <p class="text-sm text-gray-500 mt-1">
        {{ __('messages.settings.subtitle') }}
      </p>
    </div>
    <a href="{{ $safeBack }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 hover:underline">
      ← {{ __('messages.nav.back') }}
    </a>
  </div>

  {{-- Mensagens --}}
  @if (session('success_key'))
    <div class="rounded-xl px-4 py-3 bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200/60">
      {{ __(session('success_key')) }}
    </div>
  @endif
  @if ($errors->any())
    <div class="rounded-xl px-4 py-3 bg-rose-50 text-rose-700 ring-1 ring-rose-200/60">
      <ul class="list-disc list-inside text-sm">
        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('account.settings.update') }}" class="space-y-6">
    @csrf @method('PUT')

    {{-- Interface --}}
    <section class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60">
      <div class="p-6 sm:p-8 space-y-6">
        <h2 class="text-lg font-medium text-gray-900">{{ __('messages.settings.interface') }}</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.settings.theme') }}</label>
            <div class="flex gap-2">
              @foreach ([
                'light'  => __('messages.settings.themes.light'),
                'dark'   => __('messages.settings.themes.dark'),
                'system' => __('messages.settings.themes.system'),
              ] as $val => $label)
                <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl cursor-pointer
                              border border-white/50 ring-1 ring-gray-200/80 bg-gradient-to-b from-gray-50 to-gray-100
                              shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.95),inset_3px_3px_8px_rgba(15,23,42,0.10)]
                              hover:from-white hover:to-gray-50 hover:ring-gray-300/80">
                  <input type="radio" name="theme" value="{{ $val }}" class="accent-emerald-600"
                         @checked($theme === $val)>
                  <span class="text-sm text-gray-800">{{ $label }}</span>
                </label>
              @endforeach
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.settings.language') }}</label>
            <select name="language"
                    class="w-full rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600">
                <option value="pt" @selected(($prefs['language'] ?? 'pt') === 'pt')>@lang('messages.app.locale_names.pt')</option>
                <option value="en" @selected(($prefs['language'] ?? 'pt') === 'en')>@lang('messages.app.locale_names.en')</option>
                <option value="es" @selected(($prefs['language'] ?? 'pt') === 'es')>@lang('messages.app.locale_names.es')</option>
            </select>
          </div>
        </div>
      </div>
    </section>

    {{-- Notificações --}}
    <section class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60">
      <div class="p-6 sm:p-8 space-y-6">
        <h2 class="text-lg font-medium text-gray-900">{{ __('messages.settings.notifications') }}</h2>

        <div class="space-y-4">
          @php
            $switch = 'relative w-11 h-6 bg-gray-200 rounded-full transition
                       after:content-[\'\'] after:absolute after:top-0.5 after:left-0.5 after:w-5 after:h-5 after:bg-white after:rounded-full after:transition';
            $switchOn = 'peer-checked:bg-emerald-600 peer-checked:after:translate-x-5';
          @endphp

          <label class="flex items-center justify-between gap-4">
            <div>
              <div class="text-sm font-medium text-gray-800">{{ __('messages.settings.notify_email') }}</div>
              <div class="text-xs text-gray-500">{{ __('messages.settings.notify_email_hint') }}</div>
            </div>
            <input type="checkbox" name="notify_email" value="1" class="peer sr-only"
                   @checked(old('notify_email', $prefs['notify_email']))>
            <span class="{{ $switch }} {{ $switchOn }}"></span>
          </label>

          <label class="flex items-center justify-between gap-4">
            <div>
              <div class="text-sm font-medium text-gray-800">{{ __('messages.settings.notify_push') }}</div>
              <div class="text-xs text-gray-500">{{ __('messages.settings.notify_push_hint') }}</div>
            </div>
            <input type="checkbox" name="notify_push" value="1" class="peer sr-only"
                   @checked(old('notify_push', $prefs['notify_push']))>
            <span class="{{ $switch }} {{ $switchOn }}"></span>
          </label>

          <label class="flex items-center justify-between gap-4">
            <div>
              <div class="text-sm font-medium text-gray-800">{{ __('messages.settings.weekly_digest') }}</div>
              <div class="text-xs text-gray-500">{{ __('messages.settings.weekly_digest_hint') }}</div>
            </div>
            <input type="checkbox" name="weekly_digest" value="1" class="peer sr-only"
                   @checked(old('weekly_digest', $prefs['weekly_digest']))>
            <span class="{{ $switch }} {{ $switchOn }}"></span>
          </label>
        </div>
      </div>
    </section>

    {{-- Guardar --}}
    <div class="flex items-center justify-end">
      <button type="submit"
              class="px-5 py-2.5 rounded-xl text-white bg-home-medigest hover:bg-home-medigest-hover">
        {{ __('messages.actions.save_preferences') }}
      </button>
    </div>
  </form>
</div>
@endsection
