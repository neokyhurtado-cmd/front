@extends('layouts.app')

@section('content')
<div class="news-scope" x-data="dashboard()" x-init="fetchNews()">
  <div class="max-w-[1400px] mx-auto px-6">
    <main id="main-content">
      <h1 class="text-3xl font-bold mb-4 text-gray-900 text-center">Dashboard de Movilidad</h1>

      <div class="grid grid-cols-12 gap-4">
        <aside class="col-span-12 lg:col-span-2 flex">
          <div class="bg-white rounded-2xl border border-gray-200 p-6 w-full flex flex-col">
            <h3 class="text-lg font-bold mb-4 text-gray-900">Alertas recientes</h3>
            <ul class="space-y-2 text-sm text-gray-700 flex-1">
              <template x-for="(a, idx) in Array.from({length:10}).map((_,i)=> items[i] || {title:'- Sin dato -', minutesAgo:0})" :key="'left-'+idx">
                <li class="flex items-start gap-3">
                  <span class="w-2 h-2 rounded-full bg-red-400 mt-2"></span>
                  <div class="flex-1 text-xs">
                    <div class="font-medium text-gray-800 line-clamp-2" x-text="a.title"></div>
                    <div class="text-gray-500" x-text="timeAgo(a.minutesAgo)"></div>
                  </div>
                </li>
              </template>
            </ul>
            <div class="mt-4">
              <button @click="actualizar" class="w-full py-2 px-4 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <span x-show="!loading">🔄 Actualizar</span>
                <span x-show="loading">Actualizando…</span>
              </button>
            </div>
          </div>
        </aside>

        <main class="col-span-12 lg:col-span-8">
          <div class="bg-white rounded-2xl border border-gray-200 p-6">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
              <h2 class="text-lg font-semibold">Destacadas</h2>
              <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <input
                  type="search"
                  placeholder="Buscar por título..."
                  class="w-full sm:w-64 rounded-xl border border-slate-200 px-3 py-2 text-sm"
                  x-model.debounce.300ms="q"
                  @input.debounce.300ms="applyClientFilters()"
                />
                <div class="flex flex-wrap gap-2">
                  <template x-for="t in tags" :key="t">
                    <button
                      type="button"
                      class="rounded-xl px-2.5 py-1 text-xs border"
                      :class="selectedTags.has(t) ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 text-slate-700 hover:bg-slate-100'"
                      @click="toggleTag(t)"
                      x-text="t"
                    ></button>
                  </template>
                </div>
              </div>
            </div>

            <!-- Error -->
            <div x-show="error" class="mb-3 rounded-xl border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm" x-text="error"></div>

            <!-- Loading skeleton -->
            <div x-show="loading" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <template x-for="i in 4" :key="i">
                <div class="rounded-2xl border border-slate-200 p-4 animate-pulse">
                  <div class="w-full aspect-[16/9] bg-slate-100 rounded-xl mb-3"></div>
                  <div class="h-3 w-20 bg-slate-100 rounded mb-2"></div>
                  <div class="h-4 w-11/12 bg-slate-100 rounded mb-1.5"></div>
                  <div class="h-4 w-9/12 bg-slate-100 rounded"></div>
                </div>
              </template>
            </div>

            <!-- Empty -->
            <div x-show="empty && !loading && !error" class="rounded-xl border border-slate-200 px-4 py-6 text-center text-slate-500">
              No hay resultados para los filtros actuales.
            </div>

            <div id="cards" x-show="!loading" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Hero card -->
              <template x-if="filtered.length">
                <article class="col-span-1 sm:col-span-2 lg:col-span-4 bg-white rounded-xl border border-gray-200 shadow-md overflow-hidden">
                  <div class="w-full aspect-[21/9] bg-gray-100 overflow-hidden">
                    <template x-if="filtered[0].image">
                      <img :src="`/img-proxy?url=${encodeURIComponent(filtered[0].image)}`" class="w-full h-full object-cover" loading="lazy" />
                    </template>
                    <template x-if="!filtered[0].image">
                      <div class="w-full h-full grid place-items-center text-slate-400 text-4xl">📍</div>
                    </template>
                  </div>
                  <div class="p-5 space-y-3">
                    <div class="flex items-center gap-2">
                      <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-medium" :class="tagClass(filtered[0].tag)" x-text="filtered[0].tag"></span>
                      <span class="text-xs text-slate-500" x-text="timeAgo(filtered[0].minutesAgo)"></span>
                    </div>
                    <h3 class="text-base font-semibold leading-snug" x-text="filtered[0].title"></h3>
                  </div>
                  <div class="p-5 pt-0">
                    <a :href="filtered[0].href" target="_blank" rel="noreferrer noopener" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 text-white px-3 py-2 text-sm hover:bg-black">Leer fuente ↗</a>
                  </div>
                </article>
              </template>

              <!-- Other cards -->
              <template x-for="it in (filtered.slice ? filtered.slice(1) : filtered)" :key="it.id">
                <article class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                  <div class="w-full aspect-[16/9] bg-slate-100 overflow-hidden rounded-t-2xl">
                    <template x-if="it.image">
                      <img :src="`/img-proxy?url=${encodeURIComponent(it.image)}`" alt="" class="w-full h-full object-cover" loading="lazy">
                    </template>
                    <template x-if="!it.image">
                      <div class="w-full h-full grid place-items-center text-slate-400 text-3xl">📰</div>
                    </template>
                  </div>
                  <div class="p-4 space-y-3">
                    <div class="flex items-center gap-2">
                      <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-medium" :class="tagClass(it.tag)" x-text="it.tag"></span>
                      <span class="text-xs text-slate-500" x-text="timeAgo(it.minutesAgo)"></span>
                    </div>
                    <h3 class="text-sm font-medium leading-snug line-clamp-2" x-text="it.title"></h3>
                  </div>
                  <div class="p-4 pt-0">
                    <a :href="it.href" target="_blank" rel="noreferrer noopener" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 text-white px-3 py-2 text-sm hover:bg-black w-full justify-center">Leer fuente ↗</a>
                  </div>
                </article>
              </template>
            </div>

            <div class="mt-4 flex justify-center" x-show="hasMore && !loading && !error">
              <button @click="loadMore" class="rounded-xl border border-slate-200 px-4 py-2 text-sm hover:bg-slate-50">Cargar más</button>
            </div>

          </div>
        </main>

        <aside class="col-span-12 lg:col-span-2 flex">
          <div class="bg-white rounded-2xl border border-gray-200 p-6 w-full flex flex-col">
            <h3 class="text-lg font-bold mb-4 text-gray-900">Recursos adicionales</h3>
            <ul class="space-y-2 text-sm text-gray-700 flex-1">
              <template x-for="(r, idx) in Array.from({length:10}).map((_,i)=> items[items.length-1-i] || {title:'- Sin dato -', minutesAgo:0})" :key="'right-'+idx">
                <li class="flex items-start gap-3">
                  <span class="w-2 h-2 rounded-full bg-yellow-400 mt-2"></span>
                  <div class="flex-1 text-xs">
                    <div class="font-medium text-gray-800 line-clamp-2" x-text="r.title"></div>
                    <div class="text-gray-500" x-text="timeAgo(r.minutesAgo)"></div>
                  </div>
                </li>
              </template>
            </ul>
            <div class="mt-4 text-xs text-gray-500">Documentación, casos de uso y soporte.</div>
          </div>
        </aside>
      </div>

    </main>
  </div>
</div>

<script defer>
document.addEventListener('alpine:init', () => {
  Alpine.data('dashboard', () => ({
    // Estado
    benefits: [
      { title: 'Alertas en tiempo real', text: 'Notificaciones de PMT y desvíos en vivo.' },
      { title: 'Ejecución más rápida', text: 'Decisiones con datos minuto a minuto.' },
      { title: 'Seguridad y auditoría', text: 'Roles, logs y trazabilidad completa.' },
      { title: 'Integración sencilla', text: 'REST/JSON con ejemplos de código.' },
    ],
    items: [],
    filtered: [],
    loading: false,
    error: null,
    empty: false,

    // Filtros
    q: '',
    tags: ['ALERTA','INCIDENTE','OBRAS','SERVICIO','AVISO','INFO'],
    selectedTags: new Set(),

    // Paginación
    page: 1,
    perPage: 12,
    hasMore: true,

    // Utils
    timeAgo(mins){
      mins = Math.max(0, parseInt(mins ?? 0, 10));
      if (mins < 60) return `hace ${mins} min`;
      const h = Math.floor(mins/60), m = mins%60;
      return m ? `hace ${h} h ${m} min` : `hace ${h} h`;
    },
    tagClass(tag){
      const map = {
        'ALERTA': 'bg-red-100 text-red-700',
        'INCIDENTE': 'bg-amber-100 text-amber-800',
        'OBRAS': 'bg-yellow-100 text-yellow-800',
        'SERVICIO': 'bg-blue-100 text-blue-700',
        'AVISO': 'bg-teal-100 text-teal-800',
        'INFO': 'bg-slate-100 text-slate-700',
      };
      return map[tag] || 'bg-slate-100 text-slate-700';
    },

    applyClientFilters(){
      const q = this.q.trim().toLowerCase();
      const filterByQ = (it) => !q || (it.title && it.title.toLowerCase().includes(q));
      const hasTagSel = this.selectedTags.size > 0;
      const filterByTag = (it) => !hasTagSel || this.selectedTags.has(it.tag);
      this.filtered = this.items.filter(it => filterByQ(it) && filterByTag(it));
      this.empty = this.filtered.length === 0 && !this.loading && !this.error;
    },

    toggleTag(tag){
      if (this.selectedTags.has(tag)) this.selectedTags.delete(tag);
      else this.selectedTags.add(tag);
      this.applyClientFilters();
    },

    async fetchNews({ append=false } = {}){
      this.loading = true; this.error = null;

      try {
        const url = `/api/mobility/news?page=${this.page}&per_page=${this.perPage}&q=${encodeURIComponent(this.q)}`;
        const res = await fetch(url, { headers: { 'Accept':'application/json' }});
        if(!res.ok) throw new Error('network');
        const data = await res.json();

        const list = Array.isArray(data) ? data : (data.data ?? []);
        const meta = data.meta ?? {};
        if (append) this.items = [...this.items, ...list];
        else this.items = list;

        // hasMore: si hay total y meta
        if (meta.total != null && meta.page != null && meta.per_page != null) {
          const served = meta.page * meta.per_page;
          this.hasMore = served < meta.total;
        } else {
          // fallback si no hay meta: asume hasMore si recibimos un full page
          this.hasMore = list.length === this.perPage;
        }

        this.applyClientFilters();
      } catch(e) {
        this.error = 'No se pudieron cargar las noticias.';
      } finally {
        this.loading = false;
        this.empty = this.filtered.length === 0 && !this.loading && !this.error;
      }
    },

    async actualizar(){
      this.page = 1;
      await this.fetchNews({ append:false });
    },

    async loadMore(){
      if (!this.hasMore || this.loading) return;
      this.page += 1;
      await this.fetchNews({ append:true });
    }
  }));
});
</script>
@endsection
