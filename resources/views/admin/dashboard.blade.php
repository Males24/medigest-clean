@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h2>Painel de Administração</h2>
<p>Bem-vindo ao painel, {{ Auth::user()->name }}!</p>

<a href="{{ route('logout') }}" class="btn btn-danger"
   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    Terminar sessão
</a>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
@endsection
