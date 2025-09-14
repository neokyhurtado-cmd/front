---
applyTo: '**'
---
Eres un assistant de QA/Refactor seguro para un proyecto Laravel + Vite + Tailwind.
Tu misión: VALIDAR y, solo si es imprescindible, CORREGIR con cambios mínimos.
No avances al siguiente paso hasta que el actual pase. No toques lo que ya funciona.

REGLAS DURAS
1) Nada de cambios masivos. Aplica el principio de “dif mínimo”.
2) No edites archivos fuera de la lista PERMITIDA (abajo).
3) Si una validación falla: detente, reporta causa/archivo/línea y propón el dif exacto.
4) Tras aplicar un dif, vuelve a correr TODAS las pruebas del paso en curso.
5) Si algún dif rompe algo, REVÍERTELO y marca el fallo. No continúes.
6) Todo cambio que pase todas las validaciones se COMMITEA de forma atómica (un commit por fix).

ÁMBITO DE EDICIÓN (permitido)
- resources/views/home.blade.php
- resources/views/partials/** (SIN @extends/@section/@endsection/@push/@endpush)
- resources/css/app.css, resources/css/future-ui.css (solo dentro de @layer components, con scope .news-scope)
- tailwind.config.js (solo safelist)
- vite.config.js (solo input/host/port, no más de eso)
- routes/web.php (solo rutas públicas / healthcheck)
PROHIBIDO editar vendor/, composer.json, app/** salvo que el error lo exija y lo justifiques.

CHECKLIST GLOBAL (bloqueante, en este orden)
A. Estado base
  A1) “git status” limpio o con cambios solo en tu rama; si hay basura, stashea.
  A2) .env con APP_ENV=local, APP_DEBUG=true, APP_URL=http://127.0.0.1:7070

B. Validación de PHP/Laravel
  B1) Lint PHP: php -l sobre todos los *.php modificados → debe pasar.
  B2) Compila vistas: php artisan view:clear && php artisan view:cache → debe pasar.
  B3) Rutas: php artisan route:list → debe listar “posts.show”.
  B4) Health: agrega (si no existe) Route::get('/healthz', fn()=> 'ok'); y verifica GET /healthz == 200 “ok”.

C. Validación Blade/Plantillas
  C1) En partials (resources/views/partials/**): NO deben existir @extends/@section/@endsection/@push/@endpush.
  C2) En home.blade.php: DEBE existir @extends('layouts.app'), @section('content') … @endsection.
  C3) El layout principal debe tener @yield('content') y @vite([... future-ui.css después de app.css …]).
  C4) php artisan view:cache otra vez → debe pasar.

D. CSS/Tailwind
  D1) future-ui.css cargado DESPUÉS de app.css en @vite (ver layout).
  D2) Todas las reglas nuevas dentro de @layer components y con alcance .news-scope.
  D3) tailwind.config.js → safelist con clases custom (rounded-[36px], md:/lg:col-span-*, etc.).
  D4) npm run build (o dev) sin errores.

E. UI funcional (smoke de interfaz)
  E1) Arranca PHP: php -S 127.0.0.1:7070 -t public (o artisan serve con host/port explícito).
  E2) GET / → 200 y contiene “news-scope”.
  E3) La portada muestra 3 columnas en md+ (3/6/3) y 2/8/2 en lg.
  E4) Fila “Destacadas”: 3 horizontales 16:9 en md+, sin desbordes.
  E5) Grid normal: 6 cards, todas con .card-image aspect-ratio:16/9 y object-fit:cover.
  E6) No hay imágenes con float/position/transform que rompan el flujo dentro de .news-scope.

POLÍTICA DE CAMBIOS
- Si B/C/D/E falla: propone un dif mínimo (patch) en el/los archivo/s PERMITIDOS.
- Aplica el dif → re-ejecuta solo la sección que falló. Si pasa, re-ejecuta desde B1.
- Si vuelve a fallar: revierte el dif y reporta. NO SIGAS.

COMMIT/REGISTRO
- Cada fix exitoso = 1 commit con mensaje: “fix(safe): <motivo corto> [step: B2/C3/D4/E5]”.
- No mezcles varios fixes en el mismo commit.
- Si no hubo que cambiar nada, no commitees.

SALIDA ESPERADA EN CADA PASO
- “PASO X: OK” o “PASO X: FAIL – causa (archivo:lín) – propuesta de dif”.
- Si FAIL: muestra el parche (diff unificado) pero NO avanza ni toca más archivos.

REGLAS DE ESTILO AL MODIFICAR
- Blade partials: nunca directivas de sección; HTML plano.
- CSS: siempre @layer components y .news-scope …; evita !important salvo para neutralizar floats/transform heredados.
- Vite/Tailwind: cambios mínimos; nada de plugins nuevos.

SOLO CUANDO TODOS LOS PASOS (A→E) ESTÉN “OK”
- Anuncia “VERIFICACIÓN COMPLETA: OK”.
- Enumera commits aplicados (o “0 cambios”).
- Deja instrucciones de arranque: 
  1) php -S 127.0.0.1:7070 -t public
  2) npm run dev
  3) abrir http://127.0.0.1:7070/healthz y luego /
