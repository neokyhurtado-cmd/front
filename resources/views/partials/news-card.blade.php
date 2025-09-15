@php
  $title = $post->title ?? 'Sin título';
  $url   = route('posts.show', $post);
  $cat   = strtoupper($post->category ?? 'MOVILIDAD');

  $raw   = $post->image_url ?? $post->image ?? $post->thumbnail ?? null;
  $fallback = asset('img/placeholder.svg');

  if ($raw && !Str::startsWith($raw, ['http://','https://'])) {
    $raw = rtrim($post->source_base ?? '', '/').'/'.ltrim($raw,'/');
  }
@endphp

<a href="{{ $url }}" class="group block focus:outline-none focus-visible:ring ring-indigo-400 rounded-2xl">
  <article class="bg-zinc-900/70 border border-zinc-800 rounded-2xl overflow-hidden transition
                  hover:-translate-y-0.5 hover:shadow-lg/10">
    {{-- imagen con altura controlada --}}
    <figure class="relative aspect-[16/9] max-h-56">
      <img
        src="{{ $raw ?: $fallback }}"
        alt="{{ $title }}"
        loading="lazy" decoding="async"
        width="1280" height="720"
        class="h-full w-full object-cover block"
        onerror="this.onerror=null;this.src='{{ $fallback }}';"
      />
      <span class="absolute top-3 left-3 text-[10px] tracking-wide px-2.5 py-1 rounded-full
                   bg-zinc-950/60 ring-1 ring-zinc-800">
        {{ $cat }}
      </span>
    </figure>

    <div class="p-4 space-y-2">
      <h3 class="text-base font-semibold leading-snug group-hover:underline line-clamp-3">
        {{ $title }}
      </h3>
      <p class="text-[11px] text-zinc-400">
        {{ \Carbon\Carbon::parse($post->published_at ?? $post->created_at ?? now())->locale('es')->diffForHumans() }}
      </p>
    </div>
  </article>
</a>