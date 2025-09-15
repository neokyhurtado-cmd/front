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
            <h2 class="text-2xl font-bold mb-4 text-gray-900">Destacadas</h2>

            <div id="cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Hero card -->
              <template x-if="items.length">
                <article class="col-span-1 sm:col-span-2 lg:col-span-4 bg-white rounded-xl border border-gray-200 shadow-md overflow-hidden">
                  <div class="w-full aspect-[21/9] bg-gray-100 overflow-hidden">
                    <template x-if="items[0].image">
                      <img :src="'/img-proxy?url=' + encodeURIComponent(items[0].image)" class="w-full h-full object-cover" loading="lazy" />
                    </template>
                    <template x-if="!items[0].image">
                      <div class="w-full h-full grid place-items-center text-slate-400 text-4xl">📍</div>
                    </template>
                  </div>
                  <div class="p-4">
                    <h3 class="font-bold text-gray-900 text-lg leading-tight mb-1" x-text="items[0].title"></h3>
                    <div class="text-xs text-gray-500" x-text="timeAgo(items[0].minutesAgo)"></div>
                  </div>
                </article>
              </template>

              <!-- Other cards -->
              <template x-for="it in (items.slice ? items.slice(1) : items)" :key="it.id">
                <article class="bg-white rounded-xl border border-gray-200 shadow-md h-80 flex flex-col overflow-hidden">
                  <div class="h-44 bg-gray-100 flex items-center justify-center">
                      <template x-if="it.image">
                        <img :src="'/img-proxy?url=' + encodeURIComponent(it.image)" class="w-full h-full object-cover" loading="lazy" />
                      </template>
                      <template x-if="!it.image">
                        <div class="w-full h-full flex items-center justify-center text-xs text-gray-500">Sin imagen</div>
                      </template>
                    </div>
                  <div class="p-4 flex-1 flex flex-col">
                    <span class="inline-block text-xs font-bold text-blue-600 uppercase bg-blue-50 px-2 py-1 rounded mb-2" x-text="it.tag || 'MOVILIDAD'"></span>
                    <h3 class="font-bold text-gray-900 text-sm leading-tight mb-2 line-clamp-3 flex-grow" x-text="it.title"></h3>
                    <p class="text-xs text-gray-500 mb-3" x-text="timeAgo(it.minutesAgo)"></p>
                    <a :href="it.href" target="_blank" class="block w-full text-xs text-white font-medium bg-blue-600 py-2 px-3 rounded text-center">Leer fuente</a>
                  </div>
                </article>
              </template>
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
    benefits: [
      { title: 'Alertas en tiempo real', text: 'Notificaciones de PMT y desvíos en vivo.' },
      { title: 'Ejecución más rápida', text: 'Decisiones con datos minuto a minuto.' },
      { title: 'Seguridad y auditoría', text: 'Roles, logs y trazabilidad completa.' },
      { title: 'Integración sencilla', text: 'REST/JSON con ejemplos de código.' },
    ],
    items: [],
    loading: false,
    error: null,

    timeAgo(mins){
      if (mins < 60) return `hace ${mins} min`;
      const h = Math.floor(mins/60), m = mins%60;
      return `hace ${h} h ${m} min`;
    },

    async fetchNews(){
      this.loading = true; this.error = null;
      try {
        const res = await fetch('/api/mobility/news', { headers: { 'Accept': 'application/json' }});
        if(!res.ok) throw new Error('Error de red');
        this.items = await res.json();
      } catch (e) {
        this.error = 'No se pudieron cargar las noticias.';
        this.items = [
          { id: 1, title: 'Ejemplo local: cierre en la Av. 80', href:'#', tag:'ALERTA', minutesAgo: 12 },
          { id: 2, title: 'Ejemplo local: ajuste de frecuencias TM', href:'#', tag:'SERVICIO', minutesAgo: 35 },
        ];
      } finally {
        this.loading = false;
      }
    },

    async actualizar(){ await this.fetchNews(); }
  }));
});
</script>
@endsection
