@extends('layouts.app')

@section('title', 'MediGest+ | Página Inicial')

@section('content')
<div class="bg-white text-gray-900 min-h-screen flex flex-col">

    {{-- CARROSSEL --}}
    <section class="homepage-banners-carousel w-full h-[574px] bg-white">
        <div id="carousel-indicators" class="relative w-full h-full" data-carousel="static">
            <div class="relative h-full overflow-hidden">

                {{-- SLIDE 1 --}}
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <div class="relative w-full h-full bg-cover bg-center flex items-center justify-start px-[100px] text-white"
                        style="background-image: url('{{ asset('/exemplo1.png') }}');">
                        <div class="max-w-2xl z-10">
                            <p class="text-5xl font-extrabold mb-4">Médico sem complicações, nunca mais.</p>
                            <p class="text-2xl mb-6">O <strong>MediGest+</strong> conecta médicos e pacientes numa plataforma inteligente para agendamento, histórico e gestão clínica com facilidade.</p>
                            <a href="#"
                            class="inline-flex items-center justify-center bg-[var(--color-home-medigest-button)] text-[var(--color-primary)] font-semibold px-6 py-3 rounded-xl hover:bg-[var(--color-home-medigest-button-hover)] transition">
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
                    <div class="w-full h-full bg-cover bg-center flex items-center justify-start text-white px-[100px]"
                        style="background-image: url('{{ asset('/exemplo2.png') }}');">
                        <div>
                            <p class="text-3xl mb-2 font-medium">Já disponível</p>
                            <p class="text-5xl font-extrabold mb-20">Nova App MediGest+</p>
                            <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center bg-[var(--color-home-medigest-button)] text-[var(--color-home-medigest)] font-semibold px-6 py-3 rounded-xl hover:bg-[var(--color-home-medigest-button-hover)] transition">
                                Crie sua conta grátis
                            </a>
                        </div>
                        <div class="hidden lg:block absolute bottom-0 right-0 max-w-[800px] w-auto z-0">
                            <img src="{{ asset('/doctor_app.png') }}" alt="Médico com laptop" class="object-contain">
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
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/50 group-hover:bg-white/80">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            </button>
            <button type="button" class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4" data-carousel-next>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/50 group-hover:bg-white/80">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            </button>
        </div>
    </section>

    <section class="bg-zinc-50 py-20">
        <div class="container mx-auto max-w-7xl flex flex-col lg:flex-row items-center justify-between gap-6">
            <!-- Imagem com gradiente -->
            <div class="relative rounded-[2.5rem] w-full lg:w-[45%] p-10 shadow-xl min-h-[420px] flex items-center justify-center"
                style="background-image: url('{{ asset('/exemplo2.png') }}');">
                <img src="{{ asset('/medicasmile.png') }}"
                    alt="Doutora com celular"
                    class="absolute top-[-120px] left-1/2 -translate-x-[60%] w-[300px] lg:w-[400px] object-contain z-20" />
            </div>

            <!-- Funcionalidades -->
            <div class="w-full lg:w-[45%] space-y-5">
                <h1 class="text-3xl lg:text-4xl font-bold text-medigest mb-5">MediGest+</h1>
                <h2 class="text-3xl lg:text-3xl font-bold text-medigest-darktext">Controle completo na palma da mão</h2>

                @foreach([
                    ['icon' => 'calendar', 'title' => 'Agenda médica em tempo real', 'desc' => 'Visualize e atualize sua agenda instantaneamente de qualquer dispositivo.'],
                    ['icon' => 'history', 'title' => 'Histórico de atendimentos', 'desc' => 'Tenha todos os registros organizados e acessíveis para análise clínica.'],
                    ['icon' => 'bell', 'title' => 'Alertas de consulta', 'desc' => 'Receba notificações sobre compromissos para evitar esquecimentos.'],
                    ['icon' => 'check', 'title' => 'Acesso seguro e multi-dispositivo', 'desc' => 'Use em qualquer lugar com criptografia e segurança avançada.'],
                ] as $feature)
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow flex items-start gap-4">
                        <div class="bg-home-medigest-hover w-10 h-10 flex items-center justify-center rounded-xl text-white">
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
    {{-- FUNCIONALIDADES --}}
    <section class="py-20 bg-zinc-50 text-center border-t-4 border-zinc-100">
        <h2 class="text-3xl font-semibold mb-10">Porque escolher o MediGest+</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto px-4">
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
    </section>

    {{-- DEPOIMENTOS --}}
    <section class="bg-white py-16 text-center">
        <h2 class="text-3xl font-semibold mb-8">O que dizem os nossos utilizadores</h2>
        <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-8 px-4">
            <div class="bg-zinc-100 p-6 rounded-lg shadow">
                <p class="text-lg italic">"A MediGest+ facilitou a minha vida clínica. Consultas mais organizadas."</p>
                <span class="block mt-4 font-semibold text-home-medigest">Dr. João Pereira</span>
            </div>
            <div class="bg-zinc-100 p-6 rounded-lg shadow">
                <p class="text-lg italic">"Como paciente, foi muito mais simples marcar uma consulta e seguir o tratamento."</p>
                <span class="block mt-4 font-semibold text-home-medigest">Ana Costa</span>
            </div>
        </div>
    </section>

    {{-- CTA FINAL --}}
    <section class="flex justify-between items-center gap-4 px-16 py-16 bg-home-medigest-hover text-white">
        <div>
            <h2 class="text-3xl font-bold mb-4">Comece já com o MediGest+</h2>
            <p class="mb-6 text-lg">Agilize a sua clínica ou acompanhe melhor os seus cuidados de saúde</p>
        </div>
        <a href="{{ route('register') }}"
           class="inline-block px-8 py-3 bg-white text-home-medigest font-semibold rounded-xl hover:bg-gray-100 transition">
            Criar Conta
        </a>
    </section>

</div>
@endsection
