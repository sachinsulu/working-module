@extends('layouts.app')

@section('title', $role->exists ? 'Edit Role' : 'Create Role')

@section('content')
<div class="max-w-2xl mx-auto glass-card rounded-3xl border border-slate-800/80 overflow-hidden flex flex-col animate-fadeIn">
    
    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-900/40 shrink-0">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 {{ $role->exists ? '' : 'animate-pulse' }}"></span>
            <span>{{ $role->exists ? 'Edit Role Details' : 'Configure New Role' }}</span>
        </h3>
        <a href="{{ route('admin.roles.index') }}" class="text-slate-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>
    </div>

    <form action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST" class="flex flex-col flex-1"
        x-data="formValidator({
            roleName: @js(old('roleName', $role->name ?? '')),
            selectedPermissions: @js(old('selectedPermissions', $role->exists ? $role->permissions->pluck('name')->toArray() : []))
        }, {
            roleName: [{ type: 'required' }, { type: 'max', value: 255 }],
            selectedPermissions: [{ type: 'required', message: 'Please select at least one permission.' }]
        })"
        @submit="submit" novalidate>
        @csrf
        @if($role->exists)
            @method('PUT')
        @endif

        <div class="flex-1 p-6 space-y-5">
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Role Identifier Name <span class="text-indigo-400">*</span></label>
                <input name="roleName" type="text" x-model="roleName" required placeholder="e.g. support specialist" 
                    {{ ($role->exists && $role->name === 'super admin') ? 'readonly' : '' }}
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition read-only:opacity-50 read-only:cursor-not-allowed"
                    :class="{'border-red-500': errors.roleName}" />
                <template x-if="errors.roleName"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.roleName"></p></template>
                @error('roleName') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>

            @php
                $groupedPermissions = [];
                foreach($permissions as $perm) {
                    if (in_array($perm->name, ['edit team', 'edit teams'])) {
                        $module = 'projects';
                    } elseif ($perm->name === 'view dashboard') {
                        $module = 'dashboard';
                    } else {
                        $parts = explode(' ', $perm->name);
                        $module = end($parts);
                    }
                    $groupedPermissions[$module][] = $perm;
                }
            @endphp

            <div class="space-y-4 pt-3 border-t border-slate-800/60">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2" :class="{'text-rose-400': errors.selectedPermissions}">Define Permission Grants</label>
                
                <div class="space-y-4">
                    @foreach($groupedPermissions as $module => $modulePermissions)
                        <div class="border border-slate-800 rounded-xl overflow-hidden bg-slate-950/20">
                            <div class="bg-slate-900/50 px-4 py-3 border-b border-slate-800 flex items-center gap-3">
                                <input type="checkbox" 
                                    class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500/40 bg-slate-950 border-slate-700 cursor-pointer"
                                    @change="
                                        const modulePerms = {{ json_encode(collect($modulePermissions)->pluck('name')->toArray()) }};
                                        if ($event.target.checked) {
                                            selectedPermissions = [...new Set([...selectedPermissions, ...modulePerms])];
                                        } else {
                                            selectedPermissions = selectedPermissions.filter(p => !modulePerms.includes(p));
                                        }
                                    "
                                    :checked="
                                        (() => {
                                            const modulePerms = {{ json_encode(collect($modulePermissions)->pluck('name')->toArray()) }};
                                            return modulePerms.every(p => selectedPermissions.includes(p)) && modulePerms.length > 0;
                                        })()
                                    "
                                    id="module_{{ $module }}">
                                <label for="module_{{ $module }}" class="text-sm font-bold text-slate-200 capitalize cursor-pointer select-none">
                                    {{ $module }}
                                </label>
                            </div>
                            
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 bg-slate-950/40">
                                @foreach($modulePermissions as $perm)
                                    <label class="flex items-center gap-2 cursor-pointer group select-none">
                                        <input type="checkbox" name="selectedPermissions[]" value="{{ $perm->name }}" x-model="selectedPermissions" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500/40 bg-slate-900 border-slate-700 cursor-pointer">
                                        <span class="text-xs font-medium text-slate-400 group-hover:text-slate-200 transition-colors capitalize">{{ $perm->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <template x-if="errors.selectedPermissions"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.selectedPermissions"></p></template>
                @error('selectedPermissions') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="h-20 border-t border-slate-800 px-6 bg-slate-900/40 flex items-center justify-end gap-3 shrink-0">
            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 border border-slate-800 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl text-xs font-bold transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-500/10 hover:shadow-indigo-500/20 active:scale-95 transition">
                {{ $role->exists ? 'Update Permissions' : 'Configure Role' }}
            </button>
        </div>
    </form>
</div>

@include('partials.alpine-validation')
@endsection
