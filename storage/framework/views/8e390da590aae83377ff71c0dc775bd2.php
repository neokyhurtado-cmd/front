<!doctype html>
<html lang="es" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panorama</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css','resources/js/app.js']); ?>
</head>
<body class="min-h-full bg-neutral-950 text-zinc-100">
  <header class="p-4 border-b border-zinc-800">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
      <div class="font-semibold tracking-wide">PANORAMA INGENIERÍA</div>
      <nav class="flex items-center space-x-6">
        <a href="<?php echo e(route('home')); ?>" class="text-zinc-300 hover:text-cyan-300 transition-colors">Home</a>
        <?php if(auth()->guard()->check()): ?>
          <a href="<?php echo e(route('dashboard')); ?>" class="text-zinc-300 hover:text-cyan-300 transition-colors">Dashboard</a>
          <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="text-zinc-300 hover:text-red-400 transition-colors">Logout</button>
          </form>
        <?php else: ?>
          <a href="<?php echo e(route('login')); ?>" class="text-zinc-300 hover:text-cyan-300 transition-colors">Login</a>
          <a href="<?php echo e(route('register')); ?>" class="px-4 py-2 rounded-lg bg-cyan-600 text-white hover:bg-cyan-700 transition-colors">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="max-w-6xl mx-auto p-6">
    <?php echo $__env->yieldContent('content'); ?>
  </main>

</body>
</html><?php /**PATH C:\Users\USER\panorama\resources\views/layouts/app.blade.php ENDPATH**/ ?>