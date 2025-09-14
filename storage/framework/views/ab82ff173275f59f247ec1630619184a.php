

<?php $__env->startSection('content'); ?>
<article class="news-scope">
  <?php if ($__env->exists('partials.post-cards-mini', ['post' => $post])) echo $__env->make('partials.post-cards-mini', ['post' => $post], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</article>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\panorama\resources\views\blog\show.blade.php ENDPATH**/ ?>