@extends('layouts.app')
@section('title', 'Marcação de consultas | MediGest+')

@section('content')
  {{-- BREADCRUMBS --}}
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início','url'=>route('home')],
    ['label'=>'Serviços','url'=>Route::has('paciente.consultas.index') ? route('paciente.consultas.index') : '#'],
    ['label'=>'Marcação de consultas']
  ]" />

  {{-- HERO --}}
  <x-ui.hero
    title="Consulta Externa"
    subtitle="Agende atendimento presencial ou por vídeo, de forma simples e segura."
    height="160px"
  />

  <div class="bg-zinc-50">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

      {{-- Breve explicação --}}
      <section class="rounded-xl border border-zinc-200 bg-white p-5 sm:p-6">
        <h2 class="text-lg font-semibold text-zinc-900">O que é a consulta externa?</h2>
        <p class="mt-2 text-sm text-zinc-700 leading-relaxed">
          A consulta externa permite-lhe aceder a cuidados de saúde sem internamento, em
          <strong>modalidade presencial</strong> no consultório ou por <strong>teleconsulta</strong>.
          Escolha a opção preferida, selecione especialidade e médico, e confirme o horário disponível.
        </p>
      </section>

      {{-- Opções de marcação --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('paciente.consultas.create', ['tipo'=>'presencial']) }}"
           class="block rounded-xl border border-zinc-200 bg-white p-6 hover:bg-zinc-50">
          <div class="text-lg font-semibold text-zinc-900">Consulta Presencial</div>
          <p class="text-sm text-zinc-600 mt-1">Agende no consultório com o seu médico.</p>
        </a>

        <a href="{{ route('paciente.consultas.create', ['tipo'=>'teleconsulta']) }}"
           class="block rounded-xl border border-zinc-200 bg-white p-6 hover:bg-zinc-50">
          <div class="text-lg font-semibold text-zinc-900">Teleconsulta</div>
          <p class="text-sm text-zinc-600 mt-1">Atendimento por vídeo, sem deslocações.</p>
        </a>
      </div>

      {{-- Como funciona --}}
      <section class="relative rounded-xl overflow-hidden border border-zinc-200">
        <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image:url('{{ asset('/exemplo2.png') }}')"></div>
        <div class="relative p-6">
          <h2 class="text-lg font-semibold text-zinc-900 mb-2">Como funciona</h2>
          <ol class="list-decimal list-inside text-sm text-zinc-700 space-y-1">
            <li>Escolha a modalidade (presencial ou teleconsulta) e a especialidade.</li>
            <li>Selecione o médico e a data/hora disponíveis.</li>
            <li>Confirme os dados e conclua a marcação.</li>
          </ol>
        </div>
      </section>

    </div>
  </div>
@endsection
