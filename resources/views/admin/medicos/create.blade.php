@extends('layouts.dashboard')
@section('title','Criar Médico')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Novo Médico</h1>
      <p class="text-sm text-gray-500 mt-1">Preenche os dados do médico e associa as especialidades.</p>
    </div>
    <a href="{{ route('admin.medicos.index') }}" class="text-gray-600 hover:underline">Voltar</a>
  </div>

  {{-- Card --}}
  <form action="{{ route('admin.medicos.store') }}" method="POST"
        class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-6 sm:p-8">
    @csrf

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <div>
        <x-form.input
          id="name"
          name="name"
          label="Nome"
          placeholder="Nome completo"
          icon="components.icons.admin.user"
          required
        />
        @error('name') <p class="text-sm text-red-600 -mt-2 mb-2">{{ $message }}</p> @enderror
      </div>

      <div>
        <x-form.input
          id="email"
          name="email"
          type="email"
          label="Email"
          placeholder="exemplo@dominio.com"
          icon="components.icons.admin.email"
          required
        />
        @error('email') <p class="text-sm text-red-600 -mt-2 mb-2">{{ $message }}</p> @enderror
      </div>

      <div>
        <x-form.input
          id="password"
          name="password"
          type="password"
          label="Password"
          placeholder="•••••••••"
          icon="components.icons.admin.lock"
          required
        />
        @error('password') <p class="text-sm text-red-600 -mt-2 mb-2">{{ $message }}</p> @enderror
      </div>

      <div>
        <x-form.input
          id="password_confirmation"
          name="password_confirmation"
          type="password"
          label="Confirmar Password"
          placeholder="•••••••••"
          icon="components.icons.admin.lock"
          required
        />
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
      <div>
        <x-form.input
            id="crm"
            name="crm"
            label="CRM"
            placeholder="Ex.: CRM123"
            icon="components.icons.admin.id"
            required
        />
        <p class="text-xs text-gray-500 mt-1">Deve ser único.</p>
        @error('crm')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <x-form.textarea
          id="bio"
          name="bio"
          label="Bio"
          placeholder="Breve apresentação do médico"
          icon="components.icons.admin.info"
          rows="2"
        />
        @error('bio') <p class="text-sm text-red-600 -mt-2 mb-2">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- Especialidades (uma box com chips + input + dropdown) --}}
    @php
    $espBase = 'esp'; // id base — precisa bater com o JS
    $oldSelected = collect(old('especialidades', []))->map(fn($v)=>(string)$v)->all();
    @endphp

    <div class="sm:col-span-2">
    <label class="block text-sm font-medium text-zinc-700 mb-1">Especialidades</label>

    {{-- Caixa única (chips + input) --}}
    <div id="{{ $espBase }}-box"
        class="relative">
        <div id="{{ $espBase }}-control"
            class="min-h-[44px] w-full flex flex-wrap items-center gap-2 rounded-lg border border-gray-300 bg-gray-50 px-2 py-2 focus-within:ring-2 focus-within:ring-medigest focus-within:border-medigest">
        {{-- chips (se veio old()) --}}
        @foreach($especialidades as $esp)
            @if(in_array((string)$esp->id, $oldSelected))
            <span class="esp-chip inline-flex items-center gap-2 rounded-md bg-blue-50 text-blue-700 text-sm px-2.5 py-1"
                    data-id="{{ $esp->id }}" data-text="{{ $esp->nome }}">
                {{ $esp->nome }}
                <button type="button" class="esp-chip-remove text-blue-700/70 hover:text-blue-900" aria-label="Remover">&times;</button>
            </span>
            @endif
        @endforeach

        {{-- input dentro da mesma box --}}
        <input id="{{ $espBase }}-input" type="text" autocomplete="off"
                placeholder="Selecione especialidades…"
                class="flex-1 min-w-[140px] border-0 focus:ring-0 focus:outline-none text-sm text-gray-900 placeholder:text-gray-400">
        <button type="button" id="{{ $espBase }}-toggle"
                class="px-2 text-gray-500 hover:text-gray-700">▼</button>
        </div>

        {{-- Dropdown --}}
        <div id="{{ $espBase }}-dd"
            class="absolute z-50 mt-2 hidden w-full max-h-60 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow">
        <ul id="{{ $espBase }}-list" class="py-1">
            @foreach($especialidades as $esp)
            <li class="esp-item cursor-pointer px-3 py-2 hover:bg-gray-50"
                data-id="{{ $esp->id }}" data-text="{{ $esp->nome }}">
                {{ $esp->nome }}
            </li>
            @endforeach
        </ul>
        <div class="border-t border-gray-200 p-2 text-xs text-gray-500">
            Clica para adicionar. Escreve para filtrar. Enter também adiciona o primeiro resultado.
        </div>
        </div>
    </div>

    {{-- Hidden inputs para o POST --}}
    <div id="{{ $espBase }}-hidden">
        @foreach($oldSelected as $sid)
        <input type="hidden" name="especialidades[]" value="{{ $sid }}" data-id="{{ $sid }}">
        @endforeach
    </div>

    @error('especialidades')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
    <p class="text-xs text-gray-500 mt-2">Podes selecionar múltiplas especialidades.</p>
    </div>

    {{-- AÇÕES --}}
    <div class="mt-8 flex items-center gap-3">
      <button class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover">
        Criar
      </button>
      <a href="{{ route('admin.medicos.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
    </div>
  </form>
</div>

@vite('resources/js/pages/medicos-multiselect.js')
@endsection
