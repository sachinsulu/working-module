<div x-data="departmentManagement()" class="space-y-6">

    <!-- Header Actions -->
    <div class="flex items-center justify-between glass-card p-4 rounded-2xl">
        <div>
            <h3 class="text-sm font-bold text-slate-200">Departments</h3>
            <p class="text-xs text-slate-400">Manage department records and assign heads.</p>
        </div>
        <a href="{{ route('admin.departments.create') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/10 hover:shadow-indigo-500/20 active:scale-95 transition duration-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Add Department</span>
        </a>
    </div>

    <!-- Departments Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($departments as $dept)
            <div wire:key="dept-card-{{ $dept->id }}" class="glass-card rounded-2xl p-6 space-y-4 border border-slate-800/60 flex flex-col justify-between transition hover:scale-[1.01] hover:border-slate-700/60 duration-300">
                <div class="space-y-2">
                    <h4 class="text-sm font-bold text-white">{{ $dept->title }}</h4>
                    <p class="text-xs text-slate-400">Head: {{ $dept->head ? $dept->head->name : '—' }}</p>
                </div>
                <div class="flex items-center justify-end space-x-2">
                    <a href="{{ route('admin.departments.edit', $dept) }}" class="p-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-indigo-400 hover:border-indigo-500/30 transition-all" title="Edit department">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    @if($dept->id !== auth()->user()->department_id)
                        <button @click="openDeleteModal({{ $dept->toJson() }})" class="p-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-rose-400 hover:border-rose-500/30 transition-all" title="Delete department">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-2 glass-card p-12 text-center text-slate-500">
                <div class="flex flex-col items-center justify-center gap-2">
                    <svg class="w-8 h-8 text-slate-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span class="text-xs">No departments defined. Click "Add Department" to start.</span>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Modals -->


    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" style="display:none;">
        <div @click.away="showDeleteModal = false" class="glass-card bg-slate-900 border border-slate-800/80 w-full max-w-md rounded-3xl p-6 shadow-2xl animate-scaleUp">
            <div class="w-12 h-12 rounded-full bg-rose-500/20 flex items-center justify-center text-rose-400 mb-4"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
            <h3 class="text-base font-bold text-white">Delete Department?</h3>
            <p class="text-xs text-slate-400 mt-2" x-text="`Are you sure you want to delete ${deleteTargetName}? This action cannot be undone.`"></p>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button @click="showDeleteModal = false" type="button" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-bold transition">Cancel</button>
                <form :action="'/admin/departments/' + deleteTargetId" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-rose-600/15">Delete</button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function departmentManagement() {
        return {
            showDeleteModal: false,
            deleteTargetId: null,
            deleteTargetName: '',

            openDeleteModal(dept) {
                this.deleteTargetId = dept.id;
                this.deleteTargetName = dept.title;
                this.showDeleteModal = true;
            }
        };
    }
</script>

