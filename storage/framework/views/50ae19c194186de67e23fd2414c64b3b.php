<?php $__env->startSection('title', 'Overview Dashboard'); ?>

<div class="space-y-8">
    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Users Metric -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/15 transition duration-300"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Members</p>
                    <h3 class="text-3xl font-extrabold text-white mt-2 group-hover:text-indigo-300 transition duration-300"><?php echo e($stats['total_users']); ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shadow-[0_0_15px_rgba(99,102,241,0.1)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-slate-400">
                <span class="text-emerald-400 font-bold flex items-center gap-0.5">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path></svg>
                    100%
                </span>
                <span>accounts active in system</span>
            </div>
        </div>

        <!-- Roles Metric -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-violet-500/10 rounded-full blur-2xl group-hover:bg-violet-500/15 transition duration-300"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Access Roles</p>
                    <h3 class="text-3xl font-extrabold text-white mt-2 group-hover:text-violet-300 transition duration-300"><?php echo e($stats['total_roles']); ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center text-violet-400 shadow-[0_0_15px_rgba(139,92,246,0.1)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-slate-400">
                <span class="text-indigo-400 font-bold">Configured</span>
                <span>with Spatie laravel-permission</span>
            </div>
        </div>

        <!-- Permissions Metric -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-fuchsia-500/10 rounded-full blur-2xl group-hover:bg-fuchsia-500/15 transition duration-300"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">System Grants</p>
                    <h3 class="text-3xl font-extrabold text-white mt-2 group-hover:text-fuchsia-300 transition duration-300"><?php echo e($stats['total_permissions']); ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-fuchsia-500/10 border border-fuchsia-500/20 flex items-center justify-center text-fuchsia-400 shadow-[0_0_15px_rgba(217,70,239,0.1)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 4a2 2 0 01-2 2m0 5a3 3 0 11-6 0m6 0a3 3 0 01-3 3m-3-3a3 3 0 00-3 3m2-12a2 2 0 012-2h2a2 2 0 012 2m0 0V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v3m0 0h4"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-slate-400">
                <span class="text-emerald-400 font-bold">Dynamic</span>
                <span>granular controls configured</span>
            </div>
        </div>
    </div>

    <!-- Split Grid layout -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <!-- Left Columns: Department Distribution -->
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 space-y-6">
            <div>
                <h4 class="text-base font-bold text-white">Department distribution</h4>
                <p class="text-xs text-slate-400 mt-1">Breakdown of registered employee accounts by department</p>
            </div>
            
            <div class="space-y-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $stats['departments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold">
                            <span class="text-slate-300"><?php echo e($dept['department']); ?></span>
                            <span class="text-indigo-400"><?php echo e($dept['count']); ?> member<?php echo e($dept['count'] > 1 ? 's' : ''); ?></span>
                        </div>
                        <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-fuchsia-500 rounded-full" style="width: <?php echo e(($dept['count'] / max($stats['total_users'], 1)) * 100); ?>%"></div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="text-center py-6 text-slate-500 text-xs">No department data seeded.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <!-- Right Columns: Recent Signups -->
        <div class="lg:col-span-3 glass-card rounded-2xl p-6 space-y-6">
            <div>
                <h4 class="text-base font-bold text-white">Recently registered members</h4>
                <p class="text-xs text-slate-400 mt-1">Latest members added to the system directory database</p>
            </div>

            <div class="divide-y divide-slate-800/60 space-y-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $stats['recent_users']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="flex items-center justify-between pt-4 first:pt-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 font-bold text-xs uppercase border border-indigo-500/15">
                                <?php echo e(substr($rUser->name, 0, 2)); ?>

                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-200"><?php echo e($rUser->name); ?></h5>
                                <p class="text-[10px] text-slate-400 mt-0.5"><?php echo e($rUser->email); ?> • <?php echo e($rUser->department ?: 'Unassigned Dept'); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rUser->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-indigo-500/10 border border-indigo-500/20 text-indigo-300">
                                    <?php echo e($role->name); ?>

                                </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="text-center py-6 text-slate-500 text-xs">No registered users in system.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\workmodule\longtail-WM\resources\views/livewire/admin/dashboard.blade.php ENDPATH**/ ?>