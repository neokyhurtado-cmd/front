<!doctype html>
<html lang="es" class="h-full">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  @vite(['resources/css/app.css','resources/js/app.js'])
  <title>@yield('title','Panorama Ingeniería')</title>
</head>
<body class="h-full bg-bg text-white">
  {{-- Topbar --}}
  <header class="sticky top-0 z-40 border-b border-stroke bg-panel/90 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
      <a href="/" class="font-semibold tracking-wide">PANORAMA INGENIERÍA</a>
      <div class="flex items-center gap-2">
        <span class="text-xs text-neutral-400">Bogotá</span>
        <button data-drawer-target="aside" data-drawer-toggle="aside"
                class="inline-flex items-center rounded-lg border border-stroke bg-panelAlt px-3 py-1 text-sm">
          Menú
        </button>
        <button onclick="toggleTheme()" class="h-8 w-8 rounded-lg bg-panel flex items-center justify-center border border-stroke">🌙</button>
      </div>
    </div>
  </header>

  {{-- Sidebar --}}
  <aside id="aside" class="fixed left-0 top-0 z-50 h-screen w-72 -translate-x-full border-r border-stroke bg-panel p-4 transition-transform"
         tabindex="-1" aria-labelledby="aside-label">
    <h2 id="aside-label" class="mb-4 text-sm font-medium text-neutral-300">Navegación</h2>
    <nav class="space-y-1 text-sm">
      <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 hover:bg-panelAlt">Dashboard</a>
      <a href="#" class="block rounded-lg px-3 py-2 hover:bg-panelAlt">Reportes</a>
      <a href="#" class="block rounded-lg px-3 py-2 hover:bg-panelAlt">Análisis IA</a>
      <a href="#" class="block rounded-lg px-3 py-2 hover:bg-panelAlt">Ajustes</a>
    </nav>
  </aside>

  {{-- Contenido --}}
  <main class="mx-auto max-w-7xl p-4">
    @yield('content')
  </main>
</body>
</html>