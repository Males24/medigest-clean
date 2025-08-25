@extends('layouts.app')
@section('title','Especialidades | MediGest+')

@php
  use Illuminate\Support\Str;
  $especialidades = collect($especialidades ?? []);
@endphp

@push('head')
<style>
  .sp-card{position:relative;overflow:hidden;border-radius:1rem;border:1px solid #e5e7eb;background:#fff}
  .sp-media{aspect-ratio: 5 / 3; background:#f1f5f9}
  .sp-media > .bg{position:absolute;inset:0;background-size:cover;background-position:center;filter:saturate(.95);transform:scale(1.02)}
  .sp-media::after{content:"";position:absolute;inset:0;background:linear-gradient(0deg,rgba(0,0,0,.55),rgba(0,0,0,.0) 60%)}
  .sp-title{position:absolute;left:0;right:0;bottom:.8rem;padding:0 1rem;color:#fff;font-weight:800;text-shadow:0 1px 2px rgba(0,0,0,.3)}
  .sp-body{padding:0.85rem 1rem}
  .avatar{width:24px;height:24px;border-radius:999px;object-fit:cover;border:1px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.05)}
</style>
@endpush

@section('content')
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início','url'=>route('home')],
    ['label'=>'Serviços','url'=>Route::has('paciente.consultas.index') ? route('paciente.consultas.index') : '#'],
    ['label'=>'Especialidades']
  ]" />
  <x-ui.hero title="Especialidades" subtitle="Explore as áreas clínicas e encontre médicos disponíveis." height="160px" />

  <div class="bg-zinc-50">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="md:col-span-2">
          <input id="q" type="search" placeholder="Pesquisar especialidade…"
                 class="w-full h-11 rounded-xl border border-zinc-300 px-3 text-sm focus:border-emerald-600 focus:ring-emerald-600">
        </div>
        <label class="inline-flex items-center gap-2 text-sm text-zinc-700">
          <input id="chkComMedicos" type="checkbox" class="rounded border-zinc-300 text-emerald-700 focus:ring-emerald-600">
          Mostrar apenas com médicos
        </label>
      </div>

      @php $total=$especialidades->count(); @endphp
      @if($total)
        <div id="grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($especialidades as $e)
            @php
              $cover = $e->cover_url ?? asset('/exemplo2.png');
              $docs  = collect($e->medicos ?? []);
              $count = $e->medicos_count ?? $docs->count();
              $top   = $docs->take(3);
              $url   = Route::has('paciente.especialidades.show')
                      ? route('paciente.especialidades.show',$e->id)
                      : (Route::has('paciente.consultas.nova') ? route('paciente.consultas.nova',['especialidade'=>$e->id]) : '#');
            @endphp
            <a href="{{ $url }}" class="sp-card group"
               data-name="{{ Str::lower($e->nome) }}" data-hasdocs="{{ $count>0?1:0 }}">
              <div class="sp-media">
                <div class="bg" style="background-image:url('{{ $cover }}')"></div>
                <div class="sp-title text-lg leading-tight">{{ $e->nome }}</div>
              </div>
              <div class="sp-body flex items-center justify-between">
                <div class="text-sm text-zinc-600">
                  @if($count>0)
                    <span class="font-medium text-zinc-900">{{ $count }}</span> médico{{ $count>1?'s':'' }}
                  @else
                    <span class="text-zinc-500">Sem médicos</span>
                  @endif
                </div>
                @if($top->count())
                  <div class="flex -space-x-2">
                    @foreach($top as $m)
                      @php
                        $u = $m->user ?? null;
                        $mini = $u?->avatar_url
                              ?? route('avatar.initials', ['user'=>$u?->id, 'v'=>$u?->updated_at?->timestamp]);
                        $n = $u?->name ?? 'Médico';
                      @endphp
                      <img class="avatar" src="{{ $mini }}" alt="{{ $n }}">
                    @endforeach
                  </div>
                @endif
              </div>
            </a>
          @endforeach
        </div>
      @else
        <div class="rounded-xl border border-zinc-200 bg-white p-6 text-zinc-600">Sem especialidades para mostrar.</div>
      @endif
    </div>
  </div>
@endsection

@push('body-end')
  @vite('resources/js/pages/paciente/especialidades/especialidades-paciente-index.js')
@endpush
