@extends('layouts.app')
@section('title', 'Termos de serviço | ' . config('app.name', 'MediGest+'))

@php
  $appName = config('app.name', 'MediGest+');
  $siteUrl = rtrim(config('app.url', url('/')), '/');

  $contacts = [
    'email'   => config('app.contacts.support_email', 'apoio@medigest.com'),
    'address' => config('app.contacts.address', 'Av. Exemplo 123, 1000-000 Lisboa'),
  ];

  // Atualiza quando fizeres alterações relevantes
  $lastUpdate = '2025-01-15';
@endphp

@section('content')
  {{-- Breadcrumbs + Hero --}}
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início','url'=>route('home')],
    ['label'=>'Termos de serviço']
  ]" />

  <x-ui.hero
    title="Termos de serviço"
    subtitle="Regras de utilização do serviço e responsabilidades."
    height="160px"
  />

  <div class="bg-zinc-50">
    <div class="max-w-[1000px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <article class="prose max-w-none prose-zinc prose-headings:text-zinc-900 prose-a:text-emerald-700">
        <p class="text-sm text-zinc-500">Última atualização: {{ \Carbon\Carbon::parse($lastUpdate)->format('d/m/Y') }}</p>

        <h2>1. Aceitação</h2>
        <p>
          Ao aceder e utilizar o <strong>{{ $appName }}</strong> (o “Serviço”), concorda com estes Termos de Serviço (“Termos”).
          Se não concordar, não deve utilizar o Serviço.
        </p>

        <h2>2. Descrição do Serviço</h2>
        <p>
          O {{ $appName }} disponibiliza funcionalidades de marcação de consultas, gestão de conta de utilizador
          e comunicação de serviço (p. ex., confirmações e lembretes). O Serviço não substitui aconselhamento médico
          presencial e não fornece emergências médicas.
        </p>

        <h2>3. Elegibilidade e Conta</h2>
        <ul>
          <li>Deve ter pelo menos 16 anos ou usar o Serviço com consentimento do encarregado de educação.</li>
          <li>É responsável por manter a confidencialidade das suas credenciais e por toda a atividade na sua conta.</li>
          <li>Deve fornecer informação exata e mantê-la atualizada.</li>
        </ul>

        <h2>4. Marcações, Alterações e Cancelamentos</h2>
        <ul>
          <li>As marcações estão sujeitas a disponibilidade do profissional e regras internas (ex.: prazos mínimos/lead times).</li>
          <li>Alguns cancelamentos podem não ser possíveis quando faltarem menos de 24h — consulte as regras mostradas no fluxo de marcação.</li>
          <li>Reservamo-nos o direito de reprogramar/cancelar por motivos operacionais, notificando o utilizador.</li>
        </ul>

        <h2>5. Conduta do Utilizador</h2>
        <ul>
          <li>Não pode usar o Serviço para fins ilícitos, difamar, infringir direitos de terceiros ou perturbar o funcionamento da plataforma.</li>
          <li>É proibida a engenharia reversa, scraping automatizado abusivo ou acesso não autorizado.</li>
        </ul>

        <h2>6. Comunicações</h2>
        <p>
          Enviamos comunicações transacionais (p. ex., confirmações de consulta). Comunicações opcionais (p. ex., resumos/novidades)
          dependem do seu consentimento e preferências. Pode geri-las nas “Configurações”.
        </p>

        <h2>7. Privacidade e Dados</h2>
        <p>
          O tratamento de dados pessoais rege-se pela nossa
          <a href="{{ route('paciente.legal.privacy') }}">Política de privacidade</a>.
          Ao usar o Serviço, declara que a leu e compreendeu.
        </p>

        <h2>8. Propriedade Intelectual</h2>
        <p>
          Todos os conteúdos, marcas, logótipos e código associados ao {{ $appName }} pertencem aos seus respetivos titulares.
          Não adquire quaisquer direitos de propriedade intelectual ao usar o Serviço.
        </p>

        <h2>9. Suspensão e Encerramento</h2>
        <p>
          Podemos suspender ou encerrar o acesso ao Serviço, total ou parcialmente, em caso de violação destes Termos,
          risco de segurança ou motivos legais/operacionais. Pode solicitar o encerramento da sua conta a qualquer momento.
        </p>

        <h2>10. Isenção de Garantias</h2>
        <p>
          O Serviço é fornecido “tal como está” e “conforme disponível”. Apesar dos nossos esforços, não garantimos
          operação ininterrupta, isenta de erros ou compatibilidade com todos os dispositivos.
        </p>

        <h2>11. Limitação de Responsabilidade</h2>
        <p>
          Na medida máxima permitida por lei, o {{ $appName }} não se responsabiliza por danos indiretos, incidentais,
          especiais ou consequentes decorrentes do uso ou incapacidade de uso do Serviço.
        </p>

        <h2>12. Ligações de Terceiros</h2>
        <p>
          O Serviço pode conter links para sites de terceiros. Não nos responsabilizamos pelo conteúdo ou políticas desses sites.
        </p>

        <h2>13. Alterações aos Termos</h2>
        <p>
          Podemos atualizar estes Termos para refletir alterações legais ou operacionais.
          A versão em vigor estará sempre disponível em <a href="{{ $siteUrl }}/termos">{{ $siteUrl }}/termos</a>.
          A continuação do uso após alterações constitui aceitação das mesmas.
        </p>

        <h2>14. Lei Aplicável e Foro</h2>
        <p>
          Estes Termos regem-se pela lei portuguesa. Para quaisquer litígios, é competente o foro da comarca de Lisboa,
          sem prejuízo das normas imperativas aplicáveis.
        </p>

        <h2>15. Contactos</h2>
        <p>
          Dúvidas sobre estes Termos? Contacte-nos em
          <a href="mailto:{{ $contacts['email'] }}">{{ $contacts['email'] }}</a>
          ou por correio para {{ $contacts['address'] }}.
        </p>
      </article>
    </div>
  </div>
@endsection
