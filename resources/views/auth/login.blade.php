@extends('layouts.app')

@section('title', 'Entrar na MediGest+')

@section('content')
<div class="container max-w-md mx-auto mt-10 p-6 bg-white shadow-md rounded">
    <h2 class="text-2xl font-semibold mb-4 text-center">Login</h2>

    @if(session('error'))
        <div class="mb-4 text-red-600 font-medium">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block font-medium">Email</label>
            <input type="email" name="email" required autofocus
                class="w-full border border-gray-300 p-2 rounded" />
        </div>

        <div>
            <label class="block font-medium">Palavra-passe</label>
            <input type="password" name="password" required
                class="w-full border border-gray-300 p-2 rounded" />
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Entrar
            </button>
        </div>
    </form>
</div>
@endsection
