@php
  // Manejo robusto de imágenes
  $img = $post->image_url ?? $post->image ?? $post->thumbnail ?? null;
  $fallback = asset('img/placeholder.svg');
  
  // Normalizar URL si es relativa
  if ($img && !str_starts_with($img, ['http://', 'https://'])) {
    $img = str_starts_with($img, '/') ? $img : '/' . $img;
  }
  
  $src = $img ?: $fallback;
  $tag = method_exists($post,'firstTag') ? $post->firstTag() : null;
@endphp

<article class="news-card bg-zinc-900/70 border border-zinc-800 rounded-2xl overflow-hidden transition hover:-translate-y-0.5 hover:shadow-lg/10">
  <a href="{{ route('posts.show',$post) }}" class="group block focus:outline-none focus-visible:ring ring-indigo-400 rounded-2xl">
    {{-- 🔒 Alto controlado: relación 16:9 y tope de altura para evitar "pantallazos" --}}
    <figure class="card-image relative aspect-[16/9] max-h-[320px]">
      <img src="{{ $src }}" 
           alt="{{ $post->title }}" 
           loading="lazy" 
           decoding="async"
           width="1280" 
           height="720"
           class="h-full w-full object-cover block"
           onerror="this.onerror=null;this.src='{{ $fallback }}';">
      
      @if($tag)
        <span class="absolute top-3 left-3 text-xs tracking-wide px-3 py-1 rounded-full bg-zinc-950/60 ring-1 ring-zinc-800">
          {{ is_string($tag) ? $tag : ($tag->name ?? '') }}
        </span>
      @endif
    </figure>
    
    <div class="card-content p-5 space-y-2">
      <h3 class="card-title text-lg font-semibold leading-tight group-hover:underline line-clamp-3">
        {{ $post->title }}
      </h3>
      @if($post->excerpt)
        <p class="card-excerpt text-sm text-zinc-400 line-clamp-2">{{ $post->excerpt }}</p>
      @endif
      <div class="card-meta text-xs text-zinc-500 space-y-1">
        <time datetime="{{ $post->created_at->toDateString() }}">
          {{ $post->created_at->diffForHumans() }}
        </time>
        @isset($post->views_count)
          <span class="block">{{ number_format($post->views_count) }} vistas</span>
        @endisset
      </div>
    </div>
  </a>
</article>