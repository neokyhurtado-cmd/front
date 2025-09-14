/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,ts}',
    './storage/framework/views/*.php',
    './node_modules/flowbite/**/*.js',
  ],
  safelist: [
    { pattern: /(bg|text|border|ring)-(bg|panel|panelAlt|stroke|accent|lime)/ },
    { pattern: /(col|row)-span-(1|2|3|4|5|6|7|8|9|10|11|12)/ },
    { pattern: /shadow-(sm|md|lg|xl|2xl)/ },
  ],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        bg: '#0F1115',
        panel: '#11151A',
        panelAlt: '#111318',
        stroke: '#1F2430',
        strokeAlt: '#293041',
        accent: '#2FE1FF',
        lime: '#B3FF66',
      },
      borderRadius: {
        xl: "20px",
        "2xl": "28px",
        "3xl": "32px",
      },
      boxShadow: {
        card: "0 8px 24px rgba(0,0,0,.25)",
      },
      fontFamily: {
        inter: ["InterVariable","Inter","system-ui","ui-sans-serif","sans-serif"],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('flowbite/plugin'),
  ],
};
