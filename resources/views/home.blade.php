@extends('layouts.app')

@section('title', 'MediGest+ | Página Inicial')

@section('content')

  {{-- CARROSSEL (full-bleed) --}}
  <section class="w-[100vw] relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw]">
    <div class="homepage-banners-carousel w-full h-[420px] md:h-[520px] xl:h-[574px] bg-white">
      <div id="carousel-indicators" class="relative w-full h-full" data-carousel="static">
        <div class="relative h-full overflow-hidden">

          {{-- SLIDE 1 --}}
          <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <div class="relative w-full h-full bg-cover bg-center flex items-center justify-start
                        px-6 sm:px-10 lg:px-[100px] text-white"
                 style="background-image: url('{{ asset('/exemplo1.png') }}');">
              <div class="max-w-4xl z-10">
                <p class="text-sm md:text-base font-semibold tracking-wide uppercase text-white/90">Sem complicações</p>
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-4">Gestão médica, simples e poderosa</h1>
                <p class="text-base md:text-2xl mb-6 text-white/90">O MediGest+ conecta médicos e pacientes numa plataforma para agendamento, histórico e gestão clínica com facilidade.</p>
                <a href="#"
                   class="inline-flex items-center justify-center bg-[var(--color-home-medigest-button)]
                          text-[var(--color-teal-800)] font-semibold px-5 md:px-6 py-2.5 md:py-3 rounded-xl
                          hover:bg-[var(--color-teal-50)] transition">
                  Saber mais
                </a>
              </div>
              <div class="hidden lg:block absolute bottom-0 right-0 max-w-[600px] w-auto z-0">
                <img src="{{ asset('/doctorsmile.png') }}" alt="Médico com laptop" class="object-contain">
              </div>
            </div>
          </div>

          {{-- SLIDE 2 (ativo) --}}
          <div class="block duration-700 ease-in-out" data-carousel-item="active">
            <div class="w-full h-full bg-cover bg-center flex items-center justify-start text-white
                        px-6 sm:px-10 lg:px-[100px]"
                 style="background-image: url('{{ asset('/exemplo2.png') }}');">
              <div>
                <p class="text-sm md:text-base font-semibold tracking-wide uppercase text-white/90">Já disponível</p>
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-4">Nova App MediGest+</h1>
                <p class="text-base md:text-2xl mb-6 text-white/90">Crie a sua conta gratuita e comece a otimizar o seu dia.</p>
                <button type="button" data-auth-open="register"
                   class="inline-flex items-center justify-center bg-[var(--color-home-medigest-button)]
                          text-[var(--color-home-medigest)] font-semibold px-5 md:px-6 py-2.5 md:py-3 rounded-xl
                          hover:bg-[var(--color-home-medigest-button-hover)] transition">
                  Crie sua conta grátis
                </button>
              </div>
              <div class="hidden lg:block absolute bottom-0 right-0 max-w-[800px] w-auto z-0">
                <img src="{{ asset('/doctor_app.png') }}" alt="Aplicação MediGest+" class="object-contain">
              </div>
            </div>
          </div>
        </div>

        {{-- Indicadores --}}
        <div class="absolute z-30 flex space-x-3 -translate-x-1/2 bottom-5 left-1/2">
          <button type="button" class="w-3 h-3 rounded-full bg-gray-300" data-carousel-slide-to="0" aria-label="Slide 1"></button>
          <button type="button" class="w-3 h-3 rounded-full bg-gray-300" data-carousel-slide-to="1" aria-label="Slide 2"></button>
        </div>

        {{-- Navegação --}}
        <button type="button" class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4" data-carousel-prev>
          <span class="inline-flex items-center justify-center w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/50 group-hover:bg-white/80">
            <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </span>
        </button>
        <button type="button" class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4" data-carousel-next>
          <span class="inline-flex items-center justify-center w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/50 group-hover:bg-white/80">
            <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </button>
      </div>
    </div>
  </section>

  {{-- BLOCO "APP" --}}
  <section class="bg-zinc-50 py-16 md:py-20">
    <div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-center justify-between gap-8">
      <div class="relative rounded-[2.5rem] w-full lg:w-[45%] p-10 shadow-xl min-h-[360px] md:min-h-[420px] flex items-center justify-center"
           style="background-image: url('{{ asset('/exemplo2.png') }}');">
        <img src="{{ asset('/medicasmile.png') }}" alt="Doutora com celular"
             class="absolute -top-20 md:-top-28 left-1/2 -translate-x-[60%] w-[240px] md:w-[300px] lg:w-[400px] object-contain z-20" />
      </div>

      <div class="w-full lg:w-[45%] space-y-5">
        <h2 class="text-3xl lg:text-4xl font-bold text-medigest mb-3">MediGest+</h2>
        <p class="text-2xl lg:text-3xl font-bold text-medigest-darktext">Controle completo na palma da mão</p>

        @foreach([
          ['icon' => 'calendar', 'title' => 'Agenda médica em tempo real', 'desc' => 'Visualize e atualize sua agenda instantaneamente de qualquer dispositivo.'],
          ['icon' => 'history',  'title' => 'Histórico de atendimentos',   'desc' => 'Tenha todos os registros organizados e acessíveis para análise clínica.'],
          ['icon' => 'bell',     'title' => 'Alertas de consulta',         'desc' => 'Receba notificações sobre compromissos para evitar esquecimentos.'],
          ['icon' => 'check',    'title' => 'Acesso seguro e multi-dispositivo', 'desc' => 'Use em qualquer lugar com criptografia e segurança avançada.'],
        ] as $feature)
          <div class="bg-white border border-gray-200 rounded-xl p-5 shadow flex items-start gap-4">
            <div class="bg-home-medigest w-10 h-10 flex items-center justify-center rounded-xl text-home-medigest-button">
              @if ($feature['icon'] === 'calendar')
                <x-icons.calendar class="text-home-medigest" />
              @elseif ($feature['icon'] === 'history')
                <x-icons.menu class="text-home-medigest"/>
              @elseif ($feature['icon'] === 'bell')
                <x-icons.bell class="w-10 h-10 mx-auto text-home-medigest"/>
              @elseif ($feature['icon'] === 'check')
                <x-icons.check class="text-home-medigest"/>
              @endif
            </div>
            <div>
              <p class="text-md font-semibold text-medigest-darktext">{{ $feature['title'] }}</p>
              <p class="text-sm text-gray-600">{{ $feature['desc'] }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- FUNCIONALIDADES RÁPIDAS --}}
  <section class="py-16 md:py-20 bg-zinc-50 text-center border-t-4 border-zinc-100">
    <div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl font-semibold mb-10">Porque escolher o MediGest+</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
        <div class="p-6 bg-white rounded-xl shadow hover:shadow-md transition">
          <h3 class="text-xl font-semibold mt-4">Fácil de usar</h3>
          <p class="mt-2 text-sm text-gray-600">Interface intuitiva, feita para todos os perfis.</p>
        </div>
        <div class="p-6 bg-white rounded-xl shadow hover:shadow-md transition">
          <h3 class="text-xl font-semibold mt-4">Totalmente online</h3>
          <p class="mt-2 text-sm text-gray-600">Marcação de consultas com poucos cliques.</p>
        </div>
        <div class="p-6 bg-white rounded-xl shadow hover:shadow-md transition">
          <h3 class="text-xl font-semibold mt-4">Mais produtividade</h3>
          <p class="mt-2 text-sm text-gray-600">Ganhe tempo e reduza o retrabalho com automação.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- CTA FINAL (apto para produção) --}}
  <section class="relative isolate overflow-hidden bg-home-medigest-hover text-white">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute -top-20 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-24 right-0 w-96 h-96 rounded-full bg-white/5 blur-3xl"></div>
    </div>

    <div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
      <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-8">

        <div class="max-w-2xl">
          <span class="inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wide
                      bg-white/10 px-3 py-1 rounded-full ring-1 ring-white/20">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
            Novo • Grátis para começar
          </span>

          <h2 class="mt-3 text-3xl md:text-4xl font-bold leading-tight">
            Comece já com o <span class="opacity-95">MediGest+</span>
          </h2>

          <p class="mt-3 text-base md:text-lg text-white/90">
            Agilize a sua clínica e ofereça uma experiência moderna aos pacientes — marcações,
            horários e notificações, tudo no mesmo sítio.
          </p>

          <ul class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-white/90">
            <li class="inline-flex items-center gap-2">
              <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3-3A1 1 0 016.204 9.79l2.293 2.293 6.793-6.793a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
              Sem fidelização
            </li>
            <li class="inline-flex items-center gap-2">
              <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3-3A1 1 0 016.204 9.79l2.293 2.293 6.793-6.793a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
              Comece em minutos
            </li>
            <li class="inline-flex items-center gap-2">
              <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3-3A1 1 0 016.204 9.79l2.293 2.293 6.793-6.793a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
              Suporte em PT
            </li>
          </ul>
        </div>

        <div class="w-full md:w-[360px] md:self-start">
          <div class="backdrop-blur p-5 md:p-12">
            <button type="button" data-auth-open="register"
              class="w-full inline-flex items-center justify-center h-11 md:h-12 px-6 md:px-7 rounded-xl
                      font-semibold text-home-medigest bg-white hover:bg-gray-100 active:bg-white/90
                      ring-1 ring-white/40 shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80"
              aria-label="Criar conta no MediGest+">
              Criar Conta
            </button>

            <button type="button" data-auth-open="login"
              class="mt-5 block w-full text-center text-sm text-white/80 hover:text-white underline underline-offset-4">
              Já tens conta? Iniciar sessão
            </button>
          </div>
        </div>

      </div>
    </div>
  </section>

@endsection
