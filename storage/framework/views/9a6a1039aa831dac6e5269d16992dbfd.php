<!-- Header -->
<header class="h-20 flex items-center justify-between px-8 border-b border-slate-800/50 flex-shrink-0 relative z-10 bg-slate-950/40">
    <div>
        <h2 class="text-xl font-bold tracking-tight text-white capitalize"><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h2>
        <p class="text-xs text-slate-400 mt-0.5">Control panel / System access control & management modules</p>
    </div>
    
    <div class="flex items-center gap-4">
        <!-- Quick System Stats Badge -->
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-medium">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-ping"></span>
            <span>System Active (SQLite DB)</span>
        </div>
        
        <!-- Standard Logout Form -->
        <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="px-4 py-2 bg-rose-600/25 border border-rose-500/20 hover:bg-rose-600/40 text-rose-200 rounded-xl text-xs font-bold transition active:scale-95 duration-200">
                Logout
            </button>
        </form>
    </div>
</header>
<?php /**PATH D:\workmodule\longtail-WM\resources\views/layouts/partials/header.blade.php ENDPATH**/ ?>