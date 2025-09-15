

<?php $__env->startSection('content'); ?>
<div class="grid gap-6 lg:grid-cols-[320px_1fr]">
    
    <aside class="space-y-6">
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5">
            <h3 class="text-lg font-semibold mb-4">Parameters</h3>

            <label class="block text-sm text-zinc-300 mb-1">Startdate</label>
            <input value="<?php echo e($params['startdate']); ?>"
                   class="w-full h-11 rounded-xl bg-zinc-900 border border-zinc-800 px-3 mb-3 outline-none" type="date"/>

            <label class="block text-sm text-zinc-300 mb-1">City</label>
            <input value="<?php echo e($params['city']); ?>"
                   class="w-full h-11 rounded-xl bg-zinc-900 border border-zinc-800 px-3 mb-3 outline-none" type="text"/>

            <button class="w-full h-11 rounded-xl bg-cyan-500/20 text-cyan-300 border border-zinc-800 hover:bg-cyan-500/30 transition">
                Submit
            </button>
        </div>

        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5">
            <h3 class="text-lg font-semibold mb-4">Requests</h3>
            <label class="block text-sm text-zinc-300 mb-1">API Requests</label>
            <input value="<?php echo e($params['requests']); ?>"
                   class="w-full h-11 rounded-xl bg-zinc-900 border border-zinc-800 px-3 mb-3 outline-none" type="number"/>
            <button class="w-full h-11 rounded-xl bg-cyan-500/20 text-cyan-300 border border-zinc-800 hover:bg-cyan-500/30 transition">
                Submit
            </button>
        </div>
    </aside>

    
    <section class="space-y-6">
        
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5">
                <p class="text-zinc-300 mb-2">Travel Time Benefit</p>
                <p class="text-4xl font-bold text-cyan-300"><?php echo e($kpis['benefit']); ?></p>
            </div>
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5">
                <p class="text-zinc-300 mb-2">Effectiveness</p>
                <p class="text-4xl font-bold text-lime-300"><?php echo e($kpis['effectiveness']); ?></p>
            </div>
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5">
                <p class="text-zinc-300 mb-2">Cost Saving</p>
                <p class="text-4xl font-bold text-cyan-300"><?php echo e($kpis['saving']); ?></p>
            </div>
        </div>

        
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5 h-64 flex items-center justify-center text-zinc-400">
                Travel Time Reduction (chart)
            </div>
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5 h-64 flex items-center justify-center text-zinc-400">
                Traffic Analysis (map/chart)
            </div>
        </div>

        
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Blog automatizado: análisis y opinión</h3>
                <a href="/blog" class="text-cyan-300 hover:underline">Ver todo</a>
            </div>

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e($post['url'] ?? '#'); ?>" target="_blank"
                       class="rounded-2xl border border-zinc-800 bg-zinc-900/40 p-4 hover:-translate-y-0.5 transition">
                        <span class="inline-flex items-center text-[11px] px-2 h-6 rounded-full bg-zinc-800 border border-zinc-700 text-zinc-300 mb-3">
                            <?php echo e($post['category'] ?? 'NEWS'); ?>

                        </span>
                        <h4 class="text-lg leading-snug font-medium text-zinc-100">
                            <?php echo e($post['title']); ?>

                        </h4>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-zinc-400">No hay posts por ahora.</div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5 text-center">
            <p class="text-zinc-300">¿Quieres ver cómo reducimos tiempos de viaje en tu ciudad?</p>
            <a href="/contacto" class="inline-flex mt-3 h-11 px-5 rounded-xl bg-cyan-500/20 text-cyan-300 border border-zinc-800 hover:bg-cyan-500/30 transition items-center">
                Contáctanos
            </a>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\panorama\resources\views/home.blade.php ENDPATH**/ ?>