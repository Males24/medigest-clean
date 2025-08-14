<?php

return [

    'app' => [
        'name' => 'MediGest+',
        'locale_names' => [
            'pt' => 'Português (PT)',
            'en' => 'English',
            'es' => 'Español',
        ],
    ],

    'nav' => [
        'dashboard'          => 'Panel',
        'all_consultations'  => 'Todas las Consultas',
        'specialties'        => 'Especialidades',
        'create_doctor'      => 'Crear Médico',
        'schedules'          => 'Horarios',
        'profile'            => 'Perfil',
        'settings'           => 'Configuraciones',
        'logout'             => 'Cerrar sesión',
        'back'               => 'Volver',
    ],

    'actions' => [
        'save_changes'       => 'Guardar cambios',
        'save_preferences'   => 'Guardar preferencias',
        'update_password'    => 'Actualizar contraseña',
        'new_consultation'   => 'Nueva Consulta',
        'cancel'             => 'Cancelar',
        'confirm'            => 'Confirmar',
        'edit'               => 'Editar',
        'delete'             => 'Eliminar',
        'search'             => 'Buscar',
        'apply'              => 'Aplicar',
        'close'              => 'Cerrar',
        'change_photo'       => 'Cambiar foto',
        'remove_photo'       => 'Eliminar foto',
        // extras
        'next'               => 'Siguiente',
        'back'               => 'Anterior',
        'create'             => 'Crear',
        'name'               => 'Nombre',
        'select'             => '-- Selecciona --',
    ],

    'profile' => [
        'title'              => 'Perfil',
        'subtitle'           => 'Gestiona la información personal y la seguridad de la cuenta.',
        'name'               => 'Nombre',
        'email'              => 'Correo',
        'phone'              => 'Teléfono',
        'role'               => 'Rol',
        'photo_hint'         => 'PNG/JPG/WebP, hasta 2MB.',
        'security'           => 'Seguridad',
        'password_new'       => 'Nueva contraseña',
        'password_confirm'   => 'Confirmar nueva contraseña',
        'strength'           => 'Fuerza',
        'updated'            => 'Perfil actualizado con éxito.',
        'password_updated'   => 'Contraseña actualizada con éxito.',
    ],

    'settings' => [
        'title'              => 'Configuraciones',
        'subtitle'           => 'Preferencias de interfaz, idioma y notificaciones.',
        'interface'          => 'Interfaz',
        'theme'              => 'Tema',
        'themes'             => [
            'light'  => 'Claro',
            'dark'   => 'Oscuro',
            'system' => 'Automático',
        ],
        'language'           => 'Idioma',
        'notifications'      => 'Notificaciones',
        'notify_email'       => 'Correo',
        'notify_email_hint'  => 'Recibir alertas y confirmaciones por correo.',
        'notify_push'        => 'Push',
        'notify_push_hint'   => 'Notificaciones del navegador (cuando esté permitido).',
        'weekly_digest'      => 'Resumen semanal',
        'weekly_digest_hint' => 'Estadísticas y destacados de la semana.',
        'saved'              => 'Preferencias guardadas.',
    ],

    'dashboard' => [
        'title'              => 'Panel',
        'subtitle'           => 'Visión general del sistema.',
        'by_day'             => 'Consultas por día',
        'status'             => 'Estados de las consultas',
        'by_specialty'       => 'Consultas por especialidad',
        'no_data'            => 'Sin datos en el período seleccionado',
        'period'             => 'Período',
        'from'               => 'desde',
        'to'                 => 'hasta',
        'daily_total'        => 'Total diario en el período.',
        // KPIs / hints
        'kpi_users'                => 'Usuarios',
        'kpi_doctors'              => 'Médicos registrados',
        'kpi_active_consultations' => 'Consultas (activas)',
        'kpi_today_month'          => 'Hoy / Este mes',
        'distribution_hint'        => 'Distribución de estados.',
        'top_specialties_hint'     => 'Especialidades destacadas en el período.',
    ],

    'consultas' => [
        // listado
        'list_title'         => 'Consultas',
        'list_subtitle'      => 'Lista de todas las consultas registradas en el sistema.',
        'patient'            => 'Paciente',
        'doctor'             => 'Médico',
        'specialty'          => 'Especialidad',
        'date'               => 'Fecha',
        'time'               => 'Hora',
        'status'             => 'Estado',
        'actions'            => 'Acciones',

        // crear (wizard)
        'create_title'       => 'Crear Consulta',
        'create_subtitle'    => 'Completa los pasos para agendar la consulta.',
        'type'               => 'Tipo',
        'description'        => 'Descripción',
        'availability'       => 'Disponibilidad',
        'review'             => 'Revisión',
        'schedule'           => 'Agendar',

        // ayudas/placeholders
        'select_patient'          => 'Seleccionar paciente',
        'select_doctor'           => 'Seleccionar médico',
        'select_specialty_first'  => '— selecciona la especialidad —',
        'filtered_by_specialty'   => 'La lista se filtra por especialidad.',
        'select_doctor_and_date'  => '— selecciona médico y fecha —',
        'slots_hint'              => 'Elige médico y fecha para ver las horas disponibles.',
        'description_ph'          => 'Ej.: dolor lumbar, chequeo, etc.',

        'types' => [
            'normal'      => 'Normal',
            'prioritaria' => 'Prioritaria',
            'urgente'     => 'Urgente',
        ],
    ],

    'status' => [
        'scheduled'          => 'agendada',
        'confirmed'          => 'confirmada',
        'pending'            => 'pendiente',
        'pending_doctor'     => 'pendiente (médico)',
        'canceled'           => 'cancelada',
    ],

    'common' => [
        'yes'                => 'Sí',
        'no'                 => 'No',
        'none'               => '—',
        'loading'            => 'Cargando…',
        'error'              => 'Se produjo un error.',
        'success'            => 'Operación realizada con éxito.',
        'action'             => 'Acción',
        'select_placeholder' => '-- Selecciona --',
    ],

    'sidebar' => [
        'general'                => 'General',
        'consultations'          => 'Consultas',
        'management'             => 'Gestión',
        'assigned_consultations' => 'Consultas Asignadas',
    ],

    'wizard' => [
        'next'     => 'Siguiente',
        'previous' => 'Anterior',
    ],

    'specialties' => [
        'title'         => 'Especialidades',
        'subtitle'      => 'Gestión de las especialidades médicas disponibles en el sistema.',
        'create'        => 'Crear Especialidad',
        'new'           => 'Nueva Especialidad',
        'new_subtitle'  => 'Añade una nueva especialidad médica al sistema.',
        'edit'          => 'Editar Especialidad',
        'edit_subtitle' => 'Cambia el nombre de la especialidad seleccionada.',
        'name'          => 'Nombre',
        'name_ph'       => 'Ej.: Cardiología',
        'empty'         => 'Sin especialidades aún.',
    ],

    'schedules' => [
        'title'               => 'Horarios',
        'subtitle'            => 'Visualiza los horarios de atención actuales.',
        'configure'           => 'Configurar Horarios',
        'configure_subtitle'  => 'Selecciona los días y define los horarios de atención.',
        'day'                 => 'Día',
        'morning'             => 'Mañana',
        'afternoon'           => 'Tarde',
        'active'              => 'Activo',
        'weekly_schedule'     => 'Horario semanal',
        'select_all'          => 'Seleccionar todos',
        'apply_days_hint'     => 'Selecciona los días a los que deseas aplicar los cambios.',
        'morning_hours'       => 'Horario de la mañana',
        'afternoon_hours'     => 'Horario de la tarde',
        'start'               => 'Inicio',
        'end'                 => 'Fin',
    ],

    'doctors' => [
        'title'                  => 'Médicos',
        'subtitle'               => 'Lista de todos los médicos registrados en el sistema.',
        'create'                 => 'Crear Médico',
        'new'                    => 'Nuevo Médico',
        'new_subtitle'           => 'Completa los datos del médico y asocia especialidades.',
        'edit'                   => 'Editar Médico',
        'edit_subtitle'          => 'Actualiza los datos del médico y sus especialidades.',
        'name_ph'                => 'Nombre completo',
        'email_ph'               => 'ejemplo@dominio.com',
        'password_optional'      => 'Contraseña (opcional)',
        'leave_blank_hint'       => 'Déjalo en blanco para mantener la contraseña actual.',
        'crm_ph'                 => 'Ej.: CRM123',
        'unique_hint'            => 'Debe ser único.',
        'bio'                    => 'Bio',
        'bio_ph'                 => 'Breve presentación del médico',
        'select_specialties_ph'  => 'Selecciona especialidades…',
        'multiselect_hint'       => 'Haz clic para añadir. Escribe para filtrar. Enter también añade el primer resultado.',
        'multi_select_hint'      => 'Puedes seleccionar múltiples especialidades.',
    ],

    'admin' => [
        'welcome'  => 'Bienvenido, :name',
        'overview' => 'Visión general del sistema.',
    ],

    'modals' => [
        'common' => [
            'close' => 'Cerrar',
            'yes'   => 'Sí',
            'no'    => 'No',
        ],
        'consultation' => [
            'title'       => 'Consulta',
            'patient'     => 'Paciente',
            'doctor'      => 'Médico',
            'description' => 'Descripción',
        ],
        'cancel' => [
            'title'    => 'Confirmar cancelación',
            'question' => '¿Seguro que quieres cancelar esta consulta?',
            'yes'      => 'Sí, cancelar',
        ],
        'delete_doctor' => [
            'title'    => 'Confirmar eliminación del médico',
            'question' => '¿Seguro que quieres eliminar al médico “:name”?',
            'yes'      => 'Sí, eliminar',
        ],
        'delete_specialty' => [
            'title'    => 'Confirmar eliminación de la especialidad',
            'question' => '¿Seguro que quieres eliminar la especialidad “:name”?',
            'yes'      => 'Sí, eliminar',
        ],
    ],

];
