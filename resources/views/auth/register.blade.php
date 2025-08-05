@extends('layouts.app')

@section('title', 'Criar Conta | MediGest+')

@section('content')
<div class="container max-w-md mx-auto mt-10 p-6 bg-white shadow-md rounded">
    <h2 class="text-2xl font-semibold mb-4 text-center">Criar Conta</h2>

    @if ($errors->any())
        <div class="mb-4">
            <ul class="text-red-600 text-sm list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block font-medium">Nome</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full border border-gray-300 p-2 rounded" />
        </div>

        <div>
            <label class="block font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full border border-gray-300 p-2 rounded" />
        </div>

        <div>
            <label class="block font-medium">Palavra-passe</label>
            <input type="password" name="password" required
                class="w-full border border-gray-300 p-2 rounded" />
        </div>

        <div>
            <label class="block font-medium">Confirmar Palavra-passe</label>
            <input type="password" name="password_confirmation" required
                class="w-full border border-gray-300 p-2 rounded" />
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Registar
            </button>
        </div>
    </form>
</div>
@endsection
