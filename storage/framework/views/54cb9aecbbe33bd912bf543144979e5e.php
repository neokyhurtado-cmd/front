<?php $__env->startSection('content'); ?>
<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-cyan-300 mb-2">Access Panel</h1>
            <p class="text-zinc-400">Enter your credentials to continue</p>
        </div>

        
        <?php if(session('status')): ?>
            <div class="mb-4 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

        
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6 backdrop-blur-sm">
            <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-6">
                <?php echo csrf_field(); ?>

                
                <div>
                    <label for="email" class="block text-sm font-medium text-zinc-300 mb-2">
                        Email Address
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="<?php echo e(old('email')); ?>"
                           required 
                           autofocus 
                           autocomplete="username"
                           class="w-full h-12 rounded-xl bg-zinc-900 border border-zinc-800 px-4 text-zinc-100 placeholder-zinc-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 focus:outline-none transition-colors" 
                           placeholder="your@email.com">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="password" class="block text-sm font-medium text-zinc-300 mb-2">
                        Password
                    </label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           class="w-full h-12 rounded-xl bg-zinc-900 border border-zinc-800 px-4 text-zinc-100 placeholder-zinc-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 focus:outline-none transition-colors" 
                           placeholder="••••••••">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="flex items-center">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember"
                           class="h-4 w-4 rounded border-zinc-800 bg-zinc-900 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-0">
                    <label for="remember_me" class="ml-3 text-sm text-zinc-300">
                        Remember me
                    </label>
                </div>

                
                <div class="space-y-4">
                    <button type="submit" 
                            class="w-full h-12 rounded-xl bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold hover:from-cyan-700 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-zinc-900 transition-all">
                        Access System
                    </button>

                    <?php if(Route::has('password.request')): ?>
                        <div class="text-center">
                            <a href="<?php echo e(route('password.request')); ?>" 
                               class="text-sm text-zinc-400 hover:text-cyan-400 transition-colors">
                                Forgot your password?
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if(Route::has('register')): ?>
                        <div class="text-center">
                            <span class="text-sm text-zinc-500">Don't have an account? </span>
                            <a href="<?php echo e(route('register')); ?>" 
                               class="text-sm text-cyan-400 hover:text-cyan-300 transition-colors">
                                Create one
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\panorama\resources\views/auth/login.blade.php ENDPATH**/ ?>