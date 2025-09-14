

<?php $__env->startSection('content'); ?>
<section class="news-scope">
  <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
    <aside class="col col-left md:col-span-3 lg:col-span-2">
      <?php if ($__env->exists('partials.left')) echo $__env->make('partials.left', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </aside>

    <main class="col center md:col-span-6 lg:col-span-8">
      <?php if ($__env->exists('partials.featured-row')) echo $__env->make('partials.featured-row', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

      <!-- Grid responsivo mejorado -->
      <div class="container mx-auto px-3 md:px-6">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          <?php $__currentLoopData = ($posts ?? collect())->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('partials.news-card', ['post' => $post], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </main>

    <aside class="col col-right md:col-span-3 lg:col-span-2">
      <?php if ($__env->exists('partials.right')) echo $__env->make('partials.right', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </aside>
  </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\panorama\resources\views/home.blade.php ENDPATH**/ ?>