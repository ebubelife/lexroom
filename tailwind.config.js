/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        'serif': ['Instrument Serif', 'serif'],
        'sans': ['DM Sans', 'sans-serif'],
      },
      colors: {
        navy: '#0D1B2A',
        gold: {
          DEFAULT: '#C9A84C',
          light: '#E8C96A',
          pale: '#F5EDD6',
        },
      },
    },
  },
  plugins: [],
}