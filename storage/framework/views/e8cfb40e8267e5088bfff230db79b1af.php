

<?php $__env->startSection('title','Panorama Ingeniería'); ?>

<?php $__env->startSection('content'); ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-white">
  
  
  <aside class="bg-gray-800 rounded-lg p-6">
    <h2 class="text-lg font-semibold mb-4">Services</h2>
    <p class="text-sm text-gray-400 mb-4">
      Análisis de movilidad urbana, optimización de rutas y señalización inteligente para Bogotá.
    </p>
    <div class="flex space-x-3">
      <button class="w-12 h-12 rounded-full bg-amber-800 text-amber-200">📊</button>
      <button class="w-12 h-12 rounded-full bg-amber-800 text-amber-200">🚀</button>
    </div>
  </aside>

  
  <section class="bg-gray-800 rounded-lg p-6">
    <span class="text-xs text-cyan-400 uppercase">blog automatizado</span>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <?php $__currentLoopData = ($posts ?? collect())->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <article class="bg-gray-700 rounded p-4">
          <div class="bg-gray-600 h-20 rounded mb-2"></div>
          <span class="text-xs text-cyan-400">MOVILIDAD</span>
          <h3 class="text-sm font-semibold mt-1"><?php echo e($item->title ?? 'Título de prueba'); ?></h3>
          <time class="text-xs text-gray-400">1 día atrás</time>
        </article>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php if(($posts ?? collect())->isEmpty()): ?>
        <?php for($i = 1; $i <= 6; $i++): ?>
        <article class="bg-gray-700 rounded p-4">
          <div class="bg-gray-600 h-20 rounded mb-2"></div>
          <span class="text-xs text-cyan-400">MOVILIDAD</span>
          <h3 class="text-sm font-semibold mt-1">Noticia de ejemplo <?php echo e($i); ?></h3>
          <time class="text-xs text-gray-400">1 día atrás</time>
        </article>
        <?php endfor; ?>
      <?php endif; ?>
    </div>
  </section>

  
  <aside class="bg-gray-800 rounded-lg p-6">
    <h2 class="text-lg font-semibold mb-6">Benefits</h2>
    <div class="space-y-4">
      <div class="bg-gray-700 rounded p-4">
        <p class="text-xs text-gray-400 uppercase">Travel Time</p>
        <p class="text-3xl font-bold text-cyan-400">12-19%</p>
      </div>
      <div class="bg-gray-700 rounded p-4">
        <p class="text-xs text-gray-400 uppercase">Effectiveness</p>
        <p class="text-3xl font-bold text-cyan-400">93%</p>
      </div>
      <div class="bg-gray-700 rounded p-4">
        <p class="text-xs text-gray-400 uppercase">Cost Saving</p>
        <p class="text-3xl font-bold text-cyan-400">23%</p>
      </div>
    </div>
    
    <div class="mt-6 p-4 bg-green-900/20 border border-green-700 rounded">
      <p class="text-sm text-green-400">Stay informed with up-to-date news</p>
      <p class="text-xs text-gray-400">Wide range of topics covered</p>
    </div>
  </aside>
  
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.debug', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\panorama\resources\views/home-debug.blade.php ENDPATH**/ ?>