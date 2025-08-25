{{-- Modal Auth — tema claro, responsivo, panes login/register/forgot --}}
@php $oldForm = old('form_name'); @endphp
<template id="tpl-auth">
  <div id="modal-auth" class="mz-overlay" role="dialog" aria-modal="true" aria-labelledby="auth-title">
    <div class="modal-content auth-modal w-[min(98vw,1220px)] max-h-[90vh] overflow-hidden rounded-[32px] p-0 bg-white/90 backdrop-blur border border-zinc-200 shadow-2xl relative">
      <!-- Fechar -->
      <button type="button" class="mz-x" aria-label="Fechar" data-auth-close>
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
          <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
        </svg>
      </button>

      <div class="grid lg:grid-cols-2">
        {{-- ESQUERDA • IMAGEM (só desktop) --}}
        <div class="relative hidden lg:block">
          <div class="img-panel relative h-[520px] lg:h-[680px] xl:h-[720px] overflow-hidden">
            <img src="{{ asset('/medicologin.png') }}" alt=""
                 class="absolute inset-0 w-full h-full object-cover object-center"
                 loading="lazy" decoding="async" fetchpriority="low">
          </div>
        </div>

        {{-- DIREITA • CONTEÚDO (tema claro) --}}
        <div class="relative bg-gradient-to-t from-home-medigest-button to-white text-zinc-900">
          <div class="px-8 lg:px-14 xl:px-16 py-10 lg:py-14 xl:py-18">
            <div class="max-w-[450px] mx-auto">
              {{-- CAPSULAS/TABS (controlam apenas o conteúdo abaixo) --}}
              <div class="flex justify-center mb-6 lg:mb-8 xl:mb-9">
                <div class="inline-flex items-center rounded-full bg-zinc-100/80 ring-1 ring-zinc-200 p-1">
                  <button type="button"
                          data-pane-target="register" aria-selected="false"
                          class="inline-flex items-center justify-center h-10 px-5 rounded-full text-sm text-zinc-600 hover:text-zinc-900">
                    Criar conta
                  </button>
                  <button type="button"
                          data-pane-target="login" aria-selected="true"
                          class="inline-flex items-center justify-center h-10 px-5 rounded-full text-sm font-semibold bg-white text-zinc-900 shadow">
                    Iniciar sessão
                  </button>
                </div>
              </div>

              {{-- ================== PANE: LOGIN ================== --}}
              <section data-pane="login">
                <h1 id="auth-title" class="text-2xl sm:text-3xl font-bold tracking-tight">Bem-vindo(a) de volta</h1>
                <p class="mt-1 text-sm text-zinc-500">Acede com o teu email e palavra-passe.</p>

                @if ($errors->any() && $oldForm === 'login')
                  <div class="mt-6 rounded-xl bg-red-50 text-red-700 text-sm p-3 ring-1 ring-red-200">
                    <ul class="list-disc ps-5 space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li>@endforeach</ul>
                  </div>
                @endif
                @if (session('success'))
                  <div class="mt-6 rounded-xl bg-emerald-50 text-emerald-800 text-sm p-3 ring-1 ring-emerald-200">
                    {{ session('success') }}
                  </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5" novalidate data-auth-ajax="login">
                  @csrf
                  <input type="hidden" name="form_name" value="login">

                  <div>
                    <label for="login_email" class="block text-sm mb-2 text-zinc-700">Email</label>
                    <input id="login_email" name="email" type="email" inputmode="email" autocomplete="username" required autofocus
                           value="{{ old('email') }}"
                           class="w-full h-11 px-4 rounded-xl bg-white text-zinc-900 placeholder-zinc-500
                                  border border-zinc-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/70 focus:border-transparent" />
                  </div>

                  <div>
                    <div class="flex items-center justify-between">
                      <label for="login_password" class="block text-sm mb-2 text-zinc-700">Palavra-passe</label>
                      <button type="button" class="text-xs text-emerald-700 hover:text-emerald-600" data-auth-switch="forgot">
                        Esqueceste a palavra-passe?
                      </button>
                    </div>

                    <div class="relative group">
                      <input id="login_password" name="password" type="password" autocomplete="current-password" required
                             class="w-full h-11 ps-4 pe-12 rounded-xl bg-white text-zinc-900 placeholder-zinc-500
                                    border border-zinc-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/70 focus:border-transparent" />
                      <button
                        type="button"
                        data-toggle-password="login_password"
                        data-icon-toggle
                        data-state="hidden"
                        data-label-show="Mostrar"
                        data-label-hide="Ocultar"
                        aria-pressed="false"
                        aria-controls="login_password"
                        aria-label="Mostrar palavra-passe"
                        class="absolute right-2 top-1/2 -translate-y-1/2 h-9 w-9 grid place-items-center rounded-lg">
                        <span class="sr-only">Mostrar</span>
                        {{-- Olho (mostrar) --}}
                        <svg class="icon-eye" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                          <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                          <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        {{-- Olho riscado (ocultar) --}}
                        <svg class="icon-eye-off" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                          <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12a21.77 21.77 0 0 1 5.06-6.94" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                          <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.83 21.83 0 0 1-3.87 5.49" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                          <path d="M14.12 14.12A3 3 0 0 1 9.88 9.88" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                          <path d="M1 1l22 22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                      </button>
                    </div>
                  </div>

                  <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center gap-2 text-sm text-zinc-700">
                      <input type="checkbox" name="remember"
                             class="rounded border-zinc-300 bg-white text-emerald-600 focus:ring-emerald-500/60">
                      <span>Manter sessão</span>
                    </label>
                  </div>

                  <div data-auth-errors class="hidden rounded-xl bg-red-50 text-red-700 text-sm p-3 ring-1 ring-red-200"></div>

                  <button type="submit"
                          class="w-full h-11 rounded-xl bg-home-medigest hover:bg-home-medigest-hover text-white font-semibold transition">
                    Entrar
                  </button>
                </form>

                <div class="mt-9 flex items-center justify-between text-xs text-zinc-500">
                  <div>Não tens conta?
                    <button type="button" class="font-medium text-emerald-700 hover:text-emerald-600" data-auth-switch="register">
                      Criar conta
                    </button>
                  </div>
                  <a href="#" class="hover:text-zinc-700">Termos &amp; Condições</a>
                </div>
              </section>

              {{-- ================== PANE: REGISTER ================== --}}
              <section data-pane="register" hidden>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">Criar uma conta</h2>
                <p class="mt-1 text-sm text-zinc-500">Começa já — 30 dias grátis.</p>

                @if ($errors->any() && $oldForm === 'register')
                  <div class="mt-6 rounded-xl bg-red-50 text-red-700 text-sm p-3 ring-1 ring-red-200">
                    <ul class="list-disc ps-5 space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li>@endforeach</ul>
                  </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5" novalidate>
                  @csrf
                  <input type="hidden" name="form_name" value="register">

                  <div>
                    <label for="reg_name" class="block text-sm mb-2 text-zinc-700">Nome completo</label>
                    <input id="reg_name" name="name" type="text" autocomplete="name" required autofocus
                           value="{{ old('name') }}"
                           class="w-full h-11 px-4 rounded-xl bg-white text-zinc-900 placeholder-zinc-500
                                  border border-zinc-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/70 focus:border-transparent" />
                  </div>

                  <div>
                    <label for="reg_email" class="block text-sm mb-2 text-zinc-700">Email</label>
                    <input id="reg_email" name="email" type="email" autocomplete="email" required
                           value="{{ old('email') }}"
                           class="w-full h-11 px-4 rounded-xl bg-white text-zinc-900 placeholder-zinc-500
                                  border border-zinc-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/70 focus:border-transparent" />
                  </div>

                  <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                      <label for="reg_password" class="block text-sm mb-2 text-zinc-700">Palavra-passe</label>
                      <div class="relative group">
                        <input id="reg_password" name="password" type="password" autocomplete="new-password" required
                               class="w-full h-11 ps-4 pe-12 rounded-xl bg-white text-zinc-900 placeholder-zinc-500
                                      border border-zinc-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/70 focus:border-transparent" />
                        <button
                          type="button"
                          data-toggle-password="reg_password"
                          data-icon-toggle
                          data-state="hidden"
                          data-label-show="Mostrar"
                          data-label-hide="Ocultar"
                          aria-pressed="false"
                          aria-controls="reg_password"
                          aria-label="Mostrar palavra-passe"
                          class="absolute right-2 top-1/2 -translate-y-1/2 h-9 w-9 grid place-items-center rounded-lg">
                          <span class="sr-only">Mostrar</span>
                          <svg class="icon-eye" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/>
                          </svg>
                          <svg class="icon-eye-off" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12a21.77 21.77 0 0 1 5.06-6.94" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.83 21.83 0 0 1-3.87 5.49" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M14.12 14.12A3 3 0 0 1 9.88 9.88" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M1 1l22 22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                          </svg>
                        </button>
                      </div>
                    </div>

                    <div>
                      <label for="reg_password_confirmation" class="block text-sm mb-2 text-zinc-700">Confirmar palavra-passe</label>
                      <div class="relative group">
                        <input id="reg_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                               class="w-full h-11 ps-4 pe-12 rounded-xl bg-white text-zinc-900 placeholder-zinc-500
                                      border border-zinc-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/70 focus:border-transparent" />
                        <button
                          type="button"
                          data-toggle-password="reg_password_confirmation"
                          data-icon-toggle
                          data-state="hidden"
                          data-label-show="Mostrar"
                          data-label-hide="Ocultar"
                          aria-pressed="false"
                          aria-controls="reg_password_confirmation"
                          aria-label="Mostrar palavra-passe"
                          class="absolute right-2 top-1/2 -translate-y-1/2 h-9 w-9 grid place-items-center rounded-lg">
                          <span class="sr-only">Mostrar</span>
                          <svg class="icon-eye" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/>
                          </svg>
                          <svg class="icon-eye-off" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12a21.77 21.77 0 0 1 5.06-6.94" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.83 21.83 0 0 1-3.87 5.49" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M14.12 14.12A3 3 0 0 1 9.88 9.88" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M1 1l22 22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>

                  <button type="submit"
                          class="w-full h-11 rounded-xl bg-home-medigest hover:bg-home-medigest-hover text-white font-semibold transition">
                    Submeter
                  </button>
                </form>

                <div class="mt-9 flex items-center justify-between text-xs text-zinc-500">
                  <div>Já tens conta?
                    <button type="button" class="ms-1 font-medium text-emerald-700 hover:text-emerald-600" data-auth-switch="login">
                      Iniciar sessão
                    </button>
                  </div>
                  <a href="#" class="hover:text-zinc-700">Termos &amp; Condições</a>
                </div>
              </section>

              {{-- ================== PANE: FORGOT ================== --}}
              <section data-pane="forgot" hidden>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">Esqueceste a palavra-passe?</h2>
                <p class="mt-1 text-sm text-zinc-600">Introduz o teu email e enviamos um link para redefinir.</p>

                @if (session('status'))
                  <div class="mt-4 rounded-xl bg-emerald-50 text-emerald-800 text-sm p-3 ring-1 ring-emerald-200">{{ session('status') }}</div>
                @endif
                @if ($errors->any() && $oldForm === 'forgot')
                  <div class="mt-5 rounded-xl bg-red-50 text-red-700 text-sm p-3 ring-1 ring-red-200">
                    <ul class="list-disc ps-5 space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li>@endforeach</ul>
                  </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5" novalidate>
                  @csrf
                  <input type="hidden" name="form_name" value="forgot">

                  <div>
                    <label for="forgot_email" class="block text-sm mb-2 text-zinc-700">Email</label>
                    <input id="forgot_email" name="email" type="email" autocomplete="email" required autofocus
                           value="{{ old('email') }}"
                           class="w-full h-11 px-4 rounded-xl bg-white text-zinc-900 placeholder-zinc-500
                                  border border-zinc-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/70 focus:border-transparent" />
                  </div>

                  <div class="flex items-center justify-between text-xs text-zinc-600">
                    <button type="button" class="font-medium text-emerald-700 hover:text-emerald-600" data-auth-switch="login">
                      Voltar ao login
                    </button>
                    <a href="#" class="hover:text-zinc-700">Termos &amp; Condições</a>
                  </div>

                  <button type="submit"
                          class="w-full h-11 rounded-xl bg-home-medigest hover:bg-home-medigest-hover text-white font-semibold transition">
                    Enviar link de recuperação
                  </button>
                </form>
              </section>
              {{-- ================================================== --}}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
