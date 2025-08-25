import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/account/account-profile.js',
        'resources/js/pages/auth/auth-modals.js',

        'resources/js/pages/admin/consultas/consultas-admin-create.js',
        'resources/js/pages/admin/consultas/consultas-admin-index-modal.js',
        'resources/js/pages/admin/consultas/consultas-admin-index-dropdown.js',
        'resources/js/pages/admin/consultas/consultas-admin-index-filters.js',
        'resources/js/pages/admin/especialidades/especialidades-admin-index-modal.js',
        'resources/js/pages/admin/especialidades/especialidades-admin-index-dropdown.js',
        'resources/js/pages/admin/medicos/medicos-admin-index-modal.js',
        'resources/js/pages/admin/medicos/medicos-admin-index-dropdown.js',
        'resources/js/pages/admin/dashboard-admin.js',
        
        'resources/js/pages/medico/consultas/consultas-medico-create.js',
        'resources/js/pages/medico/consultas/consultas-medico-index-dropdown.js',
        'resources/js/pages/medico/consultas/consultas-medico-index-modal.js',
        'resources/js/pages/medico/consultas/consultas-medico-index-filters.js',
        'resources/js/pages/medico/calendario/medico-calendario.js',

        'resources/js/pages/paciente/consultas/consultas-paciente-create.js',
        'resources/js/pages/paciente/medicos/medicos-paciente-index.js',
        'resources/js/pages/paciente/medicos/medicos-paciente-show.js',
        'resources/js/pages/paciente/especialidades/especialidades-paciente-index.js',
        'resources/js/pages/paciente/especialidades/especialidades-paciente-show.js',
      ],
      refresh: true,
    }),
  ],
});
