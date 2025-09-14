<!doctype html>
<html lang="es" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','PANORAMA INGENIERIA IA')</title>
  
  <!-- Google Fonts - Montserrat -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  
  @vite(['resources/css/app.css','resources/js/app.js'])
  <meta name="description" content="@yield('meta_description','Noticias de movilidad y señalización vial con IA')">
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-full transition-colors" style="font-family: 'Montserrat', sans-serif;"">
<html lang="es" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','PANORAMA INGENIERIA IA')</title>
  
  <!-- Google Fonts - Montserrat -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  
  @vite(['resources/css/app.css','resources/js/app.js'])
  <meta name="description" content="@yield('meta_description','Noticias de movilidad y señalización vial con IA')">
</head>
<body class="bg-gray-50 text-gray-900 min-h-full" style="font-family: 'Montserrat', sans-serif;">
  <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto grid grid-cols-12 gap-4 items-center py-3 px-6">
      <div class="col-span-4 text-sm text-gray-600 dark:text-gray-400" id="clock">—</div>
      <h1 class="col-span-4 text-center font-semibold text-lg tracking-tight text-gray-900 dark:text-white">PANORAMA INGENIERIA IA</h1>
      <div class="col-span-4 text-right text-sm flex items-center justify-end space-x-3">
        <!-- Toggle modo oscuro -->
        <button onclick="toggleDarkMode()" class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" title="Alternar modo oscuro">
          <svg class="w-4 h-4 text-gray-600 dark:text-gray-300 dark:hidden" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
          </svg>
          <svg class="w-4 h-4 text-gray-300 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
          </svg>
        </button>
        
        <a href="/admin" class="text-blue-600 dark:text-blue-400 hover:underline">Panel Admin</a>
        <span class="text-gray-600 dark:text-gray-400">(Free/Pro)</span>
      </div>
    </div>
  </header>

  <div class="border-y border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-6 py-2 text-center text-sm text-gray-600 dark:text-gray-400">
      Servicios de <a href="https://www.panoramaingenieria.com" class="text-blue-600 dark:text-blue-400 hover:underline">www.panoramaingenieria.com</a>
    </div>
  </div>

  <main class="max-w-7xl mx-auto px-6 py-8">
    @yield('content')
  </main>

  <footer class="mt-10 py-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-6 text-center text-sm font-medium text-gray-900 dark:text-gray-300">www.panoramaingenieria.com</div>
  </footer>

  <script>
    function tick(){
      const el=document.getElementById('clock'); if(!el) return;
      const f=new Date();
      el.textContent=f.toLocaleString('es-CO',{weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit'});
    }
    tick(); setInterval(tick,30000);

    // Toggle modo oscuro
    function toggleDarkMode() {
      document.documentElement.classList.toggle('dark');
      
      // Guardar preferencia en localStorage
      if (document.documentElement.classList.contains('dark')) {
        localStorage.setItem('darkMode', 'true');
      } else {
        localStorage.removeItem('darkMode');
      }
    }

    // Cargar preferencia de modo oscuro
    if (localStorage.getItem('darkMode') === 'true') {
      document.documentElement.classList.add('dark');
    }
  </script>
</body>
</html>
