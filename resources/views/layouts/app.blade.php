<!doctype html>
<html lang="es" class="h-full">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="color-scheme" content="dark light" />
  <title>@yield('title', 'Panorama Ingeniería')</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen font-sans" style="background: #0F1115; color: #E6E8EC;">
  {{-- Header futurista estilo Mobility API --}}
  <header class="sticky top-0 z-50" style="background: rgba(15,17,21,.85); backdrop-filter: blur(12px); border-bottom: 1px solid #1F2430;">
    <div class="max-w-7xl mx-auto px-6">
      <div class="flex items-center justify-between h-16">
        {{-- Logo/Marca --}}
        <h1 class="text-xl font-bold tracking-wide" style="color: #E6E8EC;">PANORAMA INGENIERÍA</h1>
        
        {{-- Navegación --}}
        <nav class="hidden md:flex items-center space-x-8 text-sm">
          <a href="/" class="hover:text-white transition-colors" style="color: rgba(230,232,236,.8);">Documentación</a>
          <a href="/blog" class="hover:text-white transition-colors" style="color: rgba(230,232,236,.8);">Contacto</a>
          <a href="/admin" class="hover:text-white transition-colors" style="color: rgba(230,232,236,.8);">Estado API</a>
        </nav>
        
        {{-- Controles derecha --}}
        <div class="flex items-center space-x-3">
          <button class="w-8 h-8 rounded-lg flex items-center justify-center" 
                  style="background: #1F2430; color: #A4AABB;" 
                  title="Layout Density">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
          </button>
          <button class="w-8 h-8 rounded-lg flex items-center justify-center"
                  style="background: #1F2430; color: #A4AABB;"
                  title="Theme Toggle">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-7xl px-6 py-8">
    @yield('content')
  </main>

  {{-- Banda inferior CTA futurista --}}
  <footer class="bg-[#11151A] rounded-2xl border border-[#1F2430] shadow-[0_8px_24px_rgba(0,0,0,.25)] mx-auto max-w-7xl px-6 py-8 mt-12 mb-8">
    <div class="text-center">
      <h2 class="text-lg font-semibold mb-2 text-[#B3FF66]">
        Manera más efectiva de contactarnos para proyectos de movilidad
      </h2>
      <p class="text-sm mb-6 text-[#A4AABB]">
        Análisis predictivo, optimización de rutas y señalización inteligente
      </p>
      <a href="https://panorama-ingenieria.com" 
         class="inline-flex items-center px-6 py-3 rounded-lg font-semibold text-sm bg-[#2FE1FF] text-[#0F1115] hover:bg-[#26C7E0] transition-colors">
        Ver nuestros servicios →
      </a>
    </div>
  </footer>

  <!-- Skeleton loading controlado -->
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('figure.aspect-\\[16\\/9\\] img[loading="lazy"]').forEach(img=>{
      const f = img.closest('figure');
      f.classList.add('skeleton');
      const off=()=>f.classList.remove('skeleton');
      img.addEventListener('load', off, {once:true});
      img.addEventListener('error', off, {once:true});
    });
  });
  </script>
</body>
</html>