@extends('layouts.app')
@section('title', 'Minhas consultas | MediGest+')

@php
  use Carbon\Carbon;
  use Illuminate\Support\Str;

  $tab = $tab ?? request('tab','futuras'); // funciona mesmo que o controller ainda não envie $tab

  // helper p/ data segura (evita "double time")
  $safeDate = function($c){
      $dt = $c->data instanceof Carbon ? $c->data->copy() : Carbon::parse($c->data);
      if (!empty($c->hora)) { try { $dt->setTimeFromTimeString($c->hora); } catch (\Throwable $e) {} }
      return $dt;
  };

  $tipoClass = function($tipo){
      $t = Str::of((string)$tipo)->lower();
      return $t->contains('urg') ? 'bg-rose-50 text-rose-700 ring-rose-200'
           : ($t->contains('prio') ? 'bg-amber-50 text-amber-700 ring-amber-200'
           : 'bg-emerald-50 text-emerald-700 ring-emerald-200');
  };

  $estadoPill = function($estado){
      $s = Str::of((string)$estado)->lower();
      return $s->contains('confirm') ? 'bg-emerald-600'
           : ($s->contains('agend')   ? 'bg-emerald-500'
           : ($s->contains('pendente_medico') ? 'bg-violet-500'
           : ($s->contains('pend')   ? 'bg-amber-500'
           : 'bg-rose-500')));
  };
@endphp

@section('content')
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início','url'=>route('home')],
    ['label'=>'Área do paciente','url'=>route('paciente.home')],
    ['label'=>'Todas as consultas']
  ]" />

  <x-ui.hero title="Minhas consultas" subtitle="Veja o histórico e o que vem a seguir." height="140px" />

  <div class="bg-zinc-50">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-5">

      {{-- Toolbar: Tabs + CTA --}}
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="inline-flex rounded-2xl border border-zinc-200 bg-white p-1 shadow-sm">
          @foreach (['futuras'=>'Futuras', 'passadas'=>'Passadas', 'todas'=>'Todas'] as $key=>$lbl)
            <a href="{{ request()->fullUrlWithQuery(['tab'=>$key,'page'=>1]) }}"
               class="px-3.5 py-1.5 text-sm rounded-xl transition
                      {{ $tab===$key ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-zinc-700 hover:bg-zinc-50' }}">
              {{ $lbl }}
            </a>
          @endforeach
        </div>

        @if(Route::has('paciente.consultas.create'))
          <a href="{{ route('paciente.consultas.create') }}"
             class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm ring-1 ring-emerald-700/20">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Marcar consulta
          </a>
        @endif
      </div>

      @if($consultas->count())
        @php
          $from  = $consultas->firstItem() ?? 0;
          $to    = $consultas->lastItem()  ?? $consultas->count();
          $total = $consultas->total();
          $current = $consultas->currentPage();
          $last    = $consultas->lastPage();
          $window  = 1;
          $pages = [1];
          for ($i=$current-$window; $i<=$current+$window; $i++) if ($i>1 && $i<$last) $pages[]=$i;
          if ($last>1) $pages[]=$last;
          $pages = array_values(array_unique(array_filter($pages, fn($n)=>$n>=1 && $n<=$last)));
          sort($pages);
          $prevUrl = $current>1 ? request()->fullUrlWithQuery(['page'=>$current-1]) : null;
          $nextUrl = $current<$last ? request()->fullUrlWithQuery(['page'=>$current+1]) : null;
        @endphp

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-[980px] w-full text-sm text-left text-zinc-700">
              <thead class="bg-zinc-50 text-xs uppercase text-zinc-500 sticky top-0 z-10">
                <tr>
                  <th class="px-6 py-3">Médico</th>
                  <th class="px-6 py-3">Especialidade</th>
                  <th class="px-6 py-3">Data</th>
                  <th class="px-6 py-3">Hora</th>
                  <th class="px-6 py-3">Tipo</th>
                  <th class="px-6 py-3">Estado</th>
                </tr>
              </thead>
              <tbody class="[&_tr:nth-child(odd)]:bg-zinc-50/40">
                @foreach($consultas as $c)
                  @php
                    $dtObj = $safeDate($c);
                    $isFuture = $dtObj->gte(now());
                    $med = $c->medico->name ?? '—';
                    $esp = $c->especialidade->nome ?? '—';
                    $tipo= $c->tipo->nome ?? Str::headline($c->tipo_slug ?? '-');
                    $estadoDisp = (function($raw){
                      $s = Str::of((string)$raw)->lower();
                      $map = [
                        'confirmada'=>'confirmed','confirmado'=>'confirmed','agendada'=>'scheduled',
                        'pendente'=>'pending','pendente_medico'=>'pending_doctor','cancelada'=>'canceled','cancelado'=>'canceled',
                        'cancelada_medico'=>'canceled',
                      ];
                      foreach ($map as $k=>$v) if ($s->contains($k)) return __('messages.status.'.$v);
                      return $raw ?: '-';
                    })($c->estado ?? null);
                  @endphp

                  <tr class="border-t border-zinc-100 hover:bg-emerald-50/40">
                    <td class="px-6 py-3">
                      <div class="flex items-center gap-3">
                        <div class="h-7 w-7 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-semibold">
                          {{ Str::of($med)->substr(0,1)->upper() }}
                        </div>
                        <div class="max-w-[28ch] truncate">{{ $med }}</div>
                      </div>
                    </td>
                    <td class="px-6 py-3 max-w-[26ch] truncate">{{ $esp }}</td>
                    <td class="px-6 py-3">
                      <div class="font-medium text-zinc-900">{{ $dtObj->format('d/m/Y') }}</div>
                      <div class="text-[11px] text-zinc-500">{{ $dtObj->translatedFormat('D') }}</div>
                    </td>
                    <td class="px-6 py-3">{{ $c->hora }}</td>
                    <td class="px-6 py-3">
                      <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $tipoClass($tipo) }}">
                        {{ $tipo }}
                      </span>
                    </td>
                    <td class="px-6 py-3">
                      <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white {{ $estadoPill($c->estado ?? '') }}">
                          {{ $estadoDisp }}
                        </span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold
                                     {{ $isFuture ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-600' }}">
                          {{ $isFuture ? 'Futura' : 'Passada' }}
                        </span>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Footer / paginação --}}
          <nav class="flex flex-col items-start gap-3 md:flex-row md:items-center md:justify-between p-4 border-t border-zinc-100 bg-white"
               aria-label="Table navigation">
            <span class="text-sm text-zinc-500">Mostrando
              <span class="font-semibold text-zinc-900">{{ $from }}</span> –
              <span class="font-semibold text-zinc-900">{{ $to }}</span> de
              <span class="font-semibold text-zinc-900">{{ $total }}</span>
            </span>

            <ul class="inline-flex items-stretch -space-x-px">
              <li>
                @if($prevUrl)
                  <a href="{{ $prevUrl }}" class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-zinc-600 bg-white rounded-l-lg border border-zinc-300 hover:bg-zinc-100 hover:text-zinc-700">
                    <span class="sr-only">Anterior</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                  </a>
                @else
                  <span class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-zinc-400 bg-white rounded-l-lg border border-zinc-200 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                  </span>
                @endif
              </li>

              @php $prevShown = null; @endphp
              @foreach($pages as $p)
                @if(!is_null($prevShown) && $p > $prevShown + 1)
                  <li><span class="flex items-center justify-center px-3 py-2 text-sm leading-tight text-zinc-500 bg-white border border-zinc-300">…</span></li>
                @endif

                @if($p == $current)
                  <li><span aria-current="page" class="z-10 flex items-center justify-center px-3 py-2 text-sm leading-tight border text-emerald-700 bg-emerald-50 border-emerald-200">{{ $p }}</span></li>
                @else
                  <li><a href="{{ request()->fullUrlWithQuery(['page'=>$p]) }}" class="flex items-center justify-center px-3 py-2 text-sm leading-tight text-zinc-600 bg-white border border-zinc-300 hover:bg-zinc-100 hover:text-zinc-800">{{ $p }}</a></li>
                @endif

                @php $prevShown = $p; @endphp
              @endforeach

              <li>
                @if($nextUrl)
                  <a href="{{ $nextUrl }}" class="flex items-center justify-center h-full py-1.5 px-3 text-zinc-600 bg-white rounded-r-lg border border-zinc-300 hover:bg-zinc-100 hover:text-zinc-700">
                    <span class="sr-only">Seguinte</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                  </a>
                @else
                  <span class="flex items-center justify-center h-full py-1.5 px-3 text-zinc-400 bg-white rounded-r-lg border border-zinc-200 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                  </span>
                @endif
              </li>
            </ul>
          </nav>
        </div>
      @else
        <div class="p-10 text-center bg-white rounded-2xl shadow-sm ring-1 ring-zinc-200/60">
          <div class="text-zinc-700 font-medium">Ainda não tem consultas.</div>
          @if(Route::has('paciente.consultas.create'))
            <a href="{{ route('paciente.consultas.create') }}"
               class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-white bg-emerald-700 hover:bg-emerald-800">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
              Marcar primeira consulta
            </a>
          @endif
        </div>
      @endif

    </div>
  </div>
@endsection
