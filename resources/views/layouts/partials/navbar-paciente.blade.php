{{-- resources/views/layouts/partials/navbar-paciente.blade.php --}}
@php
  use Illuminate\Support\Facades\Route;

  // helper de rotas seguro
  $r = fn(string $name, array $params = [], string $fallback = '#') =>
      Route::has($name) ? route($name, $params) : $fallback;
@endphp

<nav class="bg-white border-b border-gray-200">
  <div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="h-12 flex items-center justify-center" aria-label="Navegação do paciente">
      <div class="flex items-center gap-2 sm:gap-3" id="servicos-wrap">

        {{-- SERVIÇOS (apenas 2 secções: Consulta externa + Apoio ao cliente) --}}
        <div class="relative">
          <button
            id="servicos-btn"
            type="button"
            aria-haspopup="dialog"
            aria-controls="servicos-mega"
            aria-expanded="false"
            data-default-tab="tab-consulta"
            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-zinc-900 hover:text-home-medigest focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 rounded-lg"
          >
            <span>Serviços</span>
            <svg class="w-4 h-4 text-gray-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
            </svg>
          </button>

          {{-- MEGA MENU --}}
          <div
            id="servicos-mega"
            class="hidden fixed left-1/2 -translate-x-1/2 w-[min(96vw,1200px)] z-[60] rounded-2xl bg-white/95 backdrop-blur ring-1 ring-gray-200 shadow-xl overflow-hidden"
            role="dialog"
            aria-label="Menu de serviços"
          >
            <div class="grid grid-cols-12">

              {{-- Tabs (esquerda) --}}
              <aside class="col-span-12 md:col-span-3 bg-gradient-to-b from-gray-50 to-gray-100 border-r border-gray-200">
                <div class="p-3 sm:p-4">
                  <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-600 mb-2">Categorias</div>
                  <div class="space-y-2" role="tablist" aria-orientation="vertical">
                    <button class="tab-btn w-full text-left rounded-lg px-3 py-2 text-sm border border-transparent bg-emerald-50 text-emerald-700 focus:outline-none"
                            data-tab="tab-consulta" role="tab" aria-selected="true">
                      Consulta externa
                    </button>
                    <button class="tab-btn w-full text-left rounded-lg px-3 py-2 text-sm border border-transparent bg-white focus:outline-none"
                            data-tab="tab-apoio" role="tab" aria-selected="false">
                      Apoio ao cliente
                    </button>
                  </div>
                </div>
              </aside>

              {{-- Painéis (conteúdo) --}}
              <section class="col-span-12 md:col-span-9 p-4 sm:p-6">

                {{-- CONSULTA EXTERNA --}}
                <div id="tab-consulta" class="tab-panel" role="tabpanel" aria-labelledby="Consulta externa">
                  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                      <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-600 mb-3">Sobre</h3>
                      <p class="text-sm text-gray-700 leading-relaxed">
                        A <strong>consulta externa</strong> permite-lhe agendar atendimento presencial ou por vídeo
                        com médicos da nossa rede. Escolha a modalidade mais conveniente, veja horários disponíveis e
                        finalize a marcação em poucos passos.
                      </p>

                      <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ $r('paciente.consultas.index') }}"
                           class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-semibold text-white bg-emerald-700 hover:bg-emerald-800">
                          Abrir consulta externa
                        </a>
                        <a href="{{ $r('paciente.consultas.create') }}"
                           class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-semibold border border-gray-200 bg-white hover:bg-gray-50">
                          Marcar agora
                        </a>
                      </div>
                    </div>

                    <div class="lg:col-span-1">
                      <div class="rounded-xl p-4 sm:p-5 bg-gradient-to-br from-emerald-50 to-white ring-1 ring-emerald-100">
                        <div class="text-sm font-semibold text-emerald-700 mb-1">Acesso rápido</div>
                        <div class="flex flex-wrap gap-2">
                          <a href="{{ $r('paciente.consultas.create', ['tipo'=>'presencial']) }}"
                             class="inline-flex items-center rounded-full px-3 py-1.5 text-sm border border-gray-200 hover:bg-gray-50">Presencial</a>
                          <a href="{{ $r('paciente.consultas.create', ['tipo'=>'teleconsulta']) }}"
                             class="inline-flex items-center rounded-full px-3 py-1.5 text-sm border border-gray-200 hover:bg-gray-50">Teleconsulta</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- APOIO AO CLIENTE --}}
                <div id="tab-apoio" class="tab-panel hidden" role="tabpanel" aria-labelledby="Apoio ao cliente">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <ul class="space-y-2 text-sm">
                      <li><a href="{{ $r('account.profile') }}" class="hover:underline">Dados pessoais</a></li>
                      <li><a href="{{ $r('account.settings') }}" class="hover:underline">Notificações & idioma</a></li>
                    </ul>
                    <ul class="space-y-2 text-sm">
                      <li><a href="{{ $r('paciente.legal.privacy') }}" class="hover:underline">Política de privacidade</a></li>
                      <li><a href="{{ $r('paciente.legal.terms') }}" class="hover:underline">Termos de serviço</a></li>
                      <li><a href="{{ $r('paciente.canais.index') }}" class="hover:underline">Contacte-nos</a></li>
                    </ul>
                  </div>
                </div>

              </section>
            </div>
          </div>
        </div>

        {{-- LINKS diretos (mantidos fora do mega) --}}
        <a href="{{ $r('paciente.especialidades.index') }}"
           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-zinc-900 hover:text-home-medigest">
          Especialidades
        </a>

        <a href="{{ $r('paciente.medicos.index') }}"
           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-zinc-900 hover:text-home-medigest">
          Corpo Clínico
        </a>

        <a href="{{ $r('paciente.canais.index') }}" 
           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-zinc-900 hover:text-home-medigest">
          Canais
        </a>

      </div>
    </div>
  </div>
</nav>
