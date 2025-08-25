@extends('layouts.app')
@section('title', 'Canais | MediGest+')

@php
  // Define contactos com fallback. Se quiseres, cria chaves no config/app.php (app.contacts.*)
  $contacts = [
    'urgencias'   => config('app.contacts.urgencias_phone',   '808 500 500'),
    'marcacoes'   => config('app.contacts.marcacoes_phone',   '210 000 000'),
    'apoio'       => config('app.contacts.support_phone',     '210 000 111'),
    'emailGeral'  => config('app.contacts.general_email',     'geral@medigest.com'),
    'emailMarc'   => config('app.contacts.booking_email',     'marcacoes@medigest.com'),
    'emailApoio'  => config('app.contacts.support_email',     'apoio@medigest.com'),
    'whatsapp'    => config('app.contacts.whatsapp',          '+351910000000'),
    'morada'      => config('app.contacts.address',           'Av. Exemplo 123, 1000-000 Lisboa'),
    'horario'     => config('app.contacts.hours',             'Seg–Sex 08:00–20:00 · Sáb 09:00–13:00'),
  ];
@endphp

@section('content')
  {{-- Breadcrumbs + Hero --}}
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início', 'url'=>route('home')],
    ['label'=>'Canais']
  ]" />

  <x-ui.hero
    title="Canais"
    subtitle="Fale connosco: marcações, apoio ao cliente e urgências."
    height="160px"
  />

  <div class="bg-zinc-50">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

      {{-- Contactos rápidos --}}
      <section aria-labelledby="sec-rapidos" class="space-y-4">
        <h2 id="sec-rapidos" class="text-xl font-semibold text-zinc-900">Contactos rápidos</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

          {{-- Urgências 24h --}}
          <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 shrink-0 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"/>
              </svg>
              <div>
                <div class="text-sm font-medium text-emerald-800">Urgências 24h</div>
                <div class="mt-1 text-2xl font-bold text-emerald-900">
                  <a href="tel:{{ preg_replace('/\s+/', '', $contacts['urgencias']) }}">{{ $contacts['urgencias'] }}</a>
                </div>
                <p class="mt-1 text-xs text-emerald-800/80">Atendimento permanente para situações urgentes.</p>
              </div>
            </div>
          </div>

          {{-- Marcações de consulta --}}
          <div class="rounded-2xl border border-zinc-200 bg-white p-6">
            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 shrink-0 text-zinc-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M8 2v4M16 2v4M3 10h18M5 6h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"/>
              </svg>
              <div class="flex-1">
                <div class="text-sm font-medium text-zinc-900">Marcações de Consultas</div>
                <div class="mt-1 text-zinc-700">
                  <a class="font-semibold hover:underline"
                     href="tel:{{ preg_replace('/\s+/', '', $contacts['marcacoes']) }}">{{ $contacts['marcacoes'] }}</a>
                  ·
                  <a class="hover:underline" href="mailto:{{ $contacts['emailMarc'] }}">{{ $contacts['emailMarc'] }}</a>
                </div>
                <div class="mt-3">
                  <a href="{{ route('paciente.consultas.create') }}"
                     class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-white bg-emerald-700 hover:bg-emerald-800">
                    Marcar consulta
                  </a>
                </div>
              </div>
            </div>
          </div>

          {{-- Apoio ao cliente --}}
          <div class="rounded-2xl border border-zinc-200 bg-white p-6">
            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 shrink-0 text-zinc-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 10a6 6 0 1 0-12 0v5a2 2 0 0 0 2 2h2l2 2 2-2h2a2 2 0 0 0 2-2v-5z"/>
              </svg>
              <div>
                <div class="text-sm font-medium text-zinc-900">Apoio ao Cliente</div>
                <div class="mt-1 text-zinc-700">
                  <a class="font-semibold hover:underline" href="tel:{{ preg_replace('/\s+/', '', $contacts['apoio']) }}">{{ $contacts['apoio'] }}</a>
                  ·
                  <a class="hover:underline" href="mailto:{{ $contacts['emailApoio'] }}">{{ $contacts['emailApoio'] }}</a>
                </div>
                <p class="mt-1 text-xs text-zinc-500">{{ $contacts['horario'] }}</p>
              </div>
            </div>
          </div>

          {{-- WhatsApp --}}
          <div class="rounded-2xl border border-zinc-200 bg-white p-6">
            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 shrink-0 text-zinc-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16.72 13.06c-.26-.13-1.53-.75-1.77-.84-.24-.09-.42-.13-.6.13-.18.26-.69.84-.85 1.02-.16.18-.31.2-.57.07-.26-.13-1.08-.4-2.06-1.27-.76-.68-1.28-1.52-1.43-1.78-.15-.26-.02-.4.11-.53.11-.11.26-.31.38-.46.12-.15.16-.26.24-.44.08-.18.04-.33-.02-.46-.06-.13-.6-1.44-.82-1.97-.22-.53-.44-.46-.6-.47l-.51-.01c-.18 0-.46.07-.7.33-.24.26-.92.9-.92 2.2 0 1.31.95 2.58 1.08 2.76.13.18 1.87 2.86 4.54 3.98.64.28 1.14.45 1.53.58.64.2 1.23.17 1.7.1.52-.08 1.53-.62 1.75-1.21.22-.59.22-1.1.15-1.21-.06-.11-.24-.18-.5-.31z"/>
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12c0 1.77.46 3.43 1.26 4.87L2 22l5.27-1.23A9.96 9.96 0 0 0 12 22z"/>
              </svg>
              <div>
                <div class="text-sm font-medium text-zinc-900">WhatsApp</div>
                <a class="mt-1 inline-block font-semibold text-emerald-700 hover:underline"
                   href="https://wa.me/{{ preg_replace('/\D+/', '', $contacts['whatsapp']) }}" target="_blank" rel="noopener">
                  {{ $contacts['whatsapp'] }}
                </a>
                <p class="mt-1 text-xs text-zinc-500">Resposta em horário laboral.</p>
              </div>
            </div>
          </div>

          {{-- Email geral --}}
          <div class="rounded-2xl border border-zinc-200 bg-white p-6">
            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 shrink-0 text-zinc-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/>
              </svg>
              <div>
                <div class="text-sm font-medium text-zinc-900">Email Geral</div>
                <a class="mt-1 inline-block hover:underline" href="mailto:{{ $contacts['emailGeral'] }}">{{ $contacts['emailGeral'] }}</a>
              </div>
            </div>
          </div>

        </div>
      </section>

      {{-- Localização & horário --}}
      <section aria-labelledby="sec-local" class="space-y-3">
        <h2 id="sec-local" class="text-xl font-semibold text-zinc-900">Localização e Horários</h2>
        <div class="rounded-2xl border border-zinc-200 bg-white p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <div class="text-sm font-medium text-zinc-900">Sede</div>
              <p class="mt-1 text-zinc-700">{{ $contacts['morada'] }}</p>
              <p class="mt-2 text-sm text-zinc-500">Horário de atendimento: {{ $contacts['horario'] }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 text-sm text-zinc-700">
              <p class="font-medium">Como chegar</p>
              <ul class="mt-2 space-y-1 list-disc list-inside">
                <li>Metro: Linha Verde — Estação Saúde</li>
                <li>Autocarro: 702, 717</li>
                <li>Estacionamento nas ruas adjacentes</li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      {{-- Ajuda & documentos --}}
      <section aria-labelledby="sec-ajuda" class="space-y-3">
        <h2 id="sec-ajuda" class="text-xl font-semibold text-zinc-900">Ajuda</h2>
        <div class="rounded-2xl border border-zinc-200 bg-white p-6">
          <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3 text-sm text-emerald-700">
            <li><a class="inline-flex items-center gap-2 hover:underline" href="{{ route('paciente.legal.privacy') }}">Política de privacidade</a></li>
            <li><a class="inline-flex items-center gap-2 hover:underline" href="{{ route('paciente.legal.terms') }}">Termos de serviço</a></li>
          </ul>
        </div>
      </section>

    </div>
  </div>
@endsection
