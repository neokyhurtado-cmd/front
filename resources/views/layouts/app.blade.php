<!doctype html>
<html lang="es" class="scroll-smooth">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','PANORAMA INGENIERIA IA')</title>
    <meta name="description" content="@yield('meta_description', 'Noticias de movilidad y señalización vial con IA')">
    
    <!-- Optimizaciones -->
    <meta name="color-scheme" content="dark light">
    <meta name="theme-color" content="#0b1220">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    @if(app()->environment('local'))
      <!-- Prefetch Vite HMR en desarrollo -->
      <link rel="prefetch" href="http://127.0.0.1:5174/@vite/client">
    @endif
    
  @vite(['resources/css/app.css', 'resources/css/future-ui.css', 'resources/js/app.js'])
    @stack('styles')
  </head>
  <body class="min-h-screen bg-[var(--bg)] text-[var(--fg)]">
    <!-- Header -->
    <header class="sticky top-0 z-40 border-b border-[var(--card-border)] bg-[var(--bg)]/90 backdrop-blur">
      <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
        <a href="{{ url('/') }}" class="font-bold tracking-wide text-xl">PANORAMA INGENIERIA IA</a>

        <div class="flex items-center gap-3">
          <input type="search" placeholder="Buscar…" 
                 class="hidden md:block rounded-xl border border-[var(--card-border)] bg-[var(--card)] px-3 py-2 text-sm outline-none focus:ring-2 ring-[var(--accent)]" />
          <button
            type="button"
            onclick="window.__toggleTheme?.()"
            class="rounded-xl border border-[var(--card-border)] bg-[var(--card)] px-3 py-2 text-sm hover:opacity-90"
            aria-label="Cambiar tema">
            <span class="inline dark:hidden">🌙 Oscuro</span>
            <span class="hidden dark:inline">☀️ Claro</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Contenido -->
    <main class="mx-auto max-w-7xl px-4 py-8">
      @yield('content')
    </main>

    <!-- Footer limpio -->
    <footer class="mt-12 border-t border-[var(--card-border)]">
      <div class="mx-auto max-w-7xl px-4 py-8 text-sm text-[var(--muted)]">
        © {{ date('Y') }} Panorama Ingeniería · 
        <a class="link" href="https://www.panoramaingenieria.com">www.panoramaingenieria.com</a>
      </div>
    </footer>

    <!-- Skeleton loading para imágenes -->
    <style>
    .skeleton{position:relative;overflow:hidden;background:#111}
    .skeleton::after{content:"";position:absolute;inset:0;transform:translateX(-100%);
    animation:shimmer 1.2s infinite; background:linear-gradient(90deg,transparent,#222,transparent)}
    @keyframes shimmer{100%{transform:translateX(100%)}}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('img[loading="lazy"]').forEach(img=>{
        const parent = img.closest('figure');
        parent?.classList.add('skeleton');
        img.addEventListener('load', ()=> parent?.classList.remove('skeleton'), {once:true});
        img.addEventListener('error',()=> parent?.classList.remove('skeleton'), {once:true});
      });
    });
    </script>
  
    @stack('scripts')
  </body>
</html>