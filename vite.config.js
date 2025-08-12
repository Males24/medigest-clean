import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/consultas-admin-create.js',
        'resources/js/pages/datepicker-consultas.js',
        'resources/js/pages/consultas-medico-create.js',   // novo
        'resources/js/pages/consultas-paciente-create.js', // novo
        'resources/js/pages/consultas-admin-index-modal.js',
        'resources/js/pages/especialidades-admin-index-modal.js',
        'resources/js/pages/medicos-admin-index-modal.js',
      ],
      refresh: true,
    }),
  ],
});
