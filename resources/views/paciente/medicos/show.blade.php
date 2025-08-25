@extends('layouts.app')
@section('title', (($medico->user?->name) ?? 'Médico').' | Corpo clínico | MediGest+')

@php
  use Illuminate\Support\Facades\Route;

  $r = fn(string $name, array $params = [], string $fallback = '#')
      => Route::has($name) ? route($name, $params) : $fallback;

  $u       = $medico->user ?? null;
  $nome    = $u?->name ?? 'Médico';
  $avatar  = $u?->avatar_url
            ?? route('avatar.initials', ['user' => $medico->user_id, 'v' => $u?->updated_at?->timestamp]);
  $cover   = $medico->cover_url  ?? asset('/exemplo2.png');

  $especialidades = collect($medico->especialidades ?? []);
  $espNames = $especialidades->pluck('nome')->filter()->values();

  $slotsUrl = Route::has('api.slots') ? route('api.slots') : url('/api/slots');
@endphp

@push('head')
<style>
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:1rem}
  .card-h{padding:1rem 1.25rem;border-bottom:1px solid #e5e7eb}
  .card-b{padding:1rem 1.25rem}
  .chip{display:inline-flex;align-items:center;border-radius:9999px;padding:.2rem .6rem;font-size:.72rem;background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;font-weight:600}
  .btn{display:inline-flex;align-items:center;justify-content:center;height:44px;padding:0 18px;border-radius:.75rem;font-weight:600}
  .btn-pri{background:#047857;color:#fff}.btn-pri:hover{background:#065f46}
  .btn-sec{background:#fff;border:1px solid #d1d5db}.btn-sec:hover{background:#f9fafb}
  .kpi{border:1px solid #e5e7eb;border-radius:.75rem;padding:.75rem 1rem}
  .slot-chip{border:1px solid #e5e7eb;border-radius:.75rem;padding:.5rem .75rem;font-size:.875rem;background:#fff}
  .slot-chip[aria-pressed="true"]{border-color:#10b981;background:#ecfdf5;box-shadow:0 0 0 2px rgba(16,185,129,.35) inset}
</style>
@endpush

@section('content')
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início','url'=>route('home')],
    ['label'=>'Serviços','url'=>Route::has('paciente.consultas.index') ? route('paciente.consultas.index') : '#'],
    ['label'=>'Corpo Clínico','url'=>Route::has('paciente.medicos.index') ? route('paciente.medicos.index') : '#'],
    ['label'=>$nome]
  ]" />

  <x-ui.hero :title="$nome" :subtitle="($espNames->count() ? $espNames->join(' • ') : ' ')" :bg="$cover" height="160px" />

  <meta name="api-slots" content="{{ $slotsUrl }}">
  <meta name="doctor-id" content="{{ $medico->user_id ?? '' }}">

  <div class="bg-zinc-50">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

      <section class="card">
        <div class="card-b">
          <div class="flex items-start gap-4">
            <img src="{{ $avatar }}" alt="Foto de {{ $nome }}" class="w-20 h-20 rounded-full object-cover ring-1 ring-gray-200">
            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-semibold text-zinc-900 truncate">{{ $nome }}</h1>
                @if($espNames->count())
                  <span class="text-sm text-zinc-600">•</span>
                  <div class="flex flex-wrap gap-1.5">
                    @foreach($espNames->take(3) as $n)
                      <span class="chip">{{ $n }}</span>
                    @endforeach
                    @if($espNames->count() > 3)
                      <span class="chip">+{{ $espNames->count()-3 }}</span>
                    @endif
                  </div>
                @endif
              </div>

              @if(!empty($u?->email) || !empty($u?->phone) || !empty($medico->phone))
                <div class="mt-2 flex flex-wrap gap-3 text-sm text-zinc-600">
                  @if(!empty($u?->email))
                    <span class="inline-flex items-center gap-1">
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/></svg>
                      {{ $u->email }}
                    </span>
                  @endif
                  @php $fone = $u?->phone ?? $medico->phone ?? null; @endphp
                  @if($fone)
                    <span class="inline-flex items-center gap-1">
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92V21a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 3 7.18 2 2 0 0 1 5 5h4.09a1 1 0 0 1 1 .75l1 3a1 1 0 0 1-.27 1L9.91 11a16 16 0 0 0 6.16 6.16l1.22-1.91a1 1 0 0 1 1-.27l3 1a1 1 0 0 1 .71 1Z"/></svg>
                      {{ $fone }}
                    </span>
                  @endif
                </div>
              @endif

              <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ $r('paciente.consultas.create', ['medico'=>$medico->user_id, 'medico_id'=>$medico->user_id]) }}" class="btn btn-pri">
                  Marcar consulta
                </a>
                <a href="{{ $r('paciente.consultas.index') }}" class="btn btn-sec">Ver opções de marcação</a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <section class="card lg:col-span-2">
          <header class="card-h"><h2 class="text-lg font-semibold text-zinc-900">Sobre</h2></header>
          <div class="card-b">
            @if(!empty($medico->bio))
              <p class="text-zinc-700 whitespace-pre-line leading-relaxed">{{ $medico->bio }}</p>
            @else
              <p class="text-zinc-600">Sem biografia disponível.</p>
            @endif
          </div>
        </section>

        <aside class="card">
          <header class="card-h"><h2 class="text-lg font-semibold text-zinc-900">Informação</h2></header>
          <div class="card-b space-y-3">
            <div class="kpi"><div class="text-xs text-zinc-500">Especialidades</div><div class="text-base font-semibold text-zinc-900">{{ $espNames->count() }}</div></div>
            @if(!empty($medico->localidade))
              <div class="kpi"><div class="text-xs text-zinc-500">Localidade</div><div class="text-base font-semibold text-zinc-900">{{ $medico->localidade }}</div></div>
            @endif
            @if(!empty($medico->nif))
              <div class="kpi"><div class="text-xs text-zinc-500">NIF</div><div class="text-base font-semibold text-zinc-900">{{ $medico->nif }}</div></div>
            @endif
          </div>
        </aside>
      </div>

      <section class="card" aria-labelledby="h-horarios">
        <header class="card-h flex items-center justify-between">
          <h2 id="h-horarios" class="text-lg font-semibold text-zinc-900">Disponibilidade</h2>
          <div class="text-sm text-zinc-600"><span id="slotsStatus">—</span></div>
        </header>
        <div class="card-b space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
              <label for="slotData" class="block text-sm font-medium text-zinc-800">Data</label>
              <input id="slotData" type="date" class="mt-1 w-full h-11 rounded-xl border border-zinc-300 bg-white px-3 text-sm
                     focus:border-emerald-600 focus:ring-emerald-600" min="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}">
            </div>
            <div>
              <label for="slotTipo" class="block text-sm font-medium text-zinc-800">Tipo</label>
              <select id="slotTipo" class="mt-1 w-full h-11 rounded-xl border border-zinc-300 bg-white px-3 text-sm
                      focus:border-emerald-600 focus:ring-emerald-600">
                <option value="">Qualquer</option>
                <option value="normal">Normal</option>
                <option value="prioritaria">Prioritária</option>
                <option value="urgente">Urgente</option>
              </select>
            </div>
            <div class="flex items-end">
              <a id="slotCTA" href="{{ $r('paciente.consultas.create', ['medico'=>$medico->user_id, 'medico_id'=>$medico->user_id]) }}" class="btn btn-pri w-full">
                Marcar com este médico
              </a>
            </div>
          </div>

          <div id="slotsWrap" class="rounded-xl border border-zinc-200 bg-white p-3">
            <div class="text-sm text-zinc-500 px-1">Selecione a data (e, opcionalmente, o tipo) para ver horários.</div>
          </div>
        </div>
      </section>

    </div>
  </div>
@endsection

@push('body-end')
  @vite('resources/js/pages/paciente/medicos/medicos-paciente-show.js')
@endpush
