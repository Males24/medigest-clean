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
        'dashboard'          => 'Dashboard',
        'all_consultations'  => 'All Consultations',
        'specialties'        => 'Specialties',
        'create_doctor'      => 'Create Doctor',
        'calendar'           => 'Calendar',
        'schedules'          => 'Schedules',
        'profile'            => 'Profile',
        'settings'           => 'Settings',
        'logout'             => 'Sign out',
        'back'               => 'Back',
    ],

    'actions' => [
        'save_changes'       => 'Save changes',
        'save_preferences'   => 'Save preferences',
        'update_password'    => 'Update password',
        'new_consultation'   => 'New Consultation',
        'cancel'             => 'Cancel',
        'confirm'            => 'Confirm',
        'edit'               => 'Edit',
        'delete'             => 'Delete',
        'clear'              => 'Clear',
        'search'             => 'Search',
        'apply'              => 'Apply',
        'close'              => 'Close',
        'change_photo'       => 'Change photo',
        'remove_photo'       => 'Remove photo',
        'view'               => 'View',
        // extras used in wizards/forms
        'next'               => 'Next',
        'back'               => 'Back',
        'create'             => 'Create',
        'name'               => 'Name',
        'select'             => '-- Select --',
    ],

    'profile' => [
        'title'              => 'Profile',
        'subtitle'           => 'Manage personal information and account security.',
        'name'               => 'Name',
        'email'              => 'Email',
        'phone'              => 'Phone',
        'role'               => 'Role',
        'photo_hint'         => 'PNG/JPG/WebP, up to 2MB.',
        'security'           => 'Security',
        'password_new'       => 'New password',
        'password_confirm'   => 'Confirm new password',
        'strength'           => 'Strength',
        'updated'            => 'Profile updated successfully.',
        'password_updated'   => 'Password updated successfully.',
    ],

    'settings' => [
        'title'              => 'Settings',
        'subtitle'           => 'Interface, language and notification preferences.',
        'interface'          => 'Interface',
        'theme'              => 'Theme',
        'themes'             => [
            'light'  => 'Light',
            'dark'   => 'Dark',
            'system' => 'Automatic',
        ],
        'language'           => 'Language',
        'notifications'      => 'Notifications',
        'notify_email'       => 'Email',
        'notify_email_hint'  => 'Receive alerts and confirmations by email.',
        'notify_push'        => 'Push',
        'notify_push_hint'   => 'Browser notifications (when allowed).',
        'weekly_digest'      => 'Weekly digest',
        'weekly_digest_hint' => 'Weekly stats and highlights.',
        'saved'              => 'Preferences saved.',
    ],

    'dashboard' => [
        'title'              => 'Dashboard',
        'subtitle'           => 'System overview.',
        'by_day'             => 'Consultations per day',
        'status'             => 'Consultation statuses',
        'by_specialty'       => 'Consultations by specialty',
        'no_data'            => 'No data in the selected period',
        'period'             => 'Period',
        'from'               => 'from',
        'to'                 => 'to',
        'daily_total'        => 'Daily total in the period.',
        // KPIs / hints
        'kpi_users'                => 'Users',
        'kpi_doctors'              => 'Registered doctors',
        'kpi_active_consultations' => 'Consultations (active)',
        'kpi_today_month'          => 'Today / This month',
        'distribution_hint'        => 'Status distribution.',
        'top_specialties_hint'     => 'Top specialties in the period.',
    ],

    'consultas' => [
        // list
        'list_title'         => 'Consultations',
        'list_subtitle'      => 'List of all consultations registered in the system.',
        'patient'            => 'Patient',
        'doctor'             => 'Doctor',
        'specialty'          => 'Specialty',
        'date'               => 'Date',
        'time'               => 'Time',
        'status'             => 'Status',
        'actions'            => 'Actions',

        // create (wizard)
        'create_title'       => 'Create Consultation',
        'create_subtitle'    => 'Fill the steps to schedule the consultation.',
        'type'               => 'Type',
        'description'        => 'Description',
        'availability'       => 'Availability',
        'review'             => 'Review',
        'schedule'           => 'Schedule',

        // wizard helpers / placeholders
        'select_patient'          => 'Select patient',
        'select_doctor'           => 'Select doctor',
        'select_specialty_first'  => '— select the specialty —',
        'filtered_by_specialty'   => 'The list is filtered by specialty.',
        'select_doctor_and_date'  => '— select doctor and date —',
        'slots_hint'              => 'Choose doctor and date to see available times.',
        'description_ph'          => 'E.g.: lower back pain, check-up, etc.',

        'types' => [
            'normal'      => 'Normal',
            'prioritaria' => 'Priority',
            'urgente'     => 'Urgent',
        ],
    ],

    'status' => [
        'scheduled'          => 'scheduled',
        'confirmed'          => 'confirmed',
        'pending'            => 'pending',
        'pending_doctor'     => 'pending (doctor)',
        'canceled'           => 'canceled',
    ],

    'common' => [
        'yes'                => 'Yes',
        'no'                 => 'No',
        'none'               => '—',
        'loading'            => 'Loading…',
        'error'              => 'An error occurred.',
        'success'            => 'Operation completed successfully.',
        'action'             => 'Action',
        'select_placeholder' => '-- Select --',
    ],

    'sidebar' => [
        'general'                => 'General',
        'consultations'          => 'Consultations',
        'management'             => 'Management',
        'assigned_consultations' => 'Assigned Consultations',
    ],

    'wizard' => [
        'next'     => 'Next',
        'previous' => 'Previous',
    ],

    'specialties' => [
        'title'         => 'Specialties',
        'subtitle'      => 'Manage medical specialties available in the system.',
        'create'        => 'Create Specialty',
        'new'           => 'New Specialty',
        'new_subtitle'  => 'Add a new medical specialty to the system.',
        'edit'          => 'Edit Specialty',
        'edit_subtitle' => 'Change the selected specialty name.',
        'name'          => 'Name',
        'name_ph'       => 'e.g., Cardiology',
        'empty'         => 'No specialties yet.',
    ],

    'schedules' => [
        'title'               => 'Schedules',
        'subtitle'            => 'View current service hours.',
        'configure'           => 'Configure Schedules',
        'configure_subtitle'  => 'Select days and define the service hours.',
        'day'                 => 'Day',
        'morning'             => 'Morning',
        'afternoon'           => 'Afternoon',
        'active'              => 'Active',
        'weekly_schedule'     => 'Weekly schedule',
        'select_all'          => 'Select all',
        'apply_days_hint'     => 'Select the days to which you want to apply the changes.',
        'morning_hours'       => 'Morning hours',
        'afternoon_hours'     => 'Afternoon hours',
        'start'               => 'Start',
        'end'                 => 'End',
    ],

    'doctors' => [
        'title'                  => 'Doctors',
        'subtitle'               => 'List of all doctors registered in the system.',
        'create'                 => 'Create Doctor',
        'new'                    => 'New Doctor',
        'new_subtitle'           => 'Fill the doctor data and link specialties.',
        'edit'                   => 'Edit Doctor',
        'edit_subtitle'          => 'Update doctor data and specialties.',
        'name_ph'                => 'Full name',
        'email_ph'               => 'example@domain.com',
        'password_optional'      => 'Password (optional)',
        'leave_blank_hint'       => 'Leave blank to keep the current password.',
        'crm_ph'                 => 'e.g., CRM123',
        'unique_hint'            => 'Must be unique.',
        'bio'                    => 'Bio',
        'bio_ph'                 => 'Short presentation of the doctor',
        'select_specialties_ph'  => 'Select specialties…',
        'multiselect_hint'       => 'Click to add. Type to filter. Enter also adds the first result.',
        'multi_select_hint'      => 'You can select multiple specialties.',
    ],

    'admin' => [
        'welcome'  => 'Welcome, :name',
        'overview' => 'System overview.',
    ],

    'modals' => [
        'common' => [
            'close' => 'Close',
            'yes'   => 'Yes',
            'no'    => 'No',
        ],
        'consultation' => [
            'title'       => 'Consultation',
            'patient'     => 'Patient',
            'doctor'      => 'Doctor',
            'description' => 'Description',
        ],
        'cancel' => [
            'title'    => 'Confirm cancellation',
            'question' => 'Are you sure you want to cancel this consultation?',
            'yes'      => 'Yes, cancel',
        ],
        'delete_doctor' => [
            'title'    => 'Confirm doctor removal',
            'question' => 'Are you sure you want to delete doctor “:name”?',
            'yes'      => 'Yes, delete',
        ],
        'delete_specialty' => [
            'title'    => 'Confirm specialty removal',
            'question' => 'Are you sure you want to delete specialty “:name”?',
            'yes'      => 'Yes, delete',
        ],
    ],

    'calendar' => [
        'title' => 'Calendar',
        'today' => 'Today',
        'month' => 'Month',
        'week'  => 'Week',
        'day'   => 'Day',
        'list'  => 'List',
    ],
];
