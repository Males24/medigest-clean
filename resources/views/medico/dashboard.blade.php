@extends('layouts.dashboard')

@section('title', 'Dashboard Médico')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Olá, Dr(a). {{ auth()->user()->name }}</h1>
    <p class="text-gray-600">Este é o seu painel médico. Aqui pode visualizar consultas, pacientes, notificações, etc.</p>

    {{-- Exemplo de cards / estatísticas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Consultas de hoje</h2>
            <p class="text-2xl font-bold text-home-medigest">5</p>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Pacientes ativos</h2>
            <p class="text-2xl font-bold text-home-medigest">18</p>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Alertas</h2>
            <p class="text-2xl font-bold text-red-500">2</p>
        </div>
    </div>
@endsection
