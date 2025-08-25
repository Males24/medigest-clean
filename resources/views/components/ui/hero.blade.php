@props([
  'title'    => null,
  'subtitle' => null,
  // passe asset('...') ou URL absoluta
  'bg'       => null,
  'height'   => '260px',
  'align'    => 'left', // 'left' | 'center'
])

<section class="w-[100vw] relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw]">
  <div class="relative" style="height: {{ $height }};">
    @if($bg)
      <div class="absolute inset-0 bg-cover bg-center" style="background-image:url('{{ $bg }}')"></div>
      <div class="absolute inset-0 bg-black/35"></div>
    @else
      <div class="absolute inset-0 bg-gradient-to-br from-emerald-700 to-emerald-600"></div>
    @endif

    <div class="relative h-full flex items-center {{ $align === 'center' ? 'justify-center' : 'justify-start' }}">
      <div class="max-w-[1200px] w-full mx-auto px-4 sm:px-6 lg:px-8 text-white">
        @if($title)
          <h1 class="text-2xl sm:text-3xl md:text-4xl font-semibold drop-shadow">{{ $title }}</h1>
        @endif
        @if($subtitle)
          <p class="mt-2 text-white/90 max-w-[70ch]">{{ $subtitle }}</p>
        @endif
      </div>
    </div>
  </div>
</section>
