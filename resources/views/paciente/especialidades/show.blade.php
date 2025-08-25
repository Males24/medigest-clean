@extends('layouts.app')
@section('title', ($especialidade->nome ?? 'Especialidade').' | MediGest+')

@php
  $esp = $especialidade;
  $medicos = collect($medicos ?? []);
@endphp

@section('content')
  {{-- Trilho + Hero --}}
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início','url'=>route('home')],
    ['label'=>'Especialidades','url'=>route('paciente.especialidades.index')],
    ['label'=>$esp->nome]
  ]" />

  <x-ui.hero :title="$esp->nome"
             subtitle="Médicos disponíveis e marcação rápida."
             height="160px" />

  <div class="bg-zinc-50">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

      {{-- Filtros --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="md:col-span-2">
          <input id="q" type="search" placeholder="Procurar médico por nome…"
                 class="w-full h-11 rounded-xl border border-zinc-300 px-3 text-sm focus:border-emerald-600 focus:ring-emerald-600">
        </div>
        <select id="order" class="h-11 rounded-xl border border-zinc-300 px-3 text-sm focus:border-emerald-600 focus:ring-emerald-600">
          <option value="az">Ordenar A–Z</option>
          <option value="za">Ordenar Z–A</option>
        </select>
      </div>

      {{-- Lista de médicos --}}
      @if($medicos->count())
        <div id="grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($medicos as $m)
            @php
              $u = $m->user;
              $nome = $u?->name ?? 'Médico';
              $avatar = $u?->avatar_url ?? route('avatar.initials', ['user'=>$u?->id]);
              $perfilUrl = route('paciente.medicos.show', $m->id);
              $agendarUrl = route('paciente.consultas.create', ['especialidade' => $esp->id, 'medico_id' => $u?->id]);
            @endphp

            <article class="rounded-2xl border border-zinc-200 bg-white p-4 flex flex-col gap-4"
                     data-name="{{ Str::lower($nome) }}">
              <div class="flex items-center gap-3">
                <img src="{{ $avatar }}" alt="Avatar {{ $nome }}"
                     class="w-12 h-12 rounded-full object-cover ring-1 ring-zinc-200">
                <div class="min-w-0">
                  <h3 class="font-semibold text-zinc-900 truncate">{{ $nome }}</h3>
                  <div class="text-xs text-zinc-600 truncate">{{ $esp->nome }}</div>
                </div>
              </div>

              <div class="mt-auto flex items-center justify-between gap-2">
                <a href="{{ $perfilUrl }}"
                   class="inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm hover:bg-zinc-50">
                  Ver perfil
                </a>
                <a href="{{ $agendarUrl }}"
                   class="inline-flex items-center justify-center rounded-xl bg-emerald-700 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-800">
                  Marcar consulta
                </a>
              </div>
            </article>
          @endforeach
        </div>
      @else
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 text-zinc-600">
          Não há médicos associados a esta especialidade.
        </div>
      @endif
    </div>
  </div>
@endsection

@push('body-end')
  @vite('resources/js/pages/paciente/especialidades/especialidades-paciente-show.js')
@endpush
