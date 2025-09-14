
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['item']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['item']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<a href="<?php echo e($url); ?>" class="group block focus:outline-none focus-visible:ring ring-[var(--accent)] rounded-2xl">
  <article class="bg-[var(--news-surface)] border border-[var(--news-border)] rounded-2xl overflow-hidden transition-all duration-300
                  hover:-translate-y-0.5 hover:shadow-lg hover:bg-[var(--news-surface-hover)]">
    <figure class="relative aspect-[16/9]">
      <img
        src="<?php echo e($raw ?: $fallback); ?>"
        alt="<?php echo e($title); ?>"
        loading="lazy"
        decoding="async"
        width="1280" height="720"  
        class="h-full w-full object-cover"
        onerror="this.onerror=null;this.src='<?php echo e($fallback); ?>';"
      />
      <figcaption class="sr-only"><?php echo e($cat); ?></figcaption>

      
      <span class="absolute top-3 left-3 text-xs tracking-wide px-3 py-1 rounded-full
                   bg-[var(--primary)] text-[var(--primary-ink)] font-medium">
        <?php echo e($cat); ?>

      </span>
    </figure>

    <div class="p-5 space-y-2">
      <h3 class="text-lg font-semibold leading-tight group-hover:underline line-clamp-3 text-[var(--news-title)]">
        <?php echo e($title); ?>

      </h3>
      <p class="text-xs text-[var(--news-meta)]">
        <?php echo e(\Carbon\Carbon::parse($published)->locale('es')->diffForHumans()); ?>

      </p>
    </div>
  </article>
</a>
</content>
</invoke><?php /**PATH C:\Users\USER\panorama\resources\views\components\news-card.blade.php ENDPATH**/ ?>