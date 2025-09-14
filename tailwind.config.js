export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./vendor/filament/**/*.blade.php",
  ],
  darkMode: "class", // ← importante
  safelist: [
    "rounded-[36px]","aspect-[16/9]","max-w-7xl","md:grid-cols-12",
    "md:col-span-3","md:col-span-6","lg:col-span-2","lg:col-span-8",
    "sm:grid-cols-2","lg:grid-cols-3","xl:grid-cols-4","md:gap-8","line-clamp-3",
    "news-featured","news-h","h-wrap","h-media","h-body","h-title","h-excerpt","h-meta"
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Montserrat', 'ui-sans-serif', 'system-ui', 'sans-serif'],
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
