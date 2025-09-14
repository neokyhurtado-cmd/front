

<?php $__env->startSection('title','Panorama Ingeniería'); ?>

<?php $__env->startSection('content'); ?>
  
  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    <?php $__currentLoopData = ($posts ?? collect())->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php echo $__env->make('partials.news-card', ['post' => $item], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\panorama\resources\views/home.blade.php ENDPATH**/ ?>