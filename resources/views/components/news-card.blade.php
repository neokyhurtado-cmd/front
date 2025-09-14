{{-- resources/views/components/news-card.blade.php --}}
@props(['item'])

@php
  $title = $item->title ?? 'Sin título';
  $url   = route('posts.show', $item) ?? '#';
  $cat   = strtoupper($item->category ?? $item->firstTag() ?? 'MOVILIDAD');
  $raw   = $item->image_url ?? $item->image ?? $item->thumbnail ?? null;
  $fallback = asset('img/placeholder.svg');
  $published = $item->published_at ?? $item->created_at ?? now();

  // normaliza URL relativa
  if ($raw && !Str::startsWith($raw, ['http://','https://'])) {
      $raw = rtrim($item->source_base ?? '', '/').'/'.ltrim($raw,'/');
  }
@endphp

<a href="{{ $url }}" class="group block focus:outline-none focus-visible:ring ring-[var(--accent)] rounded-2xl">
  <article class="bg-[var(--news-surface)] border border-[var(--news-border)] rounded-2xl overflow-hidden transition-all duration-300
                  hover:-translate-y-0.5 hover:shadow-lg hover:bg-[var(--news-surface-hover)]">
    <figure class="relative aspect-[16/9]">
      <img
        src="{{ $raw ?: $fallback }}"
        alt="{{ $title }}"
        loading="lazy"
        decoding="async"
        width="1280" height="720"  {{-- fija dimensiones → evita CLS --}}
        class="h-full w-full object-cover"
        onerror="this.onerror=null;this.src='{{ $fallback }}';"
      />
      <figcaption class="sr-only">{{ $cat }}</figcaption>

      {{-- badge --}}
      <span class="absolute top-3 left-3 text-xs tracking-wide px-3 py-1 rounded-full
                   bg-[var(--primary)] text-[var(--primary-ink)] font-medium">
        {{ $cat }}
      </span>
    </figure>

    <div class="p-5 space-y-2">
      <h3 class="text-lg font-semibold leading-tight group-hover:underline line-clamp-3 text-[var(--news-title)]">
        {{ $title }}
      </h3>
      <p class="text-xs text-[var(--news-meta)]">
        {{ \Carbon\Carbon::parse($published)->locale('es')->diffForHumans() }}
      </p>
    </div>
  </article>
</a>
</content>
</invoke>