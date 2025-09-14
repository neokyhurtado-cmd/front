<!doctype html>
<html lang="es" class="h-full">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="color-scheme" content="dark light" />
  <title><?php echo $__env->yieldContent('title', 'Panorama Ingeniería'); ?></title>

  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css','resources/js/app.js']); ?>
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100">
  <header class="sticky top-0 z-40 backdrop-blur border-b border-zinc-800 bg-zinc-950/80">
    <div class="mx-auto max-w-6xl px-4 h-14 flex items-center justify-between">
      <a href="/" class="font-semibold tracking-wide">
        PANORAMA INGENIERÍA IA
      </a>
      <div class="text-xs text-zinc-400">Claro / Oscuro</div>
    </div>
  </header>

  <main class="mx-auto max-w-6xl px-4 py-6">
    <?php echo $__env->yieldContent('content'); ?>
  </main>

  <footer class="border-t border-zinc-800 text-xs text-zinc-400">
    <div class="mx-auto max-w-6xl px-4 py-6">
      © <?php echo e(date('Y')); ?> Panorama Ingeniería
    </div>
  </footer>

  <!-- Skeleton loading controlado -->
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('figure.aspect-\\[16\\/9\\] img[loading="lazy"]').forEach(img=>{
      const f = img.closest('figure');
      f.classList.add('skeleton');
      const off=()=>f.classList.remove('skeleton');
      img.addEventListener('load', off, {once:true});
      img.addEventListener('error', off, {once:true});
    });
  });
  </script>
</body>
</html><?php /**PATH C:\Users\USER\panorama\resources\views/layouts/app.blade.php ENDPATH**/ ?>