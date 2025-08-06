module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './node_modules/flowbite/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: 'var(--color-primary)',
        zinc: {
          50: 'var(--color-zinc-50)',
          100: 'var(--color-zinc-100)',
          200: 'var(--color-zinc-200)',
          300: 'var(--color-zinc-300)',
          400: 'var(--color-zinc-400)',
          500: 'var(--color-zinc-500)',
          600: 'var(--color-zinc-600)',
          700: 'var(--color-zinc-700)',
          800: 'var(--color-zinc-800)',
          900: 'var(--color-zinc-900)',
          950: 'var(--color-zinc-950)',
        },
        home: {
          medigest: 'var(--color-home-medigest)',
          'medigest-hover': 'var(--color-home-medigest-hover)',
          'medigest-button': 'var(--color-home-medigest-button)',
          'medigest-button-hover': 'var(--color-home-medigest-button-hover)',
        },
      },
      fontFamily: {
        sans: ['var(--font-sans)'],
      },
    },
  },
  plugins: [
    require('flowbite/plugin'),
  ],
};
