

<?php $__env->startSection('content'); ?>
<section class="news-scope">
  <div class="news-grid md:gap-8" style="grid-auto-rows:1fr;">
    <?php $__currentLoopData = ($posts ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="flex"><?php echo $__env->make('partials.news-card', ['post' => $post], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\panorama\resources\views\blog\index.blade.php ENDPATH**/ ?>