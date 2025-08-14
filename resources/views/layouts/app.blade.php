<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'MediGest')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('head')
</head>
{{-- MUDANÇA AQUI: min-h-screen + flex + flex-col --}}
<body class="min-h-screen flex flex-col bg-zinc-100 text-gray-900">
    {{-- Header Fixo --}}
    <header class="bg-white shadow sticky top-0 z-50">
        <div class="flex justify-between items-center px-4 sm:px-6 lg:px-[70px] py-3 gap-4 border-b border-gray-200">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="py-2 px-2 rounded-md">
                    <img src="{{ asset('/Logo_Preto.svg') }}" alt="MediGest+ Logo" class="h-10 sm:h-12 w-auto">
                </div>
            </a>

            @auth
            <div class="relative flex items-center space-x-3">
                <button id="user-menu-button" type="button"
                        class="flex text-sm bg-gray-200 rounded-full hover:ring-4 hover:ring-gray-200 shadow-xl"
                        aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom-end">
                    <span class="sr-only">Abrir menu do utilizador</span>
                    <img class="w-10 h-10 rounded-full object-cover"
                         src="{{ auth()->user()->avatar_url }}"
                         alt="{{ auth()->user()->name }}">
                </button>

                <div id="user-dropdown"
                     class="z-50 hidden absolute right-0 mt-2 w-56 bg-white divide-y divide-gray-200 rounded-xl shadow-lg">
                    <div class="px-4 py-3 text-sm text-gray-900 bg-zinc-100 rounded-lg">
                        <div class="font-semibold truncate">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                    <ul class="py-2 text-sm text-gray-700" aria-labelledby="user-menu-button">
                        <li>
                            <a href="#" class="block px-4 py-2 hover:bg-home-medigest-button transition">
                                Configurações
                            </a>
                        </li>
                    </ul>
                    <div class="py-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                Terminar sessão
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endauth

            @guest
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 bg-home-medigest-button text-home-medigest font-semibold w-[180px] h-[50px] rounded-xl hover:bg-home-medigest-button-hover transition-all">
                    <x-icons.user class="w-6 h-6 text-home-main" />
                    <span>Iniciar sessão</span>
                </a>
            </div>
            @endguest
        </div>
    </header>

    {{-- Conteúdo Principal --}}
    {{-- MUDANÇA AQUI: flex-1 (ocupa o espaço restante) --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer fixo ao fundo da janela, não em cima do conteúdo --}}
    <footer class="bg-home-medigest text-white text-center py-4 text-sm">
        &copy; {{ date('Y') }} Todos os direitos reservados | <strong>MediGest+</strong>
    </footer>
</body>
</html>
