<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'MediGest')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- MUDANÇA AQUI: min-h-screen + flex + flex-col --}}
<body class="min-h-screen flex flex-col bg-zinc-100 text-gray-900">

    {{-- Conteúdo Principal --}}
    {{-- MUDANÇA AQUI: flex-1 (ocupa o espaço restante) --}}
    <main class="flex-1">
        @yield('content')
    </main>
    
</body>
</html>