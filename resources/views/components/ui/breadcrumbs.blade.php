@props([
  // Ex.: [['label'=>'Início','url'=>route('home')], ['label'=>'Área do paciente']]
  'items' => [],
  'class' => '',
])

@php
  use Illuminate\Support\Arr;

  $items = collect($items)->map(function($it){
    return [
      'label' => Arr::get($it, 'label', ''),
      'url'   => Arr::get($it, 'url'),
    ];
  });
@endphp

<nav aria-label="Breadcrumb" class="bg-zinc-50 backdrop-blur border-b border-zinc-200 {{ $class }}">
  <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
    <ol class="flex items-center gap-2 py-2.5 text-sm text-zinc-600">
      @foreach($items as $i => $it)
        @if($i > 0)
          <li aria-hidden="true" class="text-zinc-400">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.23 14.77a.75.75 0 010-1.06L10.94 10 7.23 6.29a.75.75 0 011.06-1.06l4.24 4.24a.75.75 0 010 1.06l-4.24 4.24a.75.75 0 01-1.06 0z" clip-rule="evenodd"/></svg>
          </li>
        @endif
        <li class="shrink-0">
          @if(!empty($it['url']))
            <a href="{{ $it['url'] }}" class="hover:underline text-emerald-700 hover:text-emerald-800">
              {{ $it['label'] }}
            </a>
          @else
            <span class="text-zinc-900 font-medium">{{ $it['label'] }}</span>
          @endif
        </li>
      @endforeach
    </ol>
  </div>
</nav>
