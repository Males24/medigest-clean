@extends('layouts.app')
@section('title', 'Perfil | MediGest+')

@php
  $user = $user ?? auth()->user();
  $avatarCurrent  = $user->avatar_url;
  $avatarFallback = route('avatar.initials', ['user'=>$user->id, 'v'=>$user->updated_at?->timestamp]);
@endphp

@section('content')
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início','url'=>route('home')],
    ['label'=>'Perfil']
  ]" />

  <x-ui.hero title="Perfil"
             subtitle="Atualize os seus dados pessoais e a segurança da sua conta."
             height="160px" />

  <div class="bg-zinc-50">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

      {{-- Mensagens --}}
      @if (session('success'))
        <div class="rounded-xl px-4 py-3 bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200/60">{{ session('success') }}</div>
      @endif
      @if ($errors->any())
        <div class="rounded-xl px-4 py-3 bg-rose-50 text-rose-700 ring-1 ring-rose-200/60">
          <ul class="list-disc list-inside text-sm">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Dados pessoais --}}
        <form method="POST" action="{{ route('account.profile.update') }}" enctype="multipart/form-data"
              class="lg:col-span-2 rounded-2xl border border-zinc-200 bg-white">
          @csrf @method('PUT')

          <div class="p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-5">
              <img id="avatarPreview" src="{{ $avatarCurrent }}" alt="Avatar {{ $user->name }}"
                   class="w-20 h-20 rounded-full object-cover ring-1 ring-zinc-200">
              <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                  <label for="avatar"
                         class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm border border-zinc-200 bg-white hover:bg-zinc-50 cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    Alterar foto
                  </label>
                  <input id="avatar" name="avatar" type="file" class="hidden" accept="image/png,image/jpeg,image/webp">
                  <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm border border-zinc-200 bg-white hover:bg-zinc-50 cursor-pointer select-none">
                    <input type="checkbox" name="remove_avatar" value="1" class="rounded">
                    Remover foto
                  </label>
                </div>
                <p class="text-xs text-zinc-500">PNG/JPG/WebP, até 2MB.</p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="name" class="block text-sm font-medium text-zinc-800">Nome</label>
                <input id="name" name="name" type="text" class="mt-1 w-full h-11 rounded-xl border border-zinc-300 px-3 text-sm focus:border-emerald-600 focus:ring-emerald-600"
                       value="{{ old('name', $user->name) }}" required>
              </div>

              <div>
                <label for="email" class="block text-sm font-medium text-zinc-800">Email</label>
                <input id="email" name="email" type="email" class="mt-1 w-full h-11 rounded-xl border border-zinc-300 px-3 text-sm focus:border-emerald-600 focus:ring-emerald-600"
                       value="{{ old('email', $user->email) }}" required>
              </div>

              <div>
                <label for="phone" class="block text-sm font-medium text-zinc-800">Telefone</label>
                <input id="phone" name="phone" type="tel" class="mt-1 w-full h-11 rounded-xl border border-zinc-300 px-3 text-sm focus:border-emerald-600 focus:ring-emerald-600"
                       value="{{ old('phone', $user->phone) }}">
              </div>

              <div>
                <label class="block text-sm font-medium text-zinc-800">Função</label>
                <input type="text" class="mt-1 w-full h-11 rounded-xl border border-zinc-200 bg-zinc-50 px-3 text-sm" value="{{ ucfirst($user->role) }}" disabled>
              </div>
            </div>
          </div>

          <div class="px-6 sm:px-8 py-4 bg-zinc-50 rounded-b-2xl flex items-center justify-end">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white bg-emerald-700 hover:bg-emerald-800">
              Guardar alterações
            </button>
          </div>
        </form>

        {{-- Segurança --}}
        <form method="POST" action="{{ route('account.password.update') }}" class="rounded-2xl border border-zinc-200 bg-white">
          @csrf @method('PUT')

          <div class="p-6 sm:p-8 space-y-4">
            <h2 class="text-lg font-medium text-zinc-900">Segurança</h2>

            <div>
              <label for="pwd-new" class="block text-sm font-medium text-zinc-800">Nova password</label>
              <div class="mt-1 relative">
                <input id="pwd-new" name="password" type="password" minlength="8" maxlength="72" autocomplete="new-password"
                       class="w-full h-11 rounded-xl border border-zinc-300 px-3 pr-10 text-sm focus:border-emerald-600 focus:ring-emerald-600" required>
                <button type="button" data-toggle="#pwd-new" class="absolute right-2 top-1/2 -translate-y-1/2 text-zinc-500 text-sm">mostrar</button>
              </div>
              @error('password')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
              <label for="pwd-confirm" class="block text-sm font-medium text-zinc-800">Confirmar nova password</label>
              <div class="mt-1 relative">
                <input id="pwd-confirm" name="password_confirmation" type="password" minlength="8" maxlength="72" autocomplete="new-password"
                       class="w-full h-11 rounded-xl border border-zinc-300 px-3 pr-10 text-sm focus:border-emerald-600 focus:ring-emerald-600" required>
                <button type="button" data-toggle="#pwd-confirm" class="absolute right-2 top-1/2 -translate-y-1/2 text-zinc-500 text-sm">mostrar</button>
              </div>
            </div>
          </div>

          <div class="px-6 sm:px-8 py-4 bg-zinc-50 rounded-b-2xl flex items-center justify-end">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white bg-emerald-700 hover:bg-emerald-800">
              Atualizar password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('body-end')
<script>
  // preview do avatar
  document.getElementById('avatar')?.addEventListener('change', (e)=>{
    const f = e.target.files?.[0]; if(!f) return;
    const url = URL.createObjectURL(f);
    const img = document.getElementById('avatarPreview');
    img.src = url; img.onload = ()=>URL.revokeObjectURL(url);
  });
  // mostrar/ocultar password
  document.querySelectorAll('[data-toggle]')?.forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const input = document.querySelector(btn.dataset.toggle);
      if(!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
      btn.textContent = input.type === 'password' ? 'mostrar' : 'ocultar';
    });
  });
</script>
@endpush
