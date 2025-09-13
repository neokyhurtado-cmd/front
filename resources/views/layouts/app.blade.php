@php
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
@endphp
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','PANORAMA INGENIERIA IA')</title>
    
    <!-- Google Fonts - Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! TwitterCard::generate() !!}
    {!! JsonLd::generate() !!}
    
    @vite(['resources/css/app.css','resources/js/app.js'])
    @stack('head')
  </head>
  <body class="bg-gray-50 text-gray-900" style="font-family: 'Montserrat', sans-serif;">
    @yield('content')

    <script>
      function tick(){
        const el = document.getElementById('clock');
        if(!el) return;
        const f = new Date();
        const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' };
        el.textContent = f.toLocaleString('es-CO', opts);
      }
      tick(); setInterval(tick, 30_000);
    </script>
    @stack('scripts')
  </body>
</html>
