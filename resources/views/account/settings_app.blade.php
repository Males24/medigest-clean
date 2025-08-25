@extends('layouts.app')
@section('title', 'Configurações | MediGest+')

@php
  $user = $user ?? auth()->user();
  $prefs = array_merge([
    'theme'         => 'system',
    'language'      => 'pt',
    'notify_email'  => true,
    'notify_push'   => false,
    'weekly_digest' => true,
  ], (array) ($prefs ?? []));
@endphp

@section('content')
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início','url'=>route('home')],
    ['label'=>'Configurações']
  ]" />

  <x-ui.hero title="Configurações"
             subtitle="Preferências de interface, idioma e notificações."
             height="160px" />

  <div class="bg-zinc-50">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

      {{-- Mensagens --}}
      @if (session('success_key'))
        <div class="rounded-xl px-4 py-3 bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200/60">
          {{ __(session('success_key')) }}
        </div>
      @endif
      @if ($errors->any())
        <div class="rounded-xl px-4 py-3 bg-rose-50 text-rose-700 ring-1 ring-rose-200/60">
          <ul class="list-disc list-inside text-sm">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      <form method="POST" action="{{ route('account.settings.update') }}" class="space-y-6">
        @csrf @method('PUT')

        {{-- Interface --}}
        <section class="rounded-2xl border border-zinc-200 bg-white">
          <div class="p-6 sm:p-8 space-y-6">
            <h2 class="text-lg font-medium text-zinc-900">Interface</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <div class="block text-sm font-medium text-zinc-800 mb-1">Tema</div>
                <div class="flex gap-2">
                  @foreach(['light'=>'Claro','dark'=>'Escuro','system'=>'Automático'] as $val => $label)
                    <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl cursor-pointer border border-zinc-200 bg-white hover:bg-zinc-50">
                      <input type="radio" name="theme" value="{{ $val }}" class="accent-emerald-600"
                             @checked(($prefs['theme'] ?? 'system') === $val)>
                      <span class="text-sm text-zinc-800">{{ $label }}</span>
                    </label>
                  @endforeach
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-zinc-800 mb-1">Idioma</label>
                <select name="language" class="w-full h-11 rounded-xl border border-zinc-300 px-3 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                  <option value="pt" @selected(($prefs['language'] ?? 'pt') === 'pt')>Português (PT)</option>
                  <option value="en" @selected(($prefs['language'] ?? 'pt') === 'en')>English</option>
                  <option value="es" @selected(($prefs['language'] ?? 'pt') === 'es')>Español</option>
                </select>
              </div>
            </div>
          </div>
        </section>

        {{-- Notificações --}}
        <section class="rounded-2xl border border-zinc-200 bg-white">
          <div class="p-6 sm:p-8 space-y-6">
            <h2 class="text-lg font-medium text-zinc-900">Notificações</h2>

            @php
              $switch = 'relative w-11 h-6 bg-zinc-200 rounded-full transition after:content-[\'\'] after:absolute after:top-0.5 after:left-0.5 after:w-5 after:h-5 after:bg-white after:rounded-full after:transition';
              $switchOn = 'peer-checked:bg-emerald-600 peer-checked:after:translate-x-5';
            @endphp

            <label class="flex items-center justify-between gap-4">
              <div>
                <div class="text-sm font-medium text-zinc-800">Email</div>
                <div class="text-xs text-zinc-500">Receber alertas e confirmações por email.</div>
              </div>
              <input type="checkbox" name="notify_email" value="1" class="peer sr-only" @checked($prefs['notify_email'])>
              <span class="{{ $switch }} {{ $switchOn }}"></span>
            </label>

            <label class="flex items-center justify-between gap-4">
              <div>
                <div class="text-sm font-medium text-zinc-800">Push</div>
                <div class="text-xs text-zinc-500">Notificações do navegador (quando permitido).</div>
              </div>
              <input type="checkbox" name="notify_push" value="1" class="peer sr-only" @checked($prefs['notify_push'])>
              <span class="{{ $switch }} {{ $switchOn }}"></span>
            </label>

            <label class="flex items-center justify-between gap-4">
              <div>
                <div class="text-sm font-medium text-zinc-800">Resumo semanal</div>
                <div class="text-xs text-zinc-500">Estatísticas e destaques da semana.</div>
              </div>
              <input type="checkbox" name="weekly_digest" value="1" class="peer sr-only" @checked($prefs['weekly_digest'])>
              <span class="{{ $switch }} {{ $switchOn }}"></span>
            </label>
          </div>
        </section>

        <div class="flex items-center justify-end">
          <button type="submit" class="px-5 py-2.5 rounded-xl text-white bg-emerald-700 hover:bg-emerald-800">
            Guardar preferências
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
