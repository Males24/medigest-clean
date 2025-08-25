@extends('layouts.app')
@section('title', 'Política de Privacidade | ' . config('app.name', 'MediGest+'))

@php
  $appName = config('app.name', 'MediGest+');
  $siteUrl = rtrim(config('app.url', url('/')), '/');

  $contacts = [
    'email'   => config('app.contacts.support_email', 'apoio@medigest.com'),
    'address' => config('app.contacts.address', 'Av. Exemplo 123, 1000-000 Lisboa'),
  ];

  // Atualiza a data sempre que fizeres alterações relevantes a esta página
  $lastUpdate = '2025-01-15';
@endphp

@section('content')
  {{-- Breadcrumbs + Hero --}}
  <x-ui.breadcrumbs :items="[
    ['label'=>'Início','url'=>route('home')],
    ['label'=>'Política de privacidade']
  ]" />

  <x-ui.hero
    title="Política de privacidade"
    subtitle="Como recolhemos, usamos e protegemos os seus dados pessoais."
    height="160px"
  />

  <div class="bg-zinc-50">
    <div class="max-w-[1000px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <article class="prose max-w-none prose-zinc prose-headings:text-zinc-900 prose-a:text-emerald-700">
        <p class="text-sm text-zinc-500">Última atualização: {{ \Carbon\Carbon::parse($lastUpdate)->format('d/m/Y') }}</p>

        <h2>1. Quem é o responsável pelo tratamento?</h2>
        <p>
          O responsável pelo tratamento dos seus dados pessoais é <strong>{{ $appName }}</strong>, com sede em
          {{ $contacts['address'] }}. Para questões relacionadas com proteção de dados, contacte:
          <a href="mailto:{{ $contacts['email'] }}">{{ $contacts['email'] }}</a>.
        </p>

        <h2>2. Que dados recolhemos?</h2>
        <ul>
          <li><strong>Identificação e contacto</strong>: nome, email, telefone.</li>
          <li><strong>Dados de conta</strong>: credenciais (hashed), preferências, idioma, notificações.</li>
          <li><strong>Dados clínicos mínimos operacionais</strong> associados à marcação (p.ex. especialidade, médico, data/hora, motivo resumido).</li>
          <li><strong>Dados técnicos</strong>: IP, device, browser e cookies/analytics (ver secção 8).</li>
        </ul>

        <h2>3. Para que finalidades usamos os dados?</h2>
        <ul>
          <li><strong>Prestação do serviço</strong> (gestão de conta, marcações, comunicações de serviço).</li>
          <li><strong>Segurança</strong> (autenticação, prevenção de fraude e abusos).</li>
          <li><strong>Comunicações</strong> sobre marcações, lembretes e notificações, conforme preferências.</li>
          <li><strong>Melhoria contínua</strong> (estatísticas agregadas e anónimas).</li>
        </ul>

        <h2>4. Fundamentos de licitude</h2>
        <ul>
          <li><strong>Execução de contrato</strong>: gestão de conta e marcações.</li>
          <li><strong>Interesse legítimo</strong>: segurança, melhoria de produto.</li>
          <li><strong>Consentimento</strong>: comunicações opcionais e cookies não essenciais.</li>
          <li><strong>Obrigação legal</strong>: quando aplicável.</li>
        </ul>

        <h2>5. Durante quanto tempo guardamos os dados?</h2>
        <p>
          Conservamos os dados apenas pelo período necessário às finalidades indicadas ou pelo prazo exigido por lei.
          Após esse período, os dados são eliminados ou anonimizados de forma segura.
        </p>

        <h2>6. Partilha com terceiros</h2>
        <p>
          Poderemos recorrer a <em>subcontratantes</em> (p.ex. alojamento, email transacional) que atuam em nosso nome e de acordo com instruções contratuais.
          Não vendemos os seus dados. Caso exista transferência para fora do EEE, aplicamos garantias adequadas (p.ex. Cláusulas Contratuais-Tipo).
        </p>

        <h2>7. Os seus direitos</h2>
        <p>Nos termos do RGPD, pode exercer, a qualquer momento, os seguintes direitos:</p>
        <ul>
          <li>Acesso, retificação e apagamento.</li>
          <li>Limitação e oposição ao tratamento.</li>
          <li>Portabilidade dos dados.</li>
          <li>Retirar consentimentos, sem afetar tratamentos anteriores.</li>
          <li>Reclamar junto da <a href="https://www.cnpd.pt/" target="_blank" rel="noopener">CNPD</a>.</li>
        </ul>
        <p>Para exercer direitos, contacte <a href="mailto:{{ $contacts['email'] }}">{{ $contacts['email'] }}</a>.</p>

        <h2>8. Cookies e tecnologias semelhantes</h2>
        <p>
          Utilizamos cookies essenciais ao funcionamento da aplicação e, opcionalmente, cookies analíticos com base no seu consentimento.
          Pode gerir preferências no seu navegador ou (quando disponível) no nosso gestor de cookies.
        </p>

        <h2>9. Segurança</h2>
        <p>
          Implementamos medidas técnicas e organizativas adequadas (encriptação de passwords, controlos de acesso,
          backups e monitorização). Ainda assim, nenhum sistema é 100% imune; use passwords robustas
          e mantenha os seus dispositivos atualizados.
        </p>

        <h2>10. Menores</h2>
        <p>
          O serviço não se destina a menores de 16 anos sem consentimento dos encarregados de educação.
          Se detetar dados de menores sem consentimento, contacte-nos para remoção.
        </p>

        <h2>11. Alterações a esta política</h2>
        <p>
          Podemos atualizar esta política para refletir mudanças legais ou operacionais.
          A versão atual estará sempre disponível em <a href="{{ $siteUrl }}/privacidade">{{ $siteUrl }}/privacidade</a>.
        </p>

        <h2>12. Contactos</h2>
        <p>
          Dúvidas ou pedidos sobre privacidade? Escreva para
          <a href="mailto:{{ $contacts['email'] }}">{{ $contacts['email'] }}</a>.
        </p>
      </article>
    </div>
  </div>
@endsection
