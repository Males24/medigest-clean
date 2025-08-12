@extends('layouts.auth')

@section('title', 'Recuperar Palavra-passe')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen bg-zinc-100 px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-md p-8">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Esqueceu-se da palavra-passe?</h2>

        <p class="text-sm text-gray-600 mb-4 text-center">
            Introduza o seu endereço de e-mail e enviaremos instruções para redefinir a palavra-passe.
        </p>

        @if (session('status'))
            <div class="mb-4 text-green-600 font-medium">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="#">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input id="email" type="email" name="email" required autofocus
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-home-medigest">
            </div>

            <button type="submit"
                    class="w-full bg-home-medigest text-white font-semibold py-2 px-4 rounded-lg hover:bg-home-medigest-hover transition">
                Enviar instruções
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login.form') }}" class="text-sm text-home-medigest hover:underline">
                Voltar ao login
            </a>
        </div>
    </div>
</div>
@endsection
