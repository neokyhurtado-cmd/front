<article class="group rounded-2xl border border-[var(--card-border)] bg-[var(--card)] shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300">
  @php
    $img = $post->featured_image ? asset('storage/'.$post->featured_image) : ($post->image_url ?? null);
    $fallback = asset('img/placeholder.svg');
    $src = $img ?: $fallback;
  @endphp
  
  <a href="{{ url('/blog/'.$post->slug) }}">
    <img src="{{ $src }}" 
         alt="{{ $post->title }}"
         class="h-44 w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
         onerror="this.onerror=null;this.src='{{ $fallback }}';">
  </a>
  
  <div class="p-4">
    <div class="mb-2 flex items-center gap-2 text-xs text-[var(--muted)]">
      @if($post->tags && count($post->tags) > 0)
        <span class="rounded-md bg-[var(--primary)] px-2 py-0.5 text-[var(--primary-ink)] font-medium">
          {{ $post->tags[0] }}
        </span>
      @endif
      <time datetime="{{ optional($post->publish_at ?? $post->published_at)->toDateString() }}">
        {{ optional($post->publish_at ?? $post->published_at)->diffForHumans() }}
      </time>
      @if($post->pinned)
        <span class="rounded-md bg-[var(--warning)] px-2 py-0.5 text-[var(--primary-ink)] font-medium">
          📌 Destacado
        </span>
      @endif
    </div>
    
    <h3 class="text-lg font-semibold leading-tight mb-2">
      <a href="{{ url('/blog/'.$post->slug) }}" class="hover:underline transition-colors">{{ $post->title }}</a>
    </h3>
    
    <p class="line-clamp-3 text-[var(--muted)] text-sm leading-relaxed">{{ $post->excerpt }}</p>
    
    <div class="mt-3 flex items-center justify-between">
      <span class="text-xs text-[var(--muted)]">
        {{ $post->source ?? 'Panorama IA' }}
      </span>
      <a href="{{ url('/blog/'.$post->slug) }}" class="link text-sm font-medium">
        Leer más →
      </a>
    </div>
  </div>
</article>