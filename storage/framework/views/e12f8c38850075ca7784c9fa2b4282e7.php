<?php
  $featured = ($featured ?? null) ?: (($posts ?? collect())->take(3));
?>

<?php if($featured->count()): ?>
<section class="news-featured mb-8">
  <?php $__currentLoopData = $featured; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('partials.news-card-horizontal', ['post' => $post], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</section>
<?php endif; ?><?php /**PATH C:\Users\USER\panorama\resources\views/partials/featured-row.blade.php ENDPATH**/ ?>