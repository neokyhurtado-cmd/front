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

<article class="news-h">
  <a href="{{ route('posts.show',$post) }}" class="block h-wrap">
    <div class="h-media">
      <img src="{{ $src }}" 
           alt="{{ $post->title }}" 
           loading="lazy" 
           decoding="async"
           onerror="this.onerror=null;this.src='{{ $fallback }}';">
    </div>
    <div class="h-body">
      @if($tag)<span class="pill">{{ is_string($tag) ? $tag : ($tag->name ?? '') }}</span>@endif
      <h3 class="h-title">{{ $post->title }}</h3>
      @if($post->excerpt)<p class="h-excerpt line-clamp-3">{{ $post->excerpt }}</p>@endif
      <div class="h-meta">
        <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->diffForHumans() }}</time>
        @isset($post->views_count)<span>{{ number_format($post->views_count) }} vistas</span>@endisset
      </div>
      @if($post->image_source_label && $post->image_source_url)
        <div class="text-[11px] text-[var(--news-meta)]">
          Fuente: <a class="link" href="{{ $post->image_source_url }}" target="_blank" rel="noopener">
            {{ $post->image_source_label }}
          </a>
        </div>
      @endif
    </div>
  </a>
</article>