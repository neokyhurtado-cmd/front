
<?php $__env->startSection('title','Dashboard'); ?>

<?php $__env->startSection('content'); ?>
  
  <div class="grid gap-4 md:grid-cols-3">
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'Startdate']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Startdate']); ?>
      <input type="date" value="<?php echo e(now()->format('Y-m-d')); ?>" class="mt-2 w-full rounded-lg border-stroke bg-panelAlt text-sm" />
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'City']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'City']); ?>
      <input type="text" value="Bogotá" class="mt-2 w-full rounded-lg border-stroke bg-panelAlt text-sm"/>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'API Requests']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'API Requests']); ?>
      <input type="number" value="23" class="mt-2 w-full rounded-lg border-stroke bg-panelAlt text-sm"/>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
  </div>

  
  <div class="mt-6 grid gap-4 md:grid-cols-3">
    <?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['title' => 'Effectiveness','value' => 93]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Effectiveness','value' => 93]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['title' => 'Cost Saving','value' => 23]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Cost Saving','value' => 23]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['title' => 'Travel Time Reduction','value' => 18,'suffix' => '%','range' => '12–19%']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Travel Time Reduction','value' => 18,'suffix' => '%','range' => '12–19%']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
  </div>

  
  <div class="mt-6 grid gap-4 md:grid-cols-3">
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'md:col-span-2','title' => 'Blog automatizado: análisis y opinión']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'md:col-span-2','title' => 'Blog automatizado: análisis y opinión']); ?>
      <div class="grid gap-4 sm:grid-cols-2">
        <?php $__empty_1 = true; $__currentLoopData = ($posts ?? collect())->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <article class="bg-panelAlt rounded-xl border border-stroke overflow-hidden hover:-translate-y-0.5 transition">
            <div class="aspect-[16/9] bg-gray-800 overflow-hidden">
              <img src="<?php echo e($post->image_url ?? asset('placeholder.svg')); ?>" alt="<?php echo e($post->title); ?>" class="w-full h-full object-cover" loading="lazy">
            </div>
            <div class="p-3">
              <span class="inline-block text-xs font-medium text-accent mb-1"><?php echo e(strtoupper($post->category->name ?? 'MOVILIDAD')); ?></span>
              <h3 class="text-sm font-semibold text-white line-clamp-2 mb-1"><?php echo e($post->title); ?></h3>
              <time class="text-xs text-neutral-400"><?php echo e(optional($post->published_at)->diffForHumans() ?? '1 day ago'); ?></time>
            </div>
          </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="col-span-2 p-4 text-center text-neutral-400">
            <p>No hay noticias disponibles aún.</p>
            <p class="text-xs mt-1">El sistema RSS + IA se activará pronto.</p>
          </div>
        <?php endif; ?>
      </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
    
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'Traffic Analysis']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Traffic Analysis']); ?>
      <div class="h-64 w-full rounded-lg border border-stroke bg-panelAlt grid place-items-center">
        <div class="text-center">
          <div class="text-4xl mb-2">🗺️</div>
          <span class="text-neutral-400 text-sm">Map placeholder</span>
          <p class="text-xs text-neutral-500 mt-1">Integración con Google Maps</p>
        </div>
      </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
  </div>

  
  <div class="mt-6 grid gap-4 md:grid-cols-2">
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'Travel Time Reduction']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Travel Time Reduction']); ?>
      <div class="h-48 w-full rounded-lg border border-stroke bg-panelAlt grid place-items-center">
        <div class="text-center">
          <div class="text-3xl mb-2">📊</div>
          <span class="text-neutral-400 text-sm">Chart placeholder</span>
        </div>
      </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
    
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'Assistant Analytics']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Assistant Analytics']); ?>
      <div class="h-48 w-full rounded-lg border border-stroke bg-panelAlt grid place-items-center">
        <div class="text-center">
          <div class="text-3xl mb-2">🤖</div>
          <span class="text-neutral-400 text-sm">AI Assistant integration</span>
          <p class="text-xs text-neutral-500 mt-1">Próximamente disponible</p>
        </div>
      </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
  </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\panorama\resources\views\home.blade.php ENDPATH**/ ?>