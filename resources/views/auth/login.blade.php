@extends('layouts.auth')

@section('title', 'Login - MediGest+')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-zinc-100 px-4">
    <div class="w-full max-w-md bg-white shadow-lg rounded-xl p-8">
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('/Logo_Preto.svg') }}" alt="MediGest+" class="h-12 mb-4">
            <h1 class="text-2xl font-bold text-center mb-6 text-home-medigest">Iniciar Sessão</h1>
        </div>

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       required autofocus
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-home-medigest">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium mb-1">Palavra-passe</label>
                <input type="password" name="password" id="password"
                       required
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-home-medigest">
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="inline-flex items-center text-sm">
                    <input type="checkbox" name="remember" class="mr-2">
                    Lembrar-me
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-home-medigest-button text-home-medigest font-semibold py-2 rounded-xl hover:bg-home-medigest-button-hover transition">
                Entrar
            </button>
        </form>

        <p class="text-sm text-center mt-6">
            Ainda não tem conta?
            <a href="{{ route('register.form') }}" class="text-home-medigest hover:underline">Criar Conta</a>
        </p>
    </div>
</div>
@endsection
