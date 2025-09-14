export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./vendor/filament/**/*.blade.php",
  ],
  darkMode: "class",
  safelist: [
    // Grid responsivo
    "rounded-[36px]","aspect-[16/9]","max-w-7xl","md:grid-cols-12",
    "md:col-span-3","md:col-span-6","lg:col-span-2","lg:col-span-8",
    "sm:grid-cols-2","lg:grid-cols-3","xl:grid-cols-4","md:gap-8","line-clamp-3",
    // Futurista
    "rounded-2xl","rounded-3xl","shadow-futurista","border-stroke","border-stroke-alt",
    "bg-panel","bg-panel-alt","text-muted","shadow-card"
  ],
  theme: {
    extend: {
      // 🎨 DESIGN TOKENS FUTURISTAS
      colors: {
        // Base oscuro
        'bg-dark': '#0F1115',
        'panel': '#11151A', 
        'panel-alt': '#111318',
        'stroke': '#1F2430',
        'stroke-alt': '#293041',
        
        // Textos
        'text-primary': '#E6E8EC',
        'muted': '#A4AABB',
        
        // Acentos
        'accent': '#2FE1FF',       // cyan principal
        'accent-2': '#B3FF66',     // lima secundario
        'amber-btn': '#2A130A',    // CTAs circulares
        'chip-bg': 'rgba(24,27,33,.6)',
        
        // Zinc compatibility (mantener lo existente)
        zinc: {
          50: '#fafafa',
          100: '#f4f4f5',
          200: '#e4e4e7',
          300: '#d4d4d8',
          400: '#a1a1aa', 
          500: '#71717a',
          600: '#52525b',
          700: '#3f3f46',
          800: '#27272a',
          900: '#18181b',
          950: '#09090b',
        }
      },
      fontFamily: {
        sans: ['Inter', 'Montserrat', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        futura: ['Space Grotesk', 'Inter', 'system-ui', 'sans-serif'],
      },
      borderRadius: {
        'sm': '10px',
        'md': '16px', 
        'lg': '20px',
        'xl': '28px',
        'xxl': '32px',
      },
      boxShadow: {
        'card': '0 8px 24px rgba(0,0,0,.25)',
        'futurista': '0 4px 16px rgba(0,0,0,.3), inset 0 1px 0 rgba(255,255,255,.05)',
      },
      backdropBlur: {
        'xs': '2px',
      },
      aspectRatio: {
        '16/9': '16 / 9',
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/line-clamp'),
  ],
}
