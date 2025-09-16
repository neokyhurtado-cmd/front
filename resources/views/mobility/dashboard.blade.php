@extends('layouts.app')

@section('content')
<div x-data="dashboard()" class="dashboard-layout">

  <!-- Botones móviles (opcional) -->
  <div class="flex gap-2 mb-3 lg:hidden">
    <button class="rounded-lg border px-3 py-1" @click="leftOpen=true">☰ Alertas</button>
    <button class="rounded-lg border px-3 py-1" @click="rightOpen=true">☰ Recursos</button>
  </div>

  <!-- Drawers móviles -->
  <div x-show="leftOpen" x-cloak class="fixed inset-0 z-40 bg-black/30" @click.self="leftOpen=false">
    <aside class="absolute left-0 top-0 h-full w-80 p-4 bg-white dark:bg-slate-900 overflow-y-auto">
      <!-- contenido alertas -->
      <h3 class="text-lg font-bold mb-4">Alertas recientes</h3>
      <template x-for="a in alerts" :key="a.id">
        <div class="text-sm py-1" x-text="a.title"></div>
      </template>
    </aside>
  </div>
  <div x-show="rightOpen" x-cloak class="fixed inset-0 z-40 bg-black/30" @click.self="rightOpen=false">
    <aside class="absolute right-0 top-0 h-full w-80 p-4 bg-white dark:bg-slate-900 overflow-y-auto">
      <!-- contenido recursos -->
      <h3 class="text-lg font-bold mb-4">Recursos adicionales</h3>
      <template x-for="r in resources" :key="r.id">
        <div class="text-sm py-1" x-text="r.title"></div>
      </template>
    </aside>
  </div>

  <!-- Sidebar izquierda (desktop) -->
  <aside class="recent-alerts ui-panel rounded-2xl border p-4">
    <h3 class="text-lg font-bold mb-4">Alertas recientes</h3>
    <template x-for="a in alerts" :key="a.id">
      <div class="text-sm py-1" x-text="a.title"></div>
    </template>
  </aside>

  <!-- Contenido principal -->
  <main class="featured-news">
    <!-- HERO condicional -->
    <template x-if="hero">
      <article class="news-card mb-6" x-cloak>
        <div class="news-card__image-wrap">
          <template x-if="hero.image">
            <img :src="`/img-proxy?url=${encodeURIComponent(hero.image)}`" class="news-card__image" :alt="hero.title">
          </template>
        </div>
        <div class="news-card__body">
          <div class="news-card__header">
            <span class="news-card__tag" x-text="hero.tag"></span>
            <span class="news-card__time" x-text="timeAgo(hero.minutesAgo)"></span>
          </div>
          <h2 class="news-card__title" x-text="hero.title"></h2>
          <div class="news-card__meta">
            <span x-text="domainFromUrl(hero.href) || hero.domain"></span>
          </div>
        </div>
      </article>
    </template>

    <!-- GRID de tarjetas -->
    <div class="card-grid">
      <template x-for="it in cards" :key="it.id">
        <article class="news-card">
          <div class="news-card__image-wrap">
            <template x-if="it.image">
              <img :src="`/img-proxy?url=${encodeURIComponent(it.image)}`" class="news-card__image" :alt="it.title">
            </template>
          </div>

          <div class="news-card__badge"
               x-show="['ALERTA','INCIDENTE'].includes(it.tag)"
               x-text="it.tag"></div>

          <div class="news-card__body">
            <div class="news-card__header">
              <span class="news-card__tag" x-text="it.tag"></span>
              <span class="news-card__time" x-text="timeAgo(it.minutesAgo)"></span>
            </div>
            <h3 class="news-card__title" x-text="it.title"></h3>
            <div class="news-card__meta">
              <span x-text="domainFromUrl(it.href) || it.domain"></span>
            </div>
          </div>
        </article>
      </template>
    </div>
  </main>

  <!-- Sidebar derecha (desktop) -->
  <aside class="additional-resources ui-panel rounded-2xl border p-4">
    <h3 class="text-lg font-bold mb-4">Recursos adicionales</h3>
    <template x-for="r in resources" :key="r.id">
      <div class="text-sm py-1" x-text="r.title"></div>
    </template>
  </aside>
</div>

<script defer>
document.addEventListener('alpine:init', () => {
  Alpine.data('dashboard', () => ({
    // estado ya existente: items, filtered, loading, error, q, perPage, page, selectedTags...
    items: [],
    filtered: [],
    alerts: [],
    resources: [],

    // sidebars móviles
    leftOpen:false,
    rightOpen:false,

    // render derivado
    hero: null,
    cards: [],

    // helpers
    domainFromUrl(u){ try { return new URL(u).host } catch { return '' } },
    timeAgo(m){ m=parseInt(m??0,10); if(m<60) return `hace ${m} min`; const h=Math.floor(m/60), mm=m%60; return mm?`hace ${h} h ${mm} min`:`hace ${h} h` },

    computeViews(){
      if (Array.isArray(this.filtered) && this.filtered.length){
        this.hero=this.filtered[0]; this.cards=this.filtered.slice(1);
      } else { this.hero=null; this.cards=[]; }
    },

    applyClientFilters(){
      const q=(this.q||'').trim().toLowerCase();
      const hasTag=this.selectedTags?.size>0;
      const byQ=it=>!q||(it.title||'').toLowerCase().includes(q);
      const byTag=it=>!hasTag||this.selectedTags.has(it.tag);
      this.filtered=(this.items||[]).filter(it=>byQ(it)&&byTag(it));

      // ordenar recientes (minutesAgo asc)
      this.filtered.sort((a,b)=>(a.minutesAgo??0)-(b.minutesAgo??0));

      this.computeViews();
      this.empty=this.filtered.length===0 && !this.loading && !this.error;
    },

    async fetchNews({append=false}={}){
      this.loading=true; this.error=null;
      try{
        const url=`/api/mobility/news?page=${this.page}&per_page=${this.perPage}&q=${encodeURIComponent(this.q||'')}`;
        const res=await fetch(url,{headers:{'Accept':'application/json'}});
        if(!res.ok) throw new Error(`network ${res.status}`);
        const data=await res.json();
        const list=Array.isArray(data)?data:(Array.isArray(data.data)?data.data:[]);
        this.items=append?[...(this.items||[]),...list]:list;

        const m=data.meta||{};
        this.hasMore=(Number.isFinite(m.total)&&Number.isFinite(m.page)&&Number.isFinite(m.per_page))
          ? (m.page*m.per_page<m.total)
          : (list.length>=(this.perPage||12));

        this.applyClientFilters();
      }catch(e){
        console.error('fetchNews error:',e);
        this.error='No se pudieron cargar las noticias.';
        if(!append && (!this.items||this.items.length===0)){
          this.items=[{id:-1,title:'Sin datos',href:'#',tag:'INFO',minutesAgo:0,image:null}];
        }
        this.applyClientFilters();
      }finally{
        this.loading=false;
        this.empty=this.filtered.length===0 && !this.error;
      }
    },

    init(){ this.fetchNews({append:false}); }
  }));
});
</script>
@endsection
