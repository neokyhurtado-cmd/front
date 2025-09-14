<?php
  // Manejo robusto de imágenes
  $img = $post->image_url ?? $post->image ?? $post->thumbnail ?? null;
  $fallback = asset('img/placeholder.svg');
  
  // Normalizar URL si es relativa
  if ($img && !str_starts_with($img, ['http://', 'https://'])) {
    $img = str_starts_with($img, '/') ? $img : '/' . $img;
  }
  
  $src = $img ?: $fallback;
  $tag = method_exists($post,'firstTag') ? $post->firstTag() : null;
?>

<article class="news-h">
  <a href="<?php echo e(route('posts.show',$post)); ?>" class="block h-wrap">
    <div class="h-media">
      <img src="<?php echo e($src); ?>" 
           alt="<?php echo e($post->title); ?>" 
           loading="lazy" 
           decoding="async"
           onerror="this.onerror=null;this.src='<?php echo e($fallback); ?>';">
    </div>
    <div class="h-body">
      <?php if($tag): ?><span class="pill"><?php echo e(is_string($tag) ? $tag : ($tag->name ?? '')); ?></span><?php endif; ?>
      <h3 class="h-title"><?php echo e($post->title); ?></h3>
      <?php if($post->excerpt): ?><p class="h-excerpt line-clamp-3"><?php echo e($post->excerpt); ?></p><?php endif; ?>
      <div class="h-meta">
        <time datetime="<?php echo e($post->created_at->toDateString()); ?>"><?php echo e($post->created_at->diffForHumans()); ?></time>
        <?php if(isset($post->views_count)): ?><span><?php echo e(number_format($post->views_count)); ?> vistas</span><?php endif; ?>
      </div>
      <?php if($post->image_source_label && $post->image_source_url): ?>
        <div class="text-[11px] text-[var(--news-meta)]">
          Fuente: <a class="link" href="<?php echo e($post->image_source_url); ?>" target="_blank" rel="noopener">
            <?php echo e($post->image_source_label); ?>

          </a>
        </div>
      <?php endif; ?>
    </div>
  </a>
</article><?php /**PATH C:\Users\USER\panorama\resources\views/partials/news-card-horizontal.blade.php ENDPATH**/ ?>