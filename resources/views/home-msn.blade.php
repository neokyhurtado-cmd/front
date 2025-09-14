<!doctype html>
<html lang="es" data-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Panorama Ingeniería IA — Inicio</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>

  <!-- Header tipo MSN -->
  <div class="home-header">
    <div style="display:flex; align-items:center; gap:12px">
      <a href="/portal" title="Portal" style="display:block;width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#00E5FF,#0091EA)"></a>
      <strong>PANORAMA IA</strong>
    </div>

    <form class="search" action="/" method="get">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="m21 21-4.3-4.3m1.8-5.2a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      <input name="q" value="{{ $q }}" placeholder="Buscar en Panorama IA...">
    </form>

    <div style="display:flex; gap:8px">
      <a class="btn btn--ghost" href="/admin">Admin</a>
      <button id="themeToggle" class="btn btn--ghost">Tema</button>
    </div>
  </div>

  <main class="container">
    <!-- Tabs / chips -->
    <div class="tabbar" id="tabs">
      @foreach((array)$tags as $i => $t)
        <button class="tab {{ request('tag')===$t ? 'active' : '' }}" data-tag="{{ $t }}">{{ $t }}</button>
      @endforeach
    </div>

    <!-- Masonry -->
    <section class="masonry" id="masonry">
      {{-- Widget: historias / alerta --}}
      <article class="tile widget widget--alert">
        <div class="badge badge--warn">Alerta</div>
        <h4 style="margin:6px 0 4px">Cierre programado Av. 68 – tramo Norte</h4>
        <p class="line-3" style="color:var(--muted)">Recuerde programar desvíos y señalización temporal. Vigencia: 18–22 de septiembre.</p>
      </article>

      {{-- Widget: clima simple (Bogotá) --}}
      <article class="tile widget widget--weather">
        <div style="display:flex; justify-content:space-between; align-items:center">
          <strong>Bogotá</strong>
          <span class="badge">Despejado</span>
        </div>
        <div style="display:flex; align-items:end; gap:8px; margin-top:10px">
          <div style="font-size:28px; font-weight:800">8°</div>
          <div style="color:var(--muted)">Sensación 7° · Humedad 56%</div>
        </div>
      </article>

      {{-- Hero 2x1 (destacado principal) --}}
      @if($hero)
        <article class="tile hero-tile {{ $hero->is_pinned ? 'tile--pinned' : 'tile--auto-hero' }}" 
                 data-tags="{{ implode(',', (array)$hero->tags) }}">
          @if($hero->featured_image)
            <img class="tile__media hero-media" src="{{ asset('storage/'.$hero->featured_image) }}" alt="">
          @endif
          <div class="tile__body">
            @if($hero->is_pinned)
              <div class="badge badge--pin">
                ⭐ Destacado P{{ $hero->pin_priority }}
              </div>
            @else
              <div class="badge badge--auto">🚀 Más Reciente</div>
            @endif
            <h2 class="hero-title">
              <a href="{{ url('/blog/'.$hero->slug) }}" style="color:var(--text); text-decoration:none">
                {{ $hero->title }}
              </a>
            </h2>
            <p class="hero-excerpt">{{ $hero->excerpt }}</p>
            <div class="tile__meta">
              <span>{{ optional($hero->publish_at ?? $hero->published_at)->diffForHumans() }}</span>
              @if($hero->is_pinned && $hero->pinned_at)
                <span style="color:var(--warning); font-size:0.9em">
                  📌 {{ $hero->pinned_at->diffForHumans() }}
                </span>
              @endif
              <div class="tile__actions">
                <a class="iconbtn btn-primary" href="{{ url('/blog/'.$hero->slug) }}">Leer Artículo</a>
              </div>
            </div>
          </div>
        </article>
      @endif

      {{-- Posts fijados adicionales --}}
      @foreach($pinned->skip(1) as $p)
        <article class="tile tile--pinned" data-tags="{{ implode(',', (array)$p->tags) }}">
          @if($p->featured_image)
            <img class="tile__media" src="{{ asset('storage/'.$p->featured_image) }}" alt="">
          @endif
          <div class="tile__body">
            <div class="badge badge--pin">⭐ Destacado P{{ $p->pin_priority }}</div>
            <h3 class="tile__title">
              <a href="{{ url('/blog/'.$p->slug) }}" style="color:var(--text); text-decoration:none">
                {{ $p->title }}
              </a>
            </h3>
            <p class="line-3" style="color:var(--muted)">{{ $p->excerpt }}</p>
            <div class="tile__meta">
              <span>{{ optional($p->publish_at ?? $p->published_at)->diffForHumans() }}</span>
              <div class="tile__actions">
                <a class="iconbtn" href="{{ url('/blog/'.$p->slug) }}">Abrir</a>
              </div>
            </div>
          </div>
        </article>
      @endforeach

      {{-- Grid de posts normales --}}
      @foreach($grid as $p)
        <article class="tile" data-tags="{{ implode(',', (array)$p->tags) }}">
          @if($p->featured_image)
            <img class="tile__media" src="{{ asset('storage/'.$p->featured_image) }}" alt="">
          @endif
          <div class="tile__body">
            <div class="badge">Publicado</div>
            <h3 class="tile__title">
              <a href="{{ url('/blog/'.$p->slug) }}" style="color:var(--text); text-decoration:none">
                {{ $p->title }}
              </a>
            </h3>
            <p class="line-3" style="color:var(--muted)">{{ $p->excerpt }}</p>
            <div class="tile__meta">
              <span>{{ optional($p->publish_at ?? $p->published_at)->diffForHumans() }}</span>
              <div class="tile__actions">
                <a class="iconbtn" href="{{ url('/blog/'.$p->slug) }}">Abrir</a>
              </div>
            </div>
          </div>
        </article>
      @endforeach

      {{-- Widget: bloque grande tipo video/galería (demo estático) --}}
      <article class="tile">
        <img class="tile__media" src="https://images.unsplash.com/photo-1542315192-1f61a18816ec?q=80&w=1200&auto=format&fit=crop" alt="">
        <div class="tile__body">
          <div class="badge">Galería</div>
          <h3 class="tile__title">Señalización temporal: ejemplos de campo</h3>
          <p class="line-3" style="color:var(--muted)">Casos reales de PMT con implementación correcta de conos, paneles direccionales y canalización.</p>
          <div class="tile__meta">
            <span>Actualizado hoy</span>
            <a class="iconbtn" href="/blog">Ver más</a>
          </div>
        </div>
      </article>
    </section>
  </main>

  <script>
    // tema
    const root = document.documentElement, tbtn = document.getElementById('themeToggle');
    tbtn?.addEventListener('click', () => {
      const next = root.getAttribute('data-theme')==='light' ? 'dark':'light';
      root.setAttribute('data-theme', next); localStorage.setItem('pm_theme', next);
    });
    (function(){ const s = localStorage.getItem('pm_theme'); if(s) root.setAttribute('data-theme', s); })();

    // filtro simple por chip (client-side)
    const tabs = document.getElementById('tabs'), cards = [...document.querySelectorAll('#masonry .tile[data-tags]')];
    tabs?.addEventListener('click', e=>{
      const btn = e.target.closest('.tab'); if(!btn) return;
      tabs.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
      btn.classList.add('active');
      const tag = btn.dataset.tag;
      cards.forEach(c=>{
        const OK = (c.dataset.tags||'').toLowerCase().includes(tag.toLowerCase());
        c.style.display = OK ? '' : 'none';
        // Mantener el hero visible siempre que sea posible
        if(c.classList.contains('hero-tile') && OK) {
          c.style.display = '';
        }
      });
      window.scrollTo({top:0,behavior:'smooth'});
    });
  </script>
</body>
</html>