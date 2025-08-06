@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Bem-vindo, {{ auth()->user()->name }}</h1>
    <p class="text-gray-600">Painel de administração - visão geral da aplicação.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Total de Utilizadores</h2>
            <p class="text-2xl font-bold text-home-medigest">120</p>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Médicos registados</h2>
            <p class="text-2xl font-bold text-home-medigest">10</p>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Consultas agendadas</h2>
            <p class="text-2xl font-bold text-green-600">45</p>
        </div>
    </div>
@endsection
