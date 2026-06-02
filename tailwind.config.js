/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/View/Components/**/*.php',
    './vendor/livewire/livewire/src/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        suluh: {
          bg:           '#f5f2ec',
          surface:      '#fdfcfa',
          fg:           '#2e2b25',
          muted:        '#6b6660',
          border:       '#d9d4cc',
          accent:       '#3d7a52',
          'accent-warm':'#a0622a',
          'accent-soft':'#e8f0dc',
          info:         '#3d7a99',
          success:      '#2e7a4a',
          warning:      '#9a7800',
          danger:       '#8c2e18',
        },
      },
      fontFamily: {
        display: ['"Aptos Display"', '"Segoe UI"', 'system-ui', 'sans-serif'],
        body:    ['"Aptos"', '"Segoe UI"', '-apple-system', 'BlinkMacSystemFont', 'system-ui', 'sans-serif'],
        mono:    ['"JetBrains Mono"', '"IBM Plex Mono"', 'ui-monospace', 'Menlo', 'monospace'],
      },
      borderRadius: {
        badge:  '8px',
        card:   '12px',
        panel:  '16px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}

