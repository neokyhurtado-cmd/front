<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Panorama Ingeniería IA — Portal</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
  <!-- header -->
  <header class="card" style="margin:16px; padding:14px 18px; display:flex; align-items:center; justify-content:space-between;">
    <div style="display:flex; align-items:center; gap:16px">
      <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#00E5FF,#0091EA)"></div>
      <div>
        <div style="font-weight:700">PANORAMA INGENIERÍA IA</div>
        <div style="color:var(--muted);font-size:12px">Movilidad • Señalización • Transporte — curado con IA</div>
      </div>
    </div>
    <div style="display:flex; gap:10px; align-items:center">
      <a class="btn btn--ghost" href="/">Inicio</a>
      <a class="btn" href="/admin">Panel Admin</a>
      <button id="themeToggle" class="btn btn--ghost" aria-label="Cambiar tema">Tema</button>
    </div>
  </header>

  <!-- layout 3 columnas -->
  <main class="container grid" style="grid-template-columns:260px 1fr 320px;">
    <!-- sidebar -->
    <aside class="card" style="padding:16px">
      <div style="font-weight:700;margin-bottom:8px">Temas</div>
      <div style="display:flex; flex-wrap:wrap; gap:8px">
        @foreach (['movilidad','señalización','tránsito','TransMilenio','seguridad vial','Bogotá'] as $t)
          <span class="badge hover-raise">{{ $t }}</span>
        @endforeach
      </div>

      <div style="height:1px;background:var(--bd);margin:14px 0"></div>

      <nav style="display:grid; gap:8px">
        <a class="hover-raise" style="text-decoration:none;color:var(--text)" href="/blog">Noticias</a>
        <a class="hover-raise" style="text-decoration:none;color:var(--text)" href="https://www.panoramaingenieria.com">Corporativo</a>
        <a class="hover-raise" style="text-decoration:none;color:var(--text)" href="/blog#manual">Manual de señalización</a>
      </nav>
    </aside>

    <!-- contenido central -->
    <section class="grid" style="grid-template-rows:auto auto 1fr; gap:20px">
      <!-- hero -->
      <div class="card" style="padding:22px;background:
           radial-gradient(1200px 280px at 20% -20%, rgba(0,229,255,.12), transparent 50%),
           radial-gradient(900px 220px at 80% -10%, rgba(139,92,246,.15), transparent 40%)
        ">
        <div style="display:flex;justify-content:space-between; align-items:center; gap:12px">
          <div>
            <h1 style="margin:0 0 6px 0;font-size:28px; letter-spacing:.2px">Panorama IA: Tablero de Movilidad</h1>
            <p style="margin:0;color:var(--muted)">Seguimiento en tiempo real a cierres, desvíos, normas y mejores prácticas.</p>
          </div>
          <div style="display:flex;gap:8px">
            <a class="btn btn--ghost" href="/blog">Ver blog</a>
            <a class="btn" href="/admin">Gestionar</a>
          </div>
        </div>
      </div>

      <!-- KPIs -->
      <div class="grid" style="grid-template-columns:repeat(3,1fr); gap:16px">
        @foreach($kpis as $k)
          <div class="card kpi hover-raise">
            <span class="dot" style="background:{{ $k['color'] }}"></span>
            <div>
              <div style="color:var(--muted); font-size:12px">{{ $k['label'] }}</div>
              <div style="font-weight:700; font-size:20px">{{ $k['val'] }}</div>
            </div>
          </div>
        @endforeach
      </div>

      <!-- grid 6 posts -->
      <div class="grid" style="grid-template-columns:repeat(3,1fr); gap:16px">
        @forelse($latest as $p)
          <article class="card hover-raise" style="overflow:hidden">
            @if($p->featured_image)
              <img src="{{ asset('storage/'.$p->featured_image) }}" alt="" style="width:100%; height:140px; object-fit:cover; display:block">
            @endif
            <div style="padding:14px">
              <div class="badge" style="margin-bottom:8px">publicado</div>
              <h3 style="margin:0 0 6px 0;font-size:16px">
                <a href="/blog/{{ $p->slug }}" style="color:var(--text); text-decoration:none">{{ $p->title }}</a>
              </h3>
              <p style="margin:0;color:var(--muted); font-size:13px; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden">
                {{ $p->excerpt }}
              </p>
              <div style="display:flex;justify-content:space-between; align-items:center; margin-top:10px; color:var(--muted); font-size:12px">
                <span>{{ optional($p->publish_at ?? $p->published_at)->diffForHumans() }}</span>
                <a href="/blog/{{ $p->slug }}" class="btn btn--ghost" style="padding:6px 10px">Abrir</a>
              </div>
            </div>
          </article>
        @empty
          <div class="card" style="padding:18px">No hay publicaciones.</div>
        @endforelse
      </div>
    </section>

    <!-- widgets -->
    <aside class="grid" style="gap:16px">
      <!-- alerta -->
      <div class="card" style="padding:16px;border-left:3px solid var(--warn)">
        <div style="display:flex;align-items:center;gap:8px">
          <span class="badge badge--warn">ALERTA</span>
          <strong>Notificación importante</strong>
        </div>
        <p style="margin:10px 0 0;color:var(--muted); font-size:13px">
          Sistema en pruebas: algunas publicaciones podrían reprogramarse automáticamente.
        </p>
      </div>

      <!-- calendario minimal -->
      <div class="card" style="padding:16px">
        <div style="display:flex;justify-content:space-between; align-items:center;margin-bottom:8px">
          <strong>SEPTIEMBRE</strong>
          <div style="display:flex;gap:6px">
            <button class="btn btn--ghost" onclick="cal.prev()">◀</button>
            <button class="btn" onclick="cal.next()">▶</button>
          </div>
        </div>
        <div id="cal" style="display:grid;grid-template-columns:repeat(7,1fr); gap:6px; font-size:12px"></div>
      </div>
    </aside>
  </main>

  <footer class="container" style="padding-top:10px; color:var(--muted); text-align:center">
    Servicios de <a href="https://www.panoramaingenieria.com" style="color:var(--cx)">www.panoramaingenieria.com</a>
  </footer>

  <script>
    // theme toggle
    const tbtn = document.getElementById('themeToggle');
    const root = document.documentElement;
    tbtn?.addEventListener('click', () => {
      const curr = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
      root.setAttribute('data-theme', curr);
      localStorage.setItem('pm_theme', curr);
    });
    (function initTheme(){
      const saved = localStorage.getItem('pm_theme');
      if(saved) root.setAttribute('data-theme', saved);
    })();

    // mini calendar (ultra light)
    const cal = (function(){
      const el = document.getElementById('cal');
      let dt = new Date();
      const names = ['D','L','M','M','J','V','S'];

      function draw(){
        if(!el) return;
        el.innerHTML = '';
        names.forEach(n => { const s=document.createElement('div'); s.style.color='var(--muted)'; s.textContent=n; el.appendChild(s);});
        const y = dt.getFullYear(), m = dt.getMonth();
        const first = new Date(y,m,1).getDay();
        const days = new Date(y,m+1,0).getDate();
        for(let i=0;i<first;i++){ el.appendChild(document.createElement('div')); }
        for(let d=1; d<=days; d++){
          const cell = document.createElement('button');
          cell.textContent = d;
          cell.className = 'hover-raise';
          cell.style.cssText = 'border:1px solid var(--bd);background:transparent;color:var(--text);padding:8px;border-radius:8px;cursor:pointer';
          const today = new Date();
          if(d===today.getDate() && m===today.getMonth() && y===today.getFullYear()){
            cell.style.borderColor = 'var(--cx)'; cell.style.color='var(--cx)';
          }
          el.appendChild(cell);
        }
      }
      draw();
      return {
        next(){ dt.setMonth(dt.getMonth()+1); draw(); },
        prev(){ dt.setMonth(dt.getMonth()-1); draw(); }
      };
    })();
  </script>
</body>
</html>