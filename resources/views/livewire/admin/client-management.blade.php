<div x-data="clientManagement()" class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center gap-3 glass-card p-4 rounded-2xl flex-wrap">
        <div class="relative flex-1 min-w-[220px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
            </svg>
            <input
                type="text"
                wire:model.live.debounce.500ms="search"
                placeholder="Search by name, email, number, address..."
                class="w-full bg-slate-950/80 border border-slate-800/80 rounded-xl px-4 py-2.5 pl-10 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition placeholder-slate-500"
            />
        </div>

        <button type="button" wire:click="resetFilters" class="px-4 py-2.5 bg-slate-900 border border-slate-800 text-slate-300 text-sm font-semibold rounded-xl hover:bg-slate-800 transition">
            Reset
        </button>

        <a href="{{ route('admin.clients.create') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/10 hover:shadow-indigo-500/20 active:scale-95 transition duration-300 md:ml-auto justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Add Client</span>
        </a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden border border-slate-800/40 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 border-b border-slate-800/80 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Client</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Number</th>
                        <th class="px-6 py-4">Address</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm">
                    @forelse($clients as $client)
                        <tr wire:key="client-row-{{ $client->id }}" class="hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 font-extrabold text-xs border border-indigo-500/15 shadow-inner shrink-0">
                                        {{ strtoupper(substr($client->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-200">{{ $client->name }}</div>
                                        <div class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider mt-0.5">Client record</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-slate-300">{{ $client->email }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $client->contact_no ?: 'No number' }}</td>
                            <td class="px-6 py-4 text-xs text-slate-400 max-w-xs truncate" title="{{ $client->address }}">{{ $client->address ?: 'No address' }}</td>

                            <td class="px-6 py-4">
                                @if($client->status === 'active')
                                    <span class="flex items-center gap-1.5 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Active</span>
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Inactive</span>
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" wire:click="toggleStatus({{ $client->id }})" class="p-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-emerald-400 hover:border-emerald-500/30 transition-all" title="Toggle Status">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    </button>
                                    <a href="{{ route('admin.clients.edit', $client) }}" class="p-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-indigo-400 hover:border-indigo-500/30 transition-all" title="Edit details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button type="button" @click="openDeleteModal({{ $client->toJson() }})" class="p-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-rose-400 hover:border-rose-500/30 transition-all" title="Delete client">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-slate-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs">No matching clients found. Try a different search.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-800/60">
            {{ $clients->links() }}
        </div>
    </div>

    <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" style="display: none;">
        <div @click.away="showDeleteModal = false" class="glass-card bg-slate-900 border border-slate-800/80 w-full max-w-md rounded-3xl p-6 shadow-2xl">
            <div class="w-12 h-12 rounded-full bg-rose-500/20 flex items-center justify-center text-rose-400 mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-white">Delete Client?</h3>
            <p class="text-xs text-slate-400 mt-2" x-text="`You are about to delete ${deletingClient.name}. This action cannot be undone.`"></p>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button @click="showDeleteModal = false" type="button" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-bold transition">Cancel</button>
                <form :action="'/admin/clients/' + deletingClient.id" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-rose-600/15">Delete</button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function clientManagement() {
        return {
            showDeleteModal: false,
            deletingClient: {},

            openDeleteModal(client) {
                this.deletingClient = client;
                this.showDeleteModal = true;
            }
        };
    }
</script>