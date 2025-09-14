

<?php $__env->startSection('title','Panorama Ingeniería'); ?>

<?php $__env->startSection('content'); ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
  
  
  <aside class="lg:col-span-3 space-y-4">
    
    <div class="bg-[#11151A] rounded-2xl p-6 border border-[#1F2430] shadow-[0_8px_24px_rgba(0,0,0,.25)]">
      <h2 class="text-lg font-semibold mb-4 text-[#E6E8EC]">Services</h2>
      <div class="space-y-4">
        <div>
          <h3 class="font-medium mb-2 text-[#E6E8EC]">¿Qué hacemos?</h3>
          <p class="text-sm mb-4 text-[#A4AABB] leading-relaxed">
            Análisis de movilidad urbana, optimización de rutas y señalización inteligente para Bogotá.
          </p>
        </div>
        
        
        <div class="flex space-x-3">
          <button class="w-12 h-12 rounded-full bg-[#2A130A] border border-[#3D1F0F] text-[#F59E0B] flex items-center justify-center font-semibold text-sm hover:scale-105 transition-transform" 
                  title="API Movilidad">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </button>
          <button class="w-12 h-12 rounded-full bg-[#2A130A] border border-[#3D1F0F] text-[#F59E0B] flex items-center justify-center font-semibold text-sm hover:scale-105 transition-transform" 
                  title="Consultoría">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </aside>

  
  <section class="lg:col-span-6">
    <div class="bg-[#111318] rounded-3xl p-6 border border-[#1F2430] shadow-[0_8px_24px_rgba(0,0,0,.25)]">
      
      <span class="inline-block px-3 py-1 rounded-full text-xs font-medium uppercase tracking-wider mb-6 border border-[#1F2430] text-[#2FE1FF] bg-[rgba(24,27,33,.6)]">
        blog automatizado: análisis y opinión
      </span>
      
      
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php $__currentLoopData = ($posts ?? collect())->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <article class="bg-[#11151A] rounded-xl border border-[#1F2430] overflow-hidden hover:-translate-y-0.5 hover:border-[#293041] transition-all duration-300 shadow-[0_4px_12px_rgba(0,0,0,.2)]">
            <?php
              $img = $item->image_url ?? $item->image ?? $item->thumbnail ?? null;
              $fallback = asset('img/placeholder.svg');
              $src = $img ?: $fallback;
            ?>
            
            
            <div class="aspect-[16/9] bg-gray-800 overflow-hidden">
              <img src="<?php echo e($src); ?>" 
                   alt="<?php echo e($item->title ?? 'Sin título'); ?>" 
                   class="w-full h-full object-cover" 
                   loading="lazy">
            </div>
            
            <div class="p-4">
              
              <span class="inline-block text-xs font-medium text-[#2FE1FF] mb-2">
                <?php echo e(strtoupper($item->category ?? $item->firstTag() ?? 'MOVILIDAD')); ?>

              </span>
              
              
              <h3 class="text-sm font-semibold text-[#E6E8EC] line-clamp-3 mb-2 leading-tight">
                <?php echo e($item->title ?? 'Sin título'); ?>

              </h3>
              
              
              <time class="text-xs text-[#A4AABB] block">
                <?php echo e(optional($item->published_at ?? $item->publish_at)->diffForHumans() ?? '1 day ago'); ?>

              </time>
            </div>
          </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </section>

  
  <aside class="lg:col-span-3">
    <div class="bg-[#11151A] rounded-2xl p-6 border border-[#1F2430] shadow-[0_8px_24px_rgba(0,0,0,.25)]">
      <h2 class="text-lg font-semibold mb-6 text-[#E6E8EC]">Benefits</h2>
      
      
      <div class="space-y-4">
        
        <div class="bg-[#111318] rounded-xl p-4 border border-[#1F2430] hover:-translate-y-1 hover:border-[#293041] hover:shadow-[0_12px_32px_rgba(0,0,0,.35)] transition-all duration-300">
          <p class="text-xs text-[#A4AABB] font-medium uppercase tracking-wide mb-1">Travel Time</p>
          <p class="text-4xl font-bold text-[#2FE1FF] leading-none">12-19%</p>
        </div>
        
        
        <div class="bg-[#111318] rounded-xl p-4 border border-[#1F2430] hover:-translate-y-1 hover:border-[#293041] hover:shadow-[0_12px_32px_rgba(0,0,0,.35)] transition-all duration-300">
          <p class="text-xs text-[#A4AABB] font-medium uppercase tracking-wide mb-1">Effectiveness</p>
          <p class="text-4xl font-bold text-[#2FE1FF] leading-none">93%</p>
        </div>
        
        
        <div class="bg-[#111318] rounded-xl p-4 border border-[#1F2430] hover:-translate-y-1 hover:border-[#293041] hover:shadow-[0_12px_32px_rgba(0,0,0,.35)] transition-all duration-300">
          <p class="text-xs text-[#A4AABB] font-medium uppercase tracking-wide mb-1">Cost Saving</p>
          <p class="text-4xl font-bold text-[#2FE1FF] leading-none">23%</p>
        </div>
      </div>
      
      
      <div class="mt-6 p-4 rounded-lg bg-[rgba(179,255,102,.1)] border border-[rgba(179,255,102,.2)]">
        <div class="flex items-start space-x-3">
          <div class="w-2 h-2 rounded-full bg-[#B3FF66] mt-2 flex-shrink-0"></div>
          <div>
            <p class="text-sm font-medium text-[#B3FF66] leading-tight">
              Stay informed with up-to-date news and analysis
            </p>
            <p class="text-xs text-[#A4AABB] mt-1">
              Wide range of topics covered in one place
            </p>
          </div>
        </div>
      </div>
    </div>
  </aside>
  
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\panorama\resources\views/home.blade.php ENDPATH**/ ?>