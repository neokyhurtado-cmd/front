@php 
  $img = $post->image_url ?? $post->image ?? $post->thumbnail ?? null;
  $fallback = asset('img/placeholder.svg');
  
  // Normalizar URL si es relativa
  if ($img && !str_starts_with($img, ['http://', 'https://'])) {
    $img = str_starts_with($img, '/') ? $img : '/' . $img;
  }
  
  $src = $img ?: $fallback;
@endphp

<article class="group overflow-hidden rounded-2xl border border-[var(--card-border)] bg-[var(--card)] hover:shadow-md transition-all duration-300">
  <a href="{{ url('/blog/'.$post->slug) }}" class="block">
    <figure class="aspect-[4/3] max-h-[160px] overflow-hidden">
      <img src="{{ $src }}" 
           alt="{{ $post->title }}" 
           width="800"
           height="600"
           class="h-full w-full object-cover block transition-transform duration-300 group-hover:scale-[1.02]"
           onerror="this.onerror=null;this.src='{{ $fallback }}';">
    </figure>
    <div class="p-3">
      <div class="mb-2 flex items-center gap-2 text-xs text-[var(--muted)]">
        @if($post->firstTag())
          <span class="rounded-md bg-[var(--primary)] px-2 py-0.5 text-[10px] font-medium text-[var(--primary-ink)]">
            {{ $post->firstTag() }}
          </span>
        @endif
        <time datetime="{{ optional($post->publish_at ?? $post->published_at)->toDateString() }}">
          {{ optional($post->publish_at ?? $post->published_at)->diffForHumans() }}
        </time>
        @if($post->pinned)
          <span class="text-[var(--warning)]">📌</span>
        @endif
      </div>
      
      <h3 class="line-clamp-2 text-sm font-semibold leading-tight mb-2 hover:text-[var(--link)] transition-colors">
        {{ $post->title }}
      </h3>
      
      <p class="line-clamp-2 text-xs text-[var(--muted)] leading-relaxed">
        {{ $post->excerpt }}
      </p>
      
      <div class="mt-2 flex items-center justify-between text-xs">
        <span class="text-[var(--muted)]">{{ $post->source ?? 'Panorama IA' }}</span>
        <span class="text-[var(--link)] hover:underline">Leer más →</span>
      </div>
    </div>
  </a>
</article>