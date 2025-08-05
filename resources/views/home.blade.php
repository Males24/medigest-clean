@extends('layouts.app')

@section('title', 'Bem-vindo à MediGest+')

@section('content')
<div class="container mx-auto mt-10 px-4">

    {{-- HERO SECTION + CARROSSEL --}}
    <div x-data="{ slide: 1 }" class="relative overflow-hidden rounded-lg shadow-lg">
        <div class="relative h-64 sm:h-80 md:h-96">
            <template x-if="slide === 1">
                <img src="https://source.unsplash.com/featured/?healthcare" class="w-full h-full object-cover transition-all duration-700">
            </template>
            <template x-if="slide === 2">
                <img src="https://source.unsplash.com/featured/?doctor" class="w-full h-full object-cover transition-all duration-700">
            </template>
            <template x-if="slide === 3">
                <img src="https://source.unsplash.com/featured/?hospital" class="w-full h-full object-cover transition-all duration-700">
            </template>

            <div class="absolute bottom-0 bg-gradient-to-t from-black via-transparent to-transparent p-6 text-white">
                <h2 class="text-3xl font-bold">MediGest+</h2>
                <p class="text-sm">A sua plataforma inteligente de gestão médica.</p>
            </div>
        </div>

        <div class="absolute inset-0 flex items-center justify-between px-4">
            <button @click="slide = slide === 1 ? 3 : slide - 1" class="text-white text-2xl bg-black bg-opacity-40 rounded-full px-3 py-1 hover:bg-opacity-60">&#10094;</button>
            <button @click="slide = slide === 3 ? 1 : slide + 1" class="text-white text-2xl bg-black bg-opacity-40 rounded-full px-3 py-1 hover:bg-opacity-60">&#10095;</button>
        </div>
    </div>

    {{-- BOTÕES --}}
    <div class="text-center mt-8 space-x-4">
        <a href="{{ route('login.form') }}" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700 transition duration-300">Login</a>
        <a href="{{ route('register.form') }}" class="bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700 transition duration-300">Registar</a>
    </div>

    {{-- FUNCIONALIDADES --}}
    <div class="mt-12">
        <h2 class="text-2xl font-semibold mb-6 text-center">Funcionalidades</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded shadow hover:shadow-lg transition duration-300">
                <h3 class="font-bold text-xl mb-2">📅 Agendamentos</h3>
                <p>Validação de horários, conflitos e gestão eficiente das consultas.</p>
            </div>
            <div class="bg-white p-6 rounded shadow hover:shadow-lg transition duration-300">
                <h3 class="font-bold text-xl mb-2">👥 Perfis</h3>
                <p>Área dedicada para administradores, médicos e pacientes.</p>
            </div>
            <div class="bg-white p-6 rounded shadow hover:shadow-lg transition duration-300">
                <h3 class="font-bold text-xl mb-2">📊 Painéis</h3>
                <p>Consultas, especialidades e relatórios dinâmicos.</p>
            </div>
        </div>
    </div>

</div>
@endsection
