@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto p-4">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Últimas noticias</h1>
    <span id="status" class="text-sm text-gray-500">Cargando…</span>
  </div>
  <div id="grid" class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3"></div>
</div>

<script type="module">
(async () => {
  const API = "{{ url('/api/mobility/news?per_page=12') }}";
  const grid = document.getElementById('grid');
  const status = document.getElementById('status');

  const card = (it) => {
    const img = it.image?.startsWith('/') ? it.image : (it.image || '/img/placeholder.svg');
    const href = it.href?.startsWith('http') ? it.href : "{{ url('/') }}" + (it.href || '#');

    return `
    <article class="card bg-base-100 shadow">
      <figure><img src="${img}" alt="" class="w-full h-44 object-cover"></figure>
      <div class="card-body">
        <h2 class="card-title text-base">${it.title || 'Sin título'}</h2>
        <p class="text-sm text-gray-500">${it.excerpt || ''}</p>
        <div class="card-actions justify-end">
          <a class="btn btn-primary btn-sm" href="${href}" target="_blank" rel="noopener">Leer</a>
        </div>
      </div>
    </article>`;
  };

  try {
    const res = await fetch(API, { headers: {'Accept':'application/json'} });
    const json = await res.json();
    const items = json?.data || [];
    status.textContent = items.length ? `${items.length} ítems` : 'Sin resultados';
    grid.innerHTML = items.map(card).join('');
  } catch (e) {
    status.textContent = 'Error cargando';
    grid.innerHTML = `<div class="alert alert-error">No se pudieron cargar las noticias: ${e.message}</div>`;
  }
})();
</script>
@endsection
