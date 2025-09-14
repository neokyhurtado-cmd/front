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

<article class="news-card">
  <a href="{{ route('posts.show',$post) }}" class="block">
    <div class="card-image">
      <img src="{{ $src }}" 
           alt="{{ $post->title }}" 
           loading="lazy" 
           decoding="async"
           onerror="this.onerror=null;this.src='{{ $fallback }}';">
    </div>
    <div class="card-content">
      @if($tag)<span class="pill">{{ is_string($tag) ? $tag : ($tag->name ?? '') }}</span>@endif
      <h3 class="card-title">{{ $post->title }}</h3>
      @if($post->excerpt)<p class="card-excerpt">{{ $post->excerpt }}</p>@endif
      <div class="card-meta">
        <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->diffForHumans() }}</time>
        @isset($post->views_count)<span>{{ number_format($post->views_count) }} vistas</span>@endisset
      </div>
    </div>
  </a>
</article>