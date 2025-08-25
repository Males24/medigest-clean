@extends('layouts.app')
@section('title','Corpo clínico | MediGest+')

@php
  $medicos = collect($medicos ?? []);
  $especialidades = collect($especialidades ?? []);
@endphp

@push('head')
<style>
  .doc-card{border:1px solid #e5e7eb;border-radius:1rem;background:#fff;overflow:hidden}
  .doc-top{display:flex;gap:.9rem;padding:1rem 1rem .75rem 1rem;align-items:center}
  .doc-avatar{width:64px;height:64px;border-radius:999px;object-fit:cover;border:1px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.05)}
  .chip{display:inline-flex;align-items:center;border-radius:9999px;padding:.15rem .55rem;font-size:.72rem;background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0}
  .btn{display:inline-flex;align-items:center;justify-content:center;height:40px;padding:0 14px;border-radius:.65rem;font-weight:600}
  .btn-pri{background:#047857;color:#fff}.btn-pri:hover{background:#065f46}
  .btn-sec{background:#fff;border:1px solid #d1d5db}.btn-sec:hover{background:#f9fafb}
</style>
@endpush

@section('content')
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início','url'=>route('home')],
    ['label'=>'Serviços','url'=>Route::has('paciente.consultas.index') ? route('paciente.consultas.index') : '#'],
    ['label'=>'Corpo Clínico']
  ]" />
  <x-ui.hero title="Corpo Clínico" subtitle="Conheça todos os médicos e marque a sua consulta." height="160px" />

  <div class="bg-zinc-50">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-3">
        <input id="q" type="search" placeholder="Pesquisar médico…"
               class="w-full h-11 rounded-xl border border-zinc-300 px-3 text-sm focus:border-emerald-600 focus:ring-emerald-600">
        <select id="fEsp" class="w-full h-11 rounded-xl border border-zinc-300 px-3 text-sm focus:border-emerald-600 focus:ring-emerald-600">
          <option value="">Todas as especialidades</option>
          @foreach($especialidades as $e)
            <option value="{{ strtolower($e->nome) }}">{{ $e->nome }}</option>
          @endforeach
        </select>
        <select id="ord" class="w-full h-11 rounded-xl border border-zinc-300 px-3 text-sm focus:border-emerald-600 focus:ring-emerald-600">
          <option value="az">Ordenar A–Z</option>
          <option value="za">Ordenar Z–A</option>
        </select>
      </div>

      @if($medicos->count())
        <div id="grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($medicos as $m)
            @php
              $user      = $m->user;
              $name      = $user?->name ?? 'Médico';
              $email     = $user?->email ?? null;
              $avatar    = $user?->avatar_url
                            ?? route('avatar.initials', ['user' => $m->user_id, 'v' => $user?->updated_at?->timestamp]);
              $esps      = collect($m->especialidades ?? []);
              $espNames  = $esps->pluck('nome')->filter()->values();
              $espStr    = strtolower($espNames->join(', '));
              $urlPerfil = Route::has('paciente.medicos.show') ? route('paciente.medicos.show', $m->id) : '#';
              $urlMarcar = Route::has('paciente.consultas.create')
                          ? route('paciente.consultas.create', ['medico' => $m->user_id])
                          : '#';
            @endphp

            <div class="doc-card" data-name="{{ strtolower($name) }}" data-esps="{{ $espStr }}">
              <div class="doc-top">
                <img class="doc-avatar" src="{{ $avatar }}" alt="{{ $name }}">
                <div class="min-w-0">
                  <div class="font-semibold text-zinc-900 truncate">{{ $name }}</div>
                  @if($email)
                    <div class="text-xs text-zinc-500 truncate">{{ $email }}</div>
                  @endif
                  @if($espNames->count())
                    <div class="mt-1 flex flex-wrap gap-1.5">
                      @foreach($espNames->take(3) as $n)
                        <span class="chip">{{ $n }}</span>
                      @endforeach
                      @if($espNames->count() > 3)
                        <span class="chip">+{{ $espNames->count()-3 }}</span>
                      @endif
                    </div>
                  @endif
                </div>
              </div>

              <div class="px-4 pb-4 flex items-center justify-between">
                <a href="{{ $urlPerfil }}" class="btn btn-sec">Ver perfil</a>
                <a href="{{ $urlMarcar }}" class="btn btn-pri">Marcar consulta</a>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="rounded-xl border border-zinc-200 bg-white p-6 text-zinc-600">
          Sem médicos para mostrar.
        </div>
      @endif
    </div>
  </div>
@endsection

@push('body-end')
  @vite('resources/js/pages/paciente/medicos/medicos-paciente-index.js')
@endpush
