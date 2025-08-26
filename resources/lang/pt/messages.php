<?php

return [

    // Nome da app e idiomas disponíveis
    'app' => [
        'name' => 'MediGest+',
        'locale_names' => [
            'pt' => 'Português (PT)',
            'en' => 'English',
            'es' => 'Español',
        ],
    ],

    // Itens de navegação (sidebar / header)
    'nav' => [
        'home'               => 'Início',
        'services'           => 'Serviços',
        'doctors'            => 'Corpo Clínico',
        'channels'           => 'Canais',

        'dashboard'          => 'Dashboard',
        'all_consultations'  => 'Todas as Consultas',
        'specialties'        => 'Especialidades',
        'create_doctor'      => 'Criar Médico',
        'calendar'           => 'Calendário',
        'schedules'          => 'Horários',
        'profile'            => 'Perfil',
        'settings'           => 'Configurações',
        'logout'             => 'Terminar sessão',
        'back'               => 'Voltar',
    ],

    // Ações comuns
    'actions' => [
        'save_changes'       => 'Guardar alterações',
        'save_preferences'   => 'Guardar preferências',
        'update_password'    => 'Atualizar password',
        'new_consultation'   => 'Nova Consulta',
        'cancel'             => 'Cancelar',
        'confirm'            => 'Confirmar',
        'edit'               => 'Editar',
        'delete'             => 'Eliminar',
        'clear'              => 'Limpar',
        'search'             => 'Pesquisar',
        'apply'              => 'Aplicar',
        'close'              => 'Fechar',
        'change_photo'       => 'Alterar foto',
        'remove_photo'       => 'Remover foto',
        'view'               => 'Ver',
        // opcionais (para wizards/form selects)
        'next'               => 'Seguinte',
        'back'               => 'Anterior',
        'create'             => 'Criar',
        'name'               => 'Nome',
        'select'             => '— Seleciona —',
    ],

    // Perfil
    'profile' => [
        'title'              => 'Perfil',
        'subtitle'           => 'Gerir informação pessoal e segurança da conta.',
        'name'               => 'Nome',
        'email'              => 'Email',
        'phone'              => 'Telefone',
        'role'               => 'Função',
        'photo_hint'         => 'PNG/JPG/WebP, até 2MB.',
        'security'           => 'Segurança',
        'password_new'       => 'Nova password',
        'password_confirm'   => 'Confirmar nova password',
        'strength'           => 'Força',
        'updated'            => 'Perfil atualizado com sucesso.',
        'password_updated'   => 'Password atualizada com sucesso.',
    ],

    // Configurações
    'settings' => [
        'title'              => 'Configurações',
        'subtitle'           => 'Preferências de interface, idioma e notificações.',
        'interface'          => 'Interface',
        'theme'              => 'Tema',
        'themes'             => [
            'light'  => 'Claro',
            'dark'   => 'Escuro',
            'system' => 'Automático',
        ],
        'language'           => 'Idioma',
        'notifications'      => 'Notificações',
        'notify_email'       => 'Email',
        'notify_email_hint'  => 'Receber alertas e confirmações por email.',
        'notify_push'        => 'Push',
        'notify_push_hint'   => 'Notificações do navegador (quando permitido).',
        'weekly_digest'      => 'Resumo semanal',
        'weekly_digest_hint' => 'Estatísticas e destaques da semana.',
        'saved'              => 'Preferências guardadas.',
    ],

    // Dashboard / Gráficos
    'dashboard' => [
        'title'              => 'Dashboard',
        'subtitle'           => 'Visão geral do sistema.',
        'by_day'             => 'Consultas por dia',
        'status'             => 'Estados das consultas',
        'by_specialty'       => 'Consultas por especialidade',
        'no_data'            => 'Sem dados no período',
        'period'             => 'Período',
        'from'               => 'de',
        'to'                 => 'até',
        'daily_total'        => 'Total diário no período.',
        // KPIs / dicas
        'kpi_users'               => 'Utilizadores',
        'kpi_doctors'             => 'Médicos registados',
        'kpi_active_consultations'=> 'Consultas (ativas)',
        'kpi_today_month'         => 'Hoje / Este mês',
        'distribution_hint'       => 'Distribuição de estados.',
        'top_specialties_hint'    => 'Top especialidades no período.',
    ],

    // Consultas
    'consultas' => [
        // listagem
        'list_title'         => 'Consultas',
        'list_subtitle'      => 'Lista de todas as consultas registadas no sistema.',
        'patient'            => 'Paciente',
        'doctor'             => 'Médico',
        'specialty'          => 'Especialidade',
        'date'               => 'Data',
        'time'               => 'Hora',
        'status'             => 'Estado',
        'actions'            => 'Ações',

        // criar/editar (wizard)
        'create_title'       => 'Criar Consulta',
        'create_subtitle'    => 'Preenche os passos para agendar a consulta.',
        'type'               => 'Tipo',
        'description'        => 'Descrição',
        'availability'       => 'Disponibilidade',
        'review'             => 'Revisão',
        'schedule'           => 'Agendar',

        // auxiliares do wizard/form
        'select_patient'          => 'Selecionar paciente',
        'select_doctor'           => 'Selecionar médico',
        'select_specialty_first'  => '— seleciona a especialidade —',
        'filtered_by_specialty'   => 'A lista é filtrada pela especialidade.',
        'select_doctor_and_date'  => '— seleciona médico e data —',
        'slots_hint'              => 'Escolhe médico e data para ver as horas disponíveis.',
        'description_ph'          => 'Ex.: dores lombares, exame de rotina, etc.',

        'types' => [
            'normal'      => 'Normal',
            'prioritaria' => 'Prioritária',
            'urgente'     => 'Urgente',
        ],
    ],

    // Estados de consulta
    'status' => [
        'scheduled'          => 'agendada',
        'confirmed'          => 'confirmada',
        'pending'            => 'pendente',
        'pending_doctor'     => 'pendente (médico)',
        'canceled'           => 'cancelada',
    ],

    // Textos genéricos
    'common' => [
        'yes'                => 'Sim',
        'no'                 => 'Não',
        'none'               => '—',
        'loading'            => 'A carregar…',
        'error'              => 'Ocorreu um erro.',
        'success'            => 'Operação concluída com sucesso.',
        'action'             => 'Ação',
        'select_placeholder' => '— Seleciona —',
    ],

    // Sidebar
    'sidebar' => [
        'general'                => 'Geral',
        'consultations'          => 'Consultas',
        'management'             => 'Gestão',
        'assigned_consultations' => 'Consultas Atribuídas',
    ],

    // Wizard (navegação)
    'wizard' => [
        'next'     => 'Seguinte',
        'previous' => 'Anterior',
    ],

    // Especialidades
    'specialties' => [
        'title'         => 'Especialidades',
        'subtitle'      => 'Gestão das especialidades médicas disponíveis no sistema.',
        'create'        => 'Criar Especialidade',
        'new'           => 'Nova Especialidade',
        'new_subtitle'  => 'Adiciona uma nova especialidade médica ao sistema.',
        'edit'          => 'Editar Especialidade',
        'edit_subtitle' => 'Altera o nome da especialidade selecionada.',
        'name'          => 'Nome',
        'name_ph'       => 'Ex.: Cardiologia',
        'empty'         => 'Sem especialidades ainda.',
    ],

    // Horários
    'schedules' => [
        'title'               => 'Horários',
        'subtitle'            => 'Visualiza os horários de atendimento atuais.',
        'configure'           => 'Configurar Horários',
        'configure_subtitle'  => 'Seleciona os dias e define os horários de atendimento.',
        'day'                 => 'Dia',
        'morning'             => 'Manhã',
        'afternoon'           => 'Tarde',
        'active'              => 'Ativo',
        'weekly_schedule'     => 'Horário semanal',
        'select_all'          => 'Selecionar todos',
        'apply_days_hint'     => 'Seleciona os dias a que pretendes aplicar as alterações.',
        'morning_hours'       => 'Horário da Manhã',
        'afternoon_hours'     => 'Horário da Tarde',
        'start'               => 'Início',
        'end'                 => 'Fim',
    ],

    // Médicos
    'doctors' => [
        'title'                  => 'Médicos',
        'subtitle'               => 'Lista de todos os médicos registados no sistema.',
        'create'                 => 'Criar Médico',
        'new'                    => 'Novo Médico',
        'new_subtitle'           => 'Preenche os dados do médico e associa as especialidades.',
        'edit'                   => 'Editar Médico',
        'edit_subtitle'          => 'Atualiza os dados do médico e as especialidades associadas.',
        'name_ph'                => 'Nome completo',
        'email_ph'               => 'exemplo@dominio.com',
        'password_optional'      => 'Password (opcional)',
        'leave_blank_hint'       => 'Deixa em branco para manter a password atual.',
        'crm_ph'                 => 'Ex.: CRM123',
        'unique_hint'            => 'Deve ser único.',
        'bio'                    => 'Bio',
        'bio_ph'                 => 'Breve apresentação do médico',
        'select_specialties_ph'  => 'Selecione especialidades…',
        'multiselect_hint'       => 'Clica para adicionar. Escreve para filtrar. Enter também adiciona o primeiro resultado.',
        'multi_select_hint'      => 'Podes selecionar múltiplas especialidades.',
    ],

    // Admin
    'admin' => [
        'welcome'  => 'Bem-vindo, :name',
        'overview' => 'Visão geral do sistema.',
    ],

    'modals' => [
        'common' => [
            'close' => 'Fechar',
            'yes'   => 'Sim',
            'no'    => 'Não',
        ],
        'consultation' => [
            'title'       => 'Consulta',
            'patient'     => 'Paciente',
            'doctor'      => 'Médico',
            'description' => 'Descrição',
        ],
        'cancel' => [
            'title'    => 'Confirmar cancelamento',
            'question' => 'Tens a certeza que queres cancelar esta consulta?',
            'yes'      => 'Sim, cancelar',
        ],
        'delete_doctor' => [
            'title'    => 'Confirmar remoção do médico',
            'question' => 'Tens a certeza que queres apagar o médico “:name”?',
            'yes'      => 'Sim, apagar',
        ],
        'delete_specialty' => [
            'title'    => 'Confirmar remoção da especialidade',
            'question' => 'Tens a certeza que queres apagar a especialidade “:name”?',
            'yes'      => 'Sim, apagar',
        ],
    ],

    'calendar' => [
        'title' => 'Calendário',
        'today' => 'Hoje',
        'month' => 'Mês',
        'week'  => 'Semana',
        'day'   => 'Dia',
        'list'  => 'Agenda',
    ],
];
