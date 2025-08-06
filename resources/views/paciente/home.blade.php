@extends('layouts.app')

@section('title', 'Área do Paciente | MediGest+')

@section('content')
<div class="min-h-screen bg-zinc-100 p-6">
    {{-- Header / saudação --}}
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <h1 class="text-2xl font-semibold text-home-medigest">
            Bem-vindo, {{ Auth::user()->name }}!
        </h1>
        <p class="text-gray-600 mt-1">Aqui está a sua área pessoal como paciente.</p>
    </div>

    {{-- Cards ou funcionalidades principais --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Histórico de consultas --}}
        <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-home-medigest">Histórico de Consultas</h3>
            <p class="text-sm text-gray-600 mt-2">Veja todas as suas consultas anteriores e respetivos detalhes.</p>
        </div>

        {{-- Marcar nova consulta --}}
        <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-home-medigest">Nova Consulta</h3>
            <p class="text-sm text-gray-600 mt-2">Escolha um médico e marque uma nova consulta.</p>
        </div>

        {{-- Perfil do paciente --}}
        <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-home-medigest">Meu Perfil</h3>
            <p class="text-sm text-gray-600 mt-2">Atualize suas informações pessoais e preferências.</p>
        </div>
    </div>
</div>
@endsection
