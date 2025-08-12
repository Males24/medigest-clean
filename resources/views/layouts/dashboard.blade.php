<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard') | MediGest+</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('head')
</head>
<body class="bg-gray-100 text-gray-900">

    {{-- Top Navbar --}}
    <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <div class="px-4 py-4">
            <img src="{{ asset('/Logo_Preto.svg') }}" alt="MediGest+ Logo" class="h-10 sm:h-12 w-auto">
        </div>

        {{-- Dropdown do utilizador autenticado com Flowbite --}}
            @auth
            <div class="relative flex items-center space-x-3">
                {{-- Botão/avatar --}}
                <button id="user-menu-button"
                    type="button"
                    class="flex text-sm bg-gray-200 rounded-full hover:ring-4 hover:ring-gray-200 shadow-lg"
                    aria-expanded="false"
                    data-dropdown-toggle="user-dropdown"
                    data-dropdown-placement="bottom-end"
                >
                    <span class="sr-only">Abrir menu do utilizador</span>
                    <img class="w-10 h-10 rounded-full object-cover"
                        src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=DFF3EE&color=0D8ABC"
                        alt="{{ auth()->user()->name }}">
                </button>

                {{-- Dropdown --}}
                <div id="user-dropdown"
                    class="z-50 hidden absolute right-0 mt-2 w-56 bg-white divide-y divide-gray-200 rounded-xl shadow-lg">
                    
                    {{-- Cabeçalho --}}
                    <div class="px-4 py-3 text-sm text-gray-900 bg-zinc-100 rounded-lg">
                        <div class="font-semibold truncate">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>

                    {{-- Itens --}}
                    <ul class="py-2 text-sm text-gray-700" aria-labelledby="user-menu-button">
                        <li>
                            <a href="#"
                            class="block px-4 py-2 hover:bg-home-medigest-button  transition">
                                Configurações
                            </a>
                        </li>
                    </ul>

                    {{-- Logout --}}
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
    </header>

    {{-- Main layout --}}
    <div class="flex h-[calc(100vh-4rem)]">
        {{-- Sidebar --}}
        @php
            $role = optional(auth()->user())->role;

            // classes base/ativo/inativo
            $base = 'block px-4 py-2 rounded transition';
            $active = 'bg-gray-900 text-white';
            $inactive = 'text-gray-700 hover:bg-gray-200';

            // rota de dashboard por papel
            $dashboardRoute = match ($role) {
                'admin'   => 'admin.dashboard',
                'medico'  => 'medico.dashboard',
                'paciente'=> 'paciente.home',
                default   => 'home',
            };
        @endphp

        <aside class="w-64 bg-gray-100 shadow-md p-4 space-y-2">
            {{-- DASHBOARD (sempre visível, aponta para o dashboard correto por papel) --}}
            <a href="{{ route($dashboardRoute) }}"
            class="{{ request()->routeIs($dashboardRoute) ? "$base $active" : "$base $inactive" }}">
                {{ __('Dashboard') }}
            </a>

            {{-- MÉDICO --}}
            @if ($role === 'medico')
                <a href="{{ route('medico.consultas.index') }}"
                class="{{ request()->routeIs('medico.consultas.index') ? "$base $active" : "$base $inactive" }}">
                    Consultas Atribuídas
                </a>

                <a href="{{ route('medico.consultas.create') }}"
                class="{{ request()->routeIs('medico.consultas.create') ? "$base $active" : "$base $inactive" }}">
                    Criar Consulta
                </a>

            @endif

            {{-- ADMIN --}}
            @if ($role === 'admin')
                <a href="{{ route('admin.consultas.index') }}"
                class="{{ request()->routeIs('admin.consultas.index') ? "$base $active" : "$base $inactive" }}">
                    Todas as Consultas
                </a>

                <a href="{{ route('admin.especialidades.index') }}"
                class="{{ request()->routeIs('admin.especialidades.*') ? "$base $active" : "$base $inactive" }}">
                    Especialidades
                </a>

                <a href="{{ route('admin.medicos.index') }}"
                class="{{ request()->routeIs('admin.medicos.*') ? "$base $active" : "$base $inactive" }}">
                    Criar Médico
                </a>

                {{-- Gestão de horários (resource: admin.horarios.*) --}}
                <a href="{{ route('admin.horarios.index') }}"
                class="{{ request()->routeIs('admin.horarios.*') ? "$base $active" : "$base $inactive" }}">
                    Horários
                </a>
            @endif
        </aside>

        {{-- Content --}}
        <main class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    {{-- Toggle Dropdown Script --}}
    <script>
        document.getElementById('userDropdownToggle').addEventListener('click', function () {
            document.getElementById('userDropdown').classList.toggle('hidden');
        });
    </script>

</body>
</html>
