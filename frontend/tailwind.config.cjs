/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './index.html',
    './src/**/*.{ts,tsx,js,jsx}',
    '../resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {},
  },
  plugins: [require('daisyui')],
}
module.exports = {
  content: ['./index.html', './src/**/*.{ts,tsx,js,jsx}'],
  theme: {
    extend: {},
  },
  plugins: [],
}
