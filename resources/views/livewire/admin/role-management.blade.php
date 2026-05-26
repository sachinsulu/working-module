<div x-data="roleManagement()" class="space-y-6">

    <!-- Header Actions -->
    <div class="flex items-center justify-between glass-card p-4 rounded-2xl">
        <div>
            <h3 class="text-sm font-bold text-slate-200">System roles list</h3>
            <p class="text-xs text-slate-400">Add or edit system roles and assign permissions.</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/10 hover:shadow-indigo-500/20 active:scale-95 transition duration-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Add Role</span>
        </a>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($roles as $role)
            <div wire:key="role-card-{{ $role->id }}" class="glass-card rounded-2xl p-6 space-y-4 border border-slate-800/60 flex flex-col justify-between transition hover:scale-[1.01] hover:border-slate-700/60 duration-300">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $role->name === 'super admin' ? 'bg-indigo-400' : ($role->name === 'dept head' ? 'bg-fuchsia-400' : ($role->name === 'mgmt' ? 'bg-cyan-400' : 'bg-emerald-400')) }}"></span>
                            <h4 class="text-sm font-bold text-white capitalize tracking-wide">{{ $role->name }}</h4>
                        </div>
                        @if($role->name === 'super admin')
                            <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-slate-900 border border-slate-800 text-slate-400">Protected</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400">Identified as `{{ $role->name }}` guard system role with explicit permissions mapping.</p>
                    <div class="space-y-1.5 pt-2">
                        <h5 class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Assigned permissions ({{ $role->permissions->count() }})</h5>
                        <div class="flex flex-wrap gap-1">
                             @forelse($role->permissions as $perm)
                                <span class="px-2 py-0.5 rounded text-[9px] font-medium bg-slate-900 border border-slate-800 text-slate-300">{{ $perm->name }}</span>
                            @empty
                                <span class="text-slate-500 text-xs italic">No grants assigned</span>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-800/40 flex items-center justify-between text-xs mt-4">
                    <span class="text-slate-400 font-medium">Active database scope</span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 text-xs font-semibold border border-indigo-500/15 hover:border-indigo-500/30 transition-all duration-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4"/></svg>
                            <span>Edit Permissions</span>
                        </a>
                        @if($role->name !== 'super admin')
                            <button @click="openDeleteModal({{ json_encode($role) }})" class="p-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-rose-400 hover:border-rose-500/30 transition-all" title="Delete role">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 glass-card p-12 text-center text-slate-500">
                <div class="flex flex-col items-center justify-center gap-2">
                    <svg class="w-8 h-8 text-slate-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span class="text-xs">No roles defined. Click "Add Role" to start.</span>
                </div>
            </div>
        @endforelse
    </div>

    <!-- ====================================================
         MODALS (AlpineJS Controlled)
         ==================================================== -->



    <!-- B. DELETE ROLE CONFIRMATION MODAL -->
    <div x-show="showDeleteRoleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm transition-all duration-300" style="display: none;">
        <div @click.away="showDeleteRoleModal = false" class="glass-card bg-slate-900 border border-slate-800/80 w-full max-w-md rounded-3xl p-6 shadow-2xl animate-scaleUp">
            <div class="w-12 h-12 rounded-full bg-rose-500/20 flex items-center justify-center text-rose-400 mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-white">Decommission Role?</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">You are about to delete role <span class="text-indigo-400 font-bold" x-text="deleteTargetRoleName"></span> from the system records. This action cannot be undone and will remove all associated permission grants.</p>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button @click="showDeleteRoleModal = false" type="button" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-bold transition">Cancel</button>
                <form :action="'/admin/roles/' + deleteTargetRoleId" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-rose-600/15">Decommission</button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function roleManagement() {
        return {
            showDeleteRoleModal: false,
            deleteTargetRoleId: null,
            deleteTargetRoleName: '',

            openDeleteModal(role) {
                this.deleteTargetRoleId = role.id;
                this.deleteTargetRoleName = role.name;
                this.showDeleteRoleModal = true;
            }
        };
    }
</script>

